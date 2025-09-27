// Auction countdown timers
// Scans for elements with [data-countdown-end] and updates every second.
// If remaining time <= 0 sets text to "انتهى" and dims the card.

function pad(n) {
    return String(n).padStart(2, "0");
}

function formatRemain(seconds) {
    if (seconds <= 0) return "00:00:00";
    const d = Math.floor(seconds / 86400);
    seconds -= d * 86400;
    const h = Math.floor(seconds / 3600);
    seconds -= h * 3600;
    const m = Math.floor(seconds / 60);
    const s = seconds - m * 60;
    if (d > 0) {
        return `${d}ي ${pad(h)}:${pad(m)}:${pad(s)}`;
    }
    return `${pad(h)}:${pad(m)}:${pad(s)}`;
}

function updateCountdowns() {
    const now = Math.floor(Date.now() / 1000);
    document.querySelectorAll("[data-countdown-end]").forEach((el) => {
        const end = parseInt(el.getAttribute("data-countdown-end") || "", 10);
        if (!end) return;
        const remain = end - now;
        if (remain <= 0) {
            if (!el.dataset.ended) {
                el.dataset.ended = "1";
                el.textContent = "انتهى";
                el.classList.add("text-red-300");
                // Try to find auction/card container and dim it
                const card = el.closest("a, .auction-card, .group");
                if (card) {
                    card.classList.add("opacity-70", "pointer-events-none");
                    if (!card.querySelector(".ended-overlay")) {
                        const ov = document.createElement("div");
                        ov.className =
                            "ended-overlay absolute inset-0 flex items-center justify-center bg-black/60 backdrop-blur-sm text-white text-xl font-bold arabic-font";
                        ov.textContent = "انتهى المزاد";
                        if (!card.style.position)
                            card.style.position = "relative";
                        card.appendChild(ov);
                    }
                }
            }
        } else {
            el.textContent = formatRemain(remain);
            if (remain < 60) {
                el.classList.add("animate-pulse");
            } else {
                el.classList.remove("animate-pulse");
            }
        }
    });
}

function initCountdowns() {
    updateCountdowns();
    setInterval(updateCountdowns, 1000);
}

document.addEventListener("DOMContentLoaded", initCountdowns);
