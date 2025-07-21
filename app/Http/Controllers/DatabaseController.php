<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Area;
use App\Models\Peticion;

class DatabaseController extends Controller
{
    private function getAllowedTables()
    {
        $allowedTables = ['areas', 'peticions', 'users'];
        $existingTables = \DB::select("
            SELECT table_name 
            FROM information_schema.tables 
            WHERE table_schema = ? AND table_name IN (?,?,?)
        ", [env('DB_DATABASE'), 'areas', 'peticions', 'users']);
        
        $existingTableNames = array_column($existingTables, 'table_name');
        \Log::debug('Tablas existentes en la base de datos:', [
            'database' => env('DB_DATABASE'),
            'existing_tables' => $existingTableNames,
        ]);

        if (empty($existingTableNames)) {
            \Log::warning('Ninguna tabla permitida encontrada en la base de datos.', [
                'database' => env('DB_DATABASE'),
                'attempted_tables' => $allowedTables,
            ]);
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($existingTableNames), '?'));

        $params = array_merge([env('DB_DATABASE')], $existingTableNames);

        \Log::debug('Preparando consulta para obtener tablas:', [
            'database' => env('DB_DATABASE'),
            'tables' => $existingTableNames,
            'placeholders' => $placeholders,
            'params' => $params,
        ]);

        try {
            $tables = \DB::select("
                SELECT 
                    table_name AS `name`,
                    table_rows AS `row_count`,
                    engine AS `engine_type`,
                    table_collation AS `collation`,
                    ROUND(data_length / 1024, 1) AS `size`,
                    ROUND(data_free / 1024, 1) AS `overhead`
                FROM information_schema.tables
                WHERE table_schema = ? AND table_name IN ($placeholders)
            ", $params);

            $formattedTables = array_map(function ($table) {
                return [
                    'name' => $table->name,
                    'rows' => $table->row_count ?? 0,
                    'type' => $table->engine_type ?? 'Unknown',
                    'collation' => $table->collation ?? 'Unknown',
                    'size' => number_format($table->size, 1) . ' KB',
                    'overhead' => $table->overhead > 0 ? number_format($table->overhead, 1) . ' KB' : '-',
                ];
            }, $tables);

            \Log::debug('Tablas obtenidas de la base de datos:', [
                'tables' => $formattedTables,
                'count' => count($formattedTables),
            ]);

            return $formattedTables;
        } catch (\Exception $e) {
            \Log::error('Error al consultar tablas en information_schema:', [
                'message' => $e->getMessage(),
                'params' => $params,
            ]);
            return [];
        }
    }

    public function index(Request $request)
    {
        if (!$request->user() || $request->user()->role !== 'encargado') {
            abort(403, 'No autorizado');
        }

        $tables = $this->getAllowedTables();
        \Log::debug('Datos enviados a Index.vue:', [
            'databases' => $tables,
            'count' => count($tables),
        ]);

        return Inertia::render('Databases/Index', [
            'databases' => $tables,
        ]);
    }

    public function show(Request $request, $table)
    {
        if (!$request->user() || $request->user()->role !== 'encargado') {
            abort(403, 'No autorizado');
        }

        $allowedTables = array_column($this->getAllowedTables(), 'name');
        if (!in_array($table, $allowedTables)) {
            \Log::error("Tabla no encontrada: {$table}");
            abort(404, 'Tabla no encontrada');
        }

        try {
            $excludedColumns = ['id'];
            if ($table === 'users') {
                $excludedColumns = array_merge($excludedColumns, [
                    'email_verified_at',
                    'two_factor_secret',
                    'two_factor_recovery_codes',
                    'two_factor_confirmed_at',
                    'remember_token',
                    'current_team_id',
                    'profile_photo_path',
                ]);
            }
            $records = match ($table) {
                'users' => User::all()->toArray(),
                'areas' => Area::all()->toArray(),
                'peticions' => Peticion::all()->toArray(),
                default => \DB::table($table)->get()->toArray(),
            };
            $columns = array_diff(Schema::getColumnListing($table), $excludedColumns);
            $areas = in_array($table, ['peticions', 'users']) ? Area::select('id', 'nombre')->get()->toArray() : [];

            \Log::debug("Datos enviados a Show.vue para la tabla {$table}: ", [
                'table' => $table,
                'columns' => $columns,
                'records_count' => count($records),
                'areas' => $areas,
                'areas_count' => count($areas),
            ]);

            return Inertia::render('Databases/Show', [
                'table' => $table,
                'columns' => array_values($columns),
                'records' => $records,
                'areas' => $areas,
            ]);
        } catch (\Exception $e) {
            \Log::error("Error al cargar la tabla {$table}: " . $e->getMessage());
            return redirect()->route('databases.index')->withErrors(['error' => 'Error al cargar la tabla: ' . $e->getMessage()]);
        }
    }

    public function store(Request $request, $table)
    {
        if (!$request->user() || $request->user()->role !== 'encargado') {
            abort(403, 'No autorizado');
        }

        $allowedTables = array_column($this->getAllowedTables(), 'name');
        if (!in_array($table, $allowedTables)) {
            \Log::error("Tabla no encontrada en store: {$table}");
            abort(404, 'Tabla no encontrada');
        }

        try {
            if ($table === 'users') {
                $data = $request->validate([
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                    'password' => ['required', 'string', 'min:8'],
                    'role' => ['required', 'string', 'in:encargado,cliente'],
                    'area_id' => ['required', 'exists:areas,id'],
                ], [
                    'name.required' => 'El campo nombre es obligatorio.',
                    'email.required' => 'El campo correo electrónico es obligatorio.',
                    'email.email' => 'El correo electrónico debe ser una dirección válida.',
                    'email.unique' => 'El correo electrónico ya está en uso.',
                    'password.required' => 'El campo contraseña es obligatorio.',
                    'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
                    'role.required' => 'El campo rol es obligatorio.',
                    'role.in' => 'El rol debe ser "encargado" o "cliente".',
                    'area_id.required' => 'El campo área es obligatorio.',
                    'area_id.exists' => 'El área seleccionada no es válida.',
                ]);

                $data['password'] = Hash::make($data['password']);
                User::create($data);
            } elseif ($table === 'areas') {
                $data = $request->validate([
                    'nombre' => ['required', 'string', 'max:255', 'unique:areas,nombre'],
                ], [
                    'nombre.required' => 'El campo nombre es obligatorio.',
                    'nombre.unique' => 'El nombre del área ya está en uso.',
                    'nombre.max' => 'El nombre del área no puede exceder los 255 caracteres.',
                ]);
                Area::create($data);
            } elseif ($table === 'peticions') {
                $data = $request->validate([
                    'area_id' => ['required', 'exists:areas,id'],
                    'descripcion' => ['nullable', 'string', 'max:255'],
                ], [
                    'area_id.required' => 'El campo área es obligatorio.',
                    'area_id.exists' => 'El área seleccionada no es válida.',
                    'descripcion.max' => 'La descripción no puede exceder los 255 caracteres.',
                ]);
                Peticion::create($data);
            } else {
                $excludedColumns = ['id', 'created_at', 'updated_at'];
                $columns = array_diff(Schema::getColumnListing($table), $excludedColumns);
                $rules = array_fill_keys($columns, ['nullable']);
                $data = $request->validate($rules);
                \DB::table($table)->insert($data);
            }

            return redirect()->route('databases.show', $table)->with('success', 'Registro creado correctamente');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error("Error de validación en la tabla {$table}: ", [
                'errors' => $e->errors(),
                'request' => $request->all(),
            ]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error("Error al crear registro en la tabla {$table}: " . $e->getMessage(), [
                'data' => $request->all(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Error al crear el registro: ' . $e->getMessage()])->withInput();
        }
    }

    public function update(Request $request, $table, $id)
    {
        if (!$request->user() || $request->user()->role !== 'encargado') {
            abort(403, 'No autorizado');
        }

        $allowedTables = array_column($this->getAllowedTables(), 'name');
        if (!in_array($table, $allowedTables)) {
            \Log::error("Tabla no encontrada en update: {$table}");
            abort(404, 'Tabla no encontrada');
        }

        try {
            if ($table === 'users') {
                $data = $request->validate([
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $id],
                    'password' => ['nullable', 'string', 'min:8'],
                    'role' => ['required', 'string', 'in:encargado,cliente'],
                    'area_id' => ['required', 'exists:areas,id'],
                ], [
                    'name.required' => 'El campo nombre es obligatorio.',
                    'email.required' => 'El campo correo electrónico es obligatorio.',
                    'email.email' => 'El correo electrónico debe ser una dirección válida.',
                    'email.unique' => 'El correo electrónico ya está en uso.',
                    'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
                    'role.required' => 'El campo rol es obligatorio.',
                    'role.in' => 'El rol debe ser "encargado" o "cliente".',
                    'area_id.required' => 'El campo área es obligatorio.',
                    'area_id.exists' => 'El área seleccionada no es válida.',
                ]);

                if (empty($data['password'])) {
                    unset($data['password']);
                } else {
                    $data['password'] = Hash::make($data['password']);
                }

                \Log::info("Actualizando usuario ID: {$id}", ['data' => $data]);
                User::where('id', $id)->update($data);
            } elseif ($table === 'areas') {
                $data = $request->validate([
                    'nombre' => ['required', 'string', 'max:255', 'unique:areas,nombre,' . $id],
                ], [
                    'nombre.required' => 'El campo nombre es obligatorio.',
                    'nombre.unique' => 'El nombre del área ya está en uso.',
                    'nombre.max' => 'El nombre del área no puede exceder los 255 caracteres.',
                ]);
                Area::where('id', $id)->update($data);
            } elseif ($table === 'peticions') {
                $data = $request->validate([
                    'area_id' => ['required', 'exists:areas,id'],
                    'descripcion' => ['nullable', 'string', 'max:255'],
                ], [
                    'area_id.required' => 'El campo área es obligatorio.',
                    'area_id.exists' => 'El área seleccionada no es válida.',
                    'descripcion.max' => 'La descripción no puede exceder los 255 caracteres.',
                ]);
                Peticion::where('id', $id)->update($data);
            } else {
                $excludedColumns = ['id', 'created_at', 'updated_at'];
                $columns = array_diff(Schema::getColumnListing($table), $excludedColumns);
                $rules = array_fill_keys($columns, ['nullable']);
                $data = $request->validate($rules);
                \DB::table($table)->where('id', $id)->update($data);
            }

            return redirect()->route('databases.show', $table)->with('success', 'Registro actualizado correctamente');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error("Error de validación al actualizar en la tabla {$table}: ", [
                'errors' => $e->errors(),
                'request' => $request->all(),
            ]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error("Error al actualizar registro en la tabla {$table}: " . $e->getMessage(), [
                'data' => $request->all(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Error al actualizar el registro: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(Request $request, $table, $id)
    {
        if (!$request->user() || $request->user()->role !== 'encargado') {
            abort(403, 'No autorizado');
        }

        $allowedTables = array_column($this->getAllowedTables(), 'name');
        if (!in_array($table, $allowedTables)) {
            \Log::error("Tabla no encontrada en destroy: {$table}");
            abort(404, 'Tabla no encontrada');
        }

        try {
            match ($table) {
                'users' => User::where('id', $id)->delete(),
                'areas' => Area::where('id', $id)->delete(),
                'peticions' => Peticion::where('id', $id)->delete(),
                default => \DB::table($table)->where('id', $id)->delete(),
            };

            return redirect()->route('databases.show', $table)->with('success', 'Registro eliminado correctamente');
        } catch (\Exception $e) {
            \Log::error("Error al eliminar registro en la tabla {$table}: " . $e->getMessage(), [
                'id' => $id,
            ]);
            return redirect()->back()->withErrors(['error' => 'Error al eliminar el registro: ' . $e->getMessage()]);
        }
    }
}