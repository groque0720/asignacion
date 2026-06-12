    <!-- ── Leyenda de estados + contador ─────────────────────────────────── -->
    <div class="flex items-center justify-between flex-wrap gap-3 px-1">
      <div class="flex items-center gap-5 text-xs text-slate-600">
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full" style="background:#4CAF50"></span> Libre</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full" style="background:#FFEB3B"></span> Reservado</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full" style="background:#F44336"></span> Vendido</span>
      </div>
      <div class="text-sm text-slate-500">
        <span class="font-semibold text-slate-700" x-text="filasFiltradas().length"></span> planes
        <span x-show="filtros.q || filtros.estado" class="text-slate-400">(de <span x-text="rows.length"></span>)</span>
      </div>
    </div>
