@php
    // Filament nam daje puni state path (npr. "mountedTableActionFormData.signature")
    $statePath = $getStatePath();
    $uid = 'sig_' . preg_replace('/[^a-z0-9_]/i', '_', $statePath);
@endphp

<div id="{{ $uid }}_wrap" data-ozo-signature="{{ $uid }}"
     class="md:grid md:grid-cols-[780px_auto] md:items-start md:gap-3 flex flex-col gap-3">

    <div class="border border-gray-500 rounded-md bg-white"
         style="width:780px; max-width:100%; height:240px;">
        <canvas id="{{ $uid }}_canvas"
                class="w-full h-full block"
                style="touch-action:none; user-select:none; pointer-events:auto;"></canvas>
    </div>

    <div class="md:flex md:flex-col md:gap-2 md:ml-2 flex flex-row gap-2">
        <button type="button" id="{{ $uid }}_clear"
                class="px-3 py-2 rounded-md bg-gray-600 text-white w-full md:w-28">Clear</button>
        <button type="button" id="{{ $uid }}_svg"
                class="px-3 py-2 rounded-md bg-gray-600 text-white w-full md:w-28">.svg</button>
        <button type="button" id="{{ $uid }}_png"
                class="px-3 py-2 rounded-md bg-gray-600 text-white w-full md:w-28">.png</button>
        <button type="button" id="{{ $uid }}_jpg"
                class="px-3 py-2 rounded-md bg-gray-600 text-white w-full md:w-28">.jpg</button>
    </div>

    {{-- Jedina veza s Livewire state-om --}}
    <input type="hidden" id="{{ $uid }}_state" wire:model.defer="{{ $statePath }}" />
</div>

<script>
(function () {
  function mount(uid) {
    const wrap   = document.getElementById(uid + '_wrap');
    if (!wrap || wrap.dataset.initialized === '1') return;

    const canvas = document.getElementById(uid + '_canvas');
    const clearB = document.getElementById(uid + '_clear');
    const svgB   = document.getElementById(uid + '_svg');
    const pngB   = document.getElementById(uid + '_png');
    const jpgB   = document.getElementById(uid + '_jpg');
    const stateI = document.getElementById(uid + '_state');
    if (!canvas || !stateI) return;

    const ctx = canvas.getContext('2d', { willReadFrequently: true });
    let drawing = false, lastX = 0, lastY = 0, dpr = 1;

    function resize() {
      dpr = Math.max(window.devicePixelRatio || 1, 1);
      const rect = canvas.getBoundingClientRect();
      canvas.width  = Math.floor(rect.width  * dpr);
      canvas.height = Math.floor(rect.height * dpr);
      ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
      ctx.lineCap = 'round'; ctx.lineJoin = 'round';
      ctx.strokeStyle = '#111827'; ctx.lineWidth = 2.5;
      redrawFromState();
    }

    function pos(x, y) {
      const r = canvas.getBoundingClientRect();
      return { x: x - r.left, y: y - r.top };
    }

    function dot(x, y) {
      ctx.beginPath(); ctx.arc(x, y, ctx.lineWidth/2, 0, Math.PI*2);
      ctx.fillStyle = ctx.strokeStyle; ctx.fill();
    }

    function line(x1, y1, x2, y2) { ctx.beginPath(); ctx.moveTo(x1, y1); ctx.lineTo(x2, y2); ctx.stroke(); }

    function saveToStateAsPng() {
      const url = canvas.toDataURL('image/png');
      stateI.value = url;
      stateI.dispatchEvent(new Event('input', { bubbles: true }));
      stateI.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function clearPad() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      stateI.value = '';
      stateI.dispatchEvent(new Event('input', { bubbles: true }));
      stateI.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function redrawFromState() {
      const val = stateI.value; if (!val) return;
      const img = new Image();
      img.onload = () => { ctx.clearRect(0,0,canvas.width,canvas.height); ctx.drawImage(img,0,0,canvas.width/dpr,canvas.height/dpr); };
      img.src = val;
    }

    // Mouse
    canvas.addEventListener('mousedown', (e) => { e.preventDefault(); const p = pos(e.clientX,e.clientY); drawing = true; lastX=p.x; lastY=p.y; dot(p.x,p.y); }, { passive:false });
    canvas.addEventListener('mousemove', (e) => { if(!drawing) return; e.preventDefault(); const p=pos(e.clientX,e.clientY); line(lastX,lastY,p.x,p.y); lastX=p.x; lastY=p.y; }, { passive:false });
    ['mouseup','mouseleave'].forEach(evt => canvas.addEventListener(evt, (e) => { if(!drawing) return; e.preventDefault(); drawing=false; saveToStateAsPng(); }, { passive:false }));

    // Touch
    canvas.addEventListener('touchstart', (e) => { e.preventDefault(); const t=e.touches[0]; const p=pos(t.clientX,t.clientY); drawing=true; lastX=p.x; lastY=p.y; dot(p.x,p.y); }, { passive:false });
    canvas.addEventListener('touchmove',  (e) => { if(!drawing) return; e.preventDefault(); const t=e.touches[0]; const p=pos(t.clientX,t.clientY); line(lastX,lastY,p.x,p.y); lastX=p.x; lastY=p.y; }, { passive:false });
    canvas.addEventListener('touchend',   (e) => { e.preventDefault(); if(!drawing) return; drawing=false; saveToStateAsPng(); }, { passive:false });

    clearB?.addEventListener('click', clearPad);
    svgB?.addEventListener('click', () => download('image/svg+xml','svg'));
    pngB?.addEventListener('click', () => download('image/png','png'));
    jpgB?.addEventListener('click', () => download('image/jpeg','jpg'));

    function download(mime, ext) {
      const url = canvas.toDataURL(mime);
      const a = document.createElement('a'); a.href = url; a.download = 'potpis.'+ext; a.click();
    }

    resize();
    window.addEventListener('resize', resize);
    wrap.dataset.initialized = '1';
  }

  function scanAndMount() {
    document.querySelectorAll('[data-ozo-signature]').forEach(el => mount(el.getAttribute('data-ozo-signature')));
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', scanAndMount);
  else scanAndMount();

  if (window.Livewire && window.Livewire.hook) {
    window.Livewire.hook('message.processed', scanAndMount);
  } else if (window.livewire && window.livewire.hook) {
    window.livewire.hook('message.processed', scanAndMount);
  }
})();
</script>
