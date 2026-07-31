<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập - Bến Xe Huế</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#006699',
                        primaryHover: '#005580',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 text-gray-800 font-sans flex flex-col items-center justify-center min-h-screen m-0 p-0">

    <!-- Card Đăng nhập -->
    <div class="w-[400px] bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        
        <!-- Header: Logo & Tiêu đề -->
        <div class="flex flex-col items-center pt-8 pb-6 border-b border-gray-200">
            <div class="w-12 h-12 bg-[#006699] text-white flex items-center justify-center rounded-md mb-4">
                <i class="fa-solid fa-store text-xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-1">Bến Xe Huế</h1>
            <p class="text-sm text-gray-500">Hệ thống quản lý Kiosk - Đăng nhập</p>
        </div>

        <!-- Nội dung Form -->
        <div class="p-6">
            @if($errors->any())
                <!-- Khối Thông báo lỗi -->
                <div class="mb-5 p-3 bg-red-50 border border-red-300 rounded flex items-start text-red-700 text-sm">
                    <i class="fa-solid fa-circle-exclamation mt-0.5 mr-2"></i>
                    <span>Tài khoản hoặc mật khẩu không chính xác. Vui lòng thử lại.</span>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf
                
                <!-- Input: Tên đăng nhập -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-700 mb-1.5">Tên đăng nhập</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-regular fa-user {{ $errors->any() ? 'text-red-500' : 'text-gray-400' }}"></i>
                        </div>
                        <input type="text" id="email" name="email" placeholder="Nhập tên đăng nhập" value="{{ old('email') }}"
                            class="w-full pl-9 pr-3 py-2 border {{ $errors->any() ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-primary focus:border-primary' }} rounded focus:outline-none focus:ring-1 transition-colors text-sm text-gray-800" required>
                    </div>
                </div>

                <!-- Input: Mật khẩu -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-gray-700 mb-1.5">Mật khẩu</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock {{ $errors->any() ? 'text-red-500' : 'text-gray-400' }}"></i>
                        </div>
                        <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" 
                            class="w-full pl-9 pr-10 py-2 border {{ $errors->any() ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-primary focus:border-primary' }} rounded focus:outline-none focus:ring-1 transition-colors text-sm text-gray-800" required>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer" onclick="togglePassword()">
                            <i id="toggleIcon" class="fa-regular fa-eye-slash text-gray-400 hover:text-gray-600 transition-colors"></i>
                        </div>
                    </div>
                </div>

                <!-- Tiện ích: Ghi nhớ & Quên mật khẩu -->
                <div class="flex items-center justify-between pt-1 pb-1">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="remember" class="w-3.5 h-3.5 text-primary border-gray-300 rounded focus:ring-primary cursor-pointer">
                        <span class="ml-2 text-xs text-gray-600">Ghi nhớ đăng nhập</span>
                    </label>
                    <a href="#" class="text-sm font-medium text-[#006699] hover:underline transition-colors">Quên mật khẩu?</a>
                </div>

                <!-- Nút Submit -->
                <div class="pt-1">
                    <button type="submit" class="w-full bg-[#006699] hover:bg-[#005580] text-white font-medium py-2 rounded transition-colors text-sm">
                        Đăng nhập
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer Card -->
        <div class="bg-gray-50/50 border-t border-gray-200 py-3 text-center">
            <p class="text-sm text-gray-600"><i class="fa-solid fa-circle-info mr-1 text-gray-500"></i> Cần hỗ trợ? <a href="#" class="text-[#006699] hover:underline transition-colors">Liên hệ Quản trị viên</a></p>
        </div>
    </div>

    <!-- Copyright -->
    <div class="mt-6 text-xs text-gray-400">
        &copy; 2024 Bến Xe Huế. Bảo lưu mọi quyền.
    </div>

    <!-- Script toggle password -->
    <script>
        function togglePassword() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                pwd.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }
    </script>
</body>
</html>