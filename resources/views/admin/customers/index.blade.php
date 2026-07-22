@extends('layouts.admin')

@section('title', 'Danh sách khách thuê - Bến Xe Huế')

@section('content')
<div class="bg-gray-50 min-h-screen p-6 font-sans">
    
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Danh sách khách thuê</h1>
            <p class="text-sm text-gray-500">Quản lý thông tin cá nhân và hợp đồng của khách hàng.</p>
        </div>
        
        @can('is-employee')
        <div>
            <button onclick="openModal()" class="bg-[#006699] hover:bg-[#005580] text-white px-4 py-2 rounded font-medium flex items-center text-sm transition-colors shadow-sm">
                <i class="fa-solid fa-plus mr-2"></i> Thêm khách thuê
            </button>
        </div>
        @endcan
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-t border border-gray-200 border-b-0 flex justify-between items-center">
        <form action="{{ route('admin.customers.index') }}" method="GET" class="flex w-1/3">
            <div class="relative w-full">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                </div>
                <input type="text" name="q" value="{{ request('q') }}" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-primary focus:border-primary text-sm bg-gray-50" placeholder="Tìm theo tên, SĐT, CCCD, Mã KH...">
            </div>
        </form>

        <form action="{{ route('admin.customers.index') }}" method="GET" class="flex items-center space-x-3" id="filterForm">
            @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
            <label class="text-xs font-bold text-gray-500 uppercase">Trạng thái:</label>
            <select name="status" onchange="document.getElementById('filterForm').submit()" class="border border-gray-300 rounded px-3 py-2 text-sm focus:ring-1 focus:ring-primary focus:border-primary bg-white min-w-[150px]">
                <option value="Tất cả" {{ request('status') == 'Tất cả' ? 'selected' : '' }}>Tất cả</option>
                <option value="Hoạt động" {{ request('status') == 'Hoạt động' ? 'selected' : '' }}>Hoạt động</option>
                <option value="Chờ duyệt" {{ request('status') == 'Chờ duyệt' ? 'selected' : '' }}>Chờ duyệt</option>
                <option value="Ngừng hoạt động" {{ request('status') == 'Ngừng hoạt động' ? 'selected' : '' }}>Ngừng hoạt động</option>
            </select>
            <button type="button" class="border border-gray-300 rounded px-3 py-2 text-gray-600 hover:bg-gray-50">
                <i class="fa-solid fa-filter"></i>
            </button>
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
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Mã khách hàng</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Tên khách thuê</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Số điện thoại</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Số CCCD</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Số HĐ đang thuê</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Trạng thái</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($customers as $customer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-bold text-[#006699]">
                            {{ $customer->customer_code ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-900">
                            {{ $customer->name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $customer->phone }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $customer->id_card_number ?? $customer->email }} <!-- Tạm hiển thị email nếu ko có cccd cho data cũ -->
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-800 text-xs font-bold">
                                {{ $customer->active_contracts_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($customer->status == 'active')
                                <span class="inline-flex items-center px-2 py-1 text-xs font-bold rounded-full text-green-700 bg-green-100">
                                    <i class="fa-solid fa-circle text-[8px] mr-1"></i> ACTIVE
                                </span>
                            @elseif($customer->status == 'pending')
                                <span class="inline-flex items-center px-2 py-1 text-xs font-bold rounded-full text-orange-700 bg-orange-100">
                                    <i class="fa-solid fa-circle text-[8px] mr-1"></i> PENDING
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 text-xs font-bold rounded-full text-gray-600 bg-gray-200">
                                    <i class="fa-solid fa-circle text-[8px] mr-1"></i> INACTIVE
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false" class="text-gray-400 hover:text-[#006699] p-2 rounded-full hover:bg-gray-100">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-100" 
                                 x-transition:enter-start="transform opacity-0 scale-95" 
                                 x-transition:enter-end="transform opacity-100 scale-100" 
                                 x-transition:leave="transition ease-in duration-75" 
                                 x-transition:leave-start="transform opacity-100 scale-100" 
                                 x-transition:leave-end="transform opacity-0 scale-95" 
                                 class="absolute right-8 top-10 w-48 bg-white rounded-md shadow-lg z-50 border border-gray-200 text-left" style="display: none;">
                                <a href="{{ route('admin.customers.show', $customer->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-[#006699]">
                                    <i class="fa-regular fa-eye mr-2 w-4"></i> Xem chi tiết
                                </a>
                                @can('is-employee')
                                <a href="{{ route('admin.customers.edit', $customer->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-[#006699]">
                                    <i class="fa-regular fa-pen-to-square mr-2 w-4"></i> Chỉnh sửa
                                </a>
                                <form action="{{ route('admin.customers.toggleStatus', $customer->id) }}" method="POST" class="block">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ $customer->status == 'active' ? 'hover:text-red-600' : 'hover:text-green-600' }}">
                                        @if($customer->status == 'active')
                                            <i class="fa-solid fa-lock mr-2 w-4"></i> Khoá tài khoản
                                        @else
                                            <i class="fa-solid fa-lock-open mr-2 w-4"></i> Mở khoá tài khoản
                                        @endif
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            Không tìm thấy khách hàng nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-6 py-4 border border-t-0 border-gray-200 bg-white flex items-center justify-between rounded-b">
        <div class="text-sm text-gray-500">
            Hiển thị {{ $customers->firstItem() ?? 0 }} đến {{ $customers->lastItem() ?? 0 }} trong số {{ $customers->total() }} khách hàng
        </div>
        <div>
            {{ $customers->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    </div>

</div>

<!-- Modal Thêm mới -->
<div id="customerModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 {{ $errors->any() ? '' : 'hidden' }}">
    <div class="bg-white rounded-lg shadow-xl w-[600px] max-h-[90vh] overflow-y-auto flex flex-col">
        
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white z-10">
            <h2 class="text-lg font-bold text-gray-900">Thêm mới Khách thuê</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-700">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <form action="{{ route('admin.customers.store') }}" method="POST" enctype="multipart/form-data" id="customerForm">
                @csrf

                <!-- Thông tin liên hệ -->
                <div class="mb-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center">
                        Thông tin liên hệ
                    </h3>
                    
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tên khách thuê / Doanh nghiệp <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="w-full px-3 py-2 border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }} rounded focus:outline-none focus:border-primary text-sm" placeholder="Nhập họ tên hoặc tên công ty">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Số điện thoại <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-3 py-2 border {{ $errors->has('phone') ? 'border-red-500 text-red-600' : 'border-gray-300' }} rounded focus:outline-none focus:border-primary text-sm pr-10" placeholder="098765432">
                                @error('phone')
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                                    </div>
                                @enderror
                            </div>
                            @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="w-full px-3 py-2 border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }} rounded focus:outline-none focus:border-primary text-sm" placeholder="email@domain.com">
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Địa chỉ thường trú / Trụ sở</label>
                        <input type="text" name="address" value="{{ old('address') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-primary text-sm" placeholder="Số nhà, đường, phường/xã, quận/huyện...">
                    </div>
                </div>

                <!-- Thông tin định danh -->
                <div>
                    <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center">
                        Thông tin định danh
                    </h3>

                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Số CCCD / CMND <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <!-- Giả lập trạng thái hợp lệ màu xanh nếu không có lỗi cho field này khi submit, ở đây ta dùng logic đơn giản -->
                            <input type="text" name="id_card_number" value="{{ old('id_card_number') }}" class="w-full px-3 py-2 border {{ $errors->has('id_card_number') ? 'border-red-500' : (old('id_card_number') ? 'border-green-500 text-green-700' : 'border-gray-300') }} rounded focus:outline-none focus:border-primary text-sm pr-10" placeholder="046099001234">
                            @if(old('id_card_number') && !$errors->has('id_card_number'))
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fa-regular fa-circle-check text-green-500"></i>
                                </div>
                            @endif
                            @error('id_card_number')
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                                </div>
                            @enderror
                        </div>
                        @error('id_card_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        
                        <!-- Ảnh CCCD Mặt Trước -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Ảnh CCCD (Mặt trước)</label>
                            <div class="relative border border-dashed border-gray-400 rounded-lg p-4 text-center hover:bg-gray-50 cursor-pointer h-32 flex flex-col items-center justify-center overflow-hidden" id="upload_front_box" onclick="document.getElementById('id_card_front').click()">
                                
                                <div id="preview_front_container" class="absolute inset-0 w-full h-full hidden bg-gray-100 flex items-center justify-center">
                                    <img id="preview_front" src="" alt="Mặt trước" class="max-h-full max-w-full object-contain">
                                    <button type="button" class="absolute top-2 right-2 bg-white text-red-500 hover:text-red-700 p-1 rounded-full shadow" onclick="event.stopPropagation(); removeImage('front')">
                                        <i class="fa-solid fa-trash w-4 h-4 text-xs flex justify-center items-center"></i>
                                    </button>
                                </div>

                                <div id="placeholder_front">
                                    <i class="fa-regular fa-image text-gray-400 text-2xl mb-2"></i>
                                    <p class="text-sm font-medium text-[#006699]">Tải ảnh lên <span class="text-gray-500 font-normal">hoặc kéo thả</span></p>
                                    <p class="text-xs text-gray-400 mt-1">PNG, JPG tối đa 5MB</p>
                                </div>
                            </div>
                            <input type="file" name="id_card_front" id="id_card_front" class="hidden" accept="image/*" onchange="previewImage(this, 'front')">
                            @error('id_card_front') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Ảnh CCCD Mặt Sau -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Ảnh CCCD (Mặt sau)</label>
                            <div class="relative border border-dashed border-gray-400 rounded-lg p-4 text-center hover:bg-gray-50 cursor-pointer h-32 flex flex-col items-center justify-center overflow-hidden" id="upload_back_box" onclick="document.getElementById('id_card_back').click()">
                                
                                <div id="preview_back_container" class="absolute inset-0 w-full h-full hidden bg-gray-100 flex items-center justify-center">
                                    <img id="preview_back" src="" alt="Mặt sau" class="max-h-full max-w-full object-contain">
                                    <button type="button" class="absolute top-2 right-2 bg-white text-red-500 hover:text-red-700 p-1 rounded-full shadow" onclick="event.stopPropagation(); removeImage('back')">
                                        <i class="fa-solid fa-trash w-4 h-4 text-xs flex justify-center items-center"></i>
                                    </button>
                                </div>

                                <div id="placeholder_back">
                                    <i class="fa-regular fa-image text-gray-400 text-2xl mb-2"></i>
                                    <p class="text-sm font-medium text-[#006699]">Tải ảnh lên <span class="text-gray-500 font-normal">hoặc kéo thả</span></p>
                                    <p class="text-xs text-gray-400 mt-1">PNG, JPG tối đa 5MB</p>
                                </div>
                            </div>
                            <input type="file" name="id_card_back" id="id_card_back" class="hidden" accept="image/*" onchange="previewImage(this, 'back')">
                            @error('id_card_back') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                    </div>
                </div>

            </form>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end space-x-3 rounded-b-lg">
            <button type="button" onclick="closeModal()" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded font-medium text-sm hover:bg-gray-100">
                HỦY
            </button>
            <button type="button" onclick="document.getElementById('customerForm').submit()" class="px-4 py-2 bg-[#6ba4c7] text-white rounded font-bold text-sm hover:bg-[#528cb3]">
                LƯU THÔNG TIN
            </button>
        </div>

    </div>
</div>

<script>
    function openModal() {
        document.getElementById('customerModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('customerModal').classList.add('hidden');
    }

    function previewImage(input, type) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('preview_' + type).src = e.target.result;
                document.getElementById('preview_' + type + '_container').classList.remove('hidden');
                document.getElementById('placeholder_' + type).classList.add('hidden');
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeImage(type) {
        document.getElementById('id_card_' + type).value = '';
        document.getElementById('preview_' + type).src = '';
        document.getElementById('preview_' + type + '_container').classList.add('hidden');
        document.getElementById('placeholder_' + type).classList.remove('hidden');
    }
</script>

@endsection
