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
<body class="bg-[#F4F5F7] text-gray-800 font-sans flex flex-col items-center justify-center min-h-screen m-0 p-0">

    <!-- Card Đăng nhập -->
    <div class="w-[400px] bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        
        <!-- Header: Logo & Tiêu đề -->
        <div class="flex flex-col items-center pt-8 pb-6 border-b border-gray-100">
            <div class="w-14 h-14 bg-primary text-white flex items-center justify-center rounded-xl mb-4">
                <i class="fa-solid fa-store text-2xl"></i>
            </div>
            <h1 class="text-xl font-bold text-gray-900 mb-1">Bến Xe Huế</h1>
            <p class="text-xs text-gray-500">Hệ thống Quản lý Kiosk</p>
        </div>

        <!-- Nội dung Form -->
        <div class="p-8 pb-6">
            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf
                
                <!-- Input: Tên đăng nhập -->
                <div>
                    <label for="email" class="block text-xs font-bold text-gray-600 mb-2">Email đăng nhập</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-regular fa-user text-gray-400"></i>
                        </div>
                        <input type="email" id="email" name="email" placeholder="Nhập email" value="{{ old('email') }}"
                            class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-colors text-sm text-gray-800" required>
                    </div>
                    @if($errors->has('email'))
                        <p class="text-red-500 text-xs mt-1">{{ $errors->first('email') }}</p>
                    @endif
                    </div>
                </div>

                <!-- Input: Mật khẩu -->
                <div>
                    <label for="password" class="block text-xs font-bold text-gray-600 mb-2">Mật khẩu</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" 
                            class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-colors text-sm text-gray-800" required>
                    </div>
                </div>

                <!-- Tiện ích: Ghi nhớ & Quên mật khẩu -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="remember" class="w-3.5 h-3.5 text-primary border-gray-300 rounded focus:ring-primary cursor-pointer">
                        <span class="ml-2 text-xs text-gray-500">Ghi nhớ đăng nhập</span>
                    </label>
                    <a href="#" class="text-xs font-semibold text-primary hover:text-primaryHover transition-colors">Quên mật khẩu?</a>
                </div>

                <!-- Nút Submit -->
                <div class="pt-2">
                    <button type="submit" class="w-full bg-primary hover:bg-primaryHover text-white font-semibold py-2.5 rounded transition-colors text-sm">
                        Đăng nhập
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer Card -->
        <div class="bg-[#F0F2F5] border-t border-gray-200 py-3 text-center">
            <p class="text-xs text-gray-500">Cần hỗ trợ? <a href="#" class="text-primary hover:underline transition-colors">Liên hệ Quản trị viên</a></p>
        </div>
    </div>

    <!-- Copyright -->
    <div class="mt-6 text-[11px] text-gray-400">
        &copy; 2024 Bến Xe Huế. Bảo lưu mọi quyền.
    </div>

</body>
</html>