<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $logPath = storage_path('logs/laravel.log');
        $logs = [];

        try {
            Log::info('Starting to process log file at: ' . $logPath);

            if (file_exists($logPath)) {
                $handle = fopen($logPath, 'r');
                if ($handle === false) {
                    Log::error('Could not open laravel.log at ' . $logPath);
                    $logs[] = ['message' => 'No se pudo abrir el archivo de logs.', 'timestamp' => 'N/A', 'level' => 'N/A', 'context' => 'N/A', 'details' => 'N/A'];
                } else {
                    $lineCount = 0;
                    while (($line = fgets($handle)) !== false && $lineCount < 1000) { // Limitamos a 1000 líneas por ahora
                        $line = trim($line);
                        if ($line) {
                            Log::debug("Processing line $lineCount: $line");
                            // Regex más flexible para capturar formatos comunes de Laravel logs
                            if (preg_match('/^\[(.*?)\] (\w+\.?[\w\d]*)\.?(\w+)?(?:\.(?:[\w\d]+))?: (.*?)(?: {?(?:[\w"=,\s:\/\-\.]+)}?)?$/', $line, $matches)) {
                                $log = [
                                    'timestamp' => trim($matches[1] ?? 'N/A'),
                                    'level' => trim($matches[2] ?? 'N/A'),
                                    'context' => trim($matches[3] ?? 'N/A'),
                                    'message' => trim($matches[4] ?? 'N/A'),
                                    'details' => trim(isset($matches[5]) ? $matches[5] : 'N/A'),
                                ];
                                $logs[] = $log;
                            } else {
                                Log::warning("Failed to parse line $lineCount: $line");
                                $logs[] = ['message' => $line, 'timestamp' => 'N/A', 'level' => 'N/A', 'context' => 'N/A', 'details' => 'N/A'];
                            }
                        }
                        $lineCount++;
                    }
                    fclose($handle);
                }
            } else {
                Log::error('laravel.log not found at ' . $logPath);
                $logs[] = ['message' => 'No se encontró el archivo de logs.', 'timestamp' => 'N/A', 'level' => 'N/A', 'context' => 'N/A', 'details' => 'N/A'];
            }

            Log::info('Total logs parsed: ' . count($logs));

            $perPage = 25;
            $currentPage = $request->input('page', 1);
            $total = count($logs);
            $paginatedLogs = new LengthAwarePaginator(
                array_slice($logs, ($currentPage - 1) * $perPage, $perPage),
                $total,
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            Log::info('Paginated logs count: ' . count($paginatedLogs->items()));

            return Inertia::render('Logs/Index', [
                'logs' => $paginatedLogs,
            ]);
        } catch (\Exception $e) {
            Log::error('Exception in LogController@index: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return Inertia::render('Logs/Index', [
                'logs' => new LengthAwarePaginator([], 0, $perPage, $currentPage, ['path' => $request->url(), 'query' => $request->query()]),
                'error' => 'Ocurrió un error al procesar los logs. Consulta los logs del servidor.',
            ]);
        }
    }
}