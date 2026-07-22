<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard - Kiosk Rental')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Hỗ trợ Alpine.js cho các tương tác nhẹ như dropdown -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 font-sans flex h-screen overflow-hidden">

    <!-- Sidebar cố định -->
    <aside class="w-64 bg-gray-900 text-white flex flex-col">
        <div class="p-6 text-center border-b border-gray-800">
            <h2 class="text-xl font-bold">Kiosk Admin</h2>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="/admin/dashboard" class="block px-4 py-2 rounded hover:bg-gray-800">Dashboard</a>
            <a href="/admin/requests" class="block px-4 py-2 rounded hover:bg-gray-800">Yêu cầu thuê</a>
            <a href="/admin/contracts" class="block px-4 py-2 rounded hover:bg-gray-800">Hợp đồng</a>
            <a href="/admin/kiosks" class="block px-4 py-2 rounded hover:bg-gray-800">Quản lý Kiosk</a>
            <a href="/admin/reports" class="block px-4 py-2 rounded hover:bg-gray-800">Báo cáo KPI</a>
        </nav>
    </aside>

    <!-- Main Workspace -->
    <div class="flex-1 flex flex-col h-screen">
        <!-- Header: Search, Notifications, Profile -->
        <header class="bg-white shadow px-6 py-4 flex justify-between items-center">
            <div class="w-1/3">
                <input type="text" placeholder="Tìm kiếm nhanh mã hợp đồng..." class="w-full border rounded px-4 py-2">
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-gray-600">🔔 Thông báo</span>
                <div class="border-l pl-4 font-medium text-gray-800">
                    <!-- Tên người dùng sẽ được đổ ra từ Auth -->
                    Xin chào, Admin
                </div>
                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit" class="text-red-500 hover:underline">Đăng xuất</button>
                </form>
            </div>
        </header>

        <!-- Dynamic Content Area -->
        <main class="flex-1 p-6 overflow-y-auto bg-gray-100">
            <!-- Filter Bar hoặc KPI Strip có thể đặt trong yield này -->
            @yield('content')
        </main>
    </div>

</body>
</html>