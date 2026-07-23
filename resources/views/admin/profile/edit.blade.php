@extends('layouts.admin')

@section('title', 'Chỉnh sửa thông tin cá nhân')

@section('content')
<div class="max-w-6xl mx-auto pb-10">

    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <div class="text-sm text-gray-500 mb-1">Trang chủ &rsaquo; Hồ sơ cá nhân &rsaquo; <span class="text-[#006699]">Chỉnh sửa</span></div>
                <h1 class="text-2xl font-bold text-gray-800">Chỉnh sửa thông tin cá nhân</h1>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.profile.index') }}" class="px-4 py-2 bg-white text-gray-700 text-sm font-medium border border-gray-300 rounded hover:bg-gray-50 transition shadow-sm">
                    Hủy
                </a>
                <button type="submit" class="px-4 py-2 bg-[#006699] text-white text-sm font-medium rounded hover:bg-[#005580] transition shadow-sm flex items-center">
                    <i class="fa-solid fa-floppy-disk mr-2"></i> Lưu thay đổi
                </button>
            </div>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700 font-medium">Vui lòng kiểm tra lại các lỗi bên dưới:</p>
                        <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fa-solid fa-circle-check text-green-500"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Cột Trái -->
            <div class="space-y-6">
                
                <!-- Ảnh đại diện -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm text-center">
                    <h2 class="text-sm font-bold text-gray-800 text-left mb-6">Ảnh đại diện</h2>
                    
                    <div class="relative inline-block mb-4">
                        <div class="w-32 h-32 rounded-lg border-2 border-gray-200 overflow-hidden bg-gray-50 mx-auto shadow-sm">
                            <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-full h-full object-cover" id="avatar_preview">
                        </div>
                        <label for="avatar" class="absolute -bottom-2 -right-2 w-8 h-8 bg-[#006699] text-white rounded-full flex items-center justify-center cursor-pointer hover:bg-[#005580] shadow border-2 border-white transition">
                            <i class="fa-solid fa-pen text-xs"></i>
                        </label>
                        <input type="file" id="avatar" name="avatar" class="hidden" accept="image/jpeg, image/png" onchange="previewAvatar(event)">
                    </div>
                    
                    <div class="flex justify-center space-x-4 mb-4 text-sm">
                        <label for="avatar" class="text-[#006699] font-medium cursor-pointer hover:underline">Tải ảnh mới</label>
                        <span class="text-gray-300">|</span>
                        <button type="button" class="text-red-500 font-medium hover:underline">Xóa ảnh</button>
                    </div>
                    <p class="text-xs text-gray-500">Định dạng hỗ trợ: JPG, PNG. Dung lượng tối đa 2MB.</p>
                </div>

                <!-- Thông tin công tác -->
                <div class="bg-[#f8f9fa] rounded-lg border border-gray-200 p-6 shadow-sm">
                    <h2 class="text-sm font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fa-solid fa-briefcase text-gray-500 mr-2"></i> Thông tin công tác
                    </h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">MÃ NHÂN VIÊN</label>
                            <input type="text" value="{{ $user->employee_code ?: '—' }}" class="w-full px-3 py-2 bg-gray-200 border-transparent rounded text-sm text-gray-600 cursor-not-allowed" readonly>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">PHÒNG BAN</label>
                            <input type="text" value="{{ $user->department ?: '—' }}" class="w-full px-3 py-2 bg-gray-200 border-transparent rounded text-sm text-gray-600 cursor-not-allowed" readonly>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">EMAIL CÔNG VIỆC</label>
                            <input type="text" value="{{ $user->email }}" class="w-full px-3 py-2 bg-gray-200 border-transparent rounded text-sm text-gray-600 cursor-not-allowed" readonly>
                        </div>
                    </div>
                    
                    <div class="mt-6 bg-blue-50 border border-blue-100 p-3 rounded flex items-start">
                        <i class="fa-solid fa-circle-info text-blue-500 mt-0.5 mr-2"></i>
                        <p class="text-xs text-blue-800">Thông tin công tác được quản lý bởi bộ phận Nhân sự. Vui lòng liên hệ HR nếu cần thay đổi.</p>
                    </div>
                </div>

            </div>

            <!-- Cột Phải -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Thông tin cơ bản -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                    <h2 class="text-base font-bold text-gray-800 mb-4 flex items-center border-b border-gray-100 pb-3">
                        <i class="fa-regular fa-user text-[#006699] mr-2"></i> Thông tin cơ bản
                    </h2>
                    
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-2">Họ và Tên <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-[#006699] focus:border-[#006699] text-sm @error('name') border-red-500 @enderror" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-2">Ngày sinh</label>
                            <input type="date" name="dob" value="{{ old('dob', $user->dob ? \Carbon\Carbon::parse($user->dob)->format('Y-m-d') : '') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-[#006699] focus:border-[#006699] text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-2">Giới tính</label>
                            <div class="flex space-x-6 mt-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="gender" value="Nam" {{ old('gender', $user->gender) == 'Nam' ? 'checked' : '' }} class="text-[#006699] focus:ring-[#006699]">
                                    <span class="ml-2 text-sm text-gray-700">Nam</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="gender" value="Nữ" {{ old('gender', $user->gender) == 'Nữ' ? 'checked' : '' }} class="text-[#006699] focus:ring-[#006699]">
                                    <span class="ml-2 text-sm text-gray-700">Nữ</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="gender" value="Khác" {{ old('gender', $user->gender) == 'Khác' ? 'checked' : '' }} class="text-[#006699] focus:ring-[#006699]">
                                    <span class="ml-2 text-sm text-gray-700">Khác</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-2">Số CCCD</label>
                            <input type="text" name="id_card" value="{{ old('id_card', $user->id_card) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-[#006699] focus:border-[#006699] text-sm">
                        </div>
                    </div>
                </div>

                <!-- Thông tin liên lạc -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                    <h2 class="text-base font-bold text-gray-800 mb-4 flex items-center border-b border-gray-100 pb-3">
                        <i class="fa-solid fa-address-book text-[#006699] mr-2"></i> Thông tin liên lạc
                    </h2>
                    
                    <div class="grid grid-cols-2 gap-6 mb-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-2">Số điện thoại <span class="text-red-500">*</span></label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-[#006699] focus:border-[#006699] text-sm @error('phone') border-red-500 @enderror">
                            @error('phone')
                                <p class="text-xs text-red-500 mt-1 flex items-start"><i class="fa-solid fa-circle-exclamation mt-0.5 mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-2">Email cá nhân</label>
                            <input type="email" name="personal_email" value="{{ old('personal_email', $user->personal_email) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-[#006699] focus:border-[#006699] text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Địa chỉ thường trú</label>
                        <textarea name="address" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-[#006699] focus:border-[#006699] text-sm">{{ old('address', $user->address) }}</textarea>
                    </div>
                </div>

                <!-- Bảo mật tài khoản -->
                <div class="bg-[#f8f9fa] rounded-lg border border-gray-200 p-5 shadow-sm flex items-start">
                    <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0 mt-1">
                        <i class="fa-solid fa-shield text-orange-500 text-lg"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-bold text-gray-800 mb-1">Bảo mật tài khoản</h3>
                        <p class="text-sm text-gray-600 mb-2">Lần cuối thay đổi mật khẩu là {{ $user->updated_at ? $user->updated_at->diffForHumans() : 'chưa rõ' }}. Chúng tôi khuyên bạn nên cập nhật mật khẩu định kỳ để đảm bảo an toàn cho tài khoản hệ thống.</p>
                        <a href="{{ route('admin.profile.password') }}" class="text-sm text-[#006699] font-medium hover:underline flex items-center">
                            Thay đổi mật khẩu ngay <i class="fa-solid fa-arrow-up-right-from-square ml-1 text-xs"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </form>

</div>

<script>
    function previewAvatar(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatar_preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection