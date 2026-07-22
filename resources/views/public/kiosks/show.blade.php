@extends('layouts.public')

@section('title', 'Kiosk Directory - ' . $kiosk->code)

@section('header_title')
    <div class="flex items-center gap-2 text-sm">
        <a href="/" class="text-gray-500 hover:text-blue-600 transition">Home</a>
        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        <a href="/kiosks" class="text-gray-500 hover:text-blue-600 transition">Kiosk Directory</a>
        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        <span class="text-gray-900 font-semibold">{{ $kiosk->code }}</span>
    </div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto w-full p-6 pb-20">

    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Left Column (Images, Map, Description) -->
        <div class="w-full lg:w-[60%] space-y-6">
            <!-- Image Gallery -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm p-4">
                <div class="relative w-full aspect-[16/9] mb-4 bg-gray-100 rounded overflow-hidden">
                    @if($kiosk->images->isNotEmpty())
                        <img id="main-image" src="/storage/{{ $kiosk->images->first()->path }}" class="w-full h-full object-cover" alt="{{ $kiosk->code }}">
                        <div class="absolute bottom-3 right-3 bg-white/90 backdrop-blur-sm px-3 py-1 text-xs font-bold rounded shadow-sm text-gray-700">
                            1 / {{ $kiosk->images->count() }}
                        </div>
                    @else
                        <img id="main-image" src="https://via.placeholder.com/1200x800?text=No+Image" class="w-full h-full object-cover" alt="Placeholder">
                    @endif
                </div>
                
                @if($kiosk->images->count() > 0)
                <div class="flex gap-3 overflow-x-auto pb-1">
                    @foreach($kiosk->images as $index => $img)
                        <img src="/storage/{{ $img->path }}" class="w-24 h-16 object-cover cursor-pointer rounded border-2 {{ $index == 0 ? 'border-blue-500 opacity-100' : 'border-transparent opacity-60 hover:opacity-100' }} transition" onclick="updateMainImage(this, '{{ $index + 1 }}', '{{ $kiosk->images->count() }}')">
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Location Map -->
            <div>
                <h3 class="font-bold text-gray-900 mb-3 border-b pb-2 text-lg">Location Map</h3>
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 h-64 relative flex items-center justify-center overflow-hidden">
                    <div class="absolute inset-0 bg-contain bg-center bg-no-repeat" style="background-image: url('{{ asset('maps/sitemap.jpg') }}')"></div>
                    
                    <div class="absolute z-10 bg-white border border-gray-200 shadow-md rounded-full px-4 py-2 flex items-center gap-2 top-4 right-4">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-sm font-bold text-gray-800">{{ $kiosk->code }} - Zone {{ $kiosk->position->zone ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div>
                <h3 class="font-bold text-gray-900 mb-3 border-b pb-2 text-lg">Description & Advantages</h3>
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 text-sm text-gray-700 leading-relaxed space-y-4">
                    @if($kiosk->description)
                        {!! nl2br(e($kiosk->description)) !!}
                    @else
                        <p>{{ $kiosk->name ?: 'Kiosk' }} is a prime commercial location situated in Zone {{ $kiosk->position->zone ?? 'N/A' }} of the Hue Bus Station, directly adjacent to the main ticketing concourse. This high-visibility spot benefits from consistent foot traffic from arriving and departing passengers, particularly those heading towards the sleeper bus loading zones.</p>
                        <p>The space is versatile and ideally suited for convenience retail, packaged food and beverage, or travel essentials. It features reinforced security shutters and direct access to heavy-duty electrical lines capable of supporting refrigeration units.</p>
                        <ul class="list-disc pl-5 space-y-2 mt-4 text-gray-600">
                            <li>Strategic location near major passenger flow nodes.</li>
                            <li>Pre-installed high-capacity electrical panel.</li>
                            <li>Close proximity to administrative offices ensures rapid security response.</li>
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column (Details) -->
        <div class="w-full lg:w-[40%]">
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 sticky top-6">
                @php
                    $isAvailable = $kiosk->status === 'available';
                    $isRented = $kiosk->status === 'rented';
                    
                    $badgeText = $isRented ? 'ĐANG MỞ' : ($isAvailable ? 'TRỐNG' : 'TẠM NGHỈ');
                    $badgeClass = $isRented ? 'bg-green-100 text-green-700' : ($isAvailable ? 'bg-green-100 text-green-600' : 'bg-orange-100 text-orange-700');
                @endphp
                
                <div class="flex justify-between items-start mb-2">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $kiosk->code }}</h2>
                    <span class="px-3 py-1 rounded text-xs font-bold tracking-widest {{ $badgeClass }}">{{ $badgeText }}</span>
                </div>
                <p class="text-sm text-gray-500 mb-4">Zone {{ $kiosk->position->zone ?? 'N/A' }} - Sảnh Chính</p>
                
                <hr class="border-gray-100 mb-4">
                
                <div class="mb-4">
                    <p class="text-xs text-gray-500 font-bold tracking-widest uppercase mb-1">Starting Lease</p>
                    <div class="text-3xl font-bold text-blue-600">
                        {{ number_format($kiosk->price, 0, ',', '.') }} <span class="text-sm font-medium text-gray-500">VND / tháng</span>
                    </div>
                </div>
                
                <hr class="border-gray-100 mb-4">
                
                <div class="grid grid-cols-2 gap-y-4 gap-x-2 text-sm mb-6">
                    <div>
                        <div class="flex items-center gap-1.5 text-gray-500 mb-1 font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                            Area
                        </div>
                        <div class="font-semibold text-gray-900 ml-5.5">{{ $kiosk->area }} m&sup2;</div>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5 text-gray-500 mb-1 font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            Floor
                        </div>
                        <div class="font-semibold text-gray-900 ml-5.5">Tầng 1 (Ground)</div>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5 text-gray-500 mb-1 font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            Type
                        </div>
                        <div class="font-semibold text-gray-900 ml-5.5">Bán lẻ / F&B</div>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5 text-gray-500 mb-1 font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Term
                        </div>
                        <div class="font-semibold text-gray-900 ml-5.5">Min 12 months</div>
                    </div>
                </div>

                <hr class="border-gray-100 mb-4">
                
                <div class="mb-6">
                    <p class="text-xs text-gray-500 font-bold tracking-widest uppercase mb-3">Utilities & Features</p>
                    <div class="flex flex-wrap gap-2">
                        <div class="px-3 py-1.5 bg-blue-50 text-blue-700 text-xs font-medium rounded flex items-center gap-1.5 border border-blue-100">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg> Điện 3-pha
                        </div>
                        <div class="px-3 py-1.5 bg-blue-50 text-blue-700 text-xs font-medium rounded flex items-center gap-1.5 border border-blue-100">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg> Nước sạch
                        </div>
                        <div class="px-3 py-1.5 bg-blue-50 text-blue-700 text-xs font-medium rounded flex items-center gap-1.5 border border-blue-100">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path></svg> Điều hòa TT
                        </div>
                        <div class="px-3 py-1.5 bg-gray-50 text-gray-700 text-xs font-medium rounded flex items-center gap-1.5 border border-gray-200 mt-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg> Camera Hành lang
                        </div>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <button type="button" onclick="openRentModal()" class="w-full flex justify-center items-center gap-2 bg-[#0078d4] text-white py-3 px-4 rounded font-medium hover:bg-blue-700 transition shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        Liên hệ thuê
                    </button>
                    <button type="button" class="w-full flex justify-center items-center gap-2 bg-white text-gray-700 border border-gray-300 py-3 px-4 rounded font-medium hover:bg-gray-50 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Tải brochure (PDF)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Similar Kiosks -->
    <div class="mt-12 border-t border-gray-200 pt-8">
        <h3 class="font-bold text-gray-900 mb-6 text-xl">Kiosks Tương tự</h3>
        @php
            // Mocking similar kiosks for UI
            $similar_kiosks = \App\Models\Kiosk::where('id', '!=', $kiosk->id)->limit(4)->get();
        @endphp
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($similar_kiosks as $sim)
                @php
                    $simAvailable = $sim->status === 'available';
                    $simRented = $sim->status === 'rented';
                    $simBadgeText = $simRented ? 'ĐANG MỞ' : ($simAvailable ? 'TRỐNG' : 'ĐANG BẢO TRÌ');
                    $simBadgeClass = $simRented ? 'bg-green-500 text-white' : ($simAvailable ? 'bg-green-500 text-white' : 'bg-orange-500 text-white');
                @endphp
                <a href="/kiosks/{{ $sim->id }}" class="block bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition group">
                    <div class="relative h-40 bg-gray-100 overflow-hidden">
                        @if($sim->images->isNotEmpty())
                            <img src="/storage/{{ $sim->images->first()->path }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="{{ $sim->code }}">
                        @else
                            <img src="https://via.placeholder.com/400x300?text=Kiosk" class="w-full h-full object-cover" alt="Placeholder">
                        @endif
                        <div class="absolute top-2 right-2 px-2 py-0.5 rounded text-[10px] font-bold tracking-wider {{ $simBadgeClass }} shadow-sm">
                            {{ $simBadgeText }}
                        </div>
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-gray-900 text-base mb-1">{{ $sim->code }}</h4>
                        <p class="text-xs text-gray-500 mb-3">{{ $sim->area }} m&sup2; &bull; Zone {{ $sim->position->zone ?? 'N/A' }}</p>
                        <div class="text-sm font-bold text-blue-600">
                            {{ number_format($sim->price, 0, ',', '.') }} VND
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>

<!-- Rent Modal -->
<div id="rentModal" class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-opacity duration-200">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-lg transform scale-95 transition-transform duration-200" id="rentModalContent">
        <div class="flex justify-between items-start p-5 border-b border-gray-200">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Liên hệ thuê Kiosk</h3>
                <p class="text-xs text-gray-500 mt-1">Vui lòng để lại thông tin, nhân viên bến xe sẽ liên hệ lại với bạn trong vòng 24h.</p>
            </div>
            <button type="button" onclick="closeRentModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <div class="p-5">
            <div class="bg-gray-50 border border-gray-200 rounded p-3 mb-5 flex items-center gap-3">
                @if($kiosk->images->isNotEmpty())
                    <img src="/storage/{{ $kiosk->images->first()->path }}" class="w-16 h-12 object-cover rounded border border-gray-200 bg-white">
                @else
                    <div class="w-16 h-12 bg-gray-200 rounded"></div>
                @endif
                <div>
                    <h4 class="font-bold text-sm text-gray-900">{{ $kiosk->code }}</h4>
                    <p class="text-xs text-gray-500">Diện tích: {{ $kiosk->area }}m&sup2; &bull; Giá: {{ number_format($kiosk->price, 0, ',', '.') }} đ/tháng</p>
                </div>
            </div>

            <form action="/requests" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="kiosk_id" value="{{ $kiosk->id }}">
                
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Họ và tên <span class="text-red-500">*</span></label>
                    <input type="text" name="contact_name" required placeholder="Nhập họ và tên" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm">
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Số điện thoại <span class="text-red-500">*</span></label>
                    <input type="text" name="contact_phone" required placeholder="Nhập số điện thoại" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm">
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Email (Tùy chọn)</label>
                    <input type="email" name="contact_email" placeholder="example@email.com" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm">
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Mục đích kinh doanh</label>
                    <select name="business_type" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm bg-white">
                        <option value="F&B">F&B</option>
                        <option value="Retail">Bán lẻ</option>
                        <option value="Service">Dịch vụ</option>
                        <option value="Other">Khác</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Ghi chú thêm</label>
                    <textarea name="note" rows="3" placeholder="Thông tin bổ sung..." class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm"></textarea>
                </div>
                
                <div class="pt-2 flex gap-3 justify-end">
                    <button type="button" onclick="closeRentModal()" class="px-6 py-2 border border-gray-300 rounded bg-white text-gray-700 font-medium text-sm hover:bg-gray-50 transition">Hủy bỏ</button>
                    <button type="submit" class="px-6 py-2 bg-[#0078d4] rounded text-white font-medium text-sm hover:bg-blue-700 transition">Gửi yêu cầu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateMainImage(thumbEl, current, total) {
    const mainImg = document.getElementById('main-image');
    mainImg.src = thumbEl.src;
    
    // update borders
    const thumbs = thumbEl.parentElement.querySelectorAll('img');
    thumbs.forEach(t => {
        t.classList.remove('border-blue-500', 'opacity-100');
        t.classList.add('border-transparent', 'opacity-60');
    });
    thumbEl.classList.remove('border-transparent', 'opacity-60');
    thumbEl.classList.add('border-blue-500', 'opacity-100');
    
    // Note: updating the "1 / 5" counter text would require a span id, omitted for brevity as the counter logic needs JS state.
}

function openRentModal() {
    const modal = document.getElementById('rentModal');
    const content = document.getElementById('rentModalContent');
    modal.classList.remove('opacity-0', 'pointer-events-none');
    content.classList.remove('scale-95');
    content.classList.add('scale-100');
}

function closeRentModal() {
    const modal = document.getElementById('rentModal');
    const content = document.getElementById('rentModalContent');
    modal.classList.add('opacity-0', 'pointer-events-none');
    content.classList.remove('scale-100');
    content.classList.add('scale-95');
}
</script>
@endsection
