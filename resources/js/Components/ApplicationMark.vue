<template>
  <div class="flex items-center">
    <svg class="h-12 w-auto" viewBox="0 0 120 40" fill="none" xmlns="http://www.w3.org/2000/svg">
      <!-- Rectángulo del sobre con bordes más suaves y sombra -->
      <rect x="10" y="10" width="30" height="20" rx="6" fill="#F97316" class="shadow-lg"/>
      
      <!-- Línea del sobre -->
      <path d="M10 15L25 25L40 15" stroke="white" stroke-width="2" stroke-linecap="round"/>
      
      <!-- Texto con estilo moderno y sombra sutil -->
      <text x="50" y="25" font-family="Poppins, sans-serif" font-size="18" font-weight="600" fill="#1F2937" class="text-shadow-md">
        
      </text>
    </svg>
  </div>
</template>

<style scoped>
/* Agregamos una sombra al texto para hacerlo más atractivo */
.text-shadow-md {
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
}

/* Agregar un efecto hover para resaltar el ícono */
svg:hover rect {
  fill: #f57c0b; /* Color más brillante */
  transition: fill 0.3s ease;
}

svg:hover text {
  fill: #374151; /* Cambiar el color del texto cuando se pasa el mouse */
  transition: fill 0.3s ease;
}
</style>
