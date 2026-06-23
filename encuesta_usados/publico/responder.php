<?php
/*
 * Encuesta pública de usados (mobile-first). Acceso por token único (?t=...).
 * Sin login. Renderiza la encuesta activa y postea a responder_guardar.php.
 */
require __DIR__ . '/bootstrap_publico.php';

$token = isset($_GET['t']) ? trim($_GET['t']) : '';
if ($token === '') { header('Location: expirada.php'); exit(); }

$te = mysqli_real_escape_string($con, $token);
$tk = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT id_token, id_encuesta, completada FROM encu_tokens WHERE token = '$te' LIMIT 1"));
if (!$tk) { header('Location: expirada.php'); exit(); }
if ((int)$tk['completada'] === 1) { header('Location: expirada.php?tipo=completada'); exit(); }

$id_token    = (int)$tk['id_token'];
$id_encuesta = (int)$tk['id_encuesta'];

$enc = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT id_encuesta, nombre, mensaje_bienvenida FROM encu_encuestas
     WHERE id_encuesta = $id_encuesta AND baja = 0 LIMIT 1"));
if (!$enc) { header('Location: expirada.php'); exit(); }

// Preguntas activas + opciones
$resP = mysqli_query($con,
    "SELECT id_pregunta, nro_orden, texto_pregunta, tipo_pregunta, pondera, es_observacion,
            cond_id_preg_ref, cond_operador, cond_valor
     FROM encu_preguntas WHERE id_encuesta = $id_encuesta AND baja = 0
     ORDER BY nro_orden ASC, id_pregunta ASC");
$preguntas = [];
while ($p = mysqli_fetch_assoc($resP)) {
    $idp = (int)$p['id_pregunta'];
    $op = [];
    if (in_array((int)$p['tipo_pregunta'], [3, 4], true)) {
        $resO = mysqli_query($con,
            "SELECT id_opcion, texto_opcion FROM encu_opciones
             WHERE id_pregunta = $idp AND baja = 0 ORDER BY nro_orden ASC, id_opcion ASC");
        while ($o = mysqli_fetch_assoc($resO)) {
            $op[] = ['id' => (int)$o['id_opcion'], 'texto' => $o['texto_opcion']];
        }
    }
    $preguntas[] = [
        'id'       => $idp,
        'texto'    => $p['texto_pregunta'],
        'tipo'     => (int)$p['tipo_pregunta'],
        'pondera'  => (int)$p['pondera'],
        'cond_ref' => $p['cond_id_preg_ref'] !== null ? (int)$p['cond_id_preg_ref'] : null,
        'cond_op'  => $p['cond_operador'],
        'cond_val' => $p['cond_valor'],
        'opciones' => $op,
    ];
}

$datos = [
    'token'       => $token,
    'id_token'    => $id_token,
    'id_encuesta' => $id_encuesta,
    'bienvenida'  => $enc['mensaje_bienvenida'],
    'preguntas'   => $preguntas,
];
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title><?= htmlspecialchars($enc['nombre']) ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', system-ui, sans-serif; }
    [x-cloak] { display: none !important; }
  </style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800"
      x-data="responderEncuesta(<?= htmlspecialchars(json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES) ?>)"
      x-cloak>

  <!-- Barra de progreso -->
  <div class="fixed top-0 left-0 right-0 h-1.5 bg-slate-200 z-40">
    <div class="h-full bg-blue-600 transition-all duration-300" :style="'width:' + progreso() + '%'"></div>
  </div>

  <div class="max-w-xl mx-auto px-5 pt-10 pb-32 min-h-screen flex flex-col">

    <!-- Encabezado -->
    <div class="flex items-center gap-2 text-slate-400 text-xs mb-6">
      <i class="fas fa-car-side"></i>
      <span class="font-medium uppercase tracking-wider">Derka y Vargas S.A.</span>
      <span class="ml-auto" x-show="paso >= 0" x-text="(posVisible()+1) + ' / ' + totalVisible()"></span>
    </div>

    <!-- Bienvenida -->
    <div x-show="paso === -1" class="flex-1 flex flex-col justify-center text-center">
      <div class="w-16 h-16 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-2xl mx-auto mb-5">
        <i class="fas fa-comment-dots"></i>
      </div>
      <h1 class="text-2xl font-bold text-slate-900 mb-3">¡Gracias por su compra!</h1>
      <p class="text-slate-600 leading-relaxed mb-8" x-text="bienvenida || 'Nos gustaría conocer su experiencia. La encuesta toma menos de 2 minutos.'"></p>
      <button @click="empezar()"
              class="mx-auto px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg shadow-blue-600/20">
        Comenzar <i class="fas fa-arrow-right ml-1"></i>
      </button>
    </div>

    <!-- Sin preguntas -->
    <div x-show="paso >= 0 && preguntas.length === 0" class="flex-1 flex items-center justify-center text-center text-slate-400">
      <p>Esta encuesta todavía no tiene preguntas configuradas.</p>
    </div>

    <!-- Slide de pregunta -->
    <template x-for="(p, i) in preguntas" :key="p.id">
      <div x-show="paso === i" class="flex-1 flex flex-col justify-center">
        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider mb-2">Pregunta <span x-text="posVisible()+1"></span></p>
        <h2 class="text-xl font-bold text-slate-900 mb-6 leading-snug" x-text="p.texto"></h2>

        <!-- Tipo 1: Escala 1-10 -->
        <template x-if="p.tipo === 1">
          <div>
            <div class="grid grid-cols-5 gap-2">
              <template x-for="n in 10" :key="n">
                <button type="button" @click="setValor(p.id, n)"
                        class="aspect-square rounded-xl border-2 font-bold text-lg transition"
                        :class="(resp[p.id] && resp[p.id].valor === n)
                                 ? 'bg-blue-600 border-blue-600 text-white shadow-md'
                                 : 'bg-white border-slate-200 text-slate-700 hover:border-blue-300'"
                        x-text="n"></button>
              </template>
            </div>
            <div class="flex justify-between text-[11px] text-slate-400 mt-2 px-1">
              <span>Muy malo</span><span>Excelente</span>
            </div>
          </div>
        </template>

        <!-- Tipo 2: Sí / No -->
        <template x-if="p.tipo === 2">
          <div class="grid grid-cols-2 gap-3">
            <button type="button" @click="setValor(p.id, 1)"
                    class="py-4 rounded-xl border-2 font-semibold transition"
                    :class="(resp[p.id] && resp[p.id].valor === 1) ? 'bg-emerald-600 border-emerald-600 text-white' : 'bg-white border-slate-200 text-slate-700 hover:border-emerald-300'">
              <i class="fas fa-check mr-1"></i> Sí
            </button>
            <button type="button" @click="setValor(p.id, 0)"
                    class="py-4 rounded-xl border-2 font-semibold transition"
                    :class="(resp[p.id] && resp[p.id].valor === 0) ? 'bg-red-600 border-red-600 text-white' : 'bg-white border-slate-200 text-slate-700 hover:border-red-300'">
              <i class="fas fa-xmark mr-1"></i> No
            </button>
          </div>
        </template>

        <!-- Tipo 3: Selección múltiple -->
        <template x-if="p.tipo === 3">
          <div class="space-y-2">
            <template x-for="o in p.opciones" :key="o.id">
              <button type="button" @click="toggleMultiple(p.id, o.id)"
                      class="w-full flex items-center gap-3 p-3 rounded-xl border-2 text-left transition"
                      :class="estaMarcada(p.id, o.id) ? 'bg-blue-50 border-blue-400' : 'bg-white border-slate-200 hover:border-blue-200'">
                <span class="w-5 h-5 rounded border-2 flex items-center justify-center flex-shrink-0"
                      :class="estaMarcada(p.id, o.id) ? 'bg-blue-600 border-blue-600 text-white' : 'border-slate-300'">
                  <i class="fas fa-check text-[10px]" x-show="estaMarcada(p.id, o.id)"></i>
                </span>
                <span class="text-slate-700 text-sm" x-text="o.texto"></span>
              </button>
            </template>
          </div>
        </template>

        <!-- Tipo 4: Lista Sí/No -->
        <template x-if="p.tipo === 4">
          <div class="space-y-2">
            <template x-for="o in p.opciones" :key="o.id">
              <div class="flex items-center gap-3 p-3 rounded-xl border-2 border-slate-200 bg-white">
                <span class="text-slate-700 text-sm flex-1" x-text="o.texto"></span>
                <div class="flex gap-1.5 flex-shrink-0">
                  <button type="button" @click="setLista(p.id, o.id, 1)"
                          class="px-3 py-1.5 rounded-lg text-xs font-semibold border"
                          :class="valLista(p.id, o.id) === 1 ? 'bg-emerald-600 border-emerald-600 text-white' : 'bg-white border-slate-200 text-slate-500'">Sí</button>
                  <button type="button" @click="setLista(p.id, o.id, 0)"
                          class="px-3 py-1.5 rounded-lg text-xs font-semibold border"
                          :class="valLista(p.id, o.id) === 0 ? 'bg-red-600 border-red-600 text-white' : 'bg-white border-slate-200 text-slate-500'">No</button>
                </div>
              </div>
            </template>
          </div>
        </template>

        <!-- Tipo 5: Texto libre -->
        <template x-if="p.tipo === 5">
          <textarea @input="setTexto(p.id, $event.target.value)"
                    :value="resp[p.id] ? (resp[p.id].texto || '') : ''"
                    rows="5" placeholder="Escribí tu comentario (opcional)…"
                    class="w-full border-2 border-slate-200 rounded-xl p-4 text-slate-700 focus:border-blue-400 outline-none resize-none"></textarea>
        </template>

        <p x-show="errorMsg" class="text-red-600 text-sm mt-4"><i class="fas fa-circle-exclamation mr-1"></i><span x-text="errorMsg"></span></p>
      </div>
    </template>
  </div>

  <!-- Navegación fija -->
  <div x-show="paso >= 0" class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 z-40">
    <div class="max-w-xl mx-auto px-5 py-3 flex items-center gap-3">
      <button @click="anterior()" :disabled="enviando"
              class="px-5 py-3 rounded-xl border border-slate-300 text-slate-600 font-medium disabled:opacity-40 hover:bg-slate-50">
        <i class="fas fa-arrow-left"></i>
      </button>
      <button @click="siguiente()" :disabled="enviando"
              class="flex-1 px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold disabled:opacity-60">
        <span x-show="!enviando"><span x-text="esUltima() ? 'Finalizar' : 'Siguiente'"></span>
          <i class="fas" :class="esUltima() ? 'fa-check ml-1' : 'fa-arrow-right ml-1'"></i></span>
        <span x-show="enviando"><i class="fas fa-spinner fa-spin mr-1"></i> Enviando…</span>
      </button>
    </div>
  </div>

  <script src="responder.js?v=<?= @filemtime(__DIR__ . '/responder.js') ?: 0 ?>"></script>
</body>
</html>
