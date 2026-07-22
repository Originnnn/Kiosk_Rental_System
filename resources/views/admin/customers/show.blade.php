@extends('layouts.admin')

@section('title', 'Chi tiết Khách thuê - Bến Xe Huế')

@section('content')
<div class="bg-gray-50 min-h-screen p-6 font-sans">
    
    <!-- Top actions -->
    <div class="mb-4 flex items-center">
        <a href="{{ route('admin.customers.index') }}" class="text-[#006699] hover:underline font-semibold text-sm flex items-center">
            <i class="fa-solid fa-arrow-left mr-2"></i> Quay lại danh sách
        </a>
    </div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center">
            <h1 class="text-2xl font-bold text-gray-900 mr-4">{{ $customer->name }}</h1>
            @if($customer->status == 'active')
                <span class="px-2 py-1 text-xs font-bold border rounded uppercase bg-green-100 text-green-700 border-green-200">ACTIVE</span>
            @elseif($customer->status == 'pending')
                <span class="px-2 py-1 text-xs font-bold border rounded uppercase bg-orange-100 text-orange-700 border-orange-200">PENDING</span>
            @else
                <span class="px-2 py-1 text-xs font-bold border rounded uppercase bg-gray-200 text-gray-600 border-gray-300">INACTIVE</span>
            @endif
        </div>
        <div class="flex space-x-3">
            @can('is-employee')
            <a href="{{ route('admin.customers.edit', $customer->id) }}" class="bg-[#006699] hover:bg-[#005580] text-white px-4 py-2 rounded font-medium text-sm shadow-sm flex items-center transition">
                <i class="fa-regular fa-pen-to-square mr-2"></i> Chỉnh sửa hồ sơ
            </a>
            <form action="{{ route('admin.customers.toggleStatus', $customer->id) }}" method="POST" class="inline-block">
                @csrf
                @method('PATCH')
                <button type="submit" class="bg-white border {{ $customer->status == 'active' ? 'border-red-500 text-red-500 hover:bg-red-50' : 'border-green-500 text-green-500 hover:bg-green-50' }} px-4 py-2 rounded font-medium text-sm shadow-sm flex items-center transition">
                    @if($customer->status == 'active')
                        <i class="fa-solid fa-lock mr-2"></i> Khoá tài khoản
                    @else
                        <i class="fa-solid fa-lock-open mr-2"></i> Mở khoá tài khoản
                    @endif
                </button>
            </form>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-4 rounded mb-4 text-sm font-medium border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex items-start space-x-6">
        
        <!-- Cột Trái (Thông tin chi tiết) -->
        <div class="w-1/3 space-y-6">
            
            <!-- 1. Thông tin liên hệ -->
            <div class="bg-white rounded border border-gray-200 p-6 shadow-sm">
                <h2 class="text-base font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2 flex items-center">
                    <i class="fa-regular fa-address-card mr-2 text-gray-400"></i> Thông tin liên hệ
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase mb-1">Mã Khách Hàng</p>
                        <p class="text-sm font-bold text-[#006699]">{{ $customer->customer_code ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase mb-1">Số điện thoại</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $customer->phone }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase mb-1">Email</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $customer->email ?? 'Chưa cập nhật' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase mb-1">Địa chỉ thường trú</p>
                        <p class="text-sm text-gray-700">{{ $customer->address ?? 'Chưa cập nhật' }}</p>
                    </div>
                </div>
            </div>

            <!-- 2. Thông tin định danh (CCCD) -->
            <div class="bg-white rounded border border-gray-200 p-6 shadow-sm">
                <h2 class="text-base font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2 flex items-center">
                    <i class="fa-regular fa-id-card mr-2 text-gray-400"></i> Thông tin định danh
                </h2>
                
                <div class="mb-4">
                    <p class="text-xs text-gray-500 font-bold uppercase mb-1">Số CCCD / CMND</p>
                    <p class="text-sm font-bold text-gray-900 tracking-wider">{{ $customer->id_card_number ?? 'N/A' }}</p>
                </div>
                
                <div class="space-y-4 mt-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-2 font-medium">Mặt trước CCCD:</p>
                        @if($customer->id_card_front)
                            <div class="border border-gray-200 rounded p-1">
                                <img src="{{ asset('storage/' . $customer->id_card_front) }}" alt="CCCD Mặt Trước" class="w-full h-auto rounded">
                            </div>
                        @else
                            <div class="border border-dashed border-gray-300 rounded p-4 text-center bg-gray-50 text-gray-400 text-sm">
                                Chưa tải lên ảnh mặt trước
                            </div>
                        @endif
                    </div>
                    
                    <div>
                        <p class="text-xs text-gray-500 mb-2 font-medium">Mặt sau CCCD:</p>
                        @if($customer->id_card_back)
                            <div class="border border-gray-200 rounded p-1">
                                <img src="{{ asset('storage/' . $customer->id_card_back) }}" alt="CCCD Mặt Sau" class="w-full h-auto rounded">
                            </div>
                        @else
                            <div class="border border-dashed border-gray-300 rounded p-4 text-center bg-gray-50 text-gray-400 text-sm">
                                Chưa tải lên ảnh mặt sau
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <!-- Cột Phải (Lịch sử hợp đồng) -->
        <div class="w-2/3">
            <div class="bg-white rounded border border-gray-200 p-6 shadow-sm">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                    <h2 class="text-lg font-bold text-[#006699] flex items-center">
                        <i class="fa-solid fa-file-contract mr-2"></i> Lịch sử hợp đồng ({{ $customer->contracts->count() }})
                    </h2>
                    <a href="{{ route('admin.contracts.create') }}?customer_id={{ $customer->id }}" class="text-[#006699] text-sm hover:underline font-medium">+ Tạo hợp đồng mới</a>
                </div>
                
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 border-y border-gray-200">
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Mã Hợp Đồng</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Kiosk / Quầy</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Ngày bắt đầu</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Ngày kết thúc</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase text-center">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($customer->contracts as $contract)
                            @php
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
                                <td class="px-4 py-3 text-sm font-bold text-gray-900">{{ $contract->reference_code }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $contract->kiosk->code ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 {{ $isExpiringSoon || $isExpired ? 'text-red-500 font-semibold' : '' }}">{{ \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold rounded w-24 {{ $statusClass }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">Khách hàng này chưa có hợp đồng nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
