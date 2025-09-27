// Home page Art Section slider logic (extracted from Blade inline script)
// Safely initializes if required DOM elements exist.

document.addEventListener("DOMContentLoaded", function () {
    const slider = document.getElementById("image-slider");
    const carousel = document.getElementById("art-carousel");
    const prevBtn = document.getElementById("prev-btn");
    const nextBtn = document.getElementById("next-btn");
    const thumbsWrap = document.getElementById("art-thumbs");
    const indicators = Array.from(
        document.querySelectorAll(".slide-indicator")
    );
    const title = document.getElementById("artwork-title");
    const description = document.getElementById("artwork-description");
    const slideCounter = document.getElementById("slide-counter");

    // Required elements
    if (!slider || !carousel) return;

    const slides = Array.from(slider.children);
    const totalSlides = slides.length;
    if (totalSlides === 0) return;

    let currentSlide = 0;
    let autoTimer = null;
    let isPaused = false;
    let startX = 0;
    let deltaX = 0;

    // Read artworks data from embedded JSON
    let artworks = [];
    try {
        const dataEl = document.getElementById("artworks-data");
        if (dataEl && dataEl.textContent.trim()) {
            artworks = JSON.parse(dataEl.textContent);
        }
    } catch (e) {
        artworks = [];
    }

    function ensureLoaded(i) {
        if (i < 0 || i >= totalSlides) return;
        const img = slides[i].querySelector("img");
        const src = img ? img.getAttribute("src") : null;
        if (src) {
            const pre = new Image();
            pre.src = src;
        }
    }

    function applySizes() {
        // Preload the current and adjacent images
        ensureLoaded(currentSlide);
        ensureLoaded((currentSlide + 1) % totalSlides);
        ensureLoaded((currentSlide - 1 + totalSlides) % totalSlides);
    }

    function updateIndicators() {
        if (!indicators.length) return;
        indicators.forEach((indicator, index) => {
            indicator.classList.toggle(
                "bg-category-art",
                index === currentSlide
            );
            indicator.classList.toggle("bg-gray-300", index !== currentSlide);
            indicator.setAttribute(
                "aria-selected",
                index === currentSlide ? "true" : "false"
            );
        });
        if (slideCounter)
            slideCounter.textContent = `${currentSlide + 1} / ${totalSlides}`;
        if (thumbsWrap) {
            const thumbs = thumbsWrap.querySelectorAll("button");
            thumbs.forEach((t, i) => {
                t.classList.toggle("ring-2", i === currentSlide);
                t.classList.toggle("ring-category-art", i === currentSlide);
            });
        }
    }

    function updateContent() {
        if (!title || !description) return;
        const data =
            artworks && artworks[currentSlide] ? artworks[currentSlide] : null;
        const slideImg = slides[currentSlide]?.querySelector("img");
        const fallbackTitle = slideImg?.getAttribute("alt") || "عمل فني";
        const fallbackDesc = "";

        title.style.opacity = "0";
        description.style.opacity = "0";
        setTimeout(() => {
            title.textContent = data && data.title ? data.title : fallbackTitle;
            description.textContent =
                data && data.description ? data.description : fallbackDesc;
            title.style.opacity = "1";
            description.style.opacity = "1";
        }, 200);
    }

    function updateSlider() {
        slides.forEach((s, i) => {
            if (i === currentSlide) {
                s.classList.remove("opacity-0", "pointer-events-none");
                s.setAttribute("aria-hidden", "false");
            } else {
                if (!s.classList.contains("opacity-0"))
                    s.classList.add("opacity-0");
                if (!s.classList.contains("pointer-events-none"))
                    s.classList.add("pointer-events-none");
                s.setAttribute("aria-hidden", "true");
            }
        });
        updateIndicators();
        updateContent();
        ensureLoaded(currentSlide);
        ensureLoaded((currentSlide + 1) % totalSlides);
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % totalSlides;
        updateSlider();
    }

    function prevSlide() {
        currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
        updateSlider();
    }

    // Init first state
    updateSlider();

    // Event listeners (guard for nulls)
    if (nextBtn) nextBtn.addEventListener("click", nextSlide);
    if (prevBtn) prevBtn.addEventListener("click", prevSlide);

    if (indicators.length) {
        indicators.forEach((indicator, index) => {
            indicator.addEventListener("click", () => {
                currentSlide = index;
                updateSlider();
            });
        });
    }

    function startAuto() {
        stopAuto();
        autoTimer = setInterval(() => {
            if (!isPaused) nextSlide();
        }, 6000);
    }

    function stopAuto() {
        if (autoTimer) clearInterval(autoTimer);
        autoTimer = null;
    }

    // Pause/resume on hover/focus
    const hoverables = [carousel, prevBtn, nextBtn, ...indicators].filter(
        Boolean
    );
    hoverables.forEach((el) => {
        el.addEventListener("mouseenter", () => (isPaused = true));
        el.addEventListener("mouseleave", () => (isPaused = false));
        el.addEventListener("focus", () => (isPaused = true));
        el.addEventListener("blur", () => (isPaused = false));
    });

    // Keyboard navigation
    carousel.addEventListener("keydown", (e) => {
        if (e.key === "ArrowLeft") {
            prevSlide();
            e.preventDefault();
        } else if (e.key === "ArrowRight") {
            nextSlide();
            e.preventDefault();
        }
    });

    // Touch swipe
    carousel.addEventListener(
        "touchstart",
        (e) => {
            if (!e.touches || e.touches.length === 0) return;
            startX = e.touches[0].clientX;
            deltaX = 0;
        },
        { passive: true }
    );
    carousel.addEventListener(
        "touchmove",
        (e) => {
            if (!e.touches || e.touches.length === 0) return;
            deltaX = e.touches[0].clientX - startX;
        },
        { passive: true }
    );
    carousel.addEventListener("touchend", () => {
        const threshold = 40;
        if (Math.abs(deltaX) > threshold) {
            if (deltaX < 0) nextSlide();
            else prevSlide();
        }
        startX = 0;
        deltaX = 0;
    });

    // Build thumbnails
    if (thumbsWrap) {
        const imgs = slides.map((s) => s.querySelector("img"));
        imgs.forEach((img, i) => {
            const b = document.createElement("button");
            b.type = "button";
            b.setAttribute("aria-label", `عرض الشريحة ${i + 1}`);
            b.className =
                "w-10 h-14 rounded-md overflow-hidden shadow focus:outline-none focus:ring-2 focus:ring-category-art";
            const t = document.createElement("img");
            t.src = img?.getAttribute("src") || "";
            t.alt = img?.getAttribute("alt") || `معاينة ${i + 1}`;
            t.className = "w-full h-full object-cover";
            b.appendChild(t);
            b.addEventListener("click", () => {
                currentSlide = i;
                updateSlider();
            });
            thumbsWrap.appendChild(b);
        });
    }

    // Initialize and handle resizing
    applySizes();
    updateIndicators();
    updateContent();
    startAuto();
    window.addEventListener("resize", applySizes);

    if (title) title.style.transition = "opacity 0.3s ease";
    if (description) description.style.transition = "opacity 0.3s ease";
});
