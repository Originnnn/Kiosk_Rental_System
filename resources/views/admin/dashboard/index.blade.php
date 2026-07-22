@extends('layouts.internal')

@section('title', 'Dashboard Quản trị OLAP')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard Quản trị Kiosk (OLAP)</h1>
            <p class="text-sm text-gray-500 mt-1">Dữ liệu phân tích trực tiếp từ Data Warehouse (kiosk_dwh)</p>
        </div>
    </div>

    <!-- 2 Thẻ Summary (Cards) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Tổng doanh thu -->
        <div class="bg-white rounded-lg shadow p-6 flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Tổng Doanh Thu Hợp Đồng</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalRevenue, 0, ',', '.') }}đ</p>
            </div>
        </div>

        <!-- Tổng số lượt thuê -->
        <div class="bg-white rounded-lg shadow p-6 flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Tổng Số Lượt Thuê</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalRentals, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Charts Area -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <!-- Biểu đồ phân bổ khu vực (Doughnut) -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Lượt Thuê Theo Khu Vực</h2>
            <div class="relative h-64 w-full">
                <canvas id="zoneDoughnutChart"></canvas>
            </div>
        </div>

        <!-- Biểu đồ doanh thu theo tháng (Bar) -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Doanh Thu Theo Tháng</h2>
            <div class="relative h-64 w-full">
                <canvas id="revenueBarChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Nhúng thư viện Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ==========================================
    // 1. DOUGHNUT CHART: Lượt thuê theo khu vực
    // ==========================================
    const doughnutLabels = @json($doughnutLabels);
    const doughnutData = @json($doughnutData);
    
    const ctxDoughnut = document.getElementById('zoneDoughnutChart').getContext('2d');
    new Chart(ctxDoughnut, {
        type: 'doughnut',
        data: {
            labels: doughnutLabels,
            datasets: [{
                data: doughnutData,
                // Mảng màu sắc tùy ý
                backgroundColor: [
                    '#3B82F6', // Blue
                    '#10B981', // Green
                    '#F59E0B', // Yellow
                    '#EF4444', // Red
                    '#8B5CF6'  // Purple
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            },
            cutout: '65%'
        }
    });

    // ==========================================
    // 2. BAR CHART: Doanh thu theo tháng
    // ==========================================
    const barChartLabels = @json($barChartLabels);
    const barChartData = @json($barChartData);
    
    const ctxBar = document.getElementById('revenueBarChart').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: barChartLabels,
            datasets: [{
                label: 'Doanh Thu (VNĐ)',
                data: barChartData,
                backgroundColor: '#6366F1', // Indigo
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('vi-VN') + 'đ';
                        }
                    }
                }
            },
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
                                label += context.parsed.y.toLocaleString('vi-VN') + 'đ';
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection
