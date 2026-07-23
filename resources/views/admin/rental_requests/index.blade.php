@extends('layouts.admin')

@section('title', 'Yêu cầu thuê - Bến Xe Huế')

@section('content')
<div class="bg-gray-50 min-h-screen p-6 font-sans">
    
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Yêu cầu thuê</h1>
            <p class="text-sm text-gray-500">Quản lý danh sách các yêu cầu đăng ký thuê Kiosk từ khách hàng.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-4 mb-6 text-sm font-medium border border-green-200 rounded">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-50 text-red-700 p-4 mb-6 text-sm font-medium border border-red-200 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Bảng dữ liệu -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-4 text-sm font-semibold text-gray-700">Ngày gửi</th>
                    <th class="px-6 py-4 text-sm font-semibold text-gray-700">Họ tên Khách hàng</th>
                    <th class="px-6 py-4 text-sm font-semibold text-gray-700">Số điện thoại</th>
                    <th class="px-6 py-4 text-sm font-semibold text-gray-700">Lĩnh vực kinh doanh</th>
                    <th class="px-6 py-4 text-sm font-semibold text-gray-700">Kiosk mong muốn</th>
                    <th class="px-6 py-4 text-sm font-semibold text-gray-700 text-center">Trạng thái</th>
                    <th class="px-6 py-4 text-sm font-semibold text-gray-700 text-center">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($requests as $req)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $req->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                            {{ $req->customer_name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $req->phone }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $req->business_type }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            @if($req->kiosk)
                                <span class="font-medium text-[#006699]">{{ $req->kiosk->name }} ({{ $req->kiosk->code }})</span>
                            @else
                                <span class="text-gray-400">Không rõ</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($req->status == 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold text-yellow-800 bg-yellow-100 uppercase tracking-wider border border-yellow-200">
                                    <i class="fa-solid fa-clock mr-1"></i> Chờ xử lý
                                </span>
                            @elseif($req->status == 'processing')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold text-blue-800 bg-blue-100 uppercase tracking-wider border border-blue-200">
                                    <i class="fa-solid fa-spinner mr-1"></i> Đang xử lý
                                </span>
                            @elseif($req->status == 'resolved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold text-green-800 bg-green-100 uppercase tracking-wider border border-green-200">
                                    <i class="fa-solid fa-check mr-1"></i> Đã chuyển HD
                                </span>
                            @elseif($req->status == 'rejected')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold text-gray-800 bg-gray-100 uppercase tracking-wider border border-gray-200">
                                    <i class="fa-solid fa-xmark mr-1"></i> Đã từ chối
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($req->status == 'pending' || $req->status == 'processing')
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- Nút Tạo hợp đồng -->
                                    <a href="{{ route('admin.contracts.create', [
                                        'kiosk_id' => $req->kiosk_id,
                                        'contact_name' => $req->customer_name,
                                        'contact_phone' => $req->phone,
                                        'duration_months' => $req->duration_months,
                                        'booking_request_id' => $req->id
                                    ]) }}" class="px-3 py-1.5 bg-[#006699] text-white text-xs font-bold rounded hover:bg-[#005580] transition-colors shadow-sm flex items-center">
                                        <i class="fa-solid fa-file-signature mr-1.5"></i> Tạo HD
                                    </a>
                                    
                                    <!-- Nút Từ chối -->
                                    <form action="{{ route('admin.rental_requests.updateStatus', $req->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn từ chối yêu cầu này không?');">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="px-3 py-1.5 bg-white border border-gray-300 text-red-600 text-xs font-bold rounded hover:bg-red-50 transition-colors shadow-sm flex items-center">
                                            <i class="fa-solid fa-ban mr-1.5"></i> Từ chối
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">Không có hành động</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            Không có yêu cầu thuê nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
