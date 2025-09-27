// Fade-in on viewport for elements with class 'will-fade'
document.addEventListener("DOMContentLoaded", () => {
    const els = document.querySelectorAll(".will-fade");
    if (!els.length) return;

    const io = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("fade-in-up");
                    entry.target.classList.remove("will-fade");
                    io.unobserve(entry.target);
                }
            });
        },
        {
            root: null,
            threshold: 0.15,
        }
    );

    els.forEach((el) => io.observe(el));
});
