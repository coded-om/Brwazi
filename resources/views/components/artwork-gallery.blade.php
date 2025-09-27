@props([
    'urls' => [],
    'title' => ''
])
@php
    $urls = collect($urls)->filter()->values();
@endphp
@once
<script>
if(!window.artworkLightbox){
    window.artworkLightbox = function(opts){
        const safeImgs = Array.isArray(opts?.imgs) ? opts.imgs : [];
        return {
            i: 0,
            imgs: safeImgs,
            title: (opts && typeof opts.title === 'string') ? opts.title : '',
            lightbox: false,
            scrollLocked: false,
            scrollY: 0,
            zoom: 1,
            tx: 0,
            ty: 0,
            dragging: false,
            startX: 0,
            startY: 0,
            pinch: false,
            pinchStartDist: 0,
            pinchStartZoom: 1,
            pinchCenterX: 0,
            pinchCenterY: 0,
            reset(){ this.zoom=1; this.tx=0; this.ty=0; this.dragging=false; this.pinch=false; },
            wheel(e){ if(!this.lightbox) return; e.preventDefault(); const d = e.deltaY < 0 ? 0.15 : -0.15; this.zoom = Math.min(5, Math.max(1, +(this.zoom + d).toFixed(2))); this.clamp(); },
            mdown(e){ this.dragging = true; this.startX = e.clientX - this.tx; this.startY = e.clientY - this.ty; },
            mmove(e){ if(!this.dragging) return; this.tx = e.clientX - this.startX; this.ty = e.clientY - this.startY; this.clamp(); },
            mup(){ this.dragging = false; this.clamp(); },
            getDist(t){ const dx = t[0].clientX - t[1].clientX; const dy = t[0].clientY - t[1].clientY; return Math.hypot(dx, dy); },
            tstart(e){ if(e.touches.length === 2){ this.pinch = true; this.pinchStartDist = this.getDist(e.touches); this.pinchStartZoom = this.zoom; } else if(e.touches.length === 1){ this.dragging = true; this.startX = e.touches[0].clientX - this.tx; this.startY = e.touches[0].clientY - this.ty; } },
            tmove(e){ if(this.pinch && e.touches.length === 2){ const newDist = this.getDist(e.touches); let newZoom = this.pinchStartZoom * (newDist / this.pinchStartDist); newZoom = Math.min(5, Math.max(1, newZoom)); const oldZoom = this.zoom; if(Math.abs(newZoom-oldZoom)>0.0001){ const cx=(e.touches[0].clientX+e.touches[1].clientX)/2; const cy=(e.touches[0].clientY+e.touches[1].clientY)/2; this.tx += (cx - window.innerWidth/2) * (1 - newZoom/oldZoom); this.ty += (cy - window.innerHeight/2) * (1 - newZoom/oldZoom); this.zoom = newZoom; } this.clamp(); } else if(this.dragging && e.touches.length === 1){ this.tx = e.touches[0].clientX - this.startX; this.ty = e.touches[0].clientY - this.startY; this.clamp(); } },
            tend(e){ if(e.touches.length === 0){ this.dragging=false; this.pinch=false; this.clamp(); } },
            clamp(){ this.$nextTick(()=>{ const img = this.$refs.lbImg; if(!img) return; const rect = img.getBoundingClientRect(); const vw=window.innerWidth, vh=window.innerHeight; if(rect.width <= vw){ this.tx = 0; } else { if(rect.left > 0) this.tx -= rect.left; else if(rect.right < vw) this.tx += (vw - rect.right); } if(rect.height <= vh){ this.ty=0; } else { if(rect.top > 0) this.ty -= rect.top; else if(rect.bottom < vh) this.ty += (vh - rect.bottom); } }); },
            next(){ if(!this.imgs.length) return; this.i = (this.i + 1) % this.imgs.length; this.reset(); },
            prev(){ if(!this.imgs.length) return; this.i = (this.i - 1 + this.imgs.length) % this.imgs.length; this.reset(); },
            lockScroll(){ if(this.scrollLocked) return; this.scrollY = window.scrollY || document.documentElement.scrollTop || 0; document.body.style.position='fixed'; document.body.style.top = `-${this.scrollY}px`; document.body.style.left='0'; document.body.style.right='0'; document.body.style.width='100%'; document.body.classList.add('overflow-hidden'); this.scrollLocked=true; },
            unlockScroll(){ if(!this.scrollLocked) return; document.body.style.position=''; document.body.style.top=''; document.body.style.left=''; document.body.style.right=''; document.body.style.width=''; document.body.classList.remove('overflow-hidden'); window.scrollTo(0,this.scrollY); this.scrollLocked=false; },
            openLightbox(){ if(!this.imgs.length) return; this.reset(); this.lightbox=true; this.lockScroll(); },
            closeLightbox(){ this.lightbox=false; this.unlockScroll(); }
        };
    }
}
</script>
@endonce
<div x-data="artworkLightbox({ imgs: @js($urls), title: @js($title) })" class="relative">
    <div class="aspect-[3/4] bg-white rounded-xl shadow p-3 flex items-center justify-center overflow-hidden">
        <template x-if="imgs.length">
            <img :src="imgs[i]" class="cursor-zoom-in max-h-[480px] max-w-full object-contain transition duration-500 ease-out blur-sm" @click="openLightbox()" @load="($event.target).classList.remove('blur-sm')" :alt="title + ' - اضغط للتكبير'" />
        </template>
        <template x-if="!imgs.length">
            <div class="text-gray-400 text-sm">لا توجد صور</div>
        </template>
    </div>
    <template x-if="imgs.length > 1">
        <div>
            <button @click="i = (i - 1 + imgs.length) % imgs.length" class="absolute top-1/2 right-2 -translate-y-1/2 bg-white/70 hover:bg-white rounded-full h-9 w-9 grid place-items-center shadow"><i class="fa-solid fa-chevron-right"></i></button>
            <button @click="i = (i + 1) % imgs.length" class="absolute top-1/2 left-2 -translate-y-1/2 bg-white/70 hover:bg-white rounded-full h-9 w-9 grid place-items-center shadow"><i class="fa-solid fa-chevron-left"></i></button>
        </div>
    </template>
    <template x-if="imgs.length > 1">
        <div class="mt-3 grid grid-cols-6 gap-2">
            <template x-for="(u, idx) in imgs" :key="u">
                <button @click="i = idx" class="h-14 rounded overflow-hidden ring-2" :class="idx === i ? 'ring-indigo-600' : 'ring-transparent'">
                    <img :src="u" loading="lazy" class="w-full h-full object-cover transition duration-500 ease-out blur-sm" @load="($event.target).classList.remove('blur-sm')" />
                </button>
            </template>
        </div>
    </template>
    <template x-if="lightbox">
        <div class="fixed inset-0 z-50 flex items-center justify-center select-none" x-cloak data-lightbox-overlay @keydown.window.escape="closeLightbox()" @keydown.window.prevent.arrow-right="next()" @keydown.window.prevent.arrow-left="prev()" @click.self="closeLightbox()" @wheel.prevent="wheel($event)" @mouseup.window="mup()" @mouseleave.window="mup()">
            <div class="absolute inset-0 bg-black/90"></div>
            <div data-lb-stage class="relative z-10 p-4 w-full h-full flex items-center justify-center overflow-hidden">
                <img x-ref="lbImg" :src="imgs[i]" :alt="'عرض مكبر - ' + title" class="max-h-[92vh] max-w-[94vw] object-contain cursor-grab active:cursor-grabbing will-change-transform transition-[transform] touch-none" :style="'transform: translate('+tx+'px,'+ty+'px) scale('+zoom+'); '+((dragging||pinch)? 'transition:none;' : 'transition-duration:120ms;')" draggable="false" @mousedown.prevent="mdown($event)" @mousemove.prevent="mmove($event)" @dblclick.prevent="zoom = zoom >= 2 ? 1 : 2; clamp();" @touchstart.prevent="tstart($event)" @touchmove.prevent="tmove($event)" @touchend="tend($event)" @touchcancel="tend($event)" />
                <div class="absolute bottom-3 left-1/2 -translate-x-1/2 text-xs text-white/80 bg-black/40 backdrop-blur px-3 py-1 rounded-full flex items-center gap-2">
                    <span x-text="(i+1)+' / '+imgs.length"></span><span>•</span><span x-text="zoom.toFixed(2)+'x'"></span>
                </div>
                <template x-if="imgs.length > 1">
                    <div>
                        <button type="button" @click.stop="prev()" class="absolute top-1/2 right-4 -translate-y-1/2 h-12 w-12 rounded-full bg-white/15 hover:bg-white/25 active:bg-white/30 text-white flex items-center justify-center backdrop-blur shadow focus:outline-none focus:ring focus:ring-white/30"><i class="fa-solid fa-chevron-right"></i></button>
                        <button type="button" @click.stop="next()" class="absolute top-1/2 left-4 -translate-y-1/2 h-12 w-12 rounded-full bg-white/15 hover:bg-white/25 active:bg-white/30 text-white flex items-center justify-center backdrop-blur shadow focus:outline-none focus:ring focus:ring-white/30"><i class="fa-solid fa-chevron-left"></i></button>
                    </div>
                </template>
            </div>
            <button type="button" aria-label="إغلاق" data-close-lightbox @click.stop="closeLightbox()" onclick="try{var c=document.querySelector('[x-data*=artworkLightbox]'); if(c&&c.__x){ c.__x.$data.closeLightbox(); } else { var ov=document.querySelector('[data-lightbox-overlay]'); if(ov) ov.remove(); document.body.style.position=''; document.body.style.top=''; document.body.style.left=''; document.body.style.right=''; document.body.style.width=''; document.body.classList.remove('overflow-hidden'); }}catch(e){console.error(e)} return false;" class="absolute top-4 right-4 h-11 w-11 rounded-full bg-white/25 hover:bg-white/35 active:bg-white/40 text-white flex items-center justify-center backdrop-blur shadow z-20 focus:outline-none focus:ring-2 focus:ring-white/60">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" /></svg>
            </button>
        </div>
    </template>
</div>
