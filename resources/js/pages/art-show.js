// Artwork Show Page Script (extracted from inline Blade scripts)
// Handles: report modal, add-to-cart AJAX, like/unlike toggle.

// Utility: safe notify wrapper (package provides notify())
function notifySuccess(msg) {
    if (window.notify) {
        notify().success(msg);
    }
}
function notifyError(msg) {
    if (window.notify) {
        notify().error(msg);
    }
}

window.__reportModalInstance = window.__reportModalInstance || null;

const openReportModal = (button, attempt = 0) => {
    if (window.__reportModalInstance) {
        const imageAttr = button?.getAttribute("data-report-image-id");
        const payload = button
            ? {
                  endpoint:
                      button.getAttribute("data-report-endpoint") || undefined,
                  preselect:
                      button.getAttribute("data-report-preselect") || undefined,
                  preselectImage:
                      imageAttr && !Number.isNaN(Number(imageAttr))
                          ? Number(imageAttr)
                          : undefined,
              }
            : {};
        window.__reportModalInstance.openModal(payload);
        return;
    }
    if (attempt > 20) {
        console.warn("Report modal Alpine instance not ready");
        return;
    }
    setTimeout(() => openReportModal(button, attempt + 1), 50);
};

// Expose Alpine component factory for report modal
window.reportModal = function (reportEndpoint, options = {}) {
    return {
        open: false,
        step: 1,
        selectedType: "",
        details: "",
        busy: false,
        error: "",
        endpoint: reportEndpoint,
        defaultEndpoint: reportEndpoint,
        images: options.images || [],
        selectedImageId:
            typeof options.defaultImageId !== "undefined"
                ? options.defaultImageId
                : null,
        types: [
            { value: "spam", label: "رسائل إلكترونية مزعجة" },
            { value: "adult", label: "محتوى للبالغين" },
            { value: "fraud", label: "الاحتيال/النصب" },
            { value: "illegal_or_harmful", label: "ضار أو غير قانوني" },
            { value: "rights_violation", label: "ينتهك حقوقي" },
            { value: "misleading", label: "التضليل والمعلومات المضللة" },
        ],
        init() {
            this.defaultEndpoint = this.endpoint;
            if (!this.selectedImageId && this.images.length === 1) {
                this.selectedImageId = this.images[0].id;
            }
            window.__reportModalInstance = this;
        },
        selectImage(id) {
            this.selectedImageId = Number(id);
        },
        toggleType(v) {
            this.selectedType = this.selectedType === v ? "" : v;
        },
        goNext() {
            if (this.images.length && !this.selectedImageId) {
                this.error = "فضلاً اختر الصورة التي تحتوي على المشكلة";
                return;
            }
            if (!this.selectedType) {
                this.error = "اختر نوع البلاغ";
                return;
            }
            this.error = "";
            if (this.selectedType === "rights_violation") {
                this.step = 2;
            } else {
                this.submit();
            }
        },
        submit() {
            if (this.busy) return;
            this.error = "";
            if (
                this.selectedType === "rights_violation" &&
                !this.details.trim()
            ) {
                this.error = "فضلاً اشرح المشكلة المتعلقة بالحقوق";
                return;
            }
            this.busy = true;
            fetch(this.endpoint, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                    Accept: "application/json",
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    type: this.selectedType,
                    details: this.details,
                    image_id: this.selectedImageId,
                }),
            })
                .then(async (r) => {
                    const data = await r.json().catch(() => ({}));
                    if (!r.ok || !data.success) {
                        this.error = data.message || "تعذر الإرسال";
                        notifyError(this.error);
                        return;
                    }
                    this.step = 3;
                    notifySuccess("تم إرسال البلاغ");
                })
                .catch(() => {
                    this.error = "تعذر الإرسال";
                    notifyError(this.error);
                })
                .finally(() => {
                    this.busy = false;
                });
        },
        openModal(config = {}) {
            this.endpoint = config.endpoint || this.defaultEndpoint;
            this.selectedType = config.preselect || "";
            const hasImages = this.images.length > 0;
            if (typeof config.preselectImage !== "undefined") {
                this.selectedImageId = config.preselectImage;
            } else if (hasImages && this.images.length === 1) {
                this.selectedImageId = this.images[0].id;
            } else {
                this.selectedImageId = null;
            }
            this.step = this.selectedType === "rights_violation" ? 2 : 1;
            this.error = "";
            this.details = "";
            this.busy = false;
            this.open = true;
        },
        close() {
            this.open = false;
            setTimeout(() => {
                this.step = 1;
                this.selectedType = "";
                this.details = "";
                this.error = "";
                this.busy = false;
                this.endpoint = this.defaultEndpoint;
                this.selectedImageId =
                    this.images.length === 1 ? this.images[0].id : null;
            }, 250);
        },
    };
};

document.addEventListener("click", (event) => {
    const btn = event.target.closest("[data-report-btn]");
    if (!btn) return;
    event.preventDefault();
    openReportModal(btn);
});

// Page init
document.addEventListener("DOMContentLoaded", () => {
    // Add to cart form
    const form = document.querySelector("form.js-add-to-cart");
    if (form) {
        form.addEventListener("submit", async (e) => {
            e.preventDefault();
            const btn = form.querySelector(".js-add-btn");
            if (btn?.disabled) return;
            const qtyEl = form.querySelector('input[name="quantity"]');
            const quantity = Math.max(1, parseInt(qtyEl?.value || "1", 10));
            try {
                btn.disabled = true;
                btn.classList.add("opacity-70");
                const res = await fetch(form.action, {
                    method: "POST",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        Accept: "application/json",
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content,
                    },
                    body: new URLSearchParams({
                        artwork_id: form.dataset.artId,
                        quantity: String(quantity),
                    }),
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok || !data?.success) {
                    const msg = data?.message || "تعذر الإضافة إلى السلة";
                    notifyError(msg);
                    return;
                }
                const badge = document.querySelector(
                    'header [aria-label="سلة المشتريات"] span'
                );
                if (badge) {
                    badge.textContent = data.count;
                    badge.classList.remove("hidden");
                }
                notifySuccess("تمت الإضافة إلى السلة");
            } catch (err) {
                notifyError("حدث خطأ غير متوقع");
                console.error(err);
            } finally {
                btn.disabled = false;
                btn.classList.remove("opacity-70");
            }
        });
    }

    // Like button
    const likeBtn = document.querySelector("[data-like-btn]");
    if (likeBtn) {
        likeBtn.addEventListener("click", async () => {
            if (likeBtn.dataset.busy === "1") return;
            const liked = likeBtn.dataset.liked === "1";
            const id = likeBtn.dataset.id;
            const url = liked ? `/art/${id}/unlike` : `/art/${id}/like`;
            likeBtn.dataset.busy = "1";
            try {
                const res = await fetch(url, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content,
                        Accept: "application/json",
                    },
                });
                const data = await res.json().catch(() => ({}));
                if (res.ok && data.success) {
                    likeBtn.dataset.liked = liked ? "0" : "1";
                    const icon = likeBtn.querySelector("i");
                    if (icon) {
                        icon.classList.toggle("fas", !liked);
                        icon.classList.toggle("far", liked);
                    }
                    likeBtn.classList.toggle("bg-pink-50", !liked);
                    notifySuccess(!liked ? "أضيف للمفضلة" : "أزيل من المفضلة");
                }
            } catch (e) {
                console.error(e);
            } finally {
                likeBtn.dataset.busy = "0";
            }
        });
    }
});
