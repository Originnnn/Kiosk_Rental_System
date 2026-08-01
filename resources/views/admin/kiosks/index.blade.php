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
        
        @can('create', App\Models\Kiosk::class)
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

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Tầng / Vị trí chi tiết</label>
                            <select name="floor" class="w-full px-3 py-2 border {{ $errors->has('floor') ? 'border-red-500' : 'border-gray-300' }} rounded focus:outline-none focus:border-primary text-sm bg-white">
                                <option value="">-- Chọn vị trí / tầng --</option>
                                <option value="Tầng 1 (Ground)" {{ old('floor') == 'Tầng 1 (Ground)' ? 'selected' : '' }}>Tầng 1 (Ground)</option>
                                <option value="Tầng 2" {{ old('floor') == 'Tầng 2' ? 'selected' : '' }}>Tầng 2</option>
                                <option value="Sảnh chính" {{ old('floor') == 'Sảnh chính' ? 'selected' : '' }}>Sảnh chính</option>
                                <option value="Khu vực ngoài trời" {{ old('floor') == 'Khu vực ngoài trời' ? 'selected' : '' }}>Khu vực ngoài trời</option>
                            </select>
                            @error('floor') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Loại hình kinh doanh</label>
                            <select name="kiosk_type" class="w-full px-3 py-2 border {{ $errors->has('kiosk_type') ? 'border-red-500' : 'border-gray-300' }} rounded focus:outline-none focus:border-primary text-sm bg-white">
                                <option value="">-- Chọn loại hình --</option>
                                <option value="Bán lẻ / F&B" {{ old('kiosk_type') == 'Bán lẻ / F&B' ? 'selected' : '' }}>Bán lẻ / F&B</option>
                                <option value="Dịch vụ" {{ old('kiosk_type') == 'Dịch vụ' ? 'selected' : '' }}>Dịch vụ</option>
                                <option value="Thời trang" {{ old('kiosk_type') == 'Thời trang' ? 'selected' : '' }}>Thời trang</option>
                            </select>
                            @error('kiosk_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Kỳ hạn thuê tối thiểu</label>
                        <select name="min_term" class="w-full px-3 py-2 border {{ $errors->has('min_term') ? 'border-red-500' : 'border-gray-300' }} rounded focus:outline-none focus:border-primary text-sm bg-white">
                            <option value="">-- Chọn kỳ hạn --</option>
                            <option value="Min 6 months" {{ old('min_term') == 'Min 6 months' ? 'selected' : '' }}>Min 6 months</option>
                            <option value="Min 12 months" {{ old('min_term') == 'Min 12 months' ? 'selected' : '' }}>Min 12 months</option>
                            <option value="Min 24 months" {{ old('min_term') == 'Min 24 months' ? 'selected' : '' }}>Min 24 months</option>
                        </select>
                        @error('min_term') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-gray-700 mb-2">Tiện ích đi kèm (Features)</label>
                        <div class="grid grid-cols-2 gap-2 text-sm text-gray-700">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="features[]" value="Điện 3-pha" class="rounded border-gray-300 text-[#006699] focus:ring-[#006699]" {{ is_array(old('features')) && in_array('Điện 3-pha', old('features')) ? 'checked' : '' }}>
                                <span>Điện 3-pha</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="features[]" value="Nước sạch" class="rounded border-gray-300 text-[#006699] focus:ring-[#006699]" {{ is_array(old('features')) && in_array('Nước sạch', old('features')) ? 'checked' : '' }}>
                                <span>Nước sạch</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="features[]" value="Điều hòa TT" class="rounded border-gray-300 text-[#006699] focus:ring-[#006699]" {{ is_array(old('features')) && in_array('Điều hòa TT', old('features')) ? 'checked' : '' }}>
                                <span>Điều hòa TT</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="features[]" value="Camera Hành lang" class="rounded border-gray-300 text-[#006699] focus:ring-[#006699]" {{ is_array(old('features')) && in_array('Camera Hành lang', old('features')) ? 'checked' : '' }}>
                                <span>Camera Hành lang</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="features[]" value="PCCC" class="rounded border-gray-300 text-[#006699] focus:ring-[#006699]" {{ is_array(old('features')) && in_array('PCCC', old('features')) ? 'checked' : '' }}>
                                <span>PCCC</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="features[]" value="Internet Cáp quang" class="rounded border-gray-300 text-[#006699] focus:ring-[#006699]" {{ is_array(old('features')) && in_array('Internet Cáp quang', old('features')) ? 'checked' : '' }}>
                                <span>Internet Cáp quang</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-gray-700 mb-2">Thông số kỹ thuật</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <select name="power_supply" class="w-full px-3 py-2 border {{ $errors->has('power_supply') ? 'border-red-500' : 'border-gray-300' }} rounded text-sm bg-white">
                                    <option value="">-- Nguồn điện --</option>
                                    <option value="Không có" {{ old('power_supply') == 'Không có' ? 'selected' : '' }}>Không có</option>
                                    <option value="1 Pha (Sinh hoạt)" {{ old('power_supply') == '1 Pha (Sinh hoạt)' ? 'selected' : '' }}>1 Pha (Sinh hoạt)</option>
                                    <option value="3 Pha (Công nghiệp)" {{ old('power_supply') == '3 Pha (Công nghiệp)' ? 'selected' : '' }}>3 Pha (Công nghiệp)</option>
                                </select>
                            </div>
                            <div>
                                <select name="water_supply" class="w-full px-3 py-2 border {{ $errors->has('water_supply') ? 'border-red-500' : 'border-gray-300' }} rounded text-sm bg-white">
                                    <option value="">-- Cấp nước --</option>
                                    <option value="Không có" {{ old('water_supply') == 'Không có' ? 'selected' : '' }}>Không có</option>
                                    <option value="Có đường nước chờ" {{ old('water_supply') == 'Có đường nước chờ' ? 'selected' : '' }}>Có đường nước chờ</option>
                                    <option value="Đầy đủ cấp thoát" {{ old('water_supply') == 'Đầy đủ cấp thoát' ? 'selected' : '' }}>Đầy đủ cấp thoát</option>
                                </select>
                            </div>
                            <div>
                                <select name="internet_connection" class="w-full px-3 py-2 border {{ $errors->has('internet_connection') ? 'border-red-500' : 'border-gray-300' }} rounded text-sm bg-white">
                                    <option value="">-- Internet --</option>
                                    <option value="Không có" {{ old('internet_connection') == 'Không có' ? 'selected' : '' }}>Không có</option>
                                    <option value="Wifi chung" {{ old('internet_connection') == 'Wifi chung' ? 'selected' : '' }}>Wifi chung</option>
                                    <option value="Cáp quang riêng" {{ old('internet_connection') == 'Cáp quang riêng' ? 'selected' : '' }}>Cáp quang riêng</option>
                                </select>
                            </div>
                            <div>
                                <select name="air_conditioning" class="w-full px-3 py-2 border {{ $errors->has('air_conditioning') ? 'border-red-500' : 'border-gray-300' }} rounded text-sm bg-white">
                                    <option value="">-- Điều hòa --</option>
                                    <option value="Không có" {{ old('air_conditioning') == 'Không có' ? 'selected' : '' }}>Không có</option>
                                    <option value="Điều hòa trung tâm" {{ old('air_conditioning') == 'Điều hòa trung tâm' ? 'selected' : '' }}>Điều hòa trung tâm</option>
                                    <option value="Cho phép lắp đặt" {{ old('air_conditioning') == 'Cho phép lắp đặt' ? 'selected' : '' }}>Cho phép lắp đặt</option>
                                </select>
                            </div>
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
                        <!-- VIEW MODE -->
                        <div x-show="!isEditing">
                            <!-- Info Grid -->
                            <div class="flex space-x-4 mb-6">
                            <div class="w-1/2 rounded-lg overflow-hidden relative bg-gray-100 border border-gray-200 flex items-center justify-center">
                                <template x-if="activeKiosk && activeKiosk.images && activeKiosk.images.length > 0">
                                    <img :src="'{{ asset('') }}' + activeKiosk.images[0].file_path" alt="Kiosk" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!activeKiosk || !activeKiosk.images || activeKiosk.images.length === 0">
                                    <img src="https://images.unsplash.com/photo-1555529733-0e67056058e1?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Kiosk Placeholder" class="w-full h-full object-cover opacity-50 grayscale">
                                </template>
                                <div class="absolute bottom-2 right-2 bg-white p-1.5 rounded shadow">
                                    <i class="fa-solid fa-camera text-gray-500 text-xs"></i>
                                </div>
                            </div>
                            
                            <div class="w-1/2 grid grid-cols-2 gap-3">
                                <div class="bg-gray-50 border border-gray-200 rounded p-3">
                                    <p class="text-[10px] font-bold text-gray-500 uppercase mb-1">DIỆN TÍCH</p>
                                    <p class="text-sm font-bold text-gray-900" x-text="activeKiosk.area + ' m²'"></p>
                                </div>
                                <div class="bg-gray-50 border border-gray-200 rounded p-3">
                                    <p class="text-[10px] font-bold text-gray-500 uppercase mb-1">GIÁ CƠ BẢN</p>
                                    <p class="text-sm font-bold text-[#006699]" x-text="new Intl.NumberFormat('vi-VN').format(activeKiosk.price) + ' đ/tháng'"></p>
                                </div>
                                <div class="bg-gray-50 border border-gray-200 rounded p-3">
                                    <p class="text-[10px] font-bold text-gray-500 uppercase mb-1">VỊ TRÍ / TẦNG</p>
                                    <p class="text-sm font-bold text-gray-900" x-text="activeKiosk.floor || 'Đang cập nhật'"></p>
                                </div>
                                <div class="bg-gray-50 border border-gray-200 rounded p-3">
                                    <p class="text-[10px] font-bold text-gray-500 uppercase mb-1">LOẠI HÌNH</p>
                                    <p class="text-sm font-bold text-gray-900" x-text="activeKiosk.kiosk_type || 'Đang cập nhật'"></p>
                                </div>
                                <div class="bg-gray-50 border border-gray-200 rounded p-3 col-span-2">
                                    <p class="text-[10px] font-bold text-gray-500 uppercase mb-1">KỲ HẠN TỐI THIỂU</p>
                                    <p class="text-sm font-bold text-gray-900" x-text="activeKiosk.min_term || 'Đang cập nhật'"></p>
                                </div>
                                <div class="bg-gray-50 border border-gray-200 rounded p-3 col-span-2">
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
                            <div class="p-4 grid grid-cols-2 gap-y-3 gap-x-6 text-sm border-b border-gray-100">
                                <div class="flex justify-between border-b border-gray-100 pb-1 border-dashed">
                                    <span class="text-gray-500">Nguồn điện:</span>
                                    <span class="font-semibold text-gray-900" x-text="activeKiosk ? (activeKiosk.power_supply || 'Không có') : 'Không có'"></span>
                                </div>
                                <div class="flex justify-between border-b border-gray-100 pb-1 border-dashed">
                                    <span class="text-gray-500">Cấp nước:</span>
                                    <span class="font-semibold text-gray-900" x-text="activeKiosk ? (activeKiosk.water_supply || 'Không có') : 'Không có'"></span>
                                </div>
                                <div class="flex justify-between border-b border-gray-100 pb-1 border-dashed">
                                    <span class="text-gray-500">Internet:</span>
                                    <span class="font-semibold text-gray-900" x-text="activeKiosk ? (activeKiosk.internet_connection || 'Không có') : 'Không có'"></span>
                                </div>
                                <div class="flex justify-between border-b border-gray-100 pb-1 border-dashed">
                                    <span class="text-gray-500">Điều hòa:</span>
                                    <span class="font-semibold text-gray-900" x-text="activeKiosk ? (activeKiosk.air_conditioning || 'Không có') : 'Không có'"></span>
                                </div>
                            </div>
                            <div class="p-4 bg-gray-50">
                                <p class="text-xs font-bold text-gray-500 uppercase mb-2">Tiện ích đi kèm</p>
                                <div class="flex flex-wrap gap-2">
                                    <template x-if="activeKiosk && activeKiosk.features && activeKiosk.features.length > 0">
                                        <template x-for="feature in activeKiosk.features">
                                            <span class="px-2 py-1 bg-white border border-gray-200 text-gray-700 text-xs font-medium rounded shadow-sm flex items-center gap-1.5" x-text="feature"></span>
                                        </template>
                                    </template>
                                    <template x-if="!activeKiosk || !activeKiosk.features || activeKiosk.features.length === 0">
                                        <span class="text-xs text-gray-400 italic">Chưa có thông tin tiện ích</span>
                                    </template>
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
                        
                        <!-- EDIT MODE -->
                        <div x-show="isEditing" style="display: none;" class="bg-gray-50 p-5 rounded-lg border border-gray-200 shadow-sm mb-6">
                            <h3 class="text-sm font-bold text-[#006699] mb-4 border-b border-gray-200 pb-2">
                                <i class="fa-solid fa-pen-to-square mr-2"></i> Chỉnh sửa thông tin Kiosk
                            </h3>
                            
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Mã quầy <span class="text-red-500">*</span></label>
                                    <input type="text" x-model="editData.code" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-primary text-sm" :class="editErrors.code ? 'border-red-500' : 'border-gray-300'">
                                    <template x-if="editErrors.code"><p class="text-red-500 text-xs mt-1" x-text="editErrors.code[0]"></p></template>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Vị trí <span class="text-red-500">*</span></label>
                                    <input type="text" x-model="editData.name" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-primary text-sm" :class="editErrors.name ? 'border-red-500' : 'border-gray-300'">
                                    <template x-if="editErrors.name"><p class="text-red-500 text-xs mt-1" x-text="editErrors.name[0]"></p></template>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Diện tích (m2) <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input type="number" step="0.01" x-model="editData.area" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-primary text-sm text-right pr-8" :class="editErrors.area ? 'border-red-500' : 'border-gray-300'">
                                        <span class="absolute right-3 top-2 text-sm text-gray-500 font-medium">m²</span>
                                    </div>
                                    <template x-if="editErrors.area"><p class="text-red-500 text-xs mt-1" x-text="editErrors.area[0]"></p></template>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Đơn giá mặc định <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input type="number" x-model="editData.price" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-primary text-sm text-right pr-12" :class="editErrors.price ? 'border-red-500' : 'border-gray-300'">
                                        <span class="absolute right-3 top-2 text-sm text-gray-500 font-medium">VNĐ</span>
                                    </div>
                                    <template x-if="editErrors.price"><p class="text-red-500 text-xs mt-1" x-text="editErrors.price[0]"></p></template>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tầng / Vị trí chi tiết</label>
                                    <select x-model="editData.floor" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-primary text-sm bg-white" :class="editErrors.floor ? 'border-red-500' : 'border-gray-300'">
                                        <option value="">-- Chọn vị trí / tầng --</option>
                                        <option value="Tầng 1 (Ground)">Tầng 1 (Ground)</option>
                                        <option value="Tầng 2">Tầng 2</option>
                                        <option value="Sảnh chính">Sảnh chính</option>
                                        <option value="Khu vực ngoài trời">Khu vực ngoài trời</option>
                                    </select>
                                    <template x-if="editErrors.floor"><p class="text-red-500 text-xs mt-1" x-text="editErrors.floor[0]"></p></template>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Loại hình kinh doanh</label>
                                    <select x-model="editData.kiosk_type" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-primary text-sm bg-white" :class="editErrors.kiosk_type ? 'border-red-500' : 'border-gray-300'">
                                        <option value="">-- Chọn loại hình --</option>
                                        <option value="Bán lẻ / F&B">Bán lẻ / F&B</option>
                                        <option value="Dịch vụ">Dịch vụ</option>
                                        <option value="Thời trang">Thời trang</option>
                                    </select>
                                    <template x-if="editErrors.kiosk_type"><p class="text-red-500 text-xs mt-1" x-text="editErrors.kiosk_type[0]"></p></template>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Kỳ hạn thuê tối thiểu</label>
                                <select x-model="editData.min_term" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-primary text-sm bg-white" :class="editErrors.min_term ? 'border-red-500' : 'border-gray-300'">
                                    <option value="">-- Chọn kỳ hạn --</option>
                                    <option value="Min 6 months">Min 6 months</option>
                                    <option value="Min 12 months">Min 12 months</option>
                                    <option value="Min 24 months">Min 24 months</option>
                                </select>
                                <template x-if="editErrors.min_term"><p class="text-red-500 text-xs mt-1" x-text="editErrors.min_term[0]"></p></template>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-xs font-semibold text-gray-700 mb-2">Tiện ích đi kèm (Features)</label>
                                <div class="grid grid-cols-2 gap-2 text-sm text-gray-700">
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" value="Điện 3-pha" x-model="editData.features" class="rounded border-gray-300 text-[#006699] focus:ring-[#006699]">
                                        <span>Điện 3-pha</span>
                                    </label>
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" value="Nước sạch" x-model="editData.features" class="rounded border-gray-300 text-[#006699] focus:ring-[#006699]">
                                        <span>Nước sạch</span>
                                    </label>
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" value="Điều hòa TT" x-model="editData.features" class="rounded border-gray-300 text-[#006699] focus:ring-[#006699]">
                                        <span>Điều hòa TT</span>
                                    </label>
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" value="Camera Hành lang" x-model="editData.features" class="rounded border-gray-300 text-[#006699] focus:ring-[#006699]">
                                        <span>Camera Hành lang</span>
                                    </label>
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" value="PCCC" x-model="editData.features" class="rounded border-gray-300 text-[#006699] focus:ring-[#006699]">
                                        <span>PCCC</span>
                                    </label>
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" value="Internet Cáp quang" x-model="editData.features" class="rounded border-gray-300 text-[#006699] focus:ring-[#006699]">
                                        <span>Internet Cáp quang</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray-800 mb-2 border-b pb-1"><i class="fa-solid fa-wrench mr-1 text-gray-400"></i> Thông số kỹ thuật</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">Nguồn điện</label>
                                        <select x-model="editData.power_supply" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-primary text-sm" :class="editErrors.power_supply ? 'border-red-500' : 'border-gray-300'">
                                            <option value="">Không có</option>
                                            <option value="220V - 15A">220V - 15A</option>
                                            <option value="220V - 30A">220V - 30A</option>
                                            <option value="380V">380V</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">Cấp nước</label>
                                        <select x-model="editData.water_supply" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-primary text-sm" :class="editErrors.water_supply ? 'border-red-500' : 'border-gray-300'">
                                            <option value="">Không có</option>
                                            <option value="Có (D21)">Có (D21)</option>
                                            <option value="Có (D27)">Có (D27)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">Internet</label>
                                        <select x-model="editData.internet_connection" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-primary text-sm" :class="editErrors.internet_connection ? 'border-red-500' : 'border-gray-300'">
                                            <option value="">Không có</option>
                                            <option value="Cáp quang VNPT">Cáp quang VNPT</option>
                                            <option value="Cáp quang Viettel">Cáp quang Viettel</option>
                                            <option value="Cáp quang FPT">Cáp quang FPT</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">Điều hòa</label>
                                        <select x-model="editData.air_conditioning" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-primary text-sm" :class="editErrors.air_conditioning ? 'border-red-500' : 'border-gray-300'">
                                            <option value="">Không có</option>
                                            <option value="Âm trần 18000 BTU">Âm trần 18000 BTU</option>
                                            <option value="Treo tường 9000 BTU">Treo tường 9000 BTU</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Mô tả thiết bị đi kèm</label>
                                <textarea x-model="editData.description" rows="3" class="w-full px-3 py-2 border rounded focus:outline-none focus:border-primary text-sm resize-none" :class="editErrors.description ? 'border-red-500' : 'border-gray-300'"></textarea>
                                <template x-if="editErrors.description"><p class="text-red-500 text-xs mt-1" x-text="editErrors.description[0]"></p></template>
                            </div>

                            <div class="mt-4">
                                <label class="block text-xs font-bold text-gray-800 mb-2 border-b pb-1"><i class="fa-regular fa-images mr-1 text-gray-400"></i> Hình ảnh Kiosk</label>
                                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                                    <!-- Slot 1 -->
                                    <div>
                                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">Mặt tiền *</label>
                                        <template x-if="getExistingImage(1) && !editFiles.image_front">
                                            <div class="relative w-full h-24 rounded border border-gray-200 overflow-hidden group">
                                                <img :src="'{{ asset('') }}' + getExistingImage(1).file_path" class="w-full h-full object-cover">
                                                <div class="absolute inset-0 bg-black/40 hidden group-hover:flex items-center justify-center">
                                                    <button type="button" @click="$refs.image_front.click()" class="bg-white text-gray-700 text-xs px-2 py-1 rounded shadow-sm hover:bg-gray-50">Thay đổi</button>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="editFiles.image_front">
                                            <div class="relative w-full h-24 rounded border border-[#006699] overflow-hidden group">
                                                <img :src="getFilePreview('image_front')" class="w-full h-full object-cover">
                                                <div class="absolute inset-0 bg-black/40 hidden group-hover:flex items-center justify-center">
                                                    <button type="button" @click="clearFile('image_front')" class="bg-white text-red-500 text-xs px-2 py-1 rounded shadow-sm hover:bg-gray-50"><i class="fa-solid fa-trash"></i></button>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="!getExistingImage(1) && !editFiles.image_front">
                                            <div class="border border-dashed border-gray-300 rounded h-24 flex flex-col items-center justify-center bg-white cursor-pointer hover:bg-gray-50 transition" @click="$refs.image_front.click()">
                                                <i class="fa-solid fa-cloud-arrow-up text-gray-400 mb-1 text-lg"></i>
                                                <span class="text-[10px] text-gray-500 font-medium">Tải lên</span>
                                            </div>
                                        </template>
                                        <input type="file" x-ref="image_front" class="hidden" accept="image/jpeg,image/png,image/jpg,image/webp" @change="handleFileChange('image_front', $event)">
                                        <template x-if="editErrors.image_front"><p class="text-red-500 text-[10px] mt-1" x-text="editErrors.image_front[0]"></p></template>
                                    </div>
                                    
                                    <!-- Slot 2 -->
                                    <div>
                                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">Góc nghiêng</label>
                                        <template x-if="getExistingImage(2) && !editFiles.image_angle">
                                            <div class="relative w-full h-24 rounded border border-gray-200 overflow-hidden group">
                                                <img :src="'{{ asset('') }}' + getExistingImage(2).file_path" class="w-full h-full object-cover">
                                                <div class="absolute inset-0 bg-black/40 hidden group-hover:flex items-center justify-center">
                                                    <button type="button" @click="$refs.image_angle.click()" class="bg-white text-gray-700 text-xs px-2 py-1 rounded shadow-sm hover:bg-gray-50">Thay đổi</button>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="editFiles.image_angle">
                                            <div class="relative w-full h-24 rounded border border-[#006699] overflow-hidden group">
                                                <img :src="getFilePreview('image_angle')" class="w-full h-full object-cover">
                                                <div class="absolute inset-0 bg-black/40 hidden group-hover:flex items-center justify-center">
                                                    <button type="button" @click="clearFile('image_angle')" class="bg-white text-red-500 text-xs px-2 py-1 rounded shadow-sm hover:bg-gray-50"><i class="fa-solid fa-trash"></i></button>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="!getExistingImage(2) && !editFiles.image_angle">
                                            <div class="border border-dashed border-gray-300 rounded h-24 flex flex-col items-center justify-center bg-white cursor-pointer hover:bg-gray-50 transition" @click="$refs.image_angle.click()">
                                                <i class="fa-solid fa-cloud-arrow-up text-gray-400 mb-1 text-lg"></i>
                                                <span class="text-[10px] text-gray-500 font-medium">Tải lên</span>
                                            </div>
                                        </template>
                                        <input type="file" x-ref="image_angle" class="hidden" accept="image/jpeg,image/png,image/jpg,image/webp" @change="handleFileChange('image_angle', $event)">
                                        <template x-if="editErrors.image_angle"><p class="text-red-500 text-[10px] mt-1" x-text="editErrors.image_angle[0]"></p></template>
                                    </div>

                                    <!-- Slot 3 -->
                                    <div>
                                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">Cận cảnh</label>
                                        <template x-if="getExistingImage(3) && !editFiles.image_closeup">
                                            <div class="relative w-full h-24 rounded border border-gray-200 overflow-hidden group">
                                                <img :src="'{{ asset('') }}' + getExistingImage(3).file_path" class="w-full h-full object-cover">
                                                <div class="absolute inset-0 bg-black/40 hidden group-hover:flex items-center justify-center">
                                                    <button type="button" @click="$refs.image_closeup.click()" class="bg-white text-gray-700 text-xs px-2 py-1 rounded shadow-sm hover:bg-gray-50">Thay đổi</button>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="editFiles.image_closeup">
                                            <div class="relative w-full h-24 rounded border border-[#006699] overflow-hidden group">
                                                <img :src="getFilePreview('image_closeup')" class="w-full h-full object-cover">
                                                <div class="absolute inset-0 bg-black/40 hidden group-hover:flex items-center justify-center">
                                                    <button type="button" @click="clearFile('image_closeup')" class="bg-white text-red-500 text-xs px-2 py-1 rounded shadow-sm hover:bg-gray-50"><i class="fa-solid fa-trash"></i></button>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="!getExistingImage(3) && !editFiles.image_closeup">
                                            <div class="border border-dashed border-gray-300 rounded h-24 flex flex-col items-center justify-center bg-white cursor-pointer hover:bg-gray-50 transition" @click="$refs.image_closeup.click()">
                                                <i class="fa-solid fa-cloud-arrow-up text-gray-400 mb-1 text-lg"></i>
                                                <span class="text-[10px] text-gray-500 font-medium">Tải lên</span>
                                            </div>
                                        </template>
                                        <input type="file" x-ref="image_closeup" class="hidden" accept="image/jpeg,image/png,image/jpg,image/webp" @change="handleFileChange('image_closeup', $event)">
                                        <template x-if="editErrors.image_closeup"><p class="text-red-500 text-[10px] mt-1" x-text="editErrors.image_closeup[0]"></p></template>
                                    </div>

                                    <!-- Slot 4 -->
                                    <div>
                                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">Mặt sau</label>
                                        <template x-if="getExistingImage(4) && !editFiles.image_back">
                                            <div class="relative w-full h-24 rounded border border-gray-200 overflow-hidden group">
                                                <img :src="'{{ asset('') }}' + getExistingImage(4).file_path" class="w-full h-full object-cover">
                                                <div class="absolute inset-0 bg-black/40 hidden group-hover:flex items-center justify-center">
                                                    <button type="button" @click="$refs.image_back.click()" class="bg-white text-gray-700 text-xs px-2 py-1 rounded shadow-sm hover:bg-gray-50">Thay đổi</button>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="editFiles.image_back">
                                            <div class="relative w-full h-24 rounded border border-[#006699] overflow-hidden group">
                                                <img :src="getFilePreview('image_back')" class="w-full h-full object-cover">
                                                <div class="absolute inset-0 bg-black/40 hidden group-hover:flex items-center justify-center">
                                                    <button type="button" @click="clearFile('image_back')" class="bg-white text-red-500 text-xs px-2 py-1 rounded shadow-sm hover:bg-gray-50"><i class="fa-solid fa-trash"></i></button>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="!getExistingImage(4) && !editFiles.image_back">
                                            <div class="border border-dashed border-gray-300 rounded h-24 flex flex-col items-center justify-center bg-white cursor-pointer hover:bg-gray-50 transition" @click="$refs.image_back.click()">
                                                <i class="fa-solid fa-cloud-arrow-up text-gray-400 mb-1 text-lg"></i>
                                                <span class="text-[10px] text-gray-500 font-medium">Tải lên</span>
                                            </div>
                                        </template>
                                        <input type="file" x-ref="image_back" class="hidden" accept="image/jpeg,image/png,image/jpg,image/webp" @change="handleFileChange('image_back', $event)">
                                        <template x-if="editErrors.image_back"><p class="text-red-500 text-[10px] mt-1" x-text="editErrors.image_back[0]"></p></template>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </template>
            </div>

            <!-- Drawer Footer -->
            <div class="px-6 py-4 border-t border-gray-200 bg-white flex justify-end space-x-3 flex-shrink-0">
                <div x-show="!isEditing" class="flex space-x-3 w-full justify-end">
                    <button @click="drawerOpen = false" class="px-4 py-2 border border-gray-300 text-gray-700 rounded font-medium text-sm hover:bg-gray-50">
                        Đóng
                    </button>
                    <button class="px-4 py-2 border border-gray-300 text-gray-700 rounded font-medium text-sm hover:bg-gray-50 flex items-center">
                        <i class="fa-solid fa-print mr-2"></i> In hồ sơ
                    </button>
                    @can('create', App\Models\Kiosk::class)
                    <button @click="startEdit()" class="px-4 py-2 bg-[#006699] text-white rounded font-bold text-sm hover:bg-[#005580]">
                        Cập nhật thông tin
                    </button>
                    @endcan
                </div>
                
                <div x-show="isEditing" style="display: none;" class="flex space-x-3 w-full justify-end items-center">
                    <span x-show="generalError" class="text-red-500 text-sm font-semibold mr-auto" x-text="generalError" style="display: none;"></span>
                    <button @click="isEditing = false" class="px-4 py-2 border border-gray-300 text-gray-700 rounded font-medium text-sm hover:bg-gray-50">
                        Hủy
                    </button>
                    <button @click="saveEdit()" class="px-4 py-2 bg-[#006699] text-white rounded font-bold text-sm hover:bg-[#005580] flex items-center disabled:opacity-70" :disabled="saving">
                        <i class="fa-solid fa-circle-notch fa-spin mr-2" x-show="saving" style="display: none;"></i>
                        Lưu thay đổi
                    </button>
                </div>
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
                this.isEditing = false;
                this.activeKiosk = null;

                fetch(`/kiosks/${id}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        this.activeKiosk = data;
                        this.loading = false;
                    })
                    .catch(error => {
                        console.error('Error fetching kiosk details:', error);
                        this.loading = false;
                    });
            },

            isEditing: false,
            saving: false,
            generalError: null,
            editData: { code: '', name: '', area: '', price: '', description: '', power_supply: '', water_supply: '', internet_connection: '', air_conditioning: '', floor: '', kiosk_type: '', min_term: '', features: [] },
            editFiles: { image_front: null, image_angle: null, image_closeup: null, image_back: null },
            filePreviews: { image_front: null, image_angle: null, image_closeup: null, image_back: null },
            editErrors: {},

            getExistingImage(order) {
                if (!this.activeKiosk || !this.activeKiosk.images) return null;
                return this.activeKiosk.images.find(img => img.sort_order == order);
            },
            
            handleFileChange(key, event) {
                const file = event.target.files[0];
                if (file) {
                    this.editFiles[key] = file;
                    this.filePreviews[key] = URL.createObjectURL(file);
                } else {
                    this.clearFile(key);
                }
            },
            
            clearFile(key) {
                this.editFiles[key] = null;
                this.filePreviews[key] = null;
                if (this.$refs[key]) this.$refs[key].value = '';
            },

            getFilePreview(key) {
                return this.filePreviews[key];
            },

            startEdit() {
                this.isEditing = true;
                this.editErrors = {};
                this.editFiles = { image_front: null, image_angle: null, image_closeup: null, image_back: null };
                this.filePreviews = { image_front: null, image_angle: null, image_closeup: null, image_back: null };
                
                // Clear existing file inputs if any
                ['image_front', 'image_angle', 'image_closeup', 'image_back'].forEach(key => {
                    if (this.$refs[key]) this.$refs[key].value = '';
                });

                // Copy data safely and ensure features is an array
                let activeFeatures = [];
                if (this.activeKiosk.features) {
                    try {
                        activeFeatures = Array.isArray(this.activeKiosk.features) 
                            ? [...this.activeKiosk.features] 
                            : JSON.parse(this.activeKiosk.features);
                    } catch (e) {
                        activeFeatures = [];
                    }
                }

                this.editData = {
                    code: this.activeKiosk.code || '',
                    name: this.activeKiosk.name || '',
                    area: this.activeKiosk.area || '',
                    price: this.activeKiosk.price || '',
                    description: this.activeKiosk.description || '',
                    power_supply: this.activeKiosk.power_supply || '',
                    water_supply: this.activeKiosk.water_supply || '',
                    internet_connection: this.activeKiosk.internet_connection || '',
                    air_conditioning: this.activeKiosk.air_conditioning || '',
                    floor: this.activeKiosk.floor || '',
                    kiosk_type: this.activeKiosk.kiosk_type || '',
                    min_term: this.activeKiosk.min_term || '',
                    features: activeFeatures
                };
            },

            saveEdit() {
                this.saving = true;
                this.editErrors = {};
                this.generalError = null;
                
                // Client-side file size validation (max 2MB)
                let hasLargeFile = false;
                const checkFileSize = (ref, key) => {
                    if (ref && ref.files.length > 0) {
                        if (ref.files[0].size > 5 * 1024 * 1024) {
                            this.editErrors[key] = ['Kích thước ảnh không được vượt quá 5MB.'];
                            hasLargeFile = true;
                        }
                    }
                };
                checkFileSize(this.$refs.image_front, 'image_front');
                checkFileSize(this.$refs.image_angle, 'image_angle');
                checkFileSize(this.$refs.image_closeup, 'image_closeup');
                checkFileSize(this.$refs.image_back, 'image_back');

                if (hasLargeFile) {
                    this.saving = false;
                    this.generalError = "Vui lòng chọn ảnh có kích thước nhỏ hơn 5MB.";
                    return;
                }

                let formData = new FormData();
                formData.append('_method', 'PUT');
                
                const ignoreFields = ['contracts', 'images', 'created_at', 'updated_at', 'position'];
                for (let key in this.editData) {
                    if (ignoreFields.includes(key)) continue;

                    let val = this.editData[key];
                    if (Array.isArray(val)) {
                        if (val.length === 0) {
                            formData.append(key, '');
                        } else {
                            val.forEach(item => {
                                formData.append(key + '[]', item);
                            });
                        }
                    } else {
                        formData.append(key, val !== null ? val : '');
                    }
                }
                
                if (this.$refs.image_front && this.$refs.image_front.files.length > 0) formData.append('image_front', this.$refs.image_front.files[0]);
                if (this.$refs.image_angle && this.$refs.image_angle.files.length > 0) formData.append('image_angle', this.$refs.image_angle.files[0]);
                if (this.$refs.image_closeup && this.$refs.image_closeup.files.length > 0) formData.append('image_closeup', this.$refs.image_closeup.files[0]);
                if (this.$refs.image_back && this.$refs.image_back.files.length > 0) formData.append('image_back', this.$refs.image_back.files[0]);

                fetch(`/kiosks/${this.activeKiosk.id}`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                })
                .then(async response => {
                    if (!response.ok) {
                        if (response.status === 422) {
                            let data;
                            try {
                                data = await response.json();
                            } catch (e) {
                                throw new Error('422 Response is not JSON: ' + e.message);
                            }
                            this.editErrors = data.errors;
                            throw new Error('Validation failed');
                        }
                        if (response.status === 413) {
                            this.generalError = "Ảnh tải lên có dung lượng quá lớn (vượt quá giới hạn của máy chủ).";
                            throw new Error('Payload Too Large');
                        }
                        const errText = await response.text();
                        this.generalError = `Lỗi hệ thống (${response.status}): ${errText.substring(0, 50)}`;
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    this.activeKiosk = data.kiosk;
                    this.isEditing = false;
                    this.saving = false;
                    window.location.reload(); 
                })
                .catch(error => {
                    console.error('Error updating kiosk:', error);
                    this.saving = false;
                    if (error.message !== 'Validation failed' && !this.generalError) {
                        this.generalError = "Đã xảy ra lỗi khi lưu dữ liệu! Chi tiết: " + error.message;
                    }
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
