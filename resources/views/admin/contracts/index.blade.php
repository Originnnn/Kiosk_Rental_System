@extends('layouts.admin')

@section('title', 'Quản lý hợp đồng - Bến Xe Huế')

@section('content')
<div class="bg-white min-h-screen flex flex-col m-0 p-0 font-sans">
    
    <!-- Top Header -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1">Quản lý hợp đồng</h1>
                <p class="text-sm text-gray-500">Danh sách tất cả hợp đồng cho thuê quầy/kiosk.</p>
            </div>
            
            <div class="flex space-x-3">
                <button type="button" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded flex items-center font-medium text-sm hover:bg-gray-50 transition-colors shadow-sm">
                    <i class="fa-solid fa-filter mr-2"></i> Bộ lọc
                </button>
                @can('is-employee')
                <a href="{{ route('admin.contracts.create') }}" class="bg-[#006699] hover:bg-[#005580] text-white px-4 py-2 rounded font-medium flex items-center text-sm transition-colors shadow-sm">
                    <i class="fa-solid fa-plus mr-2"></i> Tạo hợp đồng
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Title and Actions (Download, Print) -->
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-base font-bold text-gray-900">Danh sách hợp đồng ({{ $contracts->total() }})</h2>
        <div class="flex space-x-3 text-gray-500">
            <button class="hover:text-gray-900"><i class="fa-solid fa-download"></i></button>
            <button class="hover:text-gray-900"><i class="fa-solid fa-print"></i></button>
        </div>
    </div>

    <!-- Bảng dữ liệu -->
    <div class="flex-1 overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-200 bg-white">
                    <th class="px-6 py-4 text-sm font-bold text-gray-700">Số HĐ</th>
                    <th class="px-6 py-4 text-sm font-bold text-gray-700">Khách thuê</th>
                    <th class="px-6 py-4 text-sm font-bold text-gray-700">Quầy</th>
                    <th class="px-6 py-4 text-sm font-bold text-gray-700 text-center">Ngày bắt đầu</th>
                    <th class="px-6 py-4 text-sm font-bold text-gray-700 text-center">Ngày kết thúc</th>
                    <th class="px-6 py-4 text-sm font-bold text-gray-700 text-right">Tổng giá trị (VND)</th>
                    <th class="px-6 py-4 text-sm font-bold text-gray-700 text-center">Trạng thái</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($contracts as $contract)
                    @php
                        // Logic xác định trạng thái (Dựa vào status hoặc ngày)
                        $statusClass = '';
                        $statusText = '';
                        
                        $isExpired = \Carbon\Carbon::parse($contract->end_date)->isPast();
                        $isExpiringSoon = \Carbon\Carbon::parse($contract->end_date)->diffInDays(now()) <= 30 && !$isExpired;

                        if ($contract->status == 'cancelled') {
                            $statusClass = 'text-red-600 bg-red-50';
                            $statusText = 'HỦY';
                        } elseif ($isExpired) {
                            $statusClass = 'text-gray-500 bg-gray-100';
                            $statusText = 'HẾT HẠN';
                        } elseif ($isExpiringSoon) {
                            $statusClass = 'text-yellow-600 bg-yellow-50';
                            $statusText = 'SẮP HẾT HẠN';
                        } else {
                            $statusClass = 'text-green-500 bg-green-50';
                            $statusText = 'HIỆU LỰC';
                        }
                    @endphp
                    <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('admin.contracts.show', $contract->id) }}'">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                            {{ $contract->reference_code }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $contract->customer->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $contract->kiosk->code ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">
                            {{ \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-medium {{ $isExpiringSoon ? 'text-yellow-600' : 'text-gray-700' }}">
                            {{ \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">
                            {{ number_format($contract->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold rounded w-24 {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            Chưa có hợp đồng nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
        <div class="text-sm text-gray-500">
            Hiển thị {{ $contracts->firstItem() ?? 0 }}-{{ $contracts->lastItem() ?? 0 }} trong số {{ $contracts->total() }} hợp đồng
        </div>
        <div>
            {{ $contracts->links('pagination::tailwind') }}
        </div>
    </div>

</div>
@endsection
