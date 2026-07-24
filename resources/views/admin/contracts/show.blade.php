@extends('layouts.admin')

@section('title', 'Chi tiết hợp đồng - Bến Xe Huế')

@section('content')
<div class="bg-gray-50 min-h-screen p-6 font-sans">
    
    <!-- Top actions -->
    <div class="mb-4 flex items-center">
        <a href="{{ route('admin.contracts.index') }}" class="text-[#006699] hover:underline font-semibold text-sm flex items-center">
            <i class="fa-solid fa-arrow-left mr-2"></i> Quay lại danh sách
        </a>
    </div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center">
            <h1 class="text-2xl font-bold text-gray-900 mr-4">Hợp đồng {{ $contract->reference_code }}</h1>
            @php
                $isExpired = \Carbon\Carbon::parse($contract->end_date)->isPast();
                $isExpiringSoon = \Carbon\Carbon::parse($contract->end_date)->diffInDays(now()) <= 30 && !$isExpired;
                
                if ($contract->status == 'cancelled') {
                    $badgeClass = 'bg-red-100 text-red-600 border-red-200';
                    $badgeText = 'HỦY';
                } elseif ($isExpired) {
                    $badgeClass = 'bg-gray-200 text-gray-600 border-gray-300';
                    $badgeText = 'HẾT HẠN';
                } elseif ($isExpiringSoon) {
                    $badgeClass = 'bg-yellow-100 text-yellow-700 border-yellow-200';
                    $badgeText = 'SẮP HẾT HẠN';
                } else {
                    $badgeClass = 'bg-green-100 text-green-700 border-green-200';
                    $badgeText = 'ĐANG HIỆU LỰC';
                }
            @endphp
            <span class="px-2 py-1 text-xs font-bold border rounded uppercase {{ $badgeClass }}">{{ $badgeText }}</span>
        </div>
        <div class="flex space-x-3">
            <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded font-medium text-sm hover:bg-gray-50 shadow-sm flex items-center">
                <i class="fa-solid fa-print mr-2"></i> In hợp đồng
            </button>
            @can('is-employee')
            <button class="bg-white border border-red-500 text-red-500 px-4 py-2 rounded font-medium text-sm hover:bg-red-50 shadow-sm flex items-center">
                <i class="fa-regular fa-circle-xmark mr-2"></i> Kết thúc sớm
            </button>
            <a href="{{ route('admin.contracts.edit', $contract->id) }}" class="bg-[#006699] hover:bg-[#005580] text-white px-4 py-2 rounded font-medium text-sm shadow-sm flex items-center">
                <i class="fa-solid fa-clock-rotate-left mr-2"></i> Gia hạn / Chỉnh sửa
            </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-4 rounded mb-4 text-sm font-medium border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex items-start space-x-6">
        
        <!-- Cột Trái -->
        <div class="w-2/3 space-y-6">
            
            <!-- 1. Thông tin chi tiết hợp đồng -->
            <div class="bg-white rounded border border-gray-200 p-6 shadow-sm">
                <h2 class="text-lg font-bold text-[#006699] mb-4 border-b border-gray-100 pb-3 flex items-center">
                    <i class="fa-solid fa-file-contract mr-2"></i> Thông tin chi tiết hợp đồng
                </h2>
                
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase mb-1">Mã hợp đồng</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $contract->reference_code }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase mb-1">Loại hợp đồng</p>
                        <p class="text-sm font-semibold text-gray-900">Thuê Kiosk dài hạn</p>
                    </div>
                    
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase mb-1">Ngày bắt đầu</p>
                        <p class="text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase mb-1">Ngày kết thúc</p>
                        <p class="text-sm font-semibold {{ $isExpiringSoon || $isExpired ? 'text-red-500' : 'text-orange-500' }}">
                            {{ \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') }} 
                            @php
                                $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($contract->end_date), false);
                            @endphp
                            @if($daysLeft > 0)
                                <span class="text-xs font-normal text-gray-500">(Còn {{ ceil($daysLeft) }} ngày)</span>
                            @else
                                <span class="text-xs font-normal text-gray-500">(Đã quá hạn {{ abs(ceil($daysLeft)) }} ngày)</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase mb-1">Giá thuê tháng</p>
                        <p class="text-base font-bold text-[#006699]">{{ number_format($contract->total_amount / max(1, \Carbon\Carbon::parse($contract->end_date)->diffInMonths($contract->start_date)), 0, ',', '.') }} VNĐ</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase mb-1">Tiền cọc</p>
                        <p class="text-sm font-semibold text-gray-900">{{ number_format($contract->deposit_amount, 0, ',', '.') }} VNĐ</p>
                    </div>
                </div>

                <div class="mt-6">
                    <p class="text-xs text-gray-500 font-bold uppercase mb-1">Ghi chú</p>
                    <p class="text-sm text-gray-700">{{ $contract->notes ?? 'Không có ghi chú.' }}</p>
                </div>
            </div>

            <!-- 2. Lịch sử thanh toán -->
            <div class="bg-white rounded border border-gray-200 p-6 shadow-sm">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                    <h2 class="text-lg font-bold text-[#006699] flex items-center">
                        <i class="fa-solid fa-receipt mr-2"></i> Lịch sử thanh toán
                    </h2>
                    <a href="{{ route('admin.payments.index', ['q' => $contract->reference_code]) }}" class="text-[#006699] text-sm hover:underline">Xem tất cả</a>
                </div>
                
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 border-y border-gray-200">
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Kỳ thanh toán</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Số tiền</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Ngày đóng</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase text-center">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($contract->paymentSchedules as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-700">Tháng {{ \Carbon\Carbon::parse($payment->due_date)->format('m/Y') }}</td>
                            <td class="px-4 py-3 text-sm font-bold text-gray-900">{{ number_format($payment->amount, 0, ',', '.') }} đ</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y') : '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($payment->status == 'paid')
                                    <span class="text-green-600 font-bold text-xs uppercase">ĐÃ THU</span>
                                @elseif(\Carbon\Carbon::parse($payment->due_date)->isPast())
                                    <span class="text-red-500 font-bold text-xs uppercase">QUÁ HẠN</span>
                                @else
                                    <span class="text-yellow-600 font-bold text-xs uppercase">CHỜ THU</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-sm text-gray-500">Chưa có dữ liệu thanh toán.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        <!-- Cột Phải -->
        <div class="w-1/3 space-y-6">
            
            <!-- 1. Khách thuê -->
            <div class="bg-white rounded border border-gray-200 p-6 shadow-sm">
                <h2 class="text-base font-bold text-gray-900 mb-4 flex items-center border-b border-gray-100 pb-2">
                    <i class="fa-regular fa-user mr-2 text-gray-400"></i> Khách thuê
                </h2>
                
                <div class="flex items-center mb-4">
                    @php $initials = mb_substr($contract->customer->name ?? 'A', 0, 2); @endphp
                    <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-lg mr-4 shrink-0">
                        {{ $initials }}
                    </div>
                    <div class="overflow-hidden flex-1">
                        <p class="font-bold text-gray-900 truncate">{{ $contract->customer->name ?? 'Không xác định' }}</p>
                        <p class="text-sm text-gray-500 flex items-center mt-1"><i class="fa-solid fa-phone w-4"></i> <span class="truncate">{{ $contract->customer->phone ?? 'N/A' }}</span></p>
                        @if($contract->customer && $contract->customer->email && !str_ends_with($contract->customer->email, '@noemail.local'))
                            <p class="text-sm text-gray-500 flex items-center mt-0.5"><i class="fa-regular fa-envelope w-4"></i> <span class="truncate">{{ $contract->customer->email }}</span></p>
                        @endif
                    </div>
                </div>
                
                <a href="{{ route('admin.customers.show', $contract->customer_id) }}" class="block text-center w-full bg-white border border-gray-300 text-[#006699] font-medium py-2 rounded text-sm hover:bg-gray-50 transition-colors">
                    Xem hồ sơ khách thuê
                </a>
            </div>

            <!-- 2. Tài sản cho thuê -->
            <div class="bg-white rounded border border-gray-200 p-6 shadow-sm">
                <h2 class="text-base font-bold text-gray-900 mb-4 flex items-center border-b border-gray-100 pb-2">
                    <i class="fa-solid fa-store mr-2 text-gray-400"></i> Tài sản cho thuê
                </h2>
                
                <div class="bg-gray-50 rounded p-4 border border-gray-200 relative">
                    <div class="flex justify-between items-start mb-2">
                        <p class="font-bold text-gray-900 text-lg">Kiosk {{ $contract->kiosk->code ?? 'N/A' }}</p>
                        <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-1 rounded">Khu A</span>
                    </div>
                    
                    <div class="text-sm text-gray-600 space-y-1">
                        <p><span class="text-gray-500 w-24 inline-block">Diện tích:</span> {{ $contract->kiosk->area ?? 0 }} m²</p>
                        <p><span class="text-gray-500 w-24 inline-block">Ngành hàng:</span> {{ $contract->kiosk->description ?? 'Đồ lưu niệm' }}</p>
                        <p><span class="text-gray-500 w-24 inline-block">Tình trạng:</span> <span class="text-green-600 font-medium">Tốt</span></p>
                    </div>
                </div>
            </div>

            <!-- 3. File đính kèm -->
            <div class="bg-white rounded border border-gray-200 p-6 shadow-sm">
                <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-2">
                    <h2 class="text-base font-bold text-gray-900 flex items-center">
                        <i class="fa-solid fa-paperclip mr-2 text-gray-400"></i> File đính kèm
                    </h2>
                </div>
                
                <div class="space-y-3 mb-4">
                    @if(isset($contract->attachments) && count($contract->attachments) > 0)
                        @foreach($contract->attachments as $attachment)
                            @php
                                $filePath = is_string($attachment) ? $attachment : ($attachment['path'] ?? '');
                                $fileName = is_string($attachment) ? basename($attachment) : ($attachment['name'] ?? basename($filePath));
                                $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                                $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']);
                                $iconClass = $isImage ? 'fa-image' : 'fa-file-pdf';
                                $iconBg = $isImage ? 'bg-blue-100 text-blue-500' : 'bg-red-100 text-red-500';
                            @endphp
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded hover:bg-gray-50">
                                <div class="flex items-center flex-1 overflow-hidden">
                                    <div class="w-8 h-8 rounded {{ $iconBg }} flex items-center justify-center mr-3 shrink-0">
                                        <i class="fa-solid {{ $iconClass }}"></i>
                                    </div>
                                    <div class="flex-1 overflow-hidden">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $fileName }}</p>
                                    </div>
                                </div>
                                <a href="{{ asset('storage/' . $filePath) }}" target="_blank" class="text-gray-400 hover:text-[#006699] ml-3" title="Tải xuống / Xem">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                            </div>
                        @endforeach
                    @else
                        <div class="text-sm text-gray-400 italic text-center py-2">
                            Chưa có tài liệu đính kèm
                        </div>
                    @endif
                </div>
                
                <a href="{{ route('admin.contracts.edit', $contract->id) }}" class="block text-center w-full border border-dashed border-gray-300 text-gray-600 font-medium py-2 rounded text-sm hover:bg-gray-50 hover:text-[#006699] hover:border-[#006699] transition-colors">
                    <i class="fa-solid fa-upload mr-2"></i> Quản lý tài liệu đính kèm
                </a>
            </div>

        </div>

    </div>
</div>
@endsection
