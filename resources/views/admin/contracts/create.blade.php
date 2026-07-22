@extends('layouts.admin')

@section('title', 'Tạo mới hợp đồng - Bến Xe Huế')

@section('content')
<div class="bg-gray-50 min-h-screen p-6 font-sans">
    
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Tạo mới hợp đồng</h1>
            <p class="text-sm text-gray-500">Thiết lập thông tin cho hợp đồng thuê Kiosk mới.</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.contracts.index') }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded font-medium text-sm hover:bg-gray-100 transition-colors">
                Hủy
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-red-50 text-red-600 p-4 rounded mb-4 text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif
    
    @if ($errors->any())
        <div class="bg-red-50 text-red-600 p-4 rounded mb-4 text-sm font-medium">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.contracts.store') }}" method="POST" id="contractForm" class="flex items-start space-x-6">
        @csrf
        
        <!-- Cột Trái (Form Nhập Liệu) -->
        <div class="w-2/3 space-y-6">
            
            <!-- 1. Thông tin khách thuê -->
            <div class="bg-white rounded border-l-4 border-l-[#006699] border-t border-r border-b border-gray-200 p-6 shadow-sm">
                <h2 class="text-lg font-bold text-[#006699] mb-4 flex items-center">
                    <i class="fa-solid fa-user-group mr-2"></i> 1. Thông tin khách thuê
                </h2>
                
                <div class="mb-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase">CHỌN KHÁCH THUÊ</label>
                    <select name="customer_id" id="customer_id" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-primary focus:border-primary text-sm bg-white" required>
                        <option value="">-- Vui lòng chọn khách hàng --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} (SĐT: {{ $customer->phone }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- 2. Chọn Quầy / Kiosk -->
            <div class="bg-white rounded border border-gray-200 p-6 shadow-sm">
                <h2 class="text-lg font-bold text-[#006699] mb-4 flex items-center">
                    <i class="fa-solid fa-store mr-2"></i> 2. Chọn Quầy / Kiosk
                </h2>
                
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase">CHỌN QUẦY TRỐNG</label>
                    <select name="kiosk_id" id="kiosk_id" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-primary focus:border-primary text-sm bg-white" required>
                        <option value="">-- Vui lòng chọn Kiosk --</option>
                        @foreach($kiosks as $kiosk)
                            <option value="{{ $kiosk->id }}" 
                                data-code="{{ $kiosk->code }}" 
                                data-area="{{ $kiosk->area }}" 
                                data-price="{{ $kiosk->price }}"
                                {{ old('kiosk_id') == $kiosk->id ? 'selected' : '' }}>
                                {{ $kiosk->name }} ({{ $kiosk->code }}) - {{ $kiosk->area }}m²
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-3 gap-4 border-t border-gray-100 pt-4 bg-gray-50 px-4 py-3 rounded">
                    <div>
                        <p class="text-xs text-gray-500 font-bold mb-1">Mã Kiosk</p>
                        <p class="text-sm font-semibold text-gray-900" id="display_kiosk_code">...</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-bold mb-1">Diện tích</p>
                        <p class="text-sm font-semibold text-gray-900"><span id="display_kiosk_area">...</span> m²</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-bold mb-1">Đơn giá cơ sở / tháng</p>
                        <p class="text-sm font-bold text-[#006699]"><span id="display_kiosk_price">...</span> đ</p>
                    </div>
                </div>
            </div>

            <!-- 3. Thời gian thuê -->
            <div class="bg-white rounded border border-gray-200 p-6 shadow-sm">
                <h2 class="text-lg font-bold text-[#006699] mb-4 flex items-center">
                    <i class="fa-regular fa-calendar-days mr-2"></i> 3. Thời gian thuê
                </h2>
                
                <div class="grid grid-cols-2 gap-6 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase">Ngày bắt đầu</label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', date('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-primary focus:border-primary text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase">Ngày kết thúc</label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date', date('Y-m-d', strtotime('+1 year'))) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-primary focus:border-primary text-sm" required>
                    </div>
                </div>

                <div class="bg-orange-50 border border-orange-200 rounded p-3 flex items-start">
                    <i class="fa-solid fa-circle-info text-orange-500 mt-0.5 mr-2"></i>
                    <p class="text-xs text-orange-700 font-medium" id="duration_warning">Thời hạn hợp đồng tối thiểu là 1 tháng. <span id="duration_text"></span></p>
                </div>
            </div>

            <!-- 4. Giá thuê & Thanh toán -->
            <div class="bg-white rounded border border-gray-200 p-6 shadow-sm">
                <h2 class="text-lg font-bold text-[#006699] mb-4 flex items-center">
                    <i class="fa-solid fa-money-bill-wave mr-2"></i> 4. Giá thuê & Thanh toán
                </h2>
                
                <div class="grid grid-cols-2 gap-6 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase">Giá thuê thực tế (VNĐ / Tháng)</label>
                        <input type="number" name="actual_price_per_month" id="actual_price_per_month" value="{{ old('actual_price_per_month') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-primary focus:border-primary text-sm font-semibold" required>
                        <p class="text-xs text-gray-500 mt-1">Có thể điều chỉnh so với giá cơ sở</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase">Tiền cọc (VNĐ)</label>
                        <input type="number" name="deposit_amount" id="deposit_amount" value="{{ old('deposit_amount') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-primary focus:border-primary text-sm font-semibold" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase">Chu kỳ thanh toán</label>
                        <select name="payment_cycle" id="payment_cycle" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-primary focus:border-primary text-sm bg-white" required>
                            <option value="1" {{ old('payment_cycle') == '1' ? 'selected' : '' }}>1 tháng / lần</option>
                            <option value="3" {{ old('payment_cycle') == '3' ? 'selected' : '' }}>3 tháng / lần</option>
                            <option value="6" {{ old('payment_cycle') == '6' ? 'selected' : '' }}>6 tháng / lần</option>
                            <option value="12" {{ old('payment_cycle') == '12' ? 'selected' : '' }}>12 tháng / lần</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase">Hình thức thanh toán</label>
                        <select name="payment_method_pref" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-primary focus:border-primary text-sm bg-white">
                            <option value="Chuyển khoản">Chuyển khoản</option>
                            <option value="Tiền mặt">Tiền mặt</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Hidden input to store total_amount for the backend -->
            <input type="hidden" name="total_amount" id="hidden_total_amount" value="0">
        </div>

        <!-- Cột Phải (Tóm tắt hợp đồng - Sticky) -->
        <div class="w-1/3 sticky top-6">
            <div class="bg-white rounded border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-base font-bold text-gray-900">Tóm tắt hợp đồng</h3>
                </div>
                
                <div class="p-6 space-y-4">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Thời hạn</span>
                        <span class="font-bold text-gray-900" id="summary_months">0 tháng</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Giá thuê / tháng</span>
                        <span class="font-medium text-gray-900" id="summary_price">0 đ</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Tiền cọc</span>
                        <span class="font-medium text-gray-900" id="summary_deposit">0 đ</span>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-4 flex justify-between items-center">
                        <span class="text-sm font-bold text-gray-900">Tổng giá trị (dự kiến)</span>
                        <span class="text-lg font-bold text-[#006699]" id="summary_total">0 đ</span>
                    </div>

                    <div class="bg-gray-50 rounded p-4 border border-gray-200 mt-4">
                        <p class="text-xs text-gray-500 font-bold uppercase mb-1">Thanh toán đợt 1</p>
                        <p class="text-xs text-gray-500 mb-2">Cọc + <span id="summary_cycle_text">1</span> tháng đầu</p>
                        <p class="text-lg font-bold text-gray-900 text-right" id="summary_first_payment">0 đ</p>
                    </div>

                    <button type="submit" class="w-full bg-[#006699] hover:bg-[#005580] text-white px-4 py-3 rounded font-bold text-sm transition-colors shadow-sm flex items-center justify-center mt-6">
                        <i class="fa-solid fa-file-signature mr-2"></i> Tạo hợp đồng
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const kioskSelect = document.getElementById('kiosk_id');
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const actualPriceInput = document.getElementById('actual_price_per_month');
        const depositInput = document.getElementById('deposit_amount');
        const cycleSelect = document.getElementById('payment_cycle');
        
        // Displays
        const dKioskCode = document.getElementById('display_kiosk_code');
        const dKioskArea = document.getElementById('display_kiosk_area');
        const dKioskPrice = document.getElementById('display_kiosk_price');
        
        const dDurationText = document.getElementById('duration_text');
        
        const sMonths = document.getElementById('summary_months');
        const sPrice = document.getElementById('summary_price');
        const sDeposit = document.getElementById('summary_deposit');
        const sTotal = document.getElementById('summary_total');
        const sCycleText = document.getElementById('summary_cycle_text');
        const sFirstPayment = document.getElementById('summary_first_payment');
        const hTotalAmount = document.getElementById('hidden_total_amount');

        const formatter = new Intl.NumberFormat('vi-VN');

        function calculate() {
            // 1. Kiosk Data
            let basePrice = 0;
            if (kioskSelect.options[kioskSelect.selectedIndex]) {
                const opt = kioskSelect.options[kioskSelect.selectedIndex];
                if (opt.value) {
                    dKioskCode.textContent = opt.dataset.code;
                    dKioskArea.textContent = opt.dataset.area;
                    basePrice = parseFloat(opt.dataset.price);
                    dKioskPrice.textContent = formatter.format(basePrice);
                    
                    // Auto fill actual price and deposit if empty
                    if (!actualPriceInput.value && !document.activeElement.isEqualNode(actualPriceInput)) {
                        actualPriceInput.value = basePrice;
                    }
                    if (!depositInput.value && !document.activeElement.isEqualNode(depositInput)) {
                        depositInput.value = basePrice * 2; // Default 2 months deposit
                    }
                } else {
                    dKioskCode.textContent = '...';
                    dKioskArea.textContent = '...';
                    dKioskPrice.textContent = '...';
                }
            }

            // 2. Duration
            const start = new Date(startDateInput.value);
            const end = new Date(endDateInput.value);
            let months = 0;
            if (start && end && end >= start) {
                // Calculate roughly the difference in months
                months = (end.getFullYear() - start.getFullYear()) * 12;
                months -= start.getMonth();
                months += end.getMonth();
                if (end.getDate() < start.getDate()) {
                    months--;
                }
                // Handle cases where dates are very close (less than a month)
                if (months <= 0) months = 1;
                
                dDurationText.textContent = `Đã hợp lệ (${months} tháng).`;
                dDurationText.className = "text-green-700 font-bold ml-1";
            } else {
                dDurationText.textContent = "Ngày không hợp lệ!";
                dDurationText.className = "text-red-600 font-bold ml-1";
            }
            sMonths.textContent = `${months} tháng`;

            // 3. Values
            const actualPrice = parseFloat(actualPriceInput.value) || 0;
            const deposit = parseFloat(depositInput.value) || 0;
            const cycle = parseInt(cycleSelect.value) || 1;

            sPrice.textContent = `${formatter.format(actualPrice)} đ`;
            sDeposit.textContent = `${formatter.format(deposit)} đ`;

            // 4. Totals
            const total = actualPrice * months;
            sTotal.textContent = `${formatter.format(total)} đ`;
            hTotalAmount.value = total; // Save for backend

            sCycleText.textContent = cycle;
            const firstPayment = deposit + (actualPrice * cycle);
            sFirstPayment.textContent = `${formatter.format(firstPayment)} đ`;
        }

        // Attach listeners
        const inputs = [kioskSelect, startDateInput, endDateInput, actualPriceInput, depositInput, cycleSelect];
        inputs.forEach(el => {
            el.addEventListener('change', calculate);
            el.addEventListener('input', calculate);
        });

        // Initial calc
        calculate();
    });
</script>
@endsection
