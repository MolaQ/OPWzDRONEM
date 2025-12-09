<flux:main>
    <style>
    @media print {
        body { background: #fff; }
        .print\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        button, select, input { display: none !important; }
        .print\:gap-2 { gap: 0.5rem; }
    }
    </style>

    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 p-4 flex flex-col gap-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div>
                    <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">Kody kreskowe do pobrania</h1>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">Wydrukuj listę kodów kreskowych dla naklejek i identyfikatorów</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="window.print()" class="inline-flex items-center gap-2 px-3 py-2 bg-neutral-900 text-white rounded-lg text-sm shadow hover:bg-neutral-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9V4h12v5M6 18h12v2H6zm-2-7h16a2 2 0 012 2v3H2v-3a2 2 0 012-2z"/></svg>
                        Drukuj
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-4 gap-3">
                <div class="flex flex-col gap-2">
                    <label class="text-xs uppercase font-semibold text-neutral-500 dark:text-neutral-400">Źródło</label>
                    <select wire:model.live="type" class="rounded-lg border border-neutral-200 dark:border-neutral-700 px-3 py-2 text-sm dark:bg-neutral-800 dark:text-neutral-100 focus:ring-2 focus:ring-[#880000]">
                        <option value="equipment">Sprzęt</option>
                        <option value="equipment_sets">Zestawy</option>
                        <option value="students">Uczniowie (identyfikatory)</option>
                    </select>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-xs uppercase font-semibold text-neutral-500 dark:text-neutral-400">Filtr</label>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Szukaj po nazwie lub kodzie" class="rounded-lg border border-neutral-200 dark:border-neutral-700 px-3 py-2 text-sm dark:bg-neutral-800 dark:text-neutral-100 focus:ring-2 focus:ring-[#880000]" />
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-xs uppercase font-semibold text-neutral-500 dark:text-neutral-400">Szerokość kodu</label>
                    <input type="number" min="120" max="260" wire:model.live="size" class="rounded-lg border border-neutral-200 dark:border-neutral-700 px-3 py-2 text-sm dark:bg-neutral-800 dark:text-neutral-100 focus:ring-2 focus:ring-[#880000]" />
                    <p class="text-[11px] text-neutral-500">Zakres 120-260 px</p>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-xs uppercase font-semibold text-neutral-500 dark:text-neutral-400">Etykiety</label>
                    <div class="flex flex-wrap gap-3 text-sm text-neutral-700 dark:text-neutral-200">
                        <label class="inline-flex items-center gap-2"><input type="checkbox" wire:model.live="showName" class="rounded"> Nazwa</label>
                        <label class="inline-flex items-center gap-2"><input type="checkbox" wire:model.live="showSubtitle" class="rounded"> Opis</label>
                        <label class="inline-flex items-center gap-2"><input type="checkbox" wire:model.live="showBarcode" class="rounded"> Kod</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-neutral-900 rounded-lg border border-neutral-200 dark:border-neutral-700 p-4 flex-1 overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm text-neutral-600 dark:text-neutral-400">{{ $count }} pozycji</p>
                <p class="text-xs text-neutral-500 hidden print:block">Tryb wydruku: układ przyjazny naklejkom</p>
            </div>

            @if($items->isEmpty())
                <div class="text-center text-neutral-500 dark:text-neutral-400 py-10">Brak wyników dla podanych filtrów.</div>
            @else
                <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 print:grid-cols-3 print:gap-2" data-barcode-size="{{ $size }}">
                    @foreach($items as $item)
                        <div class="border border-neutral-200 dark:border-neutral-700 rounded-lg p-3 flex flex-col items-center gap-3 shadow-sm bg-white dark:bg-neutral-800 print:border print:shadow-none">
                            <svg class="barcode" data-barcode="{{ $item['barcode'] }}" data-name="{{ $item['name'] }}" width="{{ $size }}" height="80"></svg>
                            @if($showName)
                                <div class="text-sm font-semibold text-center text-neutral-900 dark:text-white leading-tight">{{ $item['name'] }}</div>
                            @endif
                            @if($showSubtitle && $item['subtitle'])
                                <div class="text-xs text-center text-neutral-500 dark:text-neutral-400 leading-tight">{{ $item['subtitle'] }}</div>
                            @endif
                            @if($showBarcode)
                                <div class="text-[11px] font-mono text-neutral-600 dark:text-neutral-300">{{ $item['barcode'] }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</flux:main>

@script
<script>
    let renderTimeout = null;

    const renderBarcodes = () => {
        if (typeof JsBarcode === 'undefined') {
            setTimeout(renderBarcodes, 100);
            return;
        }

        clearTimeout(renderTimeout);

        const container = document.querySelector('[data-barcode-size]');
        if (!container) {
            renderTimeout = setTimeout(renderBarcodes, 50);
            return;
        }

        const size = parseInt(container.dataset.barcodeSize || '180', 10);
        const barWidth = Math.max(1, Math.round(size / 90));
        const barHeight = Math.max(40, Math.round(size * 0.45));
        const margin = Math.max(4, Math.round(size * 0.05));

        const barcodes = document.querySelectorAll('svg.barcode');
        if (barcodes.length === 0) {
            renderTimeout = setTimeout(renderBarcodes, 50);
            return;
        }

        barcodes.forEach(el => {
            const code = el.dataset.barcode;
            if (!code) return;

            // Skip if already rendered and hasn't changed
            if (el.dataset.rendered === code && el.dataset.renderedSize === String(size)) {
                return;
            }

            el.innerHTML = '';
            try {
                JsBarcode(el, code, {
                    format: 'CODE128',
                    width: barWidth,
                    height: barHeight,
                    displayValue: false,
                    margin: margin,
                    lineColor: '#000',
                    background: '#fff'
                });
                el.dataset.rendered = code;
                el.dataset.renderedSize = String(size);
            } catch (e) {
                console.error('Barcode render error for', code, e);
            }
        });
    };

    // Initial render with retry
    let initRetries = 0;
    const initRender = () => {
        if (typeof JsBarcode === 'undefined' && initRetries < 20) {
            initRetries++;
            setTimeout(initRender, 100);
            return;
        }
        renderBarcodes();
    };

    setTimeout(initRender, 100);

    // Listen for Livewire updates - this runs on every component update
    $wire.on('$refresh', () => {
        setTimeout(renderBarcodes, 150);
    });

    // Watch for any wire:model.live changes
    Livewire.hook('morph.updated', ({ el, component }) => {
        clearTimeout(renderTimeout);
        renderTimeout = setTimeout(renderBarcodes, 100);
    });

    Livewire.hook('message.processed', ({ component, message }) => {
        clearTimeout(renderTimeout);
        renderTimeout = setTimeout(renderBarcodes, 100);
    });
</script>
@endscript
