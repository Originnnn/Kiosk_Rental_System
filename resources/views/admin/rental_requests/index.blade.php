@extends('layouts.internal')

@section('title', 'Quản lý Yêu cầu thuê')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Quản lý Yêu cầu thuê Kiosk</h1>
            <p class="text-sm text-gray-500 mt-1">Danh sách các yêu cầu đặt thuê từ khách hàng</p>
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khách hàng</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số điện thoại</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã Kiosk</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">T/g Thuê</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="requests-tbody">
                    @forelse($requests as $req)
                        <tr id="req-row-{{ $req->id }}" class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $req->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $req->customer_name }}</div>
                                @if($req->business_type)
                                    <div class="text-xs text-gray-500 truncate w-32" title="{{ $req->business_type }}">{{ $req->business_type }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $req->phone }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-bold bg-gray-100 text-gray-700 rounded">{{ $req->kiosk->code ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $req->duration_months }} tháng
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $badgeClass = 'bg-gray-100 text-gray-800';
                                    $badgeText = 'Unknown';
                                    if ($req->status === 'pending') {
                                        $badgeClass = 'bg-yellow-100 text-yellow-800';
                                        $badgeText = 'Chờ tiếp nhận';
                                    } elseif ($req->status === 'processing') {
                                        $badgeClass = 'bg-blue-100 text-blue-800';
                                        $badgeText = 'Đang xử lý';
                                    } elseif ($req->status === 'resolved') {
                                        $badgeClass = 'bg-green-100 text-green-800';
                                        $badgeText = 'Đã duyệt';
                                    } elseif ($req->status === 'rejected') {
                                        $badgeClass = 'bg-red-100 text-red-800';
                                        $badgeText = 'Đã từ chối';
                                    }
                                @endphp
                                <span id="status-badge-{{ $req->id }}" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeClass }}">
                                    {{ $badgeText }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" id="actions-{{ $req->id }}">
                                @if($req->status === 'pending')
                                    <button onclick="updateStatus({{ $req->id }}, 'processing')" class="text-blue-600 hover:text-blue-900 transition mr-3 font-semibold">Tiếp nhận</button>
                                    <button onclick="updateStatus({{ $req->id }}, 'rejected')" class="text-red-600 hover:text-red-900 transition font-semibold">Từ chối</button>
                                @elseif($req->status === 'processing')
                                    <button onclick="updateStatus({{ $req->id }}, 'resolved')" class="text-green-600 hover:text-green-900 transition mr-3 font-semibold">Duyệt hợp đồng</button>
                                    <button onclick="updateStatus({{ $req->id }}, 'rejected')" class="text-red-600 hover:text-red-900 transition font-semibold">Từ chối</button>
                                @else
                                    <span class="text-gray-400 italic">Đã chốt</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                                Không có yêu cầu thuê nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="admin-toast" class="fixed bottom-5 right-5 z-50 hidden transition-all duration-300 transform translate-y-10 opacity-0">
    <div class="bg-gray-900 text-white px-6 py-3 rounded-lg shadow-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span id="admin-toast-message" class="font-medium text-sm"></span>
    </div>
</div>

<script>
async function updateStatus(id, newStatus) {
    if (!confirm('Bạn có chắc chắn muốn cập nhật trạng thái yêu cầu này?')) {
        return;
    }

    try {
        const response = await fetch(`/admin/rental-requests/${id}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: newStatus })
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.message || 'Lỗi cập nhật');
        }
        
        // Show Toast
        const toast = document.getElementById('admin-toast');
        document.getElementById('admin-toast-message').innerText = data.message;
        toast.classList.remove('hidden');
        setTimeout(() => toast.classList.remove('translate-y-10', 'opacity-0'), 10);
        setTimeout(() => {
            toast.classList.add('translate-y-10', 'opacity-0');
            setTimeout(() => toast.classList.add('hidden'), 300);
        }, 3000);
        
        // Cập nhật UI (Badge và Nút thao tác)
        const badge = document.getElementById(`status-badge-${id}`);
        const actionsCol = document.getElementById(`actions-${id}`);
        
        if (newStatus === 'processing') {
            badge.className = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800';
            badge.innerText = 'Đang xử lý';
            
            actionsCol.innerHTML = `
                <button onclick="updateStatus(${id}, 'resolved')" class="text-green-600 hover:text-green-900 transition mr-3 font-semibold">Duyệt hợp đồng</button>
                <button onclick="updateStatus(${id}, 'rejected')" class="text-red-600 hover:text-red-900 transition font-semibold">Từ chối</button>
            `;
        } else if (newStatus === 'resolved') {
            badge.className = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800';
            badge.innerText = 'Đã duyệt';
            actionsCol.innerHTML = `<span class="text-gray-400 italic">Đã chốt</span>`;
        } else if (newStatus === 'rejected') {
            badge.className = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800';
            badge.innerText = 'Đã từ chối';
            actionsCol.innerHTML = `<span class="text-gray-400 italic">Đã chốt</span>`;
        }
        
    } catch (error) {
        alert('Lỗi: ' + error.message);
    }
}
</script>
@endsection
