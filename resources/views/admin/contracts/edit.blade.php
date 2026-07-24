@extends('layouts.admin')

@section('title', 'Chỉnh sửa hợp đồng - Bến Xe Huế')

@section('content')
<div class="bg-gray-50 min-h-screen p-6 font-sans">
    
    <form action="{{ route('admin.contracts.update', $contract->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">
                    <a href="{{ route('admin.contracts.index') }}" class="hover:underline">Hợp đồng</a> 
                    <i class="fa-solid fa-angle-right mx-1 text-xs"></i> Chỉnh sửa hợp đồng
                </p>
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-900 mr-4">{{ $contract->reference_code }}</h1>
                    <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded border border-blue-200">ĐANG HIỆU LỰC</span>
                    <span class="text-xs text-gray-500 ml-4">Cập nhật lần cuối: {{ $contract->updated_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.contracts.show', $contract->id) }}" class="bg-white border border-gray-300 text-gray-700 px-5 py-2 rounded font-medium text-sm hover:bg-gray-50 shadow-sm">
                    Hủy
                </a>
                <button type="submit" class="bg-[#006699] hover:bg-[#005580] text-white px-5 py-2 rounded font-medium text-sm shadow-sm flex items-center">
                    Lưu thay đổi
                </button>
            </div>
        </div>

        <div class="bg-orange-50 border-l-4 border-orange-400 p-4 mb-6 rounded shadow-sm flex items-start">
            <i class="fa-solid fa-circle-info text-orange-500 mt-0.5 mr-3"></i>
            <p class="text-sm text-orange-800">
                <span class="font-bold">Lưu ý:</span> Hợp đồng đang ở trạng thái Đang hiệu lực. Chỉ các trường thông tin phụ (không ảnh hưởng đến giá trị cốt lõi) mới được phép chỉnh sửa. Các trường bị khóa hiển thị biểu tượng ổ khóa.
            </p>
        </div>

        <div class="flex items-start space-x-6">
            
            <!-- Cột Trái (Khóa) -->
            <div class="w-2/3 space-y-6">
                
                <!-- Thông tin cốt lõi -->
                <div class="bg-white rounded border border-gray-200 p-6 shadow-sm">
                    <h2 class="text-base font-bold text-gray-900 mb-4 flex items-center border-b border-gray-100 pb-2">
                        <i class="fa-solid fa-file-lines mr-2 text-gray-400"></i> Thông tin cốt lõi
                    </h2>
                    
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-2">Số hợp đồng <i class="fa-solid fa-lock ml-1 text-gray-400"></i></label>
                            <input type="text" value="{{ $contract->reference_code }}" class="w-full px-3 py-2 border border-gray-200 rounded bg-gray-50 text-gray-500 text-sm" disabled>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-2">Khách thuê <i class="fa-solid fa-lock ml-1 text-gray-400"></i></label>
                            <input type="text" value="{{ $contract->customer->name ?? '' }}" class="w-full px-3 py-2 border border-gray-200 rounded bg-gray-50 text-gray-500 text-sm" disabled>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-2">Kiosk / Quầy <i class="fa-solid fa-lock ml-1 text-gray-400"></i></label>
                            <input type="text" value="{{ $contract->kiosk->code ?? '' }} ({{ $contract->kiosk->name ?? '' }})" class="w-full px-3 py-2 border border-gray-200 rounded bg-gray-50 text-gray-500 text-sm" disabled>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-2">Loại hợp đồng <i class="fa-solid fa-lock ml-1 text-gray-400"></i></label>
                            <input type="text" value="Thuê dài hạn ({{ max(1, \Carbon\Carbon::parse($contract->end_date)->diffInMonths($contract->start_date)) }} tháng)" class="w-full px-3 py-2 border border-gray-200 rounded bg-gray-50 text-gray-500 text-sm" disabled>
                        </div>
                    </div>
                </div>

                <!-- Giá trị & Thời hạn -->
                <div class="bg-white rounded border border-gray-200 p-6 shadow-sm">
                    <h2 class="text-base font-bold text-gray-900 mb-4 flex items-center border-b border-gray-100 pb-2">
                        <i class="fa-solid fa-money-bill-wave mr-2 text-gray-400"></i> Giá trị & Thời hạn
                    </h2>
                    
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-2">Ngày bắt đầu <i class="fa-solid fa-lock ml-1 text-gray-400"></i></label>
                            <input type="text" value="{{ \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') }}" class="w-full px-3 py-2 border border-gray-200 rounded bg-gray-50 text-gray-500 text-sm" disabled>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-2">Ngày kết thúc <i class="fa-solid fa-lock ml-1 text-gray-400"></i></label>
                            <input type="text" value="{{ \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') }}" class="w-full px-3 py-2 border border-gray-200 rounded bg-gray-50 text-gray-500 text-sm" disabled>
                        </div>
                        
                        @php
                            $months = max(1, \Carbon\Carbon::parse($contract->end_date)->diffInMonths($contract->start_date));
                            $monthlyPrice = $contract->total_amount / $months;
                        @endphp

                        <div class="relative">
                            <label class="block text-xs font-semibold text-gray-500 mb-2">Đơn giá thuê (tháng) <i class="fa-solid fa-lock ml-1 text-gray-400"></i></label>
                            <input type="text" value="{{ number_format($monthlyPrice, 0, ',', '.') }}" class="w-full pl-3 pr-12 py-2 border border-gray-200 rounded bg-gray-50 text-gray-500 text-sm" disabled>
                            <span class="absolute right-3 top-8 text-xs text-gray-400 font-bold">VNĐ</span>
                        </div>
                        <div class="relative">
                            <label class="block text-xs font-semibold text-gray-500 mb-2">Tiền cọc <i class="fa-solid fa-lock ml-1 text-gray-400"></i></label>
                            <input type="text" value="{{ number_format($contract->deposit_amount, 0, ',', '.') }}" class="w-full pl-3 pr-12 py-2 border border-gray-200 rounded bg-gray-50 text-gray-500 text-sm" disabled>
                            <span class="absolute right-3 top-8 text-xs text-gray-400 font-bold">VNĐ</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Cột Phải (Mở Khóa) -->
            <div class="w-1/3 space-y-6">
                
                <!-- Thông tin bổ sung -->
                <div class="bg-white rounded border-t-4 border-t-[#006699] border-l border-r border-b border-gray-200 p-6 shadow-sm">
                    <h2 class="text-base font-bold text-[#006699] mb-4 flex items-center border-b border-gray-100 pb-2">
                        <i class="fa-solid fa-pen-to-square mr-2"></i> Thông tin bổ sung (Có thể sửa)
                    </h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Người phụ trách (Bến xe)</label>
                            <select name="manager_name" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-primary focus:border-primary text-sm bg-white">
                                <option value="Trần Thị B (NV Hợp Đồng)" {{ $contract->manager_name == 'Trần Thị B (NV Hợp Đồng)' ? 'selected' : '' }}>Trần Thị B (NV Hợp Đồng)</option>
                                <option value="Lê Văn C (Kế Toán)" {{ $contract->manager_name == 'Lê Văn C (Kế Toán)' ? 'selected' : '' }}>Lê Văn C (Kế Toán)</option>
                                <option value="Nguyễn Văn A (Quản lý)" {{ $contract->manager_name == 'Nguyễn Văn A (Quản lý)' ? 'selected' : '' }}>Nguyễn Văn A (Quản lý)</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Người liên hệ trực tiếp (Khách thuê)</label>
                            <input type="text" name="contact_name" value="{{ old('contact_name', $contract->contact_name) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-primary focus:border-primary text-sm bg-white">
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">SĐT liên hệ trực tiếp</label>
                            <input type="text" name="contact_phone" value="{{ old('contact_phone', $contract->contact_phone) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-primary focus:border-primary text-sm bg-white">
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Ghi chú thêm</label>
                            <textarea name="notes" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-primary focus:border-primary text-sm bg-white">{{ old('notes', $contract->notes) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Tài liệu đính kèm -->
                <div class="bg-white rounded border border-gray-200 p-6 shadow-sm" x-data="{ 
                    existingFiles: {{ json_encode($contract->attachments ?? []) }}.map(f => typeof f === 'string' ? {path: f, name: f.split('/').pop()} : f),
                    removedFiles: [],
                    newFiles: [],
                    removeExisting(fileObj) {
                        this.removedFiles.push(fileObj.path);
                        this.existingFiles = this.existingFiles.filter(f => f.path !== fileObj.path);
                    },
                    removeNew() {
                        this.$refs.fileInput.value = '';
                        this.newFiles = [];
                    },
                    handleFileChange(event) {
                        this.newFiles = Array.from(event.target.files).map(f => ({ name: f.name }));
                    }
                }">
                    <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-2">
                        <h2 class="text-base font-bold text-gray-900 flex items-center">
                            <i class="fa-solid fa-paperclip mr-2 text-gray-400"></i> Tài liệu đính kèm
                        </h2>
                        <input type="file" name="new_attachments[]" multiple class="hidden" x-ref="fileInput" @change="handleFileChange" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <button type="button" @click="$refs.fileInput.click()" class="text-[#006699] text-sm hover:underline font-medium">
                            + Thêm <span x-show="newFiles.length > 0" x-text="`(${newFiles.length})`"></span>
                        </button>
                    </div>
                    
                    <div class="space-y-3">
                        <!-- Existing files list -->
                        <template x-for="file in existingFiles" :key="file.path">
                            <div class="flex items-center justify-between p-3 border border-gray-100 rounded bg-gray-50 group">
                                <div class="flex items-center flex-1 overflow-hidden">
                                    <i class="fa-solid fa-file-lines text-gray-400 text-lg mr-3"></i>
                                    <span class="text-sm font-medium text-gray-700 truncate" x-text="file.name"></span>
                                </div>
                                <button type="button" @click="removeExisting(file)" class="text-red-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition-opacity ml-3" title="Xóa tài liệu này">
                                    <i class="fa-solid fa-xmark text-lg"></i>
                                </button>
                            </div>
                        </template>

                        <!-- New files list -->
                        <template x-if="newFiles.length > 0">
                            <div class="flex items-center justify-between p-3 border border-blue-200 rounded bg-blue-50 group">
                                <div class="flex items-center flex-1 overflow-hidden">
                                    <i class="fa-solid fa-file-circle-plus text-blue-500 text-lg mr-3"></i>
                                    <span class="text-sm font-medium text-blue-800 truncate">
                                        <span x-text="newFiles.length"></span> tệp mới được chọn...
                                    </span>
                                </div>
                                <button type="button" @click="removeNew()" class="text-red-400 hover:text-red-600 ml-3" title="Hủy bỏ">
                                    <i class="fa-solid fa-xmark text-lg"></i>
                                </button>
                            </div>
                        </template>

                        <!-- Empty state -->
                        <template x-if="existingFiles.length === 0 && newFiles.length === 0">
                            <div class="text-sm text-gray-400 italic text-center py-4">
                                Chưa có tài liệu đính kèm
                            </div>
                        </template>
                        
                        <!-- Hidden inputs for removed files -->
                        <template x-for="file in removedFiles">
                            <input type="hidden" name="remove_attachments[]" :value="file">
                        </template>
                    </div>
                </div>

            </div>

        </div>
    </form>
</div>
@endsection
