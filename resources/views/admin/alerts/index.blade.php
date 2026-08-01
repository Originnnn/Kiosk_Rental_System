@extends('layouts.admin')

@section('title', 'Cảnh báo hệ thống - Bến Xe Huế')

@section('content')
<div class="bg-slate-50 min-h-screen p-6 font-sans">
    
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Cảnh báo hệ thống</h1>
            <p class="text-sm text-gray-500">Giám sát các vấn đề cần xử lý khẩn cấp.</p>
        </div>
    </div>

    <!-- Tabs/Filters -->
    <div class="bg-white border-b border-gray-200 mb-6 flex rounded shadow-sm">
        <a href="?filter=all" class="px-6 py-3 font-medium text-sm {{ $filter === 'all' ? 'border-b-2 border-primary text-primary' : 'text-gray-500 hover:text-gray-700' }}">Tất cả</a>
        <a href="?filter=unpaid" class="px-6 py-3 font-medium text-sm {{ $filter === 'unpaid' ? 'border-b-2 border-primary text-primary' : 'text-gray-500 hover:text-gray-700' }}">Chậm thanh toán</a>
        <a href="?filter=expiring" class="px-6 py-3 font-medium text-sm {{ $filter === 'expiring' ? 'border-b-2 border-primary text-primary' : 'text-gray-500 hover:text-gray-700' }}">Hợp đồng sắp hết hạn</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        @if($filter === 'all' || $filter === 'unpaid')
            @foreach($unpaidPayments as $payment)
            <div class="bg-white rounded border border-red-200 shadow-sm p-4 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-red-500"></div>
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <span class="inline-block px-2 py-1 bg-red-50 text-red-700 text-[10px] font-bold rounded uppercase mb-2">Chậm thanh toán</span>
                        <h3 class="font-bold text-gray-900">{{ $payment->contract->customer->name ?? 'Khách hàng' }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Kiosk: <span class="font-semibold text-gray-700">{{ $payment->contract->kiosk->code ?? 'N/A' }}</span></p>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center">
                        <i class="fa-solid fa-money-bill-wave text-red-500 text-sm"></i>
                    </div>
                </div>
                
                <div class="mt-4 pt-3 border-t border-gray-100 flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500">Nợ cước / Quá hạn</p>
                        <p class="font-bold text-red-600 text-sm mt-0.5">{{ number_format($payment->amount, 0, ',', '.') }} đ <span class="text-xs text-gray-500 font-normal">({{ (int) $payment->days_overdue }} ngày)</span></p>
                    </div>
                    <a href="{{ route('admin.contracts.show', $payment->contract_id) }}" class="text-[#006699] text-xs font-semibold hover:underline">Xem chi tiết <i class="fa-solid fa-arrow-right text-[10px] ml-1"></i></a>
                </div>
            </div>
            @endforeach
        @endif

        @if($filter === 'all' || $filter === 'expiring')
            @foreach($expiringContracts as $contract)
            <div class="bg-white rounded border border-red-200 shadow-sm p-4 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-orange-500"></div>
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <span class="inline-block px-2 py-1 bg-orange-50 text-orange-700 text-[10px] font-bold rounded uppercase mb-2">Sắp hết hạn</span>
                        <h3 class="font-bold text-gray-900">{{ $contract->customer->name ?? 'Khách hàng' }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Kiosk: <span class="font-semibold text-gray-700">{{ $contract->kiosk->code ?? 'N/A' }}</span></p>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-orange-50 flex items-center justify-center">
                        <i class="fa-solid fa-file-contract text-orange-500 text-sm"></i>
                    </div>
                </div>
                
                <div class="mt-4 pt-3 border-t border-gray-100 flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500">Ngày hết hạn</p>
                        <p class="font-bold text-orange-600 text-sm mt-0.5">{{ \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') }} <span class="text-xs text-gray-500 font-normal">({{ (int) $contract->days_remaining }} ngày tới)</span></p>
                    </div>
                    <a href="{{ route('admin.contracts.show', $contract->id) }}" class="text-[#006699] text-xs font-semibold hover:underline">Xem chi tiết <i class="fa-solid fa-arrow-right text-[10px] ml-1"></i></a>
                </div>
            </div>
            @endforeach
        @endif
        
        @if(($expiringContracts->isEmpty() && $unpaidPayments->isEmpty()) || ($filter === 'unpaid' && $unpaidPayments->isEmpty()) || ($filter === 'expiring' && $expiringContracts->isEmpty()))
        <div class="col-span-full py-12 flex flex-col items-center justify-center bg-white rounded border border-gray-200 border-dashed">
            <i class="fa-solid fa-circle-check text-green-400 text-4xl mb-3"></i>
            <h3 class="text-lg font-bold text-gray-700">Tuyệt vời!</h3>
            <p class="text-gray-500 text-sm mt-1">Hệ thống đang hoạt động ổn định, không có cảnh báo nào.</p>
        </div>
        @endif

    </div>
</div>
@endsection
