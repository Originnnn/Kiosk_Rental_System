@extends('layouts.admin')

@section('title', 'Danh sách Quầy/Kiosk - Bến Xe Huế')

@section('content')
<div x-data="kioskManager()" class="bg-gray-50 min-h-screen p-6 font-sans relative overflow-hidden">
    
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Danh sách Quầy/Kiosk</h1>
            <p class="text-sm text-gray-500">Quản lý không gian cho thuê và trạng thái hoạt động.</p>
        </div>
        
        @can('is-employee')
        <div>
            <button @click="openModal = true" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded font-medium flex items-center text-sm transition-colors shadow-sm">
                <i class="fa-solid fa-plus mr-2"></i> Thêm mới
            </button>
        </div>
        @endcan
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-t border border-gray-200 border-b-0 flex justify-between items-center">
        <form action="{{ route('admin.kiosks.index') }}" method="GET" class="flex w-1/3">
            <div class="relative w-full">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                </div>
                <input type="text" name="q" value="{{ request('q') }}" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-primary focus:border-primary text-sm bg-white" placeholder="Tìm theo mã quầy, vị trí...">
            </div>
        </form>

        <form action="{{ route('admin.kiosks.index') }}" method="GET" class="flex items-center space-x-3" id="filterForm">
            @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
            <select name="status" onchange="document.getElementById('filterForm').submit()" class="border border-gray-300 rounded px-3 py-2 text-sm focus:ring-1 focus:ring-primary focus:border-primary bg-white min-w-[150px] text-gray-600">
                <option value="Tất cả trạng thái" {{ request('status') == 'Tất cả trạng thái' ? 'selected' : '' }}>Tất cả trạng thái</option>
                <option value="Trống" {{ request('status') == 'Trống' ? 'selected' : '' }}>Trống</option>
                <option value="Đang thuê" {{ request('status') == 'Đang thuê' ? 'selected' : '' }}>Đang thuê</option>
                <option value="Bảo trì" {{ request('status') == 'Bảo trì' ? 'selected' : '' }}>Bảo trì</option>
            </select>
            <select class="border border-gray-300 rounded px-3 py-2 text-sm focus:ring-1 focus:ring-primary focus:border-primary bg-white min-w-[150px] text-gray-600">
                <option>Tất cả khu vực</option>
            </select>
        </form>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-4 mb-4 text-sm font-medium border border-green-200 rounded mx-4 mt-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Bảng dữ liệu -->
    <div class="bg-white border border-gray-200 rounded-b overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-4 w-12 text-center">
                        <input type="checkbox" class="rounded border-gray-300 text-[#006699] focus:ring-[#006699]">
                    </th>
                    <th class="px-4 py-4 text-xs font-bold text-gray-500 uppercase">Mã quầy</th>
                    <th class="px-4 py-4 text-xs font-bold text-gray-500 uppercase">Vị trí</th>
                    <th class="px-4 py-4 text-xs font-bold text-gray-500 uppercase text-right">Diện tích (m²)</th>
                    <th class="px-4 py-4 text-xs font-bold text-gray-500 uppercase text-right">Đơn giá thuê (VNĐ/tháng)</th>
                    <th class="px-4 py-4 text-xs font-bold text-gray-500 uppercase text-center">Trạng thái</th>
                    <th class="px-4 py-4 text-xs font-bold text-gray-500 uppercase text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($kiosks as $kiosk)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-center">
                            <input type="checkbox" class="rounded border-gray-300 text-[#006699] focus:ring-[#006699]">
                        </td>
                        <td class="px-4 py-4 text-sm font-bold text-gray-900">
                            {{ $kiosk->code }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700">
                            {{ $kiosk->name }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700 text-right">
                            {{ number_format($kiosk->area, 1) }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700 font-medium text-right">
                            {{ number_format($kiosk->price, 0, ',', ',') }}
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if($kiosk->status == 'available')
                                <span class="inline-flex items-center px-2 py-1 text-xs font-bold rounded bg-green-100 text-green-700 uppercase">
                                    TRỐNG
                                </span>
                            @elseif($kiosk->status == 'rented')
                                <span class="inline-flex items-center px-2 py-1 text-xs font-bold rounded bg-blue-100 text-blue-700 uppercase">
                                    ĐANG THUÊ
                                </span>
                            @elseif($kiosk->status == 'maintenance')
                                <span class="inline-flex items-center px-2 py-1 text-xs font-bold rounded bg-yellow-100 text-yellow-700 uppercase">
                                    BẢO TRÌ
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 text-xs font-bold rounded bg-gray-100 text-gray-700 uppercase">
                                    {{ $kiosk->status }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center">
                            <button @click="openDrawer({{ $kiosk->id }})" class="text-gray-400 hover:text-[#006699] transition">
                                <i class="fa-regular fa-eye text-lg"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            Không tìm thấy Kiosk nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-6 py-4 border border-t-0 border-gray-200 bg-white flex items-center justify-between rounded-b">
        <div class="text-sm text-gray-500">
            Hiển thị {{ $kiosks->firstItem() ?? 0 }} - {{ $kiosks->lastItem() ?? 0 }} trong số {{ $kiosks->total() }} kết quả
        </div>
        <div>
            {{ $kiosks->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    </div>

    <!-- Modal Thêm mới -->
    <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="bg-white rounded-lg shadow-xl w-[600px] flex flex-col" @click.away="openModal = false">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-900">Thêm Quầy/Kiosk mới</h2>
                <button @click="openModal = false" class="text-gray-400 hover:text-gray-700">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <form action="{{ route('admin.kiosks.store') }}" method="POST" id="kioskForm">
                    @csrf
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Mã quầy <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="text" name="code" value="{{ old('code') }}" class="w-full px-3 py-2 bg-gray-100 border {{ $errors->has('code') ? 'border-red-500' : 'border-gray-300' }} rounded text-sm focus:outline-none" placeholder="KIO-2023-049">
                                <i class="fa-solid fa-lock absolute right-3 top-2.5 text-gray-400 text-xs"></i>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1">Tự động tạo bởi hệ thống hoặc nhập tay</p>
                            @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Vị trí <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" class="w-full px-3 py-2 border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }} rounded focus:outline-none focus:border-primary text-sm" placeholder="Nhập vị trí...">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Diện tích (m2) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="number" step="0.01" name="area" value="{{ old('area') }}" class="w-full px-3 py-2 border {{ $errors->has('area') ? 'border-red-500' : 'border-gray-300' }} rounded focus:outline-none focus:border-primary text-sm text-right pr-8" placeholder="0.00">
                                <span class="absolute right-3 top-2 text-sm text-gray-500 font-medium">m²</span>
                            </div>
                            @error('area') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Đơn giá mặc định <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="number" name="price" value="{{ old('price') }}" class="w-full px-3 py-2 border {{ $errors->has('price') ? 'border-red-500' : 'border-gray-300' }} rounded focus:outline-none focus:border-primary text-sm text-right pr-12" placeholder="0">
                                <span class="absolute right-3 top-2 text-sm text-gray-500 font-medium">VNĐ</span>
                            </div>
                            @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Mô tả thiết bị đi kèm</label>
                        <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-primary text-sm resize-none" placeholder="Liệt kê các thiết bị như: Bàn ghế, ổ cắm điện, hệ thống chiếu sáng...">{{ old('description') }}</textarea>
                    </div>

                    <!-- Placeholder cho upload ảnh UI (Chưa có tính năng upload ở backend nhưng làm cho giống UI) -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Hình ảnh tham chiếu</label>
                        <div class="border border-dashed border-[#a3c2d1] rounded-lg p-6 flex flex-col items-center justify-center bg-gray-50 cursor-not-allowed">
                            <i class="fa-regular fa-image text-2xl text-[#6ba4c7] mb-2"></i>
                            <p class="text-sm text-gray-500">Kéo thả hình ảnh vào đây hoặc <span class="text-[#006699] font-medium">Chọn file</span></p>
                            <p class="text-xs text-[#6ba4c7] mt-1 font-semibold">JPG, PNG (Tối đa 5MB)</p>
                        </div>
                    </div>

                </form>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 rounded-b-lg bg-white">
                <button type="button" @click="openModal = false" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded font-medium text-sm hover:bg-gray-50">
                    Hủy bỏ
                </button>
                <button type="button" onclick="document.getElementById('kioskForm').submit()" class="px-4 py-2 bg-[#006699] text-white rounded font-bold text-sm hover:bg-[#005580]">
                    <i class="fa-regular fa-floppy-disk mr-2"></i> Lưu Kiosk
                </button>
            </div>

        </div>
    </div>

    <!-- Drawer Chi tiết -->
    <div x-show="drawerOpen" class="fixed inset-0 z-40 flex justify-end bg-black bg-opacity-20" style="display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
         
         <div class="w-[500px] bg-white h-full shadow-2xl flex flex-col transform transition-transform duration-300"
              @click.away="drawerOpen = false"
              x-transition:enter="transition ease-out duration-300"
              x-transition:enter-start="translate-x-full"
              x-transition:enter-end="translate-x-0"
              x-transition:leave="transition ease-in duration-200"
              x-transition:leave-start="translate-x-0"
              x-transition:leave-end="translate-x-full">
            
            <!-- Drawer Header -->
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-start flex-shrink-0">
                <div>
                    <h2 class="text-xl font-bold text-[#006699]" x-text="'Chi tiết Kiosk ' + (activeKiosk ? activeKiosk.code : '')"></h2>
                    <p class="text-sm text-gray-500 mt-1" x-text="activeKiosk ? activeKiosk.name : ''"></p>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="px-2 py-1 text-xs font-bold rounded uppercase" 
                          :class="{
                              'bg-green-100 text-green-700': activeKiosk && activeKiosk.status == 'available',
                              'bg-blue-100 text-blue-700': activeKiosk && activeKiosk.status == 'rented',
                              'bg-yellow-100 text-yellow-700': activeKiosk && activeKiosk.status == 'maintenance'
                          }"
                          x-text="activeKiosk ? (activeKiosk.status == 'available' ? 'TRỐNG' : (activeKiosk.status == 'rented' ? 'ĐANG THUÊ' : 'BẢO TRÌ')) : ''">
                    </span>
                    <button @click="drawerOpen = false" class="text-gray-400 hover:text-gray-700">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Drawer Body -->
            <div class="p-6 overflow-y-auto flex-1 bg-white">
                <template x-if="loading">
                    <div class="flex justify-center items-center h-full">
                        <i class="fa-solid fa-circle-notch fa-spin text-3xl text-gray-300"></i>
                    </div>
                </template>

                <template x-if="!loading && activeKiosk">
                    <div>
                        <!-- Info Grid -->
                        <div class="flex space-x-4 mb-6">
                            <!-- Placeholder Image -->
                            <div class="w-1/2 rounded-lg overflow-hidden relative bg-gray-100 border border-gray-200 flex items-center justify-center">
                                <img src="https://images.unsplash.com/photo-1555529733-0e67056058e1?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Kiosk" class="w-full h-full object-cover">
                                <div class="absolute bottom-2 right-2 bg-white p-1.5 rounded shadow">
                                    <i class="fa-solid fa-camera text-gray-500 text-xs"></i>
                                </div>
                            </div>
                            
                            <div class="w-1/2 grid grid-cols-1 gap-3">
                                <div class="bg-gray-50 border border-gray-200 rounded p-3">
                                    <p class="text-[10px] font-bold text-gray-500 uppercase mb-1">DIỆN TÍCH</p>
                                    <p class="text-sm font-bold text-gray-900" x-text="activeKiosk.area + ' m²'"></p>
                                </div>
                                <div class="bg-gray-50 border border-gray-200 rounded p-3">
                                    <p class="text-[10px] font-bold text-gray-500 uppercase mb-1">GIÁ CƠ BẢN</p>
                                    <p class="text-sm font-bold text-[#006699]" x-text="new Intl.NumberFormat('vi-VN').format(activeKiosk.price) + ' đ/tháng'"></p>
                                </div>
                                <div class="bg-gray-50 border border-gray-200 rounded p-3">
                                    <p class="text-[10px] font-bold text-gray-500 uppercase mb-1">MÔ TẢ</p>
                                    <p class="text-xs text-gray-700 truncate" x-text="activeKiosk.description || 'Không có mô tả'"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Technical Specs -->
                        <div class="border border-gray-200 rounded-lg mb-6 overflow-hidden">
                            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                <h3 class="text-sm font-bold text-gray-800 flex items-center">
                                    <i class="fa-solid fa-wrench text-gray-400 mr-2"></i> Thông số kỹ thuật
                                </h3>
                            </div>
                            <div class="p-4 grid grid-cols-2 gap-y-3 gap-x-6 text-sm">
                                <div class="flex justify-between border-b border-gray-100 pb-1 border-dashed">
                                    <span class="text-gray-500">Nguồn điện:</span>
                                    <span class="font-semibold text-gray-900">220V - 30A</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-100 pb-1 border-dashed">
                                    <span class="text-gray-500">Cấp nước:</span>
                                    <span class="font-semibold text-gray-900">Có (D21)</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-100 pb-1 border-dashed">
                                    <span class="text-gray-500">Internet:</span>
                                    <span class="font-semibold text-gray-900">Cáp quang VNPT</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-100 pb-1 border-dashed">
                                    <span class="text-gray-500">Điều hòa:</span>
                                    <span class="font-semibold text-gray-900">Âm trần 18000 BTU</span>
                                </div>
                            </div>
                        </div>

                        <!-- Current Tenant -->
                        <div class="mb-6">
                            <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center">
                                Khách thuê hiện tại
                            </h3>
                            <div x-show="currentTenant()" class="border border-[#a3c2d1] rounded-lg p-4 bg-[#f4f9fd] flex items-center justify-between relative overflow-hidden">
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#006699]"></div>
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded bg-[#d3e5f2] text-[#006699] flex items-center justify-center font-bold text-sm mr-3">
                                        NA
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900 mb-0.5" x-text="currentTenant() ? currentTenant().customer.name : ''"></p>
                                        <p class="text-xs text-gray-500 flex items-center">
                                            <i class="fa-solid fa-phone mr-1"></i> <span x-text="currentTenant() ? currentTenant().customer.phone : ''"></span>
                                            <span class="mx-2">|</span>
                                            <i class="fa-regular fa-calendar mr-1"></i> Hết hạn: <span x-text="currentTenant() ? formatDate(currentTenant().end_date) : ''"></span>
                                        </p>
                                    </div>
                                </div>
                                <span class="px-2 py-1 text-[10px] font-bold text-[#006699] bg-[#e1edf6] rounded border border-[#b8d4e8]" x-text="currentTenant() ? currentTenant().reference_code : ''"></span>
                            </div>
                            <div x-show="!currentTenant()" class="border border-gray-200 border-dashed rounded-lg p-6 text-center text-gray-500 text-sm">
                                Kiosk hiện tại đang trống.
                            </div>
                        </div>

                        <!-- History -->
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center">
                                <i class="fa-solid fa-clock-rotate-left text-gray-400 mr-2"></i> Lịch sử thuê
                            </h3>
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="px-3 py-2 font-bold text-gray-600">Khách hàng</th>
                                            <th class="px-3 py-2 font-bold text-gray-600">Thời gian</th>
                                            <th class="px-3 py-2 font-bold text-gray-600">Tình trạng HĐ</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <template x-for="history in historyTenants()" :key="history.id">
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-3 py-2 font-medium text-gray-800" x-text="history.customer.name"></td>
                                                <td class="px-3 py-2 text-gray-500" x-text="formatDate(history.start_date) + ' - ' + formatDate(history.end_date)"></td>
                                                <td class="px-3 py-2 text-gray-400" x-text="history.status == 'completed' ? 'Đã thanh lý' : (history.status == 'cancelled' ? 'Đã hủy' : 'Khác')"></td>
                                            </tr>
                                        </template>
                                        <tr x-show="historyTenants().length === 0">
                                            <td colspan="3" class="px-3 py-4 text-center text-gray-400">Không có lịch sử thuê.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </template>
            </div>

            <!-- Drawer Footer -->
            <div class="px-6 py-4 border-t border-gray-200 bg-white flex justify-end space-x-3 flex-shrink-0">
                <button @click="drawerOpen = false" class="px-4 py-2 border border-gray-300 text-gray-700 rounded font-medium text-sm hover:bg-gray-50">
                    Đóng
                </button>
                <button class="px-4 py-2 border border-gray-300 text-gray-700 rounded font-medium text-sm hover:bg-gray-50 flex items-center">
                    <i class="fa-solid fa-print mr-2"></i> In hồ sơ
                </button>
                @can('is-employee')
                <button class="px-4 py-2 bg-[#006699] text-white rounded font-bold text-sm hover:bg-[#005580]">
                    Cập nhật thông tin
                </button>
                @endcan
            </div>
         </div>
    </div>
</div>

<script>
    function kioskManager() {
        return {
            openModal: {{ $errors->any() ? 'true' : 'false' }},
            drawerOpen: false,
            loading: false,
            activeKiosk: null,

            openDrawer(id) {
                this.drawerOpen = true;
                this.loading = true;
                this.activeKiosk = null;

                fetch(`/admin/kiosks/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        this.activeKiosk = data;
                        this.loading = false;
                    })
                    .catch(error => {
                        console.error('Error fetching kiosk details:', error);
                        this.loading = false;
                    });
            },

            currentTenant() {
                if (!this.activeKiosk || !this.activeKiosk.contracts) return null;
                return this.activeKiosk.contracts.find(c => c.status === 'active');
            },

            historyTenants() {
                if (!this.activeKiosk || !this.activeKiosk.contracts) return [];
                return this.activeKiosk.contracts.filter(c => c.status !== 'active');
            },

            formatDate(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
            }
        }
    }
</script>
@endsection
