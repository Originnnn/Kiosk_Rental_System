@props(['kiosks', 'activeId' => null])
<div class="w-full h-full relative group flex-1 flex flex-col min-h-[300px]">
    <!-- Map Canvas -->
    <div id="map-wrapper" class="w-full h-full flex-1 overflow-hidden flex items-center justify-center bg-slate-50 relative cursor-grab active:cursor-grabbing">
        <div id="map-container" class="relative bg-white shadow-sm flex-shrink-0" style="width: 1829px; height: 1272px;">
            <!-- Base Map Image -->
            <img src="{{ asset('maps/sitemap.jpg') }}" class="w-full h-full block" alt="Sitemap">
            
            <!-- Dynamic Map Pins/Rectangles -->
            @foreach($kiosks as $k)
                @if($k->position && $k->position->x !== null && $k->position->y !== null)
                    @php
                        $isAvailable = $k->status === 'available';
                        $isRented = $k->status === 'rented';
                        $colorClass = $isRented ? 'bg-green-500' : ($isAvailable ? 'bg-blue-600' : 'bg-orange-500');
                        
                        $origWidth = 1829;
                        $origHeight = 1272;
                        
                        $leftPct = ($k->position->x / $origWidth) * 100;
                        $topPct = ($k->position->y / $origHeight) * 100;
                        $widthPct = ($k->position->width / $origWidth) * 100;
                        $heightPct = ($k->position->height / $origHeight) * 100;

                        $isActive = $activeId == $k->id;
                        $kioskData = [
                            'id' => $k->id,
                            'code' => $k->code,
                            'name' => $k->name ?: 'Tạp hoá & Đồ uống',
                            'status' => $k->status,
                            'area' => $k->area,
                            'zone' => $k->position->zone ?? 'N/A'
                        ];
                    @endphp
                    <div onclick="if(typeof handleKioskClick === 'function') handleKioskClick(event, this)"
                       class="kiosk-pin absolute flex items-center justify-center cursor-pointer z-20 transition-all duration-300 {{ $isActive ? 'is-active' : '' }}"
                       data-id="{{ $k->id }}"
                       data-kiosk="{{ json_encode($kioskData) }}"
                       style="left: {{ $leftPct }}%; top: {{ $topPct }}%; width: {{ $widthPct }}%; height: {{ $heightPct }}%;"
                       title="{{ $k->code }}">
                        
                        <div class="kiosk-pin-inner w-full h-full border-[1.5px] {{ $isActive ? 'border-red-500 bg-red-500 shadow-[0_0_15px_rgba(239,68,68,0.8)] animate-pulse' : 'border-white ' . $colorClass . ' bg-opacity-80 hover:bg-opacity-100' }} rounded-[2px] shadow-sm flex items-center justify-center transition-all duration-200">
                            <span class="text-white text-[8px] font-bold drop-shadow-md">{{ $k->code }}</span>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    
    <!-- Controls (optional, overlay) -->
    @if($activeId)
    <div class="absolute top-2 right-2 flex gap-1 z-30 opacity-0 group-hover:opacity-100 transition-opacity">
        <button id="zoom-in" class="w-7 h-7 bg-white/90 backdrop-blur shadow rounded flex items-center justify-center border border-gray-200 text-gray-700 hover:bg-gray-100"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg></button>
        <button id="zoom-out" class="w-7 h-7 bg-white/90 backdrop-blur shadow rounded flex items-center justify-center border border-gray-200 text-gray-700 hover:bg-gray-100"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg></button>
        <button id="zoom-reset" class="w-7 h-7 bg-white/90 backdrop-blur shadow rounded flex items-center justify-center border border-gray-200 text-gray-700 hover:bg-gray-100"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg></button>
    </div>
    @endif
</div>

@once
<script src="https://cdn.jsdelivr.net/npm/@panzoom/panzoom@4.5.1/dist/panzoom.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const elem = document.getElementById('map-container');
    const wrapper = document.getElementById('map-wrapper');
    if (!elem || !wrapper) return;

    // Calculate initial scale to fit wrapper height or width
    const wrapperRect = wrapper.getBoundingClientRect();
    const mapWidth = 1829;
    const mapHeight = 1272;
    
    // Default zoom to fit the container
    let initialScale = Math.min(wrapperRect.width / mapWidth, wrapperRect.height / mapHeight);
    // Add some padding
    initialScale = initialScale * 0.95;
    window.initialScale = initialScale;
    
    const panzoom = Panzoom(elem, {
        maxScale: 5,
        minScale: initialScale,
        startScale: initialScale,
        startX: 0,
        startY: 0,
        step: 0.3
    });
    
    window.panzoomInstance = panzoom;
    
    // Zoom controls
    const zoomIn = document.getElementById('zoom-in');
    const zoomOut = document.getElementById('zoom-out');
    const zoomReset = document.getElementById('zoom-reset');
    
    if (zoomIn) zoomIn.addEventListener('click', panzoom.zoomIn);
    if (zoomOut) zoomOut.addEventListener('click', panzoom.zoomOut);
    if (zoomReset) {
        zoomReset.addEventListener('click', () => {
            panzoom.reset();
            panzoom.zoom(initialScale);
            panzoom.pan(0, 0);
        });
    }

    elem.parentElement.addEventListener('wheel', panzoom.zoomWithWheel);

    // If activeId is provided, zoom to it
    const activePin = document.querySelector('.kiosk-pin.is-active');
    if (activePin) {
        setTimeout(() => {
            const left = parseFloat(activePin.style.left);
            const top = parseFloat(activePin.style.top);
            const width = parseFloat(activePin.style.width);
            const height = parseFloat(activePin.style.height);
            
            // Convert percent to pixels
            const pxX = (left / 100) * mapWidth;
            const pxY = (top / 100) * mapHeight;
            const pxW = (width / 100) * mapWidth;
            const pxH = (height / 100) * mapHeight;
            
            const centerX = pxX + (pxW / 2);
            const centerY = pxY + (pxH / 2);
            
            const dx = (mapWidth / 2) - centerX;
            const dy = (mapHeight / 2) - centerY;
            
            const targetScale = Math.max(initialScale * 2, 1.5);
            
            panzoom.zoom(targetScale, { animate: true });
            setTimeout(() => {
                panzoom.pan(dx, dy, { animate: true });
            }, 50);
        }, 300);
    }
});
</script>
<style>
.kiosk-pin {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.kiosk-pin.is-active {
    transform: scale(1.15);
    z-index: 40 !important;
    opacity: 1 !important;
}
.kiosk-pin.is-filtered:not(.is-active) {
    transform: scale(1.15);
    z-index: 30;
}
</style>
@endonce
