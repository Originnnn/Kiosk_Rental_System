@extends('layouts.admin')

@section('title', 'Đổi mật khẩu')

@section('content')
<div class="max-w-6xl mx-auto pb-10">

    <!-- Header -->
    <div class="mb-6">
        <div class="text-sm text-gray-500 mb-1">Trang chủ &rsaquo; Hồ sơ cá nhân &rsaquo; <span class="text-gray-800 font-medium">Đổi mật khẩu</span></div>
        <h1 class="text-2xl font-bold text-gray-800">Đổi mật khẩu</h1>
        <p class="text-sm text-gray-500 mt-1">Quản lý bảo mật tài khoản quản trị của bạn.</p>
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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Cột Trái (Form) -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
            
            <div class="bg-[#f8f9fa] border border-gray-100 rounded-lg p-4 mb-6 flex items-start">
                <i class="fa-solid fa-circle-info text-[#006699] text-lg mt-0.5 mr-3"></i>
                <div>
                    <h3 class="text-sm font-bold text-gray-800 mb-1">Mẹo bảo mật</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Vui lòng sử dụng mật khẩu mạnh không sử dụng ở nơi khác. Bến Xe Huế khuyến nghị thay đổi mật khẩu định kỳ 90 ngày một lần.</p>
                </div>
            </div>

            <form action="{{ route('admin.profile.password.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    
                    <!-- Mật khẩu hiện tại -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Mật khẩu hiện tại</label>
                        <div class="relative">
                            <input type="password" name="current_password" class="w-full px-3 py-2.5 border border-gray-300 rounded focus:ring-1 focus:ring-[#006699] focus:border-[#006699] text-sm pr-10" placeholder="Nhập mật khẩu hiện tại" required>
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <i class="fa-solid fa-eye-slash"></i>
                            </button>
                        </div>
                        <div class="mt-2 text-right">
                            <a href="#" class="text-xs text-[#006699] hover:underline">Quên mật khẩu?</a>
                        </div>
                    </div>

                    <hr class="border-gray-100">

                    <!-- Mật khẩu mới -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Mật khẩu mới</label>
                        <div class="relative">
                            <input type="password" name="password" id="new_password" class="w-full px-3 py-2.5 border border-gray-300 rounded focus:ring-1 focus:ring-[#006699] focus:border-[#006699] text-sm pr-10" placeholder="Nhập mật khẩu mới" required>
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <i class="fa-solid fa-eye-slash"></i>
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-2 mt-4">
                            <div class="flex items-center text-xs text-green-600">
                                <i class="fa-regular fa-circle-check mr-2"></i> Tối thiểu 8 ký tự
                            </div>
                            <div class="flex items-center text-xs text-gray-400">
                                <i class="fa-regular fa-circle mr-2"></i> Ít nhất 1 chữ hoa
                            </div>
                            <div class="flex items-center text-xs text-gray-400">
                                <i class="fa-regular fa-circle mr-2"></i> Ít nhất 1 số
                            </div>
                            <div class="flex items-center text-xs text-gray-400">
                                <i class="fa-regular fa-circle mr-2"></i> Ít nhất 1 ký tự đặc biệt
                            </div>
                        </div>
                    </div>

                    <!-- Nhập lại mật khẩu mới -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Nhập lại mật khẩu mới</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" class="w-full px-3 py-2.5 border border-gray-300 rounded focus:ring-1 focus:ring-[#006699] focus:border-[#006699] text-sm pr-10" placeholder="Xác nhận mật khẩu mới" required>
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <i class="fa-solid fa-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end space-x-3">
                        <a href="{{ route('admin.profile.index') }}" class="px-4 py-2 bg-white text-gray-700 text-sm font-medium border border-gray-300 rounded hover:bg-gray-50 transition shadow-sm">
                            Hủy
                        </a>
                        <button type="submit" class="px-4 py-2 bg-[#006699] text-white text-sm font-medium rounded hover:bg-[#005580] transition shadow-sm">
                            Cập nhật mật khẩu
                        </button>
                    </div>

                </div>
            </form>
        </div>

        <!-- Cột Phải (Intro) -->
        <div class="bg-[#f8f9fa] rounded-lg border border-gray-200 p-10 flex flex-col items-center justify-center text-center shadow-sm">
            <div class="w-20 h-20 bg-blue-100 rounded-2xl flex items-center justify-center mb-6 shadow-sm">
                <i class="fa-solid fa-shield-halved text-4xl text-[#006699]"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-800 mb-4">Bảo mật cấp doanh nghiệp</h2>
            <p class="text-sm text-gray-600 leading-relaxed max-w-md">
                Hệ thống quản lý Bến Xe Huế yêu cầu tiêu chuẩn bảo mật nghiêm ngặt. Việc cập nhật mật khẩu thường xuyên giúp bảo vệ dữ liệu vận hành và thông tin đối tác an toàn.
            </p>
        </div>

    </div>

</div>

<script>
    // Toggle password visibility
    document.querySelectorAll('.fa-eye-slash').forEach(icon => {
        icon.parentElement.addEventListener('click', function() {
            const input = this.previousElementSibling;
            if (input.type === 'password') {
                input.type = 'text';
                this.innerHTML = '<i class="fa-solid fa-eye"></i>';
            } else {
                input.type = 'password';
                this.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';
            }
        });
    });

    // Simple password check visualizer
    const newPasswordInput = document.getElementById('new_password');
    if (newPasswordInput) {
        newPasswordInput.addEventListener('input', function() {
            const val = this.value;
            const checks = this.parentElement.nextElementSibling.querySelectorAll('div.flex');
            
            // Length >= 8
            updateCheck(checks[0], val.length >= 8);
            // Has uppercase
            updateCheck(checks[1], /[A-Z]/.test(val));
            // Has number
            updateCheck(checks[2], /[0-9]/.test(val));
            // Has special char
            updateCheck(checks[3], /[^A-Za-z0-9]/.test(val));
        });

        function updateCheck(el, isValid) {
            const icon = el.querySelector('i');
            if (isValid) {
                el.classList.remove('text-gray-400');
                el.classList.add('text-green-600');
                icon.className = 'fa-regular fa-circle-check mr-2';
            } else {
                el.classList.remove('text-green-600');
                el.classList.add('text-gray-400');
                icon.className = 'fa-regular fa-circle mr-2';
            }
        }
    }
</script>
@endsection