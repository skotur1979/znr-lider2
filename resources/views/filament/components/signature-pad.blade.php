<div x-data="signaturePadComponent()" x-init="init()" class="space-y-2">
    <canvas
        id="signature-canvas"
        class="border border-gray-300 rounded w-full bg-white"
        width="500"
        height="200"
        style="touch-action: none"
    ></canvas>

    <div class="flex gap-2">
        <button
            type="button"
            @click="clearSignature()"
            class="px-3 py-1 text-sm bg-gray-200 rounded"
        >Poništi</button>

        <button
            type="button"
            @click="saveSignature()"
            class="px-3 py-1 text-sm bg-blue-500 text-white rounded"
        >Spremi potpis</button>
    </div>

    <input type="hidden" name="signature" x-ref="signaturePath">
</div>

<script>
function signaturePadComponent() {
    let canvas, ctx;

    return {
        init() {
            canvas = document.getElementById('signature-canvas');
            ctx = canvas.getContext('2d');
            ctx.strokeStyle = '#000';
            ctx.lineWidth = 2;

            let drawing = false;

            canvas.addEventListener('mousedown', (e) => {
                drawing = true;
                ctx.beginPath();
                ctx.moveTo(e.offsetX, e.offsetY);
            });

            canvas.addEventListener('mousemove', (e) => {
                if (drawing) {
                    ctx.lineTo(e.offsetX, e.offsetY);
                    ctx.stroke();
                }
            });

            canvas.addEventListener('mouseup', () => {
                drawing = false;
            });

            canvas.addEventListener('touchstart', (e) => {
                const touch = e.touches[0];
                const rect = canvas.getBoundingClientRect();
                ctx.beginPath();
                ctx.moveTo(touch.clientX - rect.left, touch.clientY - rect.top);
            });

            canvas.addEventListener('touchmove', (e) => {
                e.preventDefault();
                const touch = e.touches[0];
                const rect = canvas.getBoundingClientRect();
                ctx.lineTo(touch.clientX - rect.left, touch.clientY - rect.top);
                ctx.stroke();
            });
        },

        clearSignature() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        },

        saveSignature() {
            const dataUrl = canvas.toDataURL('image/png');
            const base64 = dataUrl.split(',')[1];

            fetch('/potpis/upload', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ image: base64 })
            })
            .then(res => res.json())
            .then(data => {
                if (data.path) {
                    document.querySelector('input[name=signature]').value = data.path;
                    alert('✅ Potpis spremljen!');
                }
            });
        }
    }
}
</script>















































