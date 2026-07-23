@extends('layouts.public')

@section('title', 'Gửi yêu cầu thuê')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-4">
        @if($kiosk)
            <a href="/kiosks/{{ $kiosk->id }}" class="text-blue-600 hover:underline">&larr; Quay lại Kiosk</a>
        @else
            <a href="/kiosks" class="text-blue-600 hover:underline">&larr; Quay lại danh sách Kiosk</a>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-blue-600 p-6 text-white text-center">
            <h2 class="text-2xl font-bold">Phiếu Yêu Cầu Thuê Kiosk</h2>
            <p class="mt-2 text-blue-100">Vui lòng điền đầy đủ thông tin để bộ phận kinh doanh liên hệ với bạn.</p>
        </div>
        
        <div class="p-6 md:p-8">
            @if($kiosk)
            <div class="bg-gray-50 border rounded-lg p-4 mb-6 flex flex-col sm:flex-row gap-4 items-center">
                @if($kiosk->images->isNotEmpty())
                    <img src="/storage/{{ $kiosk->images->first()->path }}" class="w-24 h-20 object-cover rounded shadow-sm border bg-white" alt="{{ $kiosk->name }}">
                @else
                    <div class="w-24 h-20 bg-gray-200 rounded shadow-sm flex items-center justify-center text-gray-400">No Image</div>
                @endif
                <div class="text-center sm:text-left">
                    <p class="font-bold text-xl text-gray-800">{{ $kiosk->code }} - {{ $kiosk->name }}</p>
                    <p class="text-gray-600 mt-1">
                        Khu vực: <span class="font-medium">{{ $kiosk->position->zone ?? 'N/A' }}</span> &bull; 
                        Diện tích: <span class="font-medium">{{ $kiosk->area }} m&sup2;</span> &bull; 
                        Giá: <span class="text-blue-600 font-bold">{{ number_format($kiosk->price, 0, ',', '.') }} đ/tháng</span>
                    </p>
                </div>
            </div>
            @endif

            <form action="{{ route('portal.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @if($kiosk)
                    <input type="hidden" name="kiosk_id" value="{{ $kiosk->id }}">
                @endif

                <div class="bg-white p-4 border border-gray-200 rounded-lg shadow-sm">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Thông tin liên hệ</h3>
                    <x-input id="contact_name" name="contact_name" label="Họ và tên" required="true" placeholder="Nguyễn Văn A" />
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-input id="contact_phone" name="contact_phone" label="Số điện thoại" required="true" placeholder="0901234567" />
                        <x-input id="contact_email" name="contact_email" type="email" label="Email" required="true" placeholder="nguyenvana@example.com" />
                    </div>
                </div>

                <div class="bg-white p-4 border border-gray-200 rounded-lg shadow-sm">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Kế hoạch thuê</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-input id="desired_start" name="desired_start" type="date" label="Ngày bắt đầu dự kiến" required="true" />
                        <x-input id="desired_end" name="desired_end" type="date" label="Ngày kết thúc dự kiến" required="true" />
                    </div>
                    
                    <div class="mt-4">
                        <label for="note" class="block text-sm font-medium text-gray-700 mb-1">Ghi chú thêm (Mục đích kinh doanh, yêu cầu đặc biệt...)</label>
                        <textarea id="note" name="note" rows="3" class="w-full border-gray-300 rounded shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2"></textarea>
                    </div>
                </div>

                <div class="bg-white p-4 border border-gray-200 rounded-lg shadow-sm">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Hồ sơ đính kèm & Xác thực</h3>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tài liệu đính kèm (CCCD, ĐKKD...)</label>
                        <input type="file" id="files" name="files[]" multiple accept=".jpg,.jpeg,.png,.pdf" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Hỗ trợ định dạng JPG, PNG, PDF. Tối đa 5MB/file.</p>
                    </div>

                    <div class="mt-4 p-4 bg-gray-50 rounded border border-dashed border-gray-300 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="captcha_mock" required class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="captcha_mock" class="text-sm font-medium text-gray-700">Tôi không phải là người máy (Captcha Mock)</label>
                        </div>
                        <div class="text-xs text-gray-400">reCAPTCHA</div>
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-bold text-lg rounded shadow hover:bg-blue-700 transition">
                        Gửi Yêu Cầu Thuê
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
