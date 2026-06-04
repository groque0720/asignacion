  <!-- ── Popover de estados (fixed: no lo recorta el scroll de la tabla) ────── -->
  <div x-show="popState.open" x-cloak x-transition.opacity.duration.100ms
       @click.outside="popState.open = false"
       @keydown.escape.window="popState.open = false"
       class="fixed z-50 w-60 bg-white rounded-xl shadow-2xl border border-gray-200 py-1.5"
       :style="`left:${popState.x}px; top:${popState.y}px`">
    <p class="px-3 pb-1.5 mb-1 text-[10px] font-semibold uppercase tracking-wide text-slate-400 border-b border-gray-100">Estados de la operación</p>
    <template x-for="b in popState.badges" :key="b.key">
      <a :href="b.href || null" :target="b.href ? '_blank' : null" @click="b.href && (popState.open = false)"
         class="flex items-center gap-2.5 px-3 py-1.5 hover:bg-slate-50"
         :class="b.href ? 'cursor-pointer' : 'cursor-default pointer-events-none'">
        <span class="w-6 h-6 rounded-md flex items-center justify-center text-xs flex-shrink-0"
              :style="`background:${b.bg};color:${b.fg};border:1px solid ${b.fg}40`">
          <i :class="b.icon"></i>
        </span>
        <span class="text-xs text-slate-700 leading-tight" x-text="b.title"></span>
        <i x-show="b.href" class="fas fa-arrow-up-right-from-square text-[9px] text-slate-300 ml-auto"></i>
      </a>
    </template>
  </div>
