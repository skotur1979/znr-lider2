@php
    $statePath = $getStatePath();
@endphp

<div
    x-data="ozoSignaturePad()"
    x-init="init()"
    class="md:grid md:grid-cols-[800px_auto] md:items-start md:gap-3 flex flex-col gap-3"
>
    {{-- Platno za potpis --}}
    <canvas
        x-ref="canvas"
        width="800"
        height="240"
        class="border border-gray-500 rounded-md bg-white"
        style="touch-action: none;"
    ></canvas>

    {{-- Gumbi: desno od platna (vertikalno na md+, ispod/horizontalno na mobitelu) --}}
    <div class="md:flex md:flex-col md:gap-2 md:ml-2 flex flex-row gap-2">
        <button type="button"
                class="px-3 py-2 rounded-md bg-gray-600 text-white hover:bg-gray-700 w-full md:w-28">
            Clear
        </button>
        <button type="button"
                class="px-3 py-2 rounded-md bg-gray-600 text-white hover:bg-gray-700 w-full md:w-28"
                @click="download('svg')">
            .svg
        </button>
        <button type="button"
                class="px-3 py-2 rounded-md bg-gray-600 text-white hover:bg-gray-700 w-full md:w-28"
                @click="download('png')">
            .png
        </button>
        <button type="button"
                class="px-3 py-2 rounded-md bg-gray-600 text-white hover:bg-gray-700 w-full md:w-28"
                @click="download('jpg')">
            .jpg
        </button>
    </div>

    {{-- Hidden input – veže na polje "signature" --}}
    <input type="hidden" x-model="state">

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script>
        function ozoSignaturePad() {
            return {
                pad: null,
                state: @entangle($statePath).defer,

                init() {
                    const canvas = this.$refs.canvas;
                    this.pad = new window.SignaturePad(canvas, {
                        backgroundColor: '#ffffff',
                        penColor: '#111827',
                    });

                    if (this.state) {
                        try { this.pad.fromDataURL(this.state); } catch (e) {}
                    }

                    const save = () => this.state = this.pad.isEmpty()
                        ? null
                        : this.pad.toDataURL('image/png');

                    canvas.addEventListener('mouseup', save);
                    canvas.addEventListener('touchend', save);
                },

                clearPad() {
                    this.pad.clear();
                    this.state = null;
                },

                download(fmt) {
                    let mime = 'image/png', ext = 'png';
                    if (fmt === 'svg') { mime = 'image/svg+xml'; ext = 'svg'; }
                    if (fmt === 'jpg') { mime = 'image/jpeg'; ext = 'jpg'; }

                    const url = this.pad.toDataURL(mime);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'potpis.' + ext;
                    a.click();
                },
            }
        }
        // bind gumb Clear nakon init-a (ili dodaj @click="clearPad()" na sam gumb ako želiš)
        document.addEventListener('alpine:init', () => {
            document.querySelectorAll('button').forEach(b => {
                if (b.textContent.trim() === 'Clear') b.addEventListener('click', () => {
                    const comp = Alpine.closestDataStack(b)[0];
                    comp.clearPad();
                });
            });
        });
    </script>
</div>

