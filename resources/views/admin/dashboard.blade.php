@extends('layouts.admin')

@section('title', 'Dashboard - Bến Xe Huế')

@section('content')
<div class="bg-gray-50 min-h-screen p-6 font-sans">
    
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Tổng quan kinh doanh</h1>
            <p class="text-sm text-gray-500">Cập nhật lúc {{ now()->format('h:i A') }}, Hôm nay</p>
        </div>
        
        <div class="flex space-x-3">
            <button class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded font-medium flex items-center text-sm shadow-sm transition">
                <i class="fa-regular fa-calendar mr-2"></i> Tháng này
            </button>
            <button id="btnExportReport" class="bg-[#006699] hover:bg-[#005580] text-white px-4 py-2 rounded font-bold flex items-center text-sm shadow-sm transition">
                <i class="fa-solid fa-download mr-2"></i> Xuất báo cáo
            </button>
        </div>
    </div>

    <div class="flex space-x-6">
        
        <!-- Main Content (Left: 70%) -->
        <div class="w-[70%] flex flex-col space-y-6">
            
            <!-- Top KPIs (Grid 4 columns) -->
            <div class="grid grid-cols-4 gap-4">
                
                <!-- KPI 1: Doanh thu tháng -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 flex flex-col justify-between h-32">
                    <div class="flex justify-between items-start">
                        <span class="text-sm font-bold text-gray-700">Doanh thu tháng</span>
                        <div class="w-8 h-8 rounded bg-[#e6f0f5] text-[#006699] flex items-center justify-center">
                            <i class="fa-solid fa-money-bill-wave"></i>
                        </div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($currentMonthRev / 1000000, 1) }}M đ</div>
                        <div class="text-xs font-semibold mt-1 {{ $monthGrowth >= 0 ? 'text-green-500' : 'text-red-500' }}">
                            <i class="fa-solid {{ $monthGrowth >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }} mr-1"></i>
                            {{ $monthGrowth > 0 ? '+' : '' }}{{ $monthGrowth }}% so với tháng trước
                        </div>
                    </div>
                </div>

                <!-- KPI 2: Doanh thu quý -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 flex flex-col justify-between h-32">
                    <div class="flex justify-between items-start">
                        <span class="text-sm font-bold text-gray-700">Doanh thu quý</span>
                        <div class="w-8 h-8 rounded bg-[#fdf2e9] text-[#d35400] flex items-center justify-center">
                            <i class="fa-solid fa-building-columns"></i>
                        </div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($currentQuarterRev / 1000000000, 2) }}B đ</div>
                        <div class="text-xs font-semibold mt-1 {{ $quarterGrowth >= 0 ? 'text-green-500' : 'text-red-500' }}">
                            <i class="fa-solid {{ $quarterGrowth >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }} mr-1"></i>
                            {{ $quarterGrowth > 0 ? '+' : '' }}{{ $quarterGrowth }}% so với quý trước
                        </div>
                    </div>
                </div>

                <!-- KPI 3: Tỷ lệ lấp đầy -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 flex flex-col justify-between h-32">
                    <div class="flex justify-between items-start">
                        <span class="text-sm font-bold text-gray-700">Tỷ lệ lấp đầy</span>
                        <div class="w-8 h-8 rounded bg-[#eef2fd] text-[#4f46e5] flex items-center justify-center">
                            <i class="fa-solid fa-store"></i>
                        </div>
                    </div>
                    <div class="flex items-end">
                        <div class="text-2xl font-bold text-gray-900 mr-3">{{ $occupancyRate }}%</div>
                        <div class="text-xs text-gray-500 pb-1">
                            <span class="font-bold text-gray-900">{{ $rentedKiosks }}/{{ $totalKiosks }}</span><br>Kiosk đang hoạt động
                        </div>
                    </div>
                </div>

                <!-- KPI 4: Hợp đồng hiệu lực -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 flex flex-col justify-between h-32">
                    <div class="flex justify-between items-start">
                        <span class="text-sm font-bold text-gray-700">Hợp đồng hiệu lực</span>
                        <div class="w-8 h-8 rounded bg-gray-100 text-gray-600 flex items-center justify-center">
                            <i class="fa-solid fa-file-contract"></i>
                        </div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ $activeContractsCount }}</div>
                        <div class="text-xs font-semibold mt-1 text-yellow-600">
                            <i class="fa-solid fa-circle-info mr-1"></i>
                            {{ $expiringCountTotal }} HĐ sắp hết hạn (30 ngày)
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex-1 flex flex-col">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-base font-bold text-gray-900">Biểu đồ doanh thu (6 tháng)</h2>
                    <button class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-ellipsis"></i>
                    </button>
                </div>
                
                <div class="flex-1 relative w-full h-[300px]">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Bottom Widgets Row -->
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">Tỷ lệ lấp đầy theo khu vực</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center text-sm">
                            <div class="flex items-center text-gray-600"><div class="w-3 h-3 bg-[#006699] mr-2"></div> Khu A (Ẩm thực)</div>
                            <div class="font-bold">40%</div>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <div class="flex items-center text-gray-600"><div class="w-3 h-3 bg-[#3b82f6] mr-2"></div> Khu B (Bán lẻ)</div>
                            <div class="font-bold">30%</div>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <div class="flex items-center text-gray-600"><div class="w-3 h-3 bg-[#93c5fd] mr-2"></div> Khu C (Dịch vụ)</div>
                            <div class="font-bold">20%</div>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <div class="flex items-center text-gray-600"><div class="w-3 h-3 bg-[#dbeafe] mr-2"></div> Khu Trống</div>
                            <div class="font-bold">10%</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">Trạng thái hệ thống</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center text-sm">
                            <div class="flex items-center text-gray-600"><i class="fa-solid fa-server text-green-500 w-5"></i> Server Kiosk Center</div>
                            <div class="px-2 py-0.5 bg-green-50 text-green-600 text-xs font-bold rounded uppercase">ONLINE</div>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <div class="flex items-center text-gray-600"><i class="fa-solid fa-network-wired text-green-500 w-5"></i> Mạng nội bộ bến xe</div>
                            <div class="px-2 py-0.5 bg-green-50 text-green-600 text-xs font-bold rounded uppercase">ỔN ĐỊNH</div>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <div class="flex items-center text-gray-600"><i class="fa-solid fa-rotate text-yellow-500 w-5"></i> Đồng bộ dữ liệu POS</div>
                            <div class="px-2 py-0.5 bg-yellow-50 text-yellow-600 text-xs font-bold rounded uppercase">ĐANG TRỄ (5P)</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Panel: Alerts (30%) -->
        <div class="w-[30%] bg-white rounded-lg shadow-sm border border-gray-200 flex flex-col h-full overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                <h2 class="text-base font-bold text-red-600 flex items-center">
                    <i class="fa-solid fa-triangle-exclamation mr-2"></i> Cần xử lý gấp
                </h2>
                <div class="w-6 h-6 rounded-full bg-red-500 text-white text-xs font-bold flex items-center justify-center">
                    {{ count($unpaidPayments) + count($expiringContracts) }}
                </div>
            </div>
            
            <div class="flex-1 overflow-y-auto p-4 space-y-3">
                
                <!-- Unpaid Payments (Red Border) -->
                @foreach($unpaidPayments as $payment)
                <div class="border-l-4 border-red-500 bg-white shadow-sm border border-gray-200 rounded p-4">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-sm font-bold text-gray-900 pr-2">{{ $payment->contract->customer->name ?? 'Khách hàng' }}</h3>
                        <span class="text-[10px] font-bold text-red-600 bg-red-50 px-2 py-1 rounded whitespace-nowrap">Quá hạn {{ now()->diffInDays($payment->due_date) }} ngày</span>
                    </div>
                    <p class="text-xs text-gray-600 mb-3">
                        Chưa thanh toán phí thuê Kiosk tháng {{ \Carbon\Carbon::parse($payment->due_date)->format('m/Y') }} 
                        ({{ number_format($payment->amount, 0, ',', '.') }} VNĐ).
                    </p>
                    <a href="#" class="text-xs font-bold text-[#006699] hover:underline">Xem chi tiết</a>
                </div>
                @endforeach

                <!-- Expiring Contracts (Yellow Border) -->
                @foreach($expiringContracts as $contract)
                <div class="border-l-4 border-yellow-500 bg-white shadow-sm border border-gray-200 rounded p-4">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-sm font-bold text-gray-900 pr-2">{{ $contract->customer->name ?? 'Khách hàng' }}</h3>
                        <span class="text-[10px] font-bold text-yellow-700 bg-yellow-50 px-2 py-1 rounded whitespace-nowrap">Sắp hết hạn</span>
                    </div>
                    <p class="text-xs text-gray-600 mb-3">
                        Hợp đồng thuê Kiosk {{ $contract->kiosk->code ?? '' }} sẽ hết hạn trong {{ now()->diffInDays($contract->end_date) }} ngày tới.
                    </p>
                    <a href="#" class="text-xs font-bold text-[#006699] hover:underline">Tạo phụ lục gia hạn</a>
                </div>
                @endforeach

                <!-- Static manual alert matching design -->
                <div class="border-l-4 border-red-500 bg-white shadow-sm border border-gray-200 rounded p-4">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-sm font-bold text-gray-900 pr-2">Quán Phở Khô Gia Lai</h3>
                        <span class="text-[10px] font-bold text-red-600 bg-red-50 px-2 py-1 rounded whitespace-nowrap">Vi phạm nội quy</span>
                    </div>
                    <p class="text-xs text-gray-600 mb-3">
                        Bảo vệ báo cáo vi phạm lấn chiếm hành lang lần 2.
                    </p>
                    <a href="#" class="text-xs font-bold text-[#006699] hover:underline">Gửi thông báo phạt</a>
                </div>

                <div class="border-l-4 border-[#006699] bg-white shadow-sm border border-gray-200 rounded p-4">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-sm font-bold text-gray-900 pr-2">Kiosk B-05 (Trống)</h3>
                        <span class="text-[10px] font-bold text-[#006699] bg-blue-50 px-2 py-1 rounded whitespace-nowrap">Yêu cầu thuê mới</span>
                    </div>
                    <p class="text-xs text-gray-600 mb-3">
                        Có 2 khách hàng đang chờ duyệt hồ sơ thuê Kiosk B-05.
                    </p>
                    <a href="#" class="text-xs font-bold text-[#006699] hover:underline">Duyệt hồ sơ</a>
                </div>
                
            </div>

            <div class="p-4 border-t border-gray-200 bg-white mt-auto">
                <button class="w-full py-2 border border-gray-300 text-gray-700 rounded font-bold text-sm hover:bg-gray-50 transition">
                    Xem tất cả cảnh báo
                </button>
            </div>
        </div>

    </div>
</div>

<!-- Modal Xuất Báo Cáo -->
<div id="exportModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <!-- Overlay -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" id="exportOverlay"></div>

    <!-- Modal Content -->
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 z-10 transform transition-all">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 rounded-t-lg">
            <h3 class="text-lg font-bold text-gray-900" id="exportModalTitle">Đang xuất báo cáo...</h3>
            <button type="button" id="closeExportModal" class="text-gray-400 hover:text-gray-500 focus:outline-none hidden">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <div class="p-6 text-center" id="exportModalBody">
            <div id="exportLoading" class="py-4">
                <i class="fa-solid fa-circle-notch fa-spin text-4xl text-[#006699] mb-4"></i>
                <p class="text-gray-600">Hệ thống đang trích xuất dữ liệu, vui lòng đợi...</p>
            </div>
            
            <div id="exportSuccess" class="hidden py-4">
                <i class="fa-solid fa-circle-check text-5xl text-green-500 mb-4"></i>
                <p class="text-gray-900 font-medium mb-4">Báo cáo đã được tạo thành công!</p>
                
                <div class="flex flex-col space-y-3">
                    <a href="#" id="viewReportBtn" target="_blank" class="w-full bg-[#006699] hover:bg-[#005580] text-white font-medium py-2.5 px-4 rounded transition shadow-sm">
                        <i class="fa-solid fa-arrow-up-right-from-square mr-2"></i> Mở xem báo cáo
                    </a>
                    <button type="button" id="copyLinkBtn" class="w-full bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-2.5 px-4 rounded transition shadow-sm">
                        <i class="fa-regular fa-copy mr-2"></i> Sao chép đường link
                    </button>
                </div>
            </div>
            
            <div id="exportError" class="hidden py-4">
                <i class="fa-solid fa-circle-xmark text-5xl text-red-500 mb-4"></i>
                <p class="text-gray-900 font-medium mb-2">Có lỗi xảy ra!</p>
                <p class="text-gray-500 text-sm" id="exportErrorText">Không thể tạo báo cáo lúc này.</p>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        const labels = {!! json_encode($barLabels) !!};
        const data = {!! json_encode($barData) !!};

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Doanh thu',
                    data: data,
                    backgroundColor: '#006699',
                    borderRadius: 4,
                    barThickness: 12, // thin bars as per design
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' VNĐ';
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f3f4f6',
                            drawBorder: false,
                        },
                        ticks: {
                            callback: function(value) {
                                if (value === 0) return '0';
                                if (value >= 1000000000) return (value / 1000000000) + 'B';
                                if (value >= 1000000) return (value / 1000000) + 'M';
                                return value;
                            },
                            color: '#9ca3af',
                            font: {
                                size: 11
                            }
                        },
                        border: { display: false }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: true,
                            borderColor: '#e5e7eb'
                        },
                        ticks: {
                            color: '#6b7280',
                            font: {
                                size: 11,
                                weight: 'bold'
                            }
                        }
                    }
                }
            }
        });

        // Xử lý xuất báo cáo
        const btnExport = document.getElementById('btnExportReport');
        const exportModal = document.getElementById('exportModal');
        const exportOverlay = document.getElementById('exportOverlay');
        const closeExportModal = document.getElementById('closeExportModal');
        const exportLoading = document.getElementById('exportLoading');
        const exportSuccess = document.getElementById('exportSuccess');
        const exportError = document.getElementById('exportError');
        const exportModalTitle = document.getElementById('exportModalTitle');
        const viewReportBtn = document.getElementById('viewReportBtn');
        const copyLinkBtn = document.getElementById('copyLinkBtn');

        function hideModal() {
            exportModal.classList.add('hidden');
        }

        exportOverlay.addEventListener('click', hideModal);
        closeExportModal.addEventListener('click', hideModal);

        btnExport.addEventListener('click', async function() {
            // Hiển thị modal loading
            exportModal.classList.remove('hidden');
            exportLoading.classList.remove('hidden');
            exportSuccess.classList.add('hidden');
            exportError.classList.add('hidden');
            closeExportModal.classList.add('hidden');
            exportModalTitle.textContent = 'Đang xuất báo cáo...';

            try {
                const response = await fetch('{{ route("admin.reports.export") }}', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                    }
                });

                const result = await response.json();

                if (response.ok && result.status === 'success') {
                    // Thành công
                    exportLoading.classList.add('hidden');
                    exportSuccess.classList.remove('hidden');
                    closeExportModal.classList.remove('hidden');
                    exportModalTitle.textContent = 'Xuất báo cáo thành công';
                    
                    viewReportBtn.href = result.url;
                    
                    copyLinkBtn.onclick = function() {
                        navigator.clipboard.writeText(result.url).then(() => {
                            const originalText = copyLinkBtn.innerHTML;
                            copyLinkBtn.innerHTML = '<i class="fa-solid fa-check text-green-500 mr-2"></i> Đã sao chép!';
                            setTimeout(() => {
                                copyLinkBtn.innerHTML = originalText;
                            }, 2000);
                        });
                    };
                } else {
                    throw new Error(result.message || 'Lỗi từ máy chủ');
                }
            } catch (error) {
                // Lỗi
                exportLoading.classList.add('hidden');
                exportError.classList.remove('hidden');
                closeExportModal.classList.remove('hidden');
                exportModalTitle.textContent = 'Xuất báo cáo thất bại';
                document.getElementById('exportErrorText').textContent = error.message;
            }
        });
    });
</script>
@endsection
