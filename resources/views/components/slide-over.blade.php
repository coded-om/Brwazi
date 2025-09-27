@props([
    'id' => 'panel',
    'title' => 'لوحة',
    'side' => 'right', // right|left
    'maxWidth' => 'max-w-sm',
])

@php
    $isRight = $side !== 'left';
    $sideClass = $isRight ? 'right-0' : 'left-0';
    $enterClass = $isRight ? 'translate-x-full' : '-translate-x-full';
@endphp

<div id="{{ $id }}Overlay" class="fixed inset-0 z-40 hidden bg-gray-900/20 opacity-0 transition-opacity" aria-hidden="true"></div>
<aside id="{{ $id }}Panel" class="fixed inset-y-0 {{ $sideClass }} z-50 w-full {{ $maxWidth }} bg-white shadow-xl {{ $enterClass }} transition-transform hidden" aria-hidden="true">
    <div class="h-full flex flex-col">
        <div class="flex items-center justify-between px-4 py-3 shadow-sm">
            <h3 class="text-sm font-semibold text-indigo-900">{{ $title }}</h3>
            <button type="button" id="{{ $id }}CloseBtn" class="h-9 w-9 inline-flex items-center justify-center rounded-full hover:bg-gray-100" aria-label="إغلاق">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-4">
            {{ $slot }}
        </div>
    </div>
</aside>

@push('scripts')
@once
    <script>
        (function(){
            const id = "{{ addslashes($id) }}";
            const body = document.body;
            const overlay = document.getElementById(id + 'Overlay');
            const panel = document.getElementById(id + 'Panel');
            const closeBtn = document.getElementById(id + 'CloseBtn');
            const openers = document.querySelectorAll('[data-slide-target="' + id + '"]');
            if (!overlay || !panel) return;
            const open = () => {
                overlay.classList.remove('hidden');
                panel.classList.remove('hidden');
                panel.setAttribute('aria-hidden', 'false');
                requestAnimationFrame(() => {
                    overlay.classList.remove('opacity-0');
                    panel.classList.remove('translate-x-full','-translate-x-full');
                    panel.classList.add('translate-x-0');
                    body.style.overflow = 'hidden';
                });
            };
            const close = () => {
                overlay.classList.add('opacity-0');
                panel.classList.remove('translate-x-0');
                // restore initial enter class depending on side by checking computed style
                if (panel.classList.contains('left-0')) panel.classList.add('-translate-x-full');
                else panel.classList.add('translate-x-full');
                setTimeout(() => {
                    overlay.classList.add('hidden');
                    panel.classList.add('hidden');
                    panel.setAttribute('aria-hidden', 'true');
                }, 200);
                body.style.overflow = '';
            };
            openers.forEach(btn => btn.addEventListener('click', open));
            overlay.addEventListener('click', (e) => { if (e.target === overlay) close(); });
            closeBtn?.addEventListener('click', close);
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
        })();
    </script>
@endonce
@endpush
