@extends('layouts.admin')

@section('title', 'Báo cáo & Thống kê - Bến Xe Huế')

@section('content')
<div class="bg-slate-50 min-h-screen p-6 font-sans">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between mb-6 min-h-[40px]">
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-slate-800 leading-tight">Xuất báo cáo & Thống kê</h1>
            <p class="text-sm text-slate-500 mt-1">Cấu hình tham số để kết xuất báo cáo dữ liệu.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex-shrink-0 flex items-start gap-2">
            <!-- Nút action (nếu có) -->
        </div>
    </div>

    <!-- Control Panel -->
    <div class="bg-white rounded border border-gray-200 p-6 shadow-sm mb-6">
        <form class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
            <div class="md:col-span-1">
                <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase">Loại báo cáo</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-primary focus:border-primary text-sm bg-white">
                    <option value="revenue">Doanh thu Kiosk</option>
                    <option value="debt">Công nợ khách hàng</option>
                    <option value="contract">Hợp đồng hết hạn</option>
                </select>
            </div>
            
            <div class="md:col-span-1">
                <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase">Từ ngày</label>
                <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-primary focus:border-primary text-sm" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
            </div>
            
            <div class="md:col-span-1">
                <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase">Đến ngày</label>
                <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-primary focus:border-primary text-sm" value="{{ now()->endOfMonth()->format('Y-m-d') }}">
            </div>
            
            <div class="md:col-span-2 flex space-x-3">
                <button type="button" class="flex-1 bg-white border border-[#006699] text-[#006699] px-4 py-2 rounded font-medium text-sm hover:bg-blue-50 transition-colors">
                    <i class="fa-solid fa-eye mr-1"></i> Xem trước
                </button>
                <button type="button" class="flex-1 bg-blue-700 text-white px-4 py-2 rounded font-medium text-sm hover:bg-blue-800 transition-colors shadow-sm">
                    <i class="fa-solid fa-file-excel mr-1"></i> Xuất Excel
                </button>
            </div>
        </form>
    </div>

    <!-- Preview Card -->
    <div class="bg-white rounded border border-gray-200 shadow-sm min-h-[400px] flex flex-col items-center justify-center p-10">
        <!-- Empty State -->
        <i class="fa-solid fa-chart-pie text-gray-200 text-7xl mb-4"></i>
        <h3 class="text-lg font-bold text-gray-700">Chưa có dữ liệu hiển thị</h3>
        <p class="text-gray-500 text-sm mt-2 text-center max-w-sm">Vui lòng chọn tiêu chí và nhấn <strong>'Xem trước'</strong> để hiển thị dữ liệu báo cáo.</p>
    </div>

</div>
@endsection
