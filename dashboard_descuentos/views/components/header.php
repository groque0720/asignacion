<?php /* Header del dashboard: logo + título + indicador de carga. */ ?>
<header class="bg-slate-900 text-white shadow-lg sticky top-0 z-30">
  <div class="max-w-[1500px] mx-auto px-6 py-3 flex items-center justify-between gap-4">
    <div class="flex items-center gap-3">
      <img src="/login/imagenes/logo_dyv.png" alt="Logo" class="w-9 h-9 rounded-full bg-white">
      <div>
        <h1 class="text-sm font-bold leading-tight">Dashboard · Descuentos</h1>
        <p class="text-slate-400 text-xs">Unidades 0km entregadas · Derka y Vargas S.A.</p>
      </div>
    </div>
    <div class="flex items-center gap-3 text-xs text-slate-300">
      <span x-show="!loading" x-cloak>
        <i class="fas fa-box-open mr-1"></i><span class="num" x-text="int(kpis.entregadas)"></span> entregadas
      </span>
      <i class="fas fa-circle-notch fa-spin text-slate-400" x-show="loading"></i>
    </div>
  </div>
</header>
