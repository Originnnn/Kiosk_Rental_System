@extends('layouts.public')

@section('title', 'Sơ đồ bến & Kiosk')
@section('header_title', 'Sơ đồ bến & Kiosk')

@section('content')
<div class="flex-1 flex overflow-hidden">
    <!-- Left Map Area -->
    <div class="flex-1 bg-gray-50 flex flex-col relative overflow-hidden border-r border-gray-200">
        <!-- Map Header/Legend -->
        <div class="h-14 bg-white border-b border-gray-200 flex items-center justify-between px-4 shrink-0">
            <div class="flex gap-6 text-sm font-medium text-gray-600">
                <div class="flex items-center gap-2"><div class="w-3.5 h-3.5 bg-green-500 rounded-sm"></div><span>Đang hoạt động</span></div>
                <div class="flex items-center gap-2"><div class="w-3.5 h-3.5 bg-gray-400 rounded-sm"></div><span>Đóng cửa/Trống</span></div>
                <div class="flex items-center gap-2"><div class="w-3.5 h-3.5 bg-orange-500 rounded-sm"></div><span>Đang bảo trì</span></div>
            </div>
            <div class="flex gap-2">
                <button id="zoom-in" class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded text-gray-600 hover:bg-gray-100" title="Phóng to"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg></button>
                <button id="zoom-out" class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded text-gray-600 hover:bg-gray-100" title="Thu nhỏ"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"></path></svg></button>
                <button id="zoom-reset" class="w-8 h-8 flex items-center justify-center border border-gray-200 rounded text-gray-600 hover:bg-gray-100" title="Mặc định"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg></button>
            </div>
        </div>
        
        <!-- Map Canvas -->
        <div id="map-wrapper" class="flex-1 overflow-hidden flex items-center justify-center bg-slate-50 relative cursor-grab active:cursor-grabbing w-full h-full">
            <div id="map-container" class="relative bg-white shadow-sm flex-shrink-0" style="width: 1829px; height: 1272px;">
                <!-- Base Map Image -->
                <img src="{{ asset('maps/sitemap.jpg') }}" class="w-full h-full block" alt="Sitemap">
                
                <!-- Dynamic Map Pins/Rectangles -->
                @foreach($kiosks as $kiosk)
                    @if($kiosk->position && $kiosk->position->x !== null && $kiosk->position->y !== null)
                        @php
                            $isAvailable = $kiosk->status === 'available';
                            $isRented = $kiosk->status === 'rented';
                            $colorClass = $isRented ? 'bg-green-500' : ($isAvailable ? 'bg-blue-600' : 'bg-orange-500');
                            
                            $origWidth = 1829;
                            $origHeight = 1272;
                            
                            $leftPct = ($kiosk->position->x / $origWidth) * 100;
                            $topPct = ($kiosk->position->y / $origHeight) * 100;
                            $widthPct = ($kiosk->position->width / $origWidth) * 100;
                            $heightPct = ($kiosk->position->height / $origHeight) * 100;

                            $kioskData = [
                                'id' => $kiosk->id,
                                'code' => $kiosk->code,
                                'name' => $kiosk->name ?: 'Tạp hoá & Đồ uống',
                                'status' => $kiosk->status,
                                'area' => $kiosk->area,
                                'zone' => $kiosk->position->zone ?? 'N/A'
                            ];
                        @endphp
                        <div onclick="handleKioskClick(event, this)"
                           class="kiosk-pin absolute flex items-center justify-center cursor-pointer group z-20"
                           data-id="{{ $kiosk->id }}"
                           data-kiosk="{{ json_encode($kioskData) }}"
                           style="left: {{ $leftPct }}%; top: {{ $topPct }}%; width: {{ $widthPct }}%; height: {{ $heightPct }}%;"
                           title="{{ $kiosk->code }}">
                            
                            <div class="kiosk-pin-inner w-full h-full border-[1.5px] border-white {{ $colorClass }} bg-opacity-80 hover:bg-opacity-100 rounded-[2px] shadow-sm flex items-center justify-center transition-all duration-200">
                                <span class="text-white text-[8px] font-bold drop-shadow-md opacity-0 group-hover:opacity-100 transition-opacity">{{ $kiosk->code }}</span>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="w-[380px] bg-white flex flex-col shrink-0 shadow-[-4px_0_15px_-3px_rgba(0,0,0,0.05)] z-10 overflow-hidden h-full">
        <div class="p-4 border-b border-gray-200 shrink-0">
            <h2 class="font-bold text-gray-800 text-lg mb-3">Danh bạ Kiosk</h2>
            
            <div id="filter-container" class="space-y-4">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" id="search-input" placeholder="Tìm tên, mã kiosk..." class="w-full border border-gray-200 rounded-md py-2.5 pl-9 pr-3 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm bg-gray-50/50">
                </div>
                
                <div class="flex gap-2 flex-wrap" id="filter-buttons">
                    <button type="button" data-filter-type="zone" data-filter-value="A" class="filter-btn px-4 py-1.5 rounded-full text-xs font-semibold transition bg-gray-100 text-gray-600 hover:bg-gray-200">Khu A</button>
                    <button type="button" data-filter-type="zone" data-filter-value="B" class="filter-btn px-4 py-1.5 rounded-full text-xs font-semibold transition bg-gray-100 text-gray-600 hover:bg-gray-200">Khu B</button>
                    <button type="button" data-filter-type="status" data-filter-value="available" class="filter-btn px-4 py-1.5 rounded-full text-xs font-semibold transition bg-gray-100 text-gray-600 hover:bg-gray-200">Trống</button>
                    <button type="button" data-filter-type="status" data-filter-value="rented" class="filter-btn px-4 py-1.5 rounded-full text-xs font-semibold transition bg-gray-100 text-gray-600 hover:bg-gray-200">Đang thuê</button>
                </div>
            </div>
        </div>
        
        <!-- Kiosk List Container -->
        <div class="flex-1 overflow-y-auto bg-gray-50 p-4 space-y-3 custom-scrollbar">
            @forelse($kiosks as $kiosk)
                @php
                    $isAvailable = $kiosk->status === 'available';
                    $isRented = $kiosk->status === 'rented';
                    $isReserved = $kiosk->status === 'reserved';
                    
                    $badgeText = $isRented ? 'ĐANG MỞ' : ($isAvailable ? 'TRỐNG' : 'TẠM NGHỈ');
                    $badgeClass = $isRented ? 'bg-green-100 text-green-700' : ($isAvailable ? 'bg-gray-100 text-gray-500' : 'bg-orange-100 text-orange-700');
                    
                    $isSelected = $loop->first && !request()->filled('q') && !request()->filled('zone');
                    
                    $kioskData = [
                        'id' => $kiosk->id,
                        'code' => $kiosk->code,
                        'name' => $kiosk->name ?: 'Tạp hoá & Đồ uống',
                        'status' => $kiosk->status,
                        'area' => $kiosk->area,
                        'zone' => $kiosk->position->zone ?? 'N/A'
                    ];
                @endphp
                <a href="/kiosks/{{ $kiosk->id }}" 
                   onclick="handleKioskClick(event, this)"
                   class="kiosk-list-item block bg-white rounded-lg p-4 transition shadow-sm {{ $isSelected ? 'border-2 border-blue-400 relative' : 'border border-gray-200 hover:border-gray-300' }}"
                   data-id="{{ $kiosk->id }}"
                   data-kiosk="{{ json_encode($kioskData) }}">
                    <div class="flex justify-between items-center mb-1.5">
                        <h3 class="font-bold text-gray-900 text-base">{{ $kiosk->code }}</h3>
                        <span class="badge px-2 py-0.5 rounded text-[10px] font-bold tracking-widest {{ $badgeClass }}">{{ $badgeText }}</span>
                    </div>
                    
                    @if($isAvailable)
                        <p class="text-sm text-gray-400 italic mb-3">Chưa cho thuê</p>
                    @else
                        <p class="text-sm text-gray-600 mb-3">{{ $kiosk->name ?: 'Tạp hoá & Đồ uống' }}</p>
                    @endif
                    
                    <div class="flex items-center gap-4 text-xs text-gray-500 font-medium">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            Khu {{ $kiosk->position->zone ?? 'N/A' }}
                        </div>
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                            {{ $kiosk->area }}m&sup2;
                        </div>
                    </div>
                    
                    <!-- Selection Indicator (SVG Check) -->
                    <div class="selection-indicator {{ $isSelected ? 'block' : 'hidden' }} absolute top-4 right-4 text-blue-500 bg-white rounded-full">
                       <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                </a>
            @empty
                <div class="text-center py-10 text-gray-500">
                    <p>Không tìm thấy kiosk nào.</p>
                </div>
            @endforelse
        </div>
        
        <!-- Bottom Panel for selected Kiosk -->
        @php $firstKiosk = $kiosks->first(); @endphp
        <div id="details-panel" class="{{ $firstKiosk ? 'block' : 'hidden' }} border-t border-gray-200 bg-white p-5 shadow-[0_-4px_10px_-2px_rgba(0,0,0,0.05)] z-20 shrink-0 transition-opacity">
            <h3 class="font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100 text-sm tracking-wide">Thông tin chi tiết</h3>
            <div class="flex justify-between items-center mb-4">
                <div id="detail-code" class="text-blue-700 font-bold text-xl">{{ $firstKiosk->code ?? '' }}</div>
                @php
                    $fstStatus = $firstKiosk->status ?? '';
                    $fstBadgeText = $fstStatus === 'rented' ? 'ĐANG MỞ' : ($fstStatus === 'available' ? 'TRỐNG' : 'TẠM NGHỈ');
                    $fstBadgeClass = $fstStatus === 'rented' ? 'bg-green-100 text-green-700' : ($fstStatus === 'available' ? 'bg-gray-100 text-gray-500' : 'bg-orange-100 text-orange-700');
                @endphp
                <span id="detail-status" class="px-2 py-0.5 rounded text-[10px] font-bold tracking-widest {{ $fstBadgeClass }}">{{ $fstBadgeText }}</span>
            </div>
            
            <div class="space-y-3 text-sm mb-5">
                <div class="flex justify-between items-center"><span class="text-gray-500">Loại hình:</span><span id="detail-type" class="font-medium text-gray-900">{{ $firstKiosk->name ?? 'Tạp hoá' }}</span></div>
                <div class="flex justify-between items-center"><span class="text-gray-500">Vị trí:</span><span id="detail-zone" class="font-medium text-gray-900">Khu {{ $firstKiosk->position->zone ?? 'A' }} - Tầng 1</span></div>
                <div class="flex justify-between items-center"><span class="text-gray-500">Diện tích:</span><span id="detail-area" class="font-medium text-gray-900">{{ $firstKiosk->area ?? 0 }}m&sup2;</span></div>
                <div class="flex justify-between items-center" id="detail-lessee-container" style="display: {{ $fstStatus === 'available' ? 'none' : 'flex' }}"><span class="text-gray-500">Chủ thuê:</span><span id="detail-lessee" class="font-medium text-gray-900">Đang cập nhật...</span></div>
            </div>
            
            <div id="detail-actions" class="flex flex-col gap-2 mt-4">
                @if($fstStatus === 'available')
                    <button onclick="openBookingModal({{ $firstKiosk->id }})" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 rounded-md text-sm font-semibold text-white flex items-center justify-center gap-2 transition shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg> 
                        Đăng ký thuê
                    </button>
                    <a href="/kiosks/{{ $firstKiosk->id }}" class="w-full py-2.5 bg-white hover:bg-gray-50 border border-gray-300 rounded-md text-sm font-semibold text-gray-700 flex items-center justify-center gap-2 transition shadow-sm">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Xem chi tiết
                    </a>
                @elseif($fstStatus === 'rented')
                    <a href="/kiosks/{{ $firstKiosk->id }}" class="w-full py-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-md text-sm font-semibold text-gray-700 flex items-center justify-center gap-2 transition shadow-sm">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Xem hợp đồng
                    </a>
                @elseif($fstStatus)
                    <a href="/kiosks/{{ $firstKiosk->id }}" class="w-full py-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-md text-sm font-semibold text-gray-700 flex items-center justify-center gap-2 transition shadow-sm">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Xem chi tiết
                    </a>
                    <button disabled class="w-full py-2.5 bg-gray-100 border border-gray-200 rounded-md text-sm font-semibold text-gray-400 cursor-not-allowed flex items-center justify-center gap-2 shadow-sm">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Tạm ngưng đăng ký
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Booking Modal -->
<div id="booking-modal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeBookingModal()"></div>
    
    <!-- Modal Content -->
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-xl shadow-2xl p-6 transition-all">
        <div class="flex justify-between items-center mb-5">
            <h3 class="text-xl font-bold text-gray-900">Đăng ký thuê Kiosk</h3>
            <button onclick="closeBookingModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <form id="booking-form" onsubmit="submitBooking(event)">
            @csrf
            <input type="hidden" id="booking-kiosk-id" name="kiosk_id" value="{{ $firstKiosk->id ?? '' }}">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên khách hàng / Doanh nghiệp <span class="text-red-500">*</span></label>
                    <input type="text" id="booking-name" name="customer_name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại liên hệ <span class="text-red-500">*</span></label>
                    <input type="tel" id="booking-phone" name="phone" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Loại hình kinh doanh dự kiến</label>
                    <input type="text" id="booking-business" name="business_type" placeholder="VD: Bán đồ ăn nhanh, Thời trang..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Thời gian thuê (Tháng) <span class="text-red-500">*</span></label>
                    <input type="number" id="booking-duration" name="duration_months" min="1" value="6" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            
            <div id="booking-error" class="hidden mt-4 p-3 bg-red-50 text-red-600 text-sm rounded-lg border border-red-200"></div>
            
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeBookingModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">Hủy</button>
                <button type="submit" id="booking-submit-btn" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition flex items-center justify-center min-w-[120px]">
                    Gửi yêu cầu
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast-container" class="fixed bottom-5 right-5 z-50 hidden transition-all duration-300 transform translate-y-10 opacity-0">
    <div class="bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span id="toast-message" class="font-medium"></span>
    </div>
</div>

<script>
function handleKioskClick(event, element) {
    event.preventDefault(); // Ngăn chặn chuyển trang
    
    // Parse JSON từ data attribute
    const kiosk = JSON.parse(element.getAttribute('data-kiosk'));

    // 1. Cập nhật dữ liệu vào bảng "Thông tin chi tiết"
    document.getElementById('details-panel').classList.remove('hidden');
    document.getElementById('detail-code').innerText = kiosk.code;
    
    const statusBadge = document.getElementById('detail-status');
    const lesseeContainer = document.getElementById('detail-lessee-container');
    const actionsContainer = document.getElementById('detail-actions');

    if (kiosk.status === 'rented') {
        statusBadge.innerText = 'ĐANG MỞ';
        statusBadge.className = 'px-2 py-0.5 rounded text-[10px] font-bold tracking-widest bg-green-100 text-green-700';
        lesseeContainer.style.display = 'flex';
        actionsContainer.innerHTML = `
            <a href="/kiosks/${kiosk.id}" class="w-full py-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-md text-sm font-semibold text-gray-700 flex items-center justify-center gap-2 transition shadow-sm">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Xem hợp đồng
            </a>
        `;
    } else if (kiosk.status === 'available') {
        statusBadge.innerText = 'TRỐNG';
        statusBadge.className = 'px-2 py-0.5 rounded text-[10px] font-bold tracking-widest bg-gray-100 text-gray-500';
        lesseeContainer.style.display = 'none';
        actionsContainer.innerHTML = `
            <button onclick="openBookingModal(${kiosk.id})" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 rounded-md text-sm font-semibold text-white flex items-center justify-center gap-2 transition shadow-sm">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg> 
                Đăng ký thuê
            </button>
            <a href="/kiosks/${kiosk.id}" class="w-full py-2.5 bg-white hover:bg-gray-50 border border-gray-300 rounded-md text-sm font-semibold text-gray-700 flex items-center justify-center gap-2 transition shadow-sm">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Xem chi tiết
            </a>
        `;
    } else {
        statusBadge.innerText = 'TẠM NGHỈ';
        statusBadge.className = 'px-2 py-0.5 rounded text-[10px] font-bold tracking-widest bg-orange-100 text-orange-700';
        lesseeContainer.style.display = 'flex';
        actionsContainer.innerHTML = `
            <a href="/kiosks/${kiosk.id}" class="w-full py-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-md text-sm font-semibold text-gray-700 flex items-center justify-center gap-2 transition shadow-sm">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Xem chi tiết
            </a>
            <button disabled class="w-full py-2.5 bg-gray-100 border border-gray-200 rounded-md text-sm font-semibold text-gray-400 cursor-not-allowed flex items-center justify-center gap-2 shadow-sm">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Tạm ngưng đăng ký
            </button>
        `;
    }

    document.getElementById('detail-type').innerText = kiosk.status === 'available' ? 'Chưa cho thuê' : kiosk.name;
    document.getElementById('detail-zone').innerText = 'Khu ' + kiosk.zone + ' - Tầng 1';
    document.getElementById('detail-area').innerText = kiosk.area + 'm²';


    // 2. Cập nhật trạng thái Active trên giao diện (xóa viền cũ, thêm viền mới)
    
    // Reset toàn bộ style của item trên sidebar
    document.querySelectorAll('.kiosk-list-item').forEach(item => {
        item.classList.remove('border-2', 'border-blue-400', 'relative');
        item.classList.add('border', 'border-gray-200');
        const indicator = item.querySelector('.selection-indicator');
        if (indicator) indicator.classList.add('hidden');
    });

    // Cập nhật style cho item được click (nếu click từ sidebar)
    if (element.classList.contains('kiosk-list-item')) {
        element.classList.remove('border', 'border-gray-200');
        element.classList.add('border-2', 'border-blue-400', 'relative');
        const indicator = element.querySelector('.selection-indicator');
        if (indicator) {
            indicator.classList.remove('hidden');
            indicator.classList.add('block');
        }
    } else {
        // Nếu click từ bản đồ, tìm item tương ứng trên sidebar để highlight
        const sidebarItem = document.querySelector('.kiosk-list-item[data-id="' + kiosk.id + '"]');
        if (sidebarItem) {
            sidebarItem.classList.remove('border', 'border-gray-200');
            sidebarItem.classList.add('border-2', 'border-blue-400', 'relative');
            const indicator = sidebarItem.querySelector('.selection-indicator');
            if (indicator) {
                indicator.classList.remove('hidden');
                indicator.classList.add('block');
            }
            // Tự động cuộn tới item đó
            sidebarItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    // Reset hiệu ứng scale của pin trên bản đồ
    document.querySelectorAll('.kiosk-pin').forEach(pin => {
        pin.classList.remove('z-30', 'scale-110', 'shadow-lg');
        pin.classList.add('z-20');
        const inner = pin.querySelector('.kiosk-pin-inner');
        if (inner) {
            inner.classList.remove('border-4', 'border-white');
            inner.classList.add('border-[1.5px]', 'border-white');
        }
    });
    
    // Thêm hiệu ứng cho map pin nếu có
    const mapPin = document.querySelector('.kiosk-pin[data-id="' + kiosk.id + '"]');
    if (mapPin) {
        mapPin.classList.remove('z-20');
        mapPin.classList.add('z-30', 'scale-110', 'shadow-lg');
        const inner = mapPin.querySelector('.kiosk-pin-inner');
        if (inner) {
            inner.classList.remove('border-[1.5px]');
            inner.classList.add('border-4', 'border-white');
        }
    }
}

// Khởi tạo Panzoom sau khi DOM load
document.addEventListener('DOMContentLoaded', function() {
    const mapContainer = document.getElementById('map-container');
    const mapWrapper = document.getElementById('map-wrapper');
    
    if(mapContainer && mapWrapper && typeof Panzoom !== 'undefined') {
        // Ảnh gốc là 1829x1272. Tính toán tỷ lệ scale ban đầu sao cho ảnh vừa khít với wrapper (95% để có chút lề)
        const wrapperRect = mapWrapper.getBoundingClientRect();
        const initialScale = Math.min(
            (wrapperRect.width - 40) / 1829,
            (wrapperRect.height - 40) / 1272
        );

        const panzoom = Panzoom(mapContainer, {
            maxScale: 5,
            minScale: initialScale * 0.5,
            startScale: initialScale,
            startX: 0,
            startY: 0,
            step: 0.3
        });
        
        // Hỗ trợ Zoom bằng con lăn chuột (Wheel)
        mapWrapper.addEventListener('wheel', panzoom.zoomWithWheel);

        // Gắn sự kiện cho các nút điều khiển
        document.getElementById('zoom-in').addEventListener('click', panzoom.zoomIn);
        document.getElementById('zoom-out').addEventListener('click', panzoom.zoomOut);
        
        // Reset về vị trí ban đầu (căn giữa + scale mặc định)
        document.getElementById('zoom-reset').addEventListener('click', function(e) {
            e.preventDefault();
            panzoom.pan(0, 0);
            panzoom.zoom(initialScale, { animate: true });
        });
    }
});

// Booking Modal Logic
function openBookingModal(kioskId) {
    document.getElementById('booking-kiosk-id').value = kioskId;
    document.getElementById('booking-error').classList.add('hidden');
    document.getElementById('booking-form').reset();
    document.getElementById('booking-duration').value = 6;
    
    const modal = document.getElementById('booking-modal');
    modal.classList.remove('hidden');
}

function closeBookingModal() {
    document.getElementById('booking-modal').classList.add('hidden');
}

function showToast(message) {
    const toast = document.getElementById('toast-container');
    document.getElementById('toast-message').innerText = message;
    
    toast.classList.remove('hidden');
    setTimeout(() => {
        toast.classList.remove('translate-y-10', 'opacity-0');
    }, 10);
    
    setTimeout(() => {
        toast.classList.add('translate-y-10', 'opacity-0');
        setTimeout(() => toast.classList.add('hidden'), 300);
    }, 4000);
}

async function submitBooking(event) {
    event.preventDefault();
    
    const form = event.target;
    const btn = document.getElementById('booking-submit-btn');
    const errorBox = document.getElementById('booking-error');
    
    const formData = {
        kiosk_id: form.kiosk_id.value,
        customer_name: form.customer_name.value,
        phone: form.phone.value,
        business_type: form.business_type.value,
        duration_months: form.duration_months.value
    };
    
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Đang xử lý...';
    
    try {
        const response = await fetch('/api/rental-requests', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.message || 'Lỗi dữ liệu, vui lòng kiểm tra lại');
        }
        
        closeBookingModal();
        showToast(data.message);
        
    } catch (error) {
        errorBox.innerText = error.message;
        errorBox.classList.remove('hidden');
    } finally {
        btn.disabled = false;
        btn.innerText = 'Gửi yêu cầu';
    }
}

// Khởi tạo trạng thái bộ lọc Client-side
const filterState = {
    searchQuery: '',
    zones: new Set(),
    statuses: new Set()
};

function initFilters() {
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            filterState.searchQuery = e.target.value.toLowerCase().trim();
            applyFilters();
        });
    }

    const filterBtns = document.querySelectorAll('.filter-btn');
    filterBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const type = btn.getAttribute('data-filter-type');
            const value = btn.getAttribute('data-filter-value');
            
            // Toggle state
            const targetSet = type === 'zone' ? filterState.zones : filterState.statuses;
            if (targetSet.has(value)) {
                targetSet.delete(value);
                // Update UI to inactive
                btn.className = 'filter-btn px-4 py-1.5 rounded-full text-xs font-semibold transition bg-gray-100 text-gray-600 hover:bg-gray-200';
            } else {
                targetSet.add(value);
                // Update UI to active
                btn.className = 'filter-btn px-4 py-1.5 rounded-full text-xs font-semibold transition bg-blue-600 text-white shadow-sm';
            }
            applyFilters();
        });
    });
}

function applyFilters() {
    // Collect all kiosks DOM elements
    const kioskListItems = document.querySelectorAll('.kiosk-list-item');
    
    kioskListItems.forEach(listItem => {
        const kioskData = JSON.parse(listItem.getAttribute('data-kiosk'));
        const id = kioskData.id;
        
        let matchSearch = true;
        let matchZone = true;
        let matchStatus = true;
        
        // Check Search
        if (filterState.searchQuery) {
            const code = (kioskData.code || '').toLowerCase();
            const name = (kioskData.name || '').toLowerCase();
            matchSearch = code.includes(filterState.searchQuery) || name.includes(filterState.searchQuery);
        }
        
        // Check Zone (If any zone is selected, kiosk zone must be in the set)
        if (filterState.zones.size > 0) {
            matchZone = filterState.zones.has(kioskData.zone);
        }
        
        // Check Status
        if (filterState.statuses.size > 0) {
            matchStatus = filterState.statuses.has(kioskData.status);
        }
        
        const isMatch = matchSearch && matchZone && matchStatus;
        
        // Update Sidebar List Item
        if (isMatch) {
            listItem.style.display = 'block';
        } else {
            listItem.style.display = 'none';
        }
        
        // Update Map Pin
        const mapPin = document.querySelector(`.kiosk-pin[data-id="${id}"]`);
        if (mapPin) {
            if (isMatch) {
                mapPin.classList.remove('opacity-30', 'pointer-events-none');
                mapPin.classList.add('opacity-100');
            } else {
                mapPin.classList.add('opacity-30', 'pointer-events-none');
                mapPin.classList.remove('opacity-100');
            }
        }
    });
}

// Khởi chạy khi DOM load xong
document.addEventListener('DOMContentLoaded', () => {
    initFilters();
});
</script>

<script src="https://cdn.jsdelivr.net/npm/@panzoom/panzoom@4.5.1/dist/panzoom.min.js"></script>

<style>
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent; 
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1; 
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8; 
}
</style>
@endsection
