@extends('layouts.public')

@section('title', 'Sơ đồ bến xe')

@section('content')
<div class="mb-6 flex items-end justify-between gap-4 flex-wrap">
    <div>
        <h2 class="text-3xl font-bold text-gray-800">Sơ đồ Kiosk</h2>
        <p class="mt-1 text-gray-600">Chọn một kiosk trên bản đồ để xem chi tiết và gửi yêu cầu thuê.</p>
    </div>

    <div class="flex gap-4 text-sm text-gray-700">
        <div class="flex items-center"><span class="mr-2 inline-block h-4 w-4 rounded bg-green-500"></span> Trống</div>
        <div class="flex items-center"><span class="mr-2 inline-block h-4 w-4 rounded bg-yellow-500"></span> Đã đặt</div>
        <div class="flex items-center"><span class="mr-2 inline-block h-4 w-4 rounded bg-red-500"></span> Đang thuê</div>
    </div>
</div>

<!-- Container bọc bên ngoài, cho phép cuộn khi bản đồ lớn hơn màn hình -->
<div class="relative w-full overflow-auto rounded bg-white shadow h-[700px] border">
    
    <!-- Container chính cố định kích thước 1829x1272 theo ảnh gốc -->
    <div id="map-container" class="relative" style="width: 1829px; height: 1272px;">
        
        <!-- Ảnh nền bản đồ -->
        <img id="map-image" src="{{ asset('maps/sitemap.jpg') }}" alt="Sơ đồ bến xe" class="absolute top-0 left-0 w-[1829px] h-[1272px] block">
        
        <!-- Lớp overlay chứa các khối Kiosk -->
        <div id="kiosks-overlay" class="absolute top-0 left-0 w-[1829px] h-[1272px] z-10"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const overlayContainer = document.getElementById('kiosks-overlay');

    // Gọi API lấy dữ liệu Kiosk và tọa độ
    fetch('/api/sitemap')
        .then(res => res.json())
        .then(response => {
            if (response.success && response.data.kiosks) {
                renderKiosks(response.data.kiosks);
            }
        })
        .catch(err => console.error("Error loading sitemap data:", err));

    function renderKiosks(kiosks) {
        overlayContainer.innerHTML = '';
        kiosks.forEach(kiosk => {
            const el = document.createElement('a');
            // Cấu hình link chuyển hướng đến trang chi tiết
            el.href = '/kiosks/' + kiosk.kioskId;
            el.className = 'absolute flex cursor-pointer items-center justify-center border-2 text-xs font-bold text-white transition group z-0 hover:z-10';
            
            // Thiết lập vị trí tuyệt đối dựa trên database
            el.style.left = kiosk.x + 'px';
            el.style.top = kiosk.y + 'px';
            el.style.width = kiosk.width + 'px';
            el.style.height = kiosk.height + 'px';

            // Thiết lập màu sắc theo trạng thái
            if (kiosk.status === 'available') {
                el.classList.add('border-green-700', 'bg-green-500/70', 'hover:bg-green-500/90');
            } else if (kiosk.status === 'reserved') {
                el.classList.add('border-yellow-700', 'bg-yellow-500/70', 'hover:bg-yellow-500/90');
            } else {
                el.classList.add('border-red-700', 'bg-red-500/70', 'hover:bg-red-500/90');
            }

            // Tạo nội dung Tooltip khi hover
            el.innerHTML = `
                ${kiosk.code}
                <div class="pointer-events-none absolute bottom-full left-1/2 mb-2 w-max -translate-x-1/2 opacity-0 transition-opacity group-hover:opacity-100 bg-gray-900 text-white text-xs rounded px-3 py-2 shadow-lg">
                    <p class="font-bold text-sm mb-1">${kiosk.code} - ${kiosk.zone}</p>
                    <p class="font-normal">Trạng thái: <span class="font-semibold">${formatStatus(kiosk.status)}</span></p>
                    <p class="font-normal mt-1 text-green-300">Giá: ${formatPrice(kiosk.price)}/tháng</p>
                    <div class="absolute top-full left-1/2 -mt-[1px] -ml-1 border-[5px] border-transparent border-t-gray-900"></div>
                </div>
            `;
            
            overlayContainer.appendChild(el);
        });
    }

    function formatStatus(status) {
        if (status === 'available') return 'Trống';
        if (status === 'reserved') return 'Đã đặt';
        return 'Đang thuê';
    }

    function formatPrice(price) {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
    }
});
</script>
@endsection