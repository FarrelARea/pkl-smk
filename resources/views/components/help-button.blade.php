@props([
    'id' => null,
    'title' => 'Cara Menggunakan',
    'floating' => true,
    'buttonClass' => '',
    'targets' => ['[data-help-target]'],
    'targetLabels' => [],
])

@php
    $helpId = $id ?: 'help-modal-' . uniqid();
    $overlayId = 'help-overlay-' . uniqid();
    $buttonClasses = $floating
        ? 'fixed bottom-5 right-5 sm:bottom-6 sm:right-6 z-[120] w-12 h-12 rounded-full primary-gradient text-white shadow-xl shadow-primary/30 hover:opacity-90 active:scale-[0.98] inline-flex items-center justify-center text-lg font-extrabold transition-all duration-200'
        : 'w-7 h-7 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200 inline-flex items-center justify-center text-sm font-bold transition-colors duration-200';

    $defaultTargets = [
        '[data-help-target="page-title"]',
        '[data-help-target="header-actions"]',
        '[data-help-target="import-export"]',
        '[data-help-target="add-button"]',
        '[data-help-target="filter-bar"]',
        '[data-help-target="search-field"]',
        '[data-help-target="per-page-field"]',
        '[data-help-target="data-table"]',
        '[data-help-target="edit-action"]',
        '[data-help-target="delete-action"]',
        '[data-help-target="pagination"]',
        '[data-help-target="modal"]',
    ];

    $defaultTargetLabels = [
        'page-title' => 'Ini judul halaman yang menjelaskan data apa yang sedang kamu kelola.',
        'header-actions' => 'Bagian ini berisi aksi utama yang tersedia untuk halaman ini.',
        'import-export' => 'Gunakan tombol ini untuk export atau import data jika fitur tersedia.',
        'add-button' => 'Klik tombol ini untuk menambahkan data baru.',
        'filter-bar' => 'Area ini dipakai untuk mencari, memfilter, dan membatasi data yang ditampilkan.',
        'search-field' => 'Ketik kata kunci di sini untuk mencari data lebih cepat.',
        'per-page-field' => 'Atur jumlah data yang ditampilkan dalam satu halaman.',
        'data-table' => 'Tabel ini menampilkan daftar data utama pada halaman ini.',
        'edit-action' => 'Gunakan aksi ini untuk membuka form edit data.',
        'delete-action' => 'Gunakan aksi ini untuk menghapus data yang dipilih.',
        'pagination' => 'Gunakan navigasi ini untuk berpindah halaman data.',
        'modal' => 'Form atau detail tambahan biasanya akan muncul di modal ini.',
    ];

    $resolvedTargets = $targets === ['[data-help-target]'] ? $defaultTargets : $targets;
    $resolvedTargetLabels = empty($targetLabels) ? $defaultTargetLabels : array_merge($defaultTargetLabels, $targetLabels);
@endphp

<button
    type="button"
    data-help-open="{{ $helpId }}"
    class="{{ trim($buttonClasses . ' ' . $buttonClass) }}"
    title="{{ $title }}"
    aria-haspopup="dialog"
    aria-controls="{{ $helpId }}"
>
    ?
</button>

<div id="{{ $helpId }}" class="fixed inset-0 z-[999] flex items-center justify-center p-4 hidden" aria-hidden="true">
    <div class="fixed inset-0 bg-black/40" data-help-close="{{ $helpId }}"></div>
    <div class="relative z-10 w-full max-w-lg max-h-[80vh] overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl">
        <div class="mb-4 flex items-center justify-between gap-4">
            <h3 class="text-lg font-bold text-slate-800">{{ $title }}</h3>
            <button type="button" data-help-close="{{ $helpId }}" class="text-slate-400 transition-colors hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="space-y-2 text-sm leading-relaxed text-slate-600">
            {{ $slot }}
        </div>
        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <button
                type="button"
                data-help-close="{{ $helpId }}"
                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700"
            >
                Tutup
            </button>
            <button
                type="button"
                data-help-highlight="{{ $helpId }}"
                data-overlay-id="{{ $overlayId }}"
                data-highlight-targets='@json($resolvedTargets)'
                data-highlight-labels='@json($resolvedTargetLabels)'
                class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 transition-colors hover:bg-blue-100"
            >
                Tunjukkan di layar
            </button>
        </div>
    </div>
</div>

<div id="{{ $overlayId }}" class="fixed inset-0 z-[998] hidden bg-black/45 backdrop-blur-[1px]" aria-hidden="true"></div>

<script>
    (() => {
        const helpId = @json($helpId);
        const modal = document.getElementById(helpId);
        const overlay = document.getElementById(@json($overlayId));
        const highlightClassNames = ['relative', 'z-[999]', 'ring-4', 'ring-primary/40', 'ring-offset-4', 'ring-offset-white', 'rounded-xl'];
        const viewportPadding = 16;
        const gap = 18;
        const connectorGap = 10;
        const calloutPalettes = [
            {
                border: '#2563eb',
                line: '#2563eb',
                dot: '#2563eb',
                glow: 'rgba(37, 99, 235, 0.2)',
            },
            {
                border: '#7c3aed',
                line: '#7c3aed',
                dot: '#7c3aed',
                glow: 'rgba(124, 58, 237, 0.2)',
            },
            {
                border: '#db2777',
                line: '#db2777',
                dot: '#db2777',
                glow: 'rgba(219, 39, 119, 0.2)',
            },
            {
                border: '#ea580c',
                line: '#ea580c',
                dot: '#ea580c',
                glow: 'rgba(234, 88, 12, 0.2)',
            },
            {
                border: '#059669',
                line: '#059669',
                dot: '#059669',
                glow: 'rgba(5, 150, 105, 0.2)',
            },
            {
                border: '#0891b2',
                line: '#0891b2',
                dot: '#0891b2',
                glow: 'rgba(8, 145, 178, 0.2)',
            },
        ];
        let activeTargets = [];
        let activeCallouts = [];
        let occupiedCalloutRects = [];
        let helpSequence = [];
        let activeSequenceIndex = -1;
        let isHelpSequenceActive = false;

        if (!modal || !overlay) {
            return;
        }

        const openModal = () => {
            modal.classList.remove('hidden');
            modal.setAttribute('aria-hidden', 'false');
        };

        const intersects = (a, b) => {
            return !(a.right <= b.left || a.left >= b.right || a.bottom <= b.top || a.top >= b.bottom);
        };

        const clamp = (value, min, max) => Math.min(max, Math.max(min, value));

        const shiftInsideViewport = (rect, width, height) => {
            const minLeft = window.scrollX + viewportPadding;
            const maxLeft = window.scrollX + window.innerWidth - width - viewportPadding;
            const minTop = window.scrollY + viewportPadding;
            const maxTop = window.scrollY + window.innerHeight - height - viewportPadding;

            return {
                left: clamp(rect.left, minLeft, Math.max(minLeft, maxLeft)),
                top: clamp(rect.top, minTop, Math.max(minTop, maxTop)),
            };
        };

        const resolveOverlap = (rect, width, height) => {
            let nextRect = { ...rect };

            occupiedCalloutRects.forEach((occupiedRect) => {
                if (!intersects(nextRect, occupiedRect)) {
                    return;
                }

                const shiftedDown = {
                    top: occupiedRect.bottom + gap,
                    left: nextRect.left,
                };
                const adjustedDown = shiftInsideViewport(shiftedDown, width, height);
                nextRect = {
                    top: adjustedDown.top,
                    left: adjustedDown.left,
                    right: adjustedDown.left + width,
                    bottom: adjustedDown.top + height,
                };

                if (!intersects(nextRect, occupiedRect)) {
                    return;
                }

                const shiftedSide = {
                    top: nextRect.top,
                    left: occupiedRect.right + gap,
                };
                const adjustedSide = shiftInsideViewport(shiftedSide, width, height);
                nextRect = {
                    top: adjustedSide.top,
                    left: adjustedSide.left,
                    right: adjustedSide.left + width,
                    bottom: adjustedSide.top + height,
                };
            });

            return nextRect;
        };

        const getPlacementOrder = (rect, calloutWidth, calloutHeight) => {
            const available = {
                top: rect.top - calloutHeight - gap,
                bottom: window.innerHeight - rect.bottom - calloutHeight - gap,
                right: window.innerWidth - rect.right - calloutWidth - gap,
                left: rect.left - calloutWidth - gap,
            };

            return ['top', 'right', 'bottom', 'left'].sort((a, b) => available[b] - available[a]);
        };

        const fitsPlacement = (rect, width, height, placement) => {
            switch (placement) {
                case 'top':
                    return rect.top >= height + gap + viewportPadding;
                case 'bottom':
                    return window.innerHeight - rect.bottom >= height + gap + viewportPadding;
                case 'left':
                    return rect.left >= width + gap + viewportPadding;
                default:
                    return window.innerWidth - rect.right >= width + gap + viewportPadding;
            }
        };

        const getCalloutPosition = (rect, width, height, placement) => {
            switch (placement) {
                case 'top':
                    return {
                        top: rect.top + window.scrollY - height - gap,
                        left: rect.left + window.scrollX + (rect.width / 2) - (width / 2),
                    };
                case 'bottom':
                    return {
                        top: rect.bottom + window.scrollY + gap,
                        left: rect.left + window.scrollX + (rect.width / 2) - (width / 2),
                    };
                case 'left':
                    return {
                        top: rect.top + window.scrollY + (rect.height / 2) - (height / 2),
                        left: rect.left + window.scrollX - width - gap,
                    };
                default:
                    return {
                        top: rect.top + window.scrollY + (rect.height / 2) - (height / 2),
                        left: rect.right + window.scrollX + gap,
                    };
            }
        };

        const getAnchorPoints = (rect, calloutRect, placement) => {
            switch (placement) {
                case 'top':
                    return {
                        startX: calloutRect.left + (calloutRect.width / 2),
                        startY: calloutRect.bottom,
                        endX: rect.left + window.scrollX + (rect.width / 2),
                        endY: rect.top + window.scrollY - connectorGap,
                    };
                case 'bottom':
                    return {
                        startX: calloutRect.left + (calloutRect.width / 2),
                        startY: calloutRect.top,
                        endX: rect.left + window.scrollX + (rect.width / 2),
                        endY: rect.bottom + window.scrollY + connectorGap,
                    };
                case 'left':
                    return {
                        startX: calloutRect.right,
                        startY: calloutRect.top + (calloutRect.height / 2),
                        endX: rect.left + window.scrollX - connectorGap,
                        endY: rect.top + window.scrollY + (rect.height / 2),
                    };
                default:
                    return {
                        startX: calloutRect.left,
                        startY: calloutRect.top + (calloutRect.height / 2),
                        endX: rect.right + window.scrollX + connectorGap,
                        endY: rect.top + window.scrollY + (rect.height / 2),
                    };
            }
        };

        const createConnector = (startX, startY, endX, endY, palette) => {
            const line = document.createElement('div');
            const deltaX = endX - startX;
            const deltaY = endY - startY;
            const length = Math.hypot(deltaX, deltaY);
            const angle = Math.atan2(deltaY, deltaX) * (180 / Math.PI);

            line.className = 'fixed z-[1000] rounded-full shadow-sm';
            line.style.width = `${Math.max(length, 12)}px`;
            line.style.height = '3px';
            line.style.left = `${startX}px`;
            line.style.top = `${startY - 1.5}px`;
            line.style.transformOrigin = '0 50%';
            line.style.transform = `rotate(${angle}deg)`;
            line.style.backgroundColor = palette.line;
            line.style.boxShadow = `0 0 0 3px ${palette.glow}`;

            const dot = document.createElement('div');
            dot.className = 'fixed z-[1001] h-3 w-3 rounded-full border-2 shadow-md';
            dot.style.left = `${endX - 6}px`;
            dot.style.top = `${endY - 6}px`;
            dot.style.backgroundColor = palette.dot;
            dot.style.borderColor = '#ffffff';
            dot.style.boxShadow = `0 0 0 4px ${palette.glow}`;

            document.body.appendChild(line);
            document.body.appendChild(dot);
            activeCallouts.push(line, dot);
        };

        const createCallout = (element, label, index) => {
            if (!label) {
                return;
            }

            const palette = calloutPalettes[index % calloutPalettes.length];
            const callout = document.createElement('div');
            callout.className = 'fixed z-[1001] max-w-[220px] rounded-xl bg-white px-3 py-2 text-xs font-medium leading-relaxed text-slate-700 shadow-xl';
            callout.innerHTML = `
                <div>${label}</div>
                <div class="mt-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400">Klik di mana saja untuk lanjut</div>
            `;
            callout.style.visibility = 'hidden';
            callout.style.border = `2px solid ${palette.border}`;
            callout.style.boxShadow = `0 16px 40px ${palette.glow}`;
            document.body.appendChild(callout);

            const rect = element.getBoundingClientRect();
            const calloutRect = callout.getBoundingClientRect();
            const placements = getPlacementOrder(rect, calloutRect.width, calloutRect.height);
            const fallbackPlacement = placements[0];
            const preferredPlacements = placements.filter((placement) => fitsPlacement(rect, calloutRect.width, calloutRect.height, placement));
            const placementSequence = preferredPlacements.length > 0 ? preferredPlacements : placements;

            let finalRect = null;
            let finalPlacement = fallbackPlacement;

            placementSequence.forEach((placement, placementIndex) => {
                if (finalRect && placementIndex > 0) {
                    return;
                }

                const rawPosition = getCalloutPosition(rect, calloutRect.width, calloutRect.height, placement);
                const shiftedPosition = shiftInsideViewport(rawPosition, calloutRect.width, calloutRect.height);
                const candidateRect = resolveOverlap({
                    top: shiftedPosition.top,
                    left: shiftedPosition.left,
                    right: shiftedPosition.left + calloutRect.width,
                    bottom: shiftedPosition.top + calloutRect.height,
                }, calloutRect.width, calloutRect.height);

                finalRect = candidateRect;
                finalPlacement = placement;
            });

            if (!finalRect) {
                const rawPosition = getCalloutPosition(rect, calloutRect.width, calloutRect.height, fallbackPlacement);
                const shiftedPosition = shiftInsideViewport(rawPosition, calloutRect.width, calloutRect.height);
                finalRect = {
                    top: shiftedPosition.top,
                    left: shiftedPosition.left,
                    right: shiftedPosition.left + calloutRect.width,
                    bottom: shiftedPosition.top + calloutRect.height,
                };
            }

            callout.style.top = `${finalRect.top}px`;
            callout.style.left = `${finalRect.left}px`;
            callout.style.visibility = 'visible';
            activeCallouts.push(callout);
            occupiedCalloutRects.push(finalRect);

            const anchors = getAnchorPoints(rect, {
                top: finalRect.top,
                left: finalRect.left,
                right: finalRect.right,
                bottom: finalRect.bottom,
                width: calloutRect.width,
                height: calloutRect.height,
            }, finalPlacement);

            createConnector(anchors.startX, anchors.startY, anchors.endX, anchors.endY, palette);
        };

        const renderSequenceStep = (index) => {
            activeCallouts.forEach((node) => node.remove());
            activeCallouts = [];
            occupiedCalloutRects = [];

            const step = helpSequence[index];
            if (!step) {
                clearHighlights();
                return;
            }

            activeSequenceIndex = index;
            createCallout(step.element, step.label, step.index);
        };

        const advanceSequence = () => {
            if (!isHelpSequenceActive) {
                return;
            }

            const nextIndex = activeSequenceIndex + 1;
            if (nextIndex >= helpSequence.length) {
                clearHighlights();
                return;
            }

            renderSequenceStep(nextIndex);
        };

        const clearHighlights = () => {
            isHelpSequenceActive = false;
            helpSequence = [];
            activeSequenceIndex = -1;
            activeTargets.forEach((element) => {
                highlightClassNames.forEach((className) => element.classList.remove(className));
                element.removeAttribute('data-help-active');
            });
            activeTargets = [];
            activeCallouts.forEach((node) => node.remove());
            activeCallouts = [];
            occupiedCalloutRects = [];
            overlay.classList.add('hidden');
            overlay.setAttribute('aria-hidden', 'true');
        };

        const startSequence = (items) => {
            helpSequence = items;
            activeSequenceIndex = -1;
            isHelpSequenceActive = items.length > 0;

            if (isHelpSequenceActive) {
                overlay.classList.remove('hidden');
                overlay.setAttribute('aria-hidden', 'false');
                closeModal();
                renderSequenceStep(0);
            }
        };

        const closeModal = () => {
            modal.classList.add('hidden');
            modal.setAttribute('aria-hidden', 'true');
        };

        document.querySelectorAll(`[data-help-open="${helpId}"]`).forEach((button) => {
            button.addEventListener('click', openModal);
        });

        modal.querySelectorAll(`[data-help-close="${helpId}"]`).forEach((button) => {
            button.addEventListener('click', closeModal);
        });

        const highlightButton = modal.querySelector(`[data-help-highlight="${helpId}"]`);

        if (highlightButton) {
            highlightButton.addEventListener('click', () => {
                clearHighlights();

                const selectors = JSON.parse(highlightButton.dataset.highlightTargets || '[]');
                const labels = JSON.parse(highlightButton.dataset.highlightLabels || '{}');
                const nextSequence = [];
                let calloutIndex = 0;

                selectors.forEach((selector) => {
                    document.querySelectorAll(selector).forEach((element) => {
                        highlightClassNames.forEach((className) => element.classList.add(className));
                        element.setAttribute('data-help-active', 'true');
                        activeTargets.push(element);
                        nextSequence.push({
                            element,
                            label: labels[selector] || labels[element.dataset.helpTarget],
                            index: calloutIndex,
                        });
                        calloutIndex += 1;
                    });
                });

                startSequence(nextSequence.filter((step) => step.label));
            });
        }

        overlay.addEventListener('click', advanceSequence);

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                clearHighlights();
                closeModal();
            }
        });
    })();
</script>
