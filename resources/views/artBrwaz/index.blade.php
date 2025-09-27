<x-layout>
    <!-- model-viewer (3D) library -->
    <script type="module" src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>

    <section class="carousel relative h-[calc(100vh-72px)] overflow-hidden px-[6%] bg-[#f7f7fb] text-[#222]" dir="rtl">
        <!-- Navigation buttons -->
        <button id="prev" type="button"
            class="nav absolute top-5 left-1/2 -translate-x-1/2 z-10 w-12 h-12 rounded-full bg-white/20 hover:bg-white/60 text-[22px] text-gray-500 hover:text-gray-600 shadow-lg flex items-center justify-center transition transform hover:-translate-y-0.5">
            ▲
        </button>
        <button id="next" type="button"
            class="nav absolute bottom-5 left-1/2 -translate-x-1/2 z-10 w-12 h-12 rounded-full bg-white/20 hover:bg-white/60 text-[22px] text-gray-500 hover:text-gray-600 shadow-lg flex items-center justify-center transition transform hover:-translate-y-0.5">
            ▼
        </button>

        <!-- Slides wrapper (vertical) -->
        <div id="wrapper" dir="ltr" class="wrapper flex h-full transition-transform duration-700 ease-in-out flex-col">
            @php($slides = ($settings->slides ?? []) ?: [])
            @foreach($slides as $slide)
            <article
                class="slide min-h-full w-full flex flex-col-reverse md:flex-row items-center justify-between pt-[6%] pb-[6%] px-2 gap-12"
                data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
                <div class="viewer flex-1 md:max-w-[50%] max-w-full h-[60vh] md:h-[80vh] flex items-center justify-center overflow-hidden"
                    data-aos="zoom-in" data-aos-delay="{{ $loop->index * 80 + 50 }}">
                    <model-viewer class="w-full h-full" src="{{ asset_url($slide['model_path'] ?? '') }}"
                        alt="نموذج ثلاثي الأبعاد" camera-controls auto-rotate disable-zoom shadow-intensity="1"
                        exposure="1.1" loading="{{ $loop->first ? 'eager' : 'lazy' }}">
                    </model-viewer>
                </div>
                <div class="info flex-1 md:max-w-[45%] max-w-full text-right" data-aos="fade-left"
                    data-aos-delay="{{ $loop->index * 80 + 100 }}">
                    <h2 class="text-2xl md:text-3xl font-bold text-[#2c2149] mb-4">{{ $slide['title'] ?? '' }}</h2>
                    <p class="text-base md:text-lg leading-8 text-gray-700 mb-5">{{ $slide['description'] ?? '' }}</p>
                    @php($ctaLink = ($slide['cta_link'] ?? null) ?: route('artbrwaz.exhibit'))
                    <a href="{{ $ctaLink }}"
                        class="inline-block px-6 py-3 rounded-xl bg-[#9b4de2] hover:bg-[#7a38b8] text-white text-base transition-colors">
                        {{ $slide['cta_text'] ?? 'دخول' }}
                    </a>
                </div>
            </article>
            @endforeach
        </div>

        <!-- Dots (vertical) -->
        <div id="dots" class="dots absolute right-5 top-1/2 -translate-y-1/2 flex flex-col gap-2"></div>
    </section>

    <script>
        const wrapper = document.getElementById('wrapper');
        const slides = document.querySelectorAll('.slide');
        const dots = document.getElementById('dots');
        let index = 0;

        // Create dots with Tailwind classes
        slides.forEach((s, i) => {
            const dot = document.createElement('span');
            dot.className = 'dot w-3 h-3 rounded-full cursor-pointer ' + (i === 0 ? 'bg-purple-600' : 'bg-gray-400');
            dot.addEventListener('click', () => goTo(i));
            dots.appendChild(dot);
        });

        function update() {
            // move slides vertically
            wrapper.style.transform = `translateY(${-index * 100}%)`;
            [...dots.children].forEach((d, i) => {
                d.classList.toggle('bg-purple-600', i === index);
                d.classList.toggle('bg-gray-400', i !== index);
            });
            // Refresh AOS to ensure animations bind after slide change
            if (window.AOS && typeof AOS.refresh === 'function') {
                AOS.refresh();
            }
        }

        function next() { index = (index + 1) % slides.length; update(); }
        function prev() { index = (index - 1 + slides.length) % slides.length; update(); }
        function goTo(i) { index = i; update(); }

        document.getElementById('next').onclick = next;
        document.getElementById('prev').onclick = prev;

        update();

        // Enable wheel navigation on the full carousel area
        (function enableWheelNav() {
            const carouselEl = document.querySelector('.carousel');
            if (!carouselEl) return;
            let locked = false;
            const LOCK_MS = 700;
            carouselEl.addEventListener('wheel', (ev) => {
                ev.preventDefault();
                if (locked) return;
                if (Math.abs(ev.deltaY) < 20) return;
                locked = true;
                if (ev.deltaY > 0) { next(); } else { prev(); }
                setTimeout(() => locked = false, LOCK_MS);
            }, { passive: false });
        })();
    </script>
</x-layout>
