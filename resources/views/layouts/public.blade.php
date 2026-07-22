<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Hue Station Management')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 font-sans h-screen flex flex-col overflow-hidden">
    <!-- Header / Navigation -->
    <header class="bg-white border-b border-gray-200 shrink-0">
        <div class="px-6 h-16 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <h1 class="text-xl font-bold text-blue-800 tracking-tight">Hue Station Management</h1>
                <div class="w-px h-6 bg-gray-300"></div>
                <div class="text-gray-600 font-medium text-sm">@yield('header_title')</div>
            </div>
            
            <div class="flex-1 max-w-xl mx-8">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" placeholder="Tìm kiếm nhanh..." class="w-full bg-gray-100 border border-transparent rounded py-2 pl-9 pr-4 focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition outline-none text-sm text-gray-700">
                </div>
            </div>

            <div class="flex items-center gap-4 text-gray-600">
                <button class="hover:text-blue-600 transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg></button>
                <button class="hover:text-blue-600 transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></button>
                <button class="hover:text-blue-600 transition">
                    <div class="w-7 h-7 rounded-full border border-gray-300 flex items-center justify-center bg-gray-50 text-gray-500 overflow-hidden">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto flex flex-col relative">
        @yield('content')
    </main>
</body>
</html>