<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản trị - Bến Xe Huế')</title>
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
                        sidebar: '#2B3139',
                        sidebarActive: '#0070BA',
                        primary: '#0070BA',
                        textDark: '#1F2937',
                        textLight: '#6B7280',
                        bgGray: '#F9FAFB',
                        cardBorder: '#E5E7EB',
                        dangerText: '#DC2626',
                        warningText: '#D97706',
                    }
                }
            }
        }
    </script>
    
    <!-- Chart.js (nếu cần) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-white font-sans flex h-screen overflow-hidden text-sm m-0 p-0">

    <!-- Sidebar (Left) - Fix cứng kích thước -->
    <aside class="w-[240px] bg-sidebar text-white flex flex-col flex-shrink-0">
        <!-- Logo -->
        <div class="h-[60px] flex items-center px-4 border-b border-gray-700">
            <div class="w-8 h-8 bg-primary rounded flex items-center justify-center mr-3">
                <i class="fa-solid fa-store text-white"></i>
            </div>
            <div>
                <h1 class="font-bold text-base leading-tight">Bến Xe Huế</h1>
                <p class="text-[11px] text-gray-400 leading-tight">Hệ thống Kiosk</p>
            </div>
        </div>
        
        <!-- Menu Items -->
        <nav class="flex-1 overflow-y-auto py-4">
            <ul class="space-y-1">
                @can('view-dashboard')
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2.5 {{ request()->is('admin/dashboard') ? 'bg-sidebarActive text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white transition' }}">
                        <i class="fa-solid fa-border-all w-6 text-center"></i>
                        <span class="ml-2 font-medium">Dashboard</span>
                    </a>
                </li>
                @endcan
                
                @can('is-admin')
                <li>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-2.5 {{ request()->is('admin/users*') ? 'bg-sidebarActive text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white transition' }}">
                        <i class="fa-solid fa-users w-6 text-center"></i>
                        <span class="ml-2">Quản lý người dùng</span>
                    </a>
                </li>
                @endcan

                @can('view-operations')
                <li>
                    <a href="{{ route('admin.rental_requests.index') }}" class="flex items-center px-4 py-2.5 {{ request()->is('admin/rental-requests*') ? 'bg-sidebarActive text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white transition' }}">
                        <i class="fa-solid fa-inbox w-6 text-center"></i>
                        <span class="ml-2">Yêu cầu thuê</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.customers.index') }}" class="flex items-center px-4 py-2.5 {{ request()->is('admin/customers*') ? 'bg-sidebarActive text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white transition' }}">
                        <i class="fa-solid fa-user-tie w-6 text-center"></i>
                        <span class="ml-2">Khách thuê</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.kiosks.index') }}" class="flex items-center px-4 py-2.5 {{ request()->is('admin/kiosks*') ? 'bg-sidebarActive text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white transition' }}">
                        <i class="fa-solid fa-store w-6 text-center"></i>
                        <span class="ml-2">Quầy/Kiosk</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.contracts.index') }}" class="flex items-center px-4 py-2.5 {{ request()->is('admin/contracts*') ? 'bg-sidebarActive text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white transition' }}">
                        <i class="fa-solid fa-file-contract w-6 text-center"></i>
                        <span class="ml-2">Hợp đồng</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.payments.index') }}" class="flex items-center px-4 py-2.5 {{ request()->is('admin/payments*') ? 'bg-sidebarActive text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white transition' }}">
                        <i class="fa-solid fa-credit-card w-6 text-center"></i>
                        <span class="ml-2">Thanh toán</span>
                    </a>
                </li>
                @endcan
                
                @can('is-manager')
                <li>
                    <a href="#" class="flex items-center px-4 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white transition">
                        <i class="fa-solid fa-chart-line w-6 text-center"></i>
                        <span class="ml-2">Báo cáo</span>
                    </a>
                </li>
                @endcan
            </ul>
        </nav>
        
        <!-- Footer Sidebar -->
        <div class="px-4 py-4 border-t border-gray-700 text-[11px] text-gray-400 flex items-center">
            <i class="fa-solid fa-circle-info mr-2"></i> Phiên bản 1.2.0
        </div>
    </aside>

    <!-- Main Content (Right) -->
    <main class="flex-1 bg-white flex flex-col overflow-hidden min-w-[900px]">
        
        <!-- Header -->
        <header class="h-[60px] border-b border-cardBorder flex items-center justify-between px-6 flex-shrink-0 bg-white">
            <!-- Search bar -->
            <div class="relative w-[300px]">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                </div>
                <input type="text" class="w-full bg-gray-100 border-none rounded-full py-1.5 pl-10 pr-4 focus:ring-1 focus:ring-primary text-sm" placeholder="Tìm kiếm...">
            </div>
            
            <!-- User menu -->
            <div class="flex items-center space-x-5">
                <button class="text-gray-500 hover:text-gray-700">
                    <i class="fa-regular fa-bell text-lg"></i>
                </button>
                <button class="text-gray-500 hover:text-gray-700">
                    <i class="fa-regular fa-circle-question text-lg"></i>
                </button>
                <div class="h-6 w-px bg-gray-300 mx-2"></div>
                
                @if(auth()->check())
                <a href="{{ route('admin.profile.index') }}" class="flex items-center group cursor-pointer">
                    <div class="text-right mr-3">
                        <div class="text-sm font-bold text-gray-800 group-hover:text-[#006699] transition">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-gray-500 uppercase">{{ auth()->user()->role == 'admin' ? 'Administrator' : (auth()->user()->role == 'manager' ? 'Manager' : 'Employee') }}</div>
                    </div>
                    <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="w-9 h-9 rounded-full border border-gray-200 object-cover group-hover:border-[#006699] transition">
                </a>
                @endif
                
                <form method="POST" action="{{ route('logout') }}" class="ml-2">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-red-600" title="Đăng xuất">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </header>
        
        <!-- Content Area -->
        <div class="flex-1 overflow-y-auto bg-gray-50">
            @yield('content')
        </div>
    </main>

    @yield('scripts')
</body>
</html>
