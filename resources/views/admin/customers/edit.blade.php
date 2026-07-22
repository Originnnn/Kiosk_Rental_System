@extends('layouts.admin')

@section('title', 'Chỉnh sửa Khách thuê - Bến Xe Huế')

@section('content')
<div class="bg-gray-50 min-h-screen p-6 font-sans">
    
    <!-- Top actions -->
    <div class="mb-4 flex items-center">
        <a href="{{ route('admin.customers.show', $customer->id) }}" class="text-[#006699] hover:underline font-semibold text-sm flex items-center">
            <i class="fa-solid fa-arrow-left mr-2"></i> Quay lại chi tiết
        </a>
    </div>

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-1">Chỉnh sửa Khách thuê: {{ $customer->name }}</h1>
        <p class="text-sm text-gray-500">Cập nhật thông tin cá nhân và giấy tờ định danh.</p>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 text-red-700 p-4 rounded mb-6 text-sm font-medium border border-red-200">
            Có lỗi xảy ra, vui lòng kiểm tra lại thông tin bên dưới.
        </div>
    @endif

    <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST" enctype="multipart/form-data" id="editCustomerForm">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded border border-gray-200 shadow-sm overflow-hidden max-w-4xl">
            <div class="p-6">
                <!-- Thông tin liên hệ -->
                <div class="mb-8">
                    <h3 class="text-base font-bold text-[#006699] mb-4 border-b border-gray-100 pb-2 flex items-center">
                        <i class="fa-regular fa-address-card mr-2"></i> Thông tin liên hệ
                    </h3>
                    
                    <div class="grid grid-cols-2 gap-6 mb-4">
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Tên khách thuê / Doanh nghiệp <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $customer->name) }}" class="w-full px-3 py-2 border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }} rounded focus:outline-none focus:border-primary text-sm" placeholder="Nhập họ tên hoặc tên công ty">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Số điện thoại <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" class="w-full px-3 py-2 border {{ $errors->has('phone') ? 'border-red-500 text-red-600' : 'border-gray-300' }} rounded focus:outline-none focus:border-primary text-sm pr-10">
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
                            <input type="email" name="email" value="{{ old('email', $customer->email) }}" class="w-full px-3 py-2 border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }} rounded focus:outline-none focus:border-primary text-sm">
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Địa chỉ thường trú / Trụ sở</label>
                        <input type="text" name="address" value="{{ old('address', $customer->address) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-primary text-sm" placeholder="Số nhà, đường, phường/xã, quận/huyện...">
                    </div>
                </div>

                <!-- Thông tin định danh -->
                <div>
                    <h3 class="text-base font-bold text-[#006699] mb-4 border-b border-gray-100 pb-2 flex items-center">
                        <i class="fa-regular fa-id-card mr-2"></i> Thông tin định danh
                    </h3>

                    <div class="mb-6">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Số CCCD / CMND <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="text" name="id_card_number" value="{{ old('id_card_number', $customer->id_card_number) }}" class="w-1/2 px-3 py-2 border {{ $errors->has('id_card_number') ? 'border-red-500' : 'border-gray-300' }} rounded focus:outline-none focus:border-primary text-sm pr-10">
                            @error('id_card_number')
                                <div class="absolute inset-y-0 right-1/2 pr-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                                </div>
                            @enderror
                        </div>
                        @error('id_card_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <!-- Ảnh CCCD Mặt Trước -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Ảnh CCCD (Mặt trước)</label>
                            
                            <div class="relative border border-dashed border-gray-400 rounded-lg p-4 text-center hover:bg-gray-50 cursor-pointer h-48 flex flex-col items-center justify-center overflow-hidden group" id="upload_front_box" onclick="document.getElementById('id_card_front').click()">
                                
                                <div id="preview_front_container" class="absolute inset-0 w-full h-full {{ $customer->id_card_front ? '' : 'hidden' }} bg-gray-100 flex items-center justify-center">
                                    <img id="preview_front" src="{{ $customer->id_card_front ? asset('storage/'.$customer->id_card_front) : '' }}" alt="Mặt trước" class="max-h-full max-w-full object-contain">
                                    <div class="absolute inset-0 bg-black bg-opacity-40 hidden group-hover:flex items-center justify-center transition">
                                        <p class="text-white text-sm font-semibold"><i class="fa-solid fa-camera mr-2"></i> Nhấn để đổi ảnh</p>
                                    </div>
                                    <button type="button" class="absolute top-2 right-2 bg-white text-red-500 hover:text-red-700 p-1.5 rounded-full shadow z-10" onclick="event.stopPropagation(); removeImage('front')">
                                        <i class="fa-solid fa-trash w-4 h-4 flex justify-center items-center"></i>
                                    </button>
                                </div>

                                <div id="placeholder_front" class="{{ $customer->id_card_front ? 'hidden' : '' }}">
                                    <i class="fa-regular fa-image text-gray-400 text-3xl mb-2"></i>
                                    <p class="text-sm font-medium text-[#006699]">Tải ảnh mới lên</p>
                                    <p class="text-xs text-gray-400 mt-1">PNG, JPG tối đa 5MB</p>
                                </div>
                            </div>
                            <input type="file" name="id_card_front" id="id_card_front" class="hidden" accept="image/*" onchange="previewImage(this, 'front')">
                            @error('id_card_front') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Ảnh CCCD Mặt Sau -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Ảnh CCCD (Mặt sau)</label>
                            
                            <div class="relative border border-dashed border-gray-400 rounded-lg p-4 text-center hover:bg-gray-50 cursor-pointer h-48 flex flex-col items-center justify-center overflow-hidden group" id="upload_back_box" onclick="document.getElementById('id_card_back').click()">
                                
                                <div id="preview_back_container" class="absolute inset-0 w-full h-full {{ $customer->id_card_back ? '' : 'hidden' }} bg-gray-100 flex items-center justify-center">
                                    <img id="preview_back" src="{{ $customer->id_card_back ? asset('storage/'.$customer->id_card_back) : '' }}" alt="Mặt sau" class="max-h-full max-w-full object-contain">
                                    <div class="absolute inset-0 bg-black bg-opacity-40 hidden group-hover:flex items-center justify-center transition">
                                        <p class="text-white text-sm font-semibold"><i class="fa-solid fa-camera mr-2"></i> Nhấn để đổi ảnh</p>
                                    </div>
                                    <button type="button" class="absolute top-2 right-2 bg-white text-red-500 hover:text-red-700 p-1.5 rounded-full shadow z-10" onclick="event.stopPropagation(); removeImage('back')">
                                        <i class="fa-solid fa-trash w-4 h-4 flex justify-center items-center"></i>
                                    </button>
                                </div>

                                <div id="placeholder_back" class="{{ $customer->id_card_back ? 'hidden' : '' }}">
                                    <i class="fa-regular fa-image text-gray-400 text-3xl mb-2"></i>
                                    <p class="text-sm font-medium text-[#006699]">Tải ảnh mới lên</p>
                                    <p class="text-xs text-gray-400 mt-1">PNG, JPG tối đa 5MB</p>
                                </div>
                            </div>
                            <input type="file" name="id_card_back" id="id_card_back" class="hidden" accept="image/*" onchange="previewImage(this, 'back')">
                            @error('id_card_back') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <a href="{{ route('admin.customers.show', $customer->id) }}" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 rounded font-medium text-sm hover:bg-gray-100 transition shadow-sm">
                    HỦY BỎ
                </a>
                <button type="submit" class="px-6 py-2.5 bg-[#006699] text-white rounded font-bold text-sm hover:bg-[#005580] transition shadow-sm">
                    CẬP NHẬT
                </button>
            </div>
        </div>
    </form>
</div>

<script>
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
        // Note: For existing image, this just removes it visually and clears the file input.
        // It won't delete it from DB unless you add a hidden input logic for "delete_image=true"
        // In this implementation, if file is empty in request, we keep old image. 
        // We only overwrite if a NEW file is uploaded.
    }
</script>
@endsection
