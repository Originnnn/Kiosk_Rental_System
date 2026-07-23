@extends('layouts.admin')

@section('title', 'Quản lý thanh toán - Bến Xe Huế')

@section('content')
<div class="bg-white min-h-screen flex flex-col m-0 p-0 font-sans">
    
    <!-- Top Header / Breadcrumb -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">HỆ THỐNG <i class="fa-solid fa-angle-right mx-1 text-gray-400"></i> <span class="text-primary">QUẢN LÝ THANH TOÁN</span></p>
                <h1 class="text-2xl font-bold text-gray-900 mb-1">Danh sách thanh toán</h1>
                <p class="text-sm text-gray-500">Theo dõi và xử lý các giao dịch tài chính của các quầy Kiosk.</p>
            </div>
            
            <!-- KPIs -->
            <div class="flex space-x-4">
                <div class="bg-white border border-gray-200 rounded-lg p-3 flex items-center shadow-sm w-64">
                    <div class="w-10 h-10 rounded bg-green-50 text-green-600 flex items-center justify-center mr-3">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase">DOANH THU THÁNG</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($currentMonthRevenue, 0, ',', '.') }}đ</p>
                    </div>
                </div>
                
                <div class="bg-white border border-gray-200 rounded-lg p-3 flex items-center shadow-sm w-48">
                    <div class="w-10 h-10 rounded bg-red-50 text-red-500 flex items-center justify-center mr-3">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase">KHOẢN NỢ QUÁ HẠN</p>
                        <p class="text-lg font-bold text-gray-900">{{ number_format($overdueAmount, 0, ',', '.') }}đ</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="px-6 py-5 bg-gray-50 border-b border-gray-200">
        <form method="GET" action="{{ route('admin.payments.index') }}" class="flex items-end space-x-4">
            
            <!-- Tìm kiếm -->
            <div class="w-1/3">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Tìm kiếm</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                    </div>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Mã hợp đồng, khách thuê..." 
                        class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-primary focus:border-primary text-sm">
                </div>
            </div>

            <!-- Trạng thái -->
            <div class="w-1/4">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Trạng thái</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-primary focus:border-primary text-sm text-gray-700">
                    <option value="">Tất cả trạng thái</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                    <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Chờ thanh toán / Quá hạn</option>
                </select>
            </div>

            <!-- Thời gian -->
            <div class="w-1/4">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Khoảng thời gian</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-regular fa-calendar text-gray-400"></i>
                    </div>
                    <input type="text" name="date_range" value="{{ request('date_range') }}" placeholder="01/10/2023 - 31/10/2023" 
                        class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-primary focus:border-primary text-sm">
                </div>
            </div>

            <!-- Nút Lọc -->
            <div class="flex space-x-2">
                <button type="submit" class="bg-[#006699] hover:bg-[#005580] text-white px-5 py-2 rounded font-medium flex items-center text-sm transition-colors shadow-sm">
                    <i class="fa-solid fa-filter mr-2"></i> Lọc dữ liệu
                </button>
                <a href="{{ route('admin.payments.index') }}" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-600 px-3 py-2 rounded flex items-center text-sm transition-colors shadow-sm">
                    <i class="fa-solid fa-rotate-right"></i>
                </a>
            </div>
            
        </form>
    </div>

    <!-- Bảng dữ liệu -->
    <div class="flex-1 overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-4 text-xs font-bold text-gray-700 uppercase">Mã giao dịch</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-700 uppercase">Khách thuê</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-700 uppercase">Quầy/Kiosk</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-700 uppercase">Kỳ thanh toán</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-700 uppercase">Số tiền</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-700 uppercase">Hạn thanh toán</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-700 uppercase text-center">Trạng thái</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-700 uppercase text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($payments as $payment)
                    @php
                        $contractCode = $payment->contract->reference_code ?? 'N/A';
                        $customerName = $payment->contract->customer->name ?? 'Unknown';
                        $kioskName = $payment->contract->kiosk->code ?? 'Unknown';
                        
                        $isOverdue = $payment->status == 'unpaid' && \Carbon\Carbon::parse($payment->due_date)->endOfDay()->isPast();
                        
                        if ($payment->status == 'paid') {
                            $statusText = 'Đã thanh toán';
                            $statusClass = 'bg-green-100 text-green-700';
                        } elseif ($isOverdue) {
                            $statusText = 'Quá hạn';
                            $statusClass = 'bg-red-100 text-red-700';
                        } else {
                            $statusText = 'Chờ thanh toán';
                            $statusClass = 'bg-yellow-100 text-yellow-700';
                        }

                        $initials = mb_substr($customerName, 0, 2);
                        $avatarColors = ['bg-blue-100 text-blue-700', 'bg-orange-100 text-orange-700', 'bg-pink-100 text-pink-700', 'bg-purple-100 text-purple-700'];
                        $avatarClass = $avatarColors[$payment->id % 4];
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <!-- Mã Hợp đồng thay cho mã giao dịch (do ko có) -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-[#006699] font-medium text-sm">{{ $contractCode }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs uppercase {{ $avatarClass }} mr-3">
                                    {{ $initials }}
                                </div>
                                <span class="text-sm font-semibold text-gray-900">{{ $customerName }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $kioskName }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            T{{ \Carbon\Carbon::parse($payment->due_date)->format('m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                            {{ number_format($payment->amount, 0, ',', '.') }}đ
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ $isOverdue ? 'text-red-500 font-medium' : 'text-gray-700' }}">
                            {{ \Carbon\Carbon::parse($payment->due_date)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex flex-col items-center justify-center {{ $statusClass }} text-[11px] font-bold px-2 py-1 rounded w-20 leading-tight">
                                {{ $statusText }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-3">
                            <button type="button" class="btn-view-details text-primary hover:text-[#005580]"
                                data-contract="{{ $contractCode }}"
                                data-customer="{{ $customerName }}"
                                data-kiosk="{{ $kioskName }}"
                                data-period="T{{ \Carbon\Carbon::parse($payment->due_date)->format('m/Y') }}"
                                data-amount="{{ number_format($payment->amount, 0, ',', '.') }}đ"
                                data-status-text="{{ $statusText }}"
                                data-status-class="{{ $statusClass }}"
                                data-due-date="{{ \Carbon\Carbon::parse($payment->due_date)->format('d/m/Y') }}"
                                data-actual-amount="{{ $payment->actual_amount ? number_format($payment->actual_amount, 0, ',', '.') . 'đ' : 'N/A' }}"
                                data-paid-at="{{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y H:i') : 'N/A' }}"
                                data-method="{{ $payment->payment_method ?? 'N/A' }}"
                                data-notes="{{ $payment->notes ?? 'Không có ghi chú' }}">
                                <i class="fa-regular fa-eye text-lg"></i>
                            </button>
                            
                            @can('is-employee')
                            @if($payment->status != 'paid')
                                <a href="{{ route('admin.payments.form', $payment->id) }}" class="text-green-500 hover:text-green-700" title="Ghi nhận thanh toán">
                                    <i class="fa-regular fa-circle-check text-lg"></i>
                                </a>
                            @endif
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                            Không tìm thấy giao dịch nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
        <div class="text-sm text-gray-600">
            Hiển thị {{ $payments->firstItem() ?? 0 }} - {{ $payments->lastItem() ?? 0 }} trong số {{ $payments->total() }} giao dịch
        </div>
        <div>
            {{ $payments->links('pagination::tailwind') }}
        </div>

    <!-- Modal Xem Chi Tiết -->
    <div id="paymentModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 modal-overlay" aria-hidden="true"></div>

            <!-- Modal panel -->
            <div class="relative inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <div class="flex items-start justify-between mb-4 border-b pb-3">
                        <h3 class="text-lg font-bold text-gray-900" id="modal-title">
                            Chi tiết giao dịch
                        </h3>
                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center close-modal">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center bg-gray-50 p-3 rounded">
                            <span class="text-sm font-semibold text-gray-500">Trạng thái</span>
                            <span id="modal-status" class="inline-flex flex-col items-center justify-center text-[11px] font-bold px-2 py-1 rounded w-28 leading-tight"></span>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold mb-1">Mã hợp đồng</p>
                                <p id="modal-contract" class="text-sm font-semibold text-gray-900"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold mb-1">Khách thuê</p>
                                <p id="modal-customer" class="text-sm font-semibold text-gray-900"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold mb-1">Kiosk</p>
                                <p id="modal-kiosk" class="text-sm font-semibold text-gray-900"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold mb-1">Kỳ thanh toán</p>
                                <p id="modal-period" class="text-sm font-semibold text-gray-900"></p>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-4 grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold mb-1">Số tiền cần thu</p>
                                <p id="modal-amount" class="text-sm font-bold text-[#006699]"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold mb-1">Hạn thanh toán</p>
                                <p id="modal-due-date" class="text-sm font-semibold text-gray-900"></p>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-4 grid grid-cols-2 gap-4 bg-green-50 p-3 rounded mt-2">
                            <div>
                                <p class="text-xs text-green-700 uppercase font-bold mb-1">Thực thu</p>
                                <p id="modal-actual-amount" class="text-sm font-bold text-green-600"></p>
                            </div>
                            <div>
                                <p class="text-xs text-green-700 uppercase font-bold mb-1">Ngày đóng</p>
                                <p id="modal-paid-at" class="text-sm font-semibold text-gray-900"></p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-xs text-green-700 uppercase font-bold mb-1">Hình thức</p>
                                <p id="modal-method" class="text-sm font-semibold text-gray-900"></p>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-3">
                            <p class="text-xs text-gray-500 uppercase font-bold mb-1">Ghi chú</p>
                            <p id="modal-notes" class="text-sm text-gray-700 italic"></p>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                    <button type="button" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm close-modal">
                        Đóng lại
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modal = document.getElementById('paymentModal');
        const overlay = document.querySelector('.modal-overlay');
        const closeBtns = document.querySelectorAll('.close-modal');
        const viewBtns = document.querySelectorAll('.btn-view-details');

        function openModal(data) {
            document.getElementById('modal-contract').textContent = data.contract;
            document.getElementById('modal-customer').textContent = data.customer;
            document.getElementById('modal-kiosk').textContent = data.kiosk;
            document.getElementById('modal-period').textContent = data.period;
            document.getElementById('modal-amount').textContent = data.amount;
            document.getElementById('modal-due-date').textContent = data.dueDate;
            document.getElementById('modal-actual-amount').textContent = data.actualAmount;
            document.getElementById('modal-paid-at').textContent = data.paidAt;
            document.getElementById('modal-method').textContent = data.method;
            document.getElementById('modal-notes').textContent = data.notes;

            const statusEl = document.getElementById('modal-status');
            statusEl.textContent = data.statusText;
            statusEl.className = `inline-flex flex-col items-center justify-center text-[11px] font-bold px-2 py-1 rounded w-28 leading-tight ${data.statusClass}`;

            modal.classList.remove('hidden');
        }

        function closeModal() {
            modal.classList.add('hidden');
        }

        viewBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                openModal(this.dataset);
            });
        });

        closeBtns.forEach(btn => btn.addEventListener('click', closeModal));
        overlay.addEventListener('click', closeModal);
    });
</script>
@endsection
