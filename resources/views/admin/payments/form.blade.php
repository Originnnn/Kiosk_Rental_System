@extends('layouts.admin')

@section('title', 'Ghi nhận thanh toán - Bến Xe Huế')

@section('content')
<div class="bg-[#F4F5F7] min-h-screen font-sans p-6 m-0">

    <!-- Header / Breadcrumb -->
    <div class="mb-6">
        <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">
            <a href="#" class="hover:underline">Quản lý thanh toán</a> <i class="fa-solid fa-angle-right mx-1"></i> <span class="text-gray-900">Ghi nhận thanh toán mới</span>
        </p>
        <h1 class="text-2xl font-bold text-gray-900">Ghi nhận thanh toán</h1>
    </div>

    <!-- Main Layout -->
    <div class="flex gap-6 max-w-6xl" x-data="paymentForm()">
        
        <!-- CỘT TRÁI (Form & Thông tin) -->
        <div class="flex-1 flex flex-col gap-6">
            
            <!-- Card 1: Thông tin hợp đồng -->
            <div class="bg-blue-50 border border-blue-100 rounded-lg p-5 flex justify-between items-start relative shadow-sm">
                <!-- Badge Góc Phải -->
                <div class="absolute top-4 right-4 bg-[#D1FAE5] text-[#065F46] text-[10px] font-bold px-2 py-1 rounded uppercase">
                    ĐANG HIỆU LỰC
                </div>

                <div class="flex flex-col w-full">
                    <h3 class="text-primary font-bold text-sm mb-4 flex items-center">
                        <i class="fa-solid fa-file-contract mr-2"></i> Thông tin hợp đồng đang chọn
                    </h3>
                    
                    <div class="grid grid-cols-4 gap-4">
                        <div>
                            <p class="text-[10px] font-bold text-gray-500 uppercase mb-1">MÃ HỢP ĐỒNG</p>
                            <p class="text-sm text-gray-900">{{ $payment->contract->reference_code ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-500 uppercase mb-1">KHÁCH THUÊ</p>
                            <p class="text-sm text-gray-900">{{ $payment->contract->customer->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-500 uppercase mb-1">KIOSK</p>
                            <p class="text-sm text-gray-900">{{ $payment->contract->kiosk->code ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-primary uppercase mb-1">SỐ TIỀN CẦN ĐÓNG</p>
                            <p class="text-base font-bold text-primary">{{ number_format($payment->amount, 0, ',', '.') }} đ</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Biểu mẫu nhập liệu -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-gray-900">Biểu mẫu nhập liệu</h3>
                </div>
                
                <div class="p-6">
                    <form action="{{ route('admin.payments.process', $payment->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <!-- Ngày thực đóng -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Ngày thực đóng <span class="text-red-500">*</span></label>
                                <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-primary focus:border-primary text-sm">
                            </div>
                            
                            <!-- Số tiền thực đóng -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Số tiền thực đóng (VNĐ) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="number" name="actual_amount" id="actual_amount" required min="0" x-model="enteringAmount"
                                        class="w-full pl-3 pr-8 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-primary focus:border-primary text-sm">
                                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 text-sm pointer-events-none">đ</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <!-- Hình thức thanh toán -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Hình thức thanh toán <span class="text-red-500">*</span></label>
                                <select name="payment_method" required class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-primary focus:border-primary text-sm">
                                    <option value="Chuyển khoản">Chuyển khoản</option>
                                    <option value="Tiền mặt">Tiền mặt</option>
                                    <option value="Thẻ tín dụng">Thẻ tín dụng</option>
                                </select>
                            </div>
                            
                            <!-- Chứng từ đính kèm -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Chứng từ đính kèm (Ảnh/PDF)</label>
                                <div class="border-2 border-dashed border-gray-300 rounded bg-gray-50 text-center py-2 relative hover:bg-gray-100 transition cursor-pointer" id="file-upload-container">
                                    <input type="file" name="document" id="receipt_file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept=".jpg,.jpeg,.png,.pdf">
                                    <span class="text-xs text-gray-500" id="file-upload-text"><i class="fa-solid fa-cloud-arrow-up mr-1 text-primary"></i> <span class="text-primary font-medium">Nhấn để tải lên</span> hoặc kéo thả file</span>
                                </div>
                                <div id="file-preview" class="hidden mt-2 flex flex-col gap-2">
                                </div>
                            </div>
                        </div>

                        <!-- Ghi chú -->
                        <div class="mb-8">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Ghi chú</label>
                            <textarea name="note" rows="3" placeholder="Nhập ghi chú chi tiết về giao dịch này..."
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-primary focus:border-primary text-sm"></textarea>
                        </div>

                        <!-- Nút Submit -->
                        <div class="flex justify-end space-x-3 border-t border-gray-200 pt-4 mt-4">
                            <a href="{{ route('admin.payments.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 font-semibold rounded hover:bg-gray-50 transition text-sm">
                                Hủy
                            </a>
                            <button type="submit" class="px-6 py-2 bg-[#006699] text-white font-semibold rounded hover:bg-[#005580] transition flex items-center text-sm shadow-sm">
                                <i class="fa-regular fa-circle-check mr-2"></i> Xác nhận thanh toán
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI (Summary) -->
        <div class="w-[340px] flex flex-col gap-6 flex-shrink-0">
            
            <!-- Card 3: Tóm tắt giao dịch -->
            <div class="bg-[#F9FAFB] border border-gray-200 rounded-lg shadow-sm">
                <!-- Header Đen -->
                <div class="bg-[#2B3139] text-white px-5 py-3 rounded-t-lg flex items-center">
                    <i class="fa-solid fa-receipt mr-2"></i>
                    <h3 class="font-bold text-sm">Tóm tắt giao dịch</h3>
                </div>
                
                <div class="p-5">
                    <!-- Dòng tiền -->
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-sm text-gray-600">Tổng kỳ:</span>
                        <span class="text-sm font-bold text-gray-900">{{ number_format($payment->amount, 0, ',', '.') }} đ</span>
                    </div>
                    
                    <div class="flex justify-between items-center mb-3 text-green-600 border-b border-gray-200 pb-3">
                        <span class="text-sm">Đã thu:</span>
                        <span class="text-sm font-bold">{{ number_format($payment->actual_amount ?? 0, 0, ',', '.') }} đ</span>
                    </div>
                    
                    <div class="flex justify-between items-center mb-6 pt-3">
                        <span class="text-sm font-bold text-gray-800">Cần đóng:</span>
                        <span class="text-xl font-bold text-[#006699]" x-text="formatCurrency(neededAmount)"></span>
                    </div>
                    
                    <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
                        <span class="text-sm text-green-600 flex items-center"><i class="fa-solid fa-plus-circle mr-1 text-xs"></i> Số tiền đang nhập:</span>
                        <span class="text-sm font-bold text-green-600" x-text="formatCurrency(enteringAmount)"></span>
                    </div>
                    
                    <div class="flex justify-between items-center mb-6">
                        <span class="text-sm font-bold text-gray-800">Nợ còn lại (sau thanh toán):</span>
                        <span class="text-xl font-bold" :class="remainingDebt > 0 ? 'text-red-500' : 'text-[#006699]'" x-text="formatCurrency(remainingDebt)"></span>
                    </div>
                    
                    <!-- Alert Box -->
                    <div class="bg-blue-50 border border-blue-100 rounded p-3 flex items-start mb-6">
                        <i class="fa-solid fa-circle-info text-blue-500 mt-0.5 mr-2"></i>
                        <p class="text-xs text-blue-800 leading-relaxed">
                            Hệ thống sẽ tự động gửi email thông báo xác nhận thanh toán cho khách thuê sau khi bạn nhấn "Xác nhận".
                        </p>
                    </div>

                    <!-- Lịch sử thanh toán -->
                    <div>
                        <h4 class="text-xs font-bold text-gray-500 uppercase mb-3">LỊCH SỬ THANH TOÁN GẦN NHẤT</h4>
                        <ul class="space-y-3">
                            @forelse($recentPayments as $recent)
                                <li class="flex items-start">
                                    <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center mr-3 flex-shrink-0">
                                        <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-gray-800">Tháng {{ \Carbon\Carbon::parse($recent->due_date)->format('m/Y') }}</p>
                                        <p class="text-xs text-green-600 font-medium">Đã hoàn tất</p>
                                    </div>
                                    <div class="text-sm font-bold text-gray-900">
                                        {{ number_format($recent->amount / 1000000, 1) }}M
                                    </div>
                                </li>
                            @empty
                                <li class="text-xs text-gray-500">Chưa có lịch sử thanh toán.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Card 4: Ảnh quảng cáo -->
            <div class="rounded-lg overflow-hidden relative shadow-sm border border-gray-200">
                <img src="https://images.unsplash.com/photo-1542744173-8e7e53415bb0?auto=format&fit=crop&q=80&w=400&h=200" alt="Hue Station BIZ" class="w-full h-32 object-cover opacity-80 filter grayscale">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                <div class="absolute bottom-3 left-4">
                    <p class="text-[10px] font-bold text-white/80 uppercase tracking-widest mb-0.5">HUE STATION BIZ</p>
                    <p class="text-sm font-bold text-white">Bảo mật & Minh bạch</p>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function paymentForm() {
        return {
            totalAmount: {{ $payment->amount ?? 0 }},
            paidAmount: {{ $payment->actual_amount ?? 0 }},
            neededAmount: 0,
            enteringAmount: {{ $payment->remaining_debt ?? $payment->amount ?? 0 }},
            
            init() {
                this.neededAmount = this.totalAmount - this.paidAmount;
            },
            
            get remainingDebt() {
                let entered = parseFloat(this.enteringAmount) || 0;
                return Math.max(0, this.neededAmount - entered);
            },
            
            formatCurrency(amount) {
                let val = parseFloat(amount) || 0;
                return val.toLocaleString('vi-VN') + ' đ';
            }
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Logic hiển thị file đính kèm (hỗ trợ chọn thêm lần lượt)
        const fileInput = document.getElementById('receipt_file');
        const filePreview = document.getElementById('file-preview');
        const uploadText = document.getElementById('file-upload-text');
        
        const dataTransfer = new DataTransfer();

        function renderPreviews() {
            filePreview.innerHTML = '';
            
            if (dataTransfer.files.length > 0) {
                filePreview.classList.remove('hidden');
                uploadText.innerHTML = `<span class="text-primary font-medium">Đã chọn ${dataTransfer.files.length} file đính kèm</span>`;
                
                Array.from(dataTransfer.files).forEach((file, index) => {
                    const fileItem = document.createElement('div');
                    fileItem.className = 'flex items-center justify-between bg-blue-50 border border-blue-100 px-3 py-2 rounded mb-2';
                    fileItem.innerHTML = `
                        <div class="flex items-center overflow-hidden">
                            <i class="fa-solid fa-file-invoice text-blue-500 mr-2 text-lg"></i>
                            <span class="text-sm font-medium text-blue-800 truncate" title="${file.name}">${file.name}</span>
                        </div>
                        <button type="button" class="ml-2 text-gray-400 hover:text-red-500 flex-shrink-0" onclick="removeFile(${index})">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    `;
                    filePreview.appendChild(fileItem);
                });
                
                // Nút xóa tất cả
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'text-xs text-red-500 hover:text-red-700 mt-1 self-start font-medium flex items-center';
                removeBtn.innerHTML = '<i class="fa-solid fa-trash-can mr-1"></i> Xóa tất cả';
                removeBtn.onclick = () => {
                    dataTransfer.items.clear();
                    fileInput.files = dataTransfer.files;
                    renderPreviews();
                };
                filePreview.appendChild(removeBtn);
            } else {
                filePreview.classList.add('hidden');
                uploadText.innerHTML = '<i class="fa-solid fa-cloud-arrow-up mr-1 text-primary"></i> <span class="text-primary font-medium">Nhấn để tải lên</span> hoặc kéo thả file';
            }
        }

        // Expose removeFile to global scope for the onclick handler
        window.removeFile = function(index) {
            dataTransfer.items.remove(index);
            fileInput.files = dataTransfer.files;
            renderPreviews();
        };

        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                Array.from(this.files).forEach(file => {
                    dataTransfer.items.add(file);
                });
                // Cập nhật lại input files từ dataTransfer
                this.files = dataTransfer.files;
            }
            renderPreviews();
        });
    });
</script>
@endsection
