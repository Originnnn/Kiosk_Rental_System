@extends('layouts.admin')

@section('title', 'Hồ sơ cá nhân')

@section('content')
<div class="max-w-6xl mx-auto pb-10">

    <!-- Header / Banner -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden mb-6 shadow-sm relative">
        <div class="h-32 bg-[#006699]"></div>
        <div class="px-8 pb-6 relative flex justify-between items-end">
            <!-- User Info Left -->
            <div class="flex items-end -mt-12 space-x-6">
                <div class="w-28 h-28 rounded-lg border-4 border-white overflow-hidden bg-white shadow-md">
                    <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-full h-full object-cover">
                </div>
                <div class="pb-2">
                    <h1 class="text-2xl font-bold text-gray-800">{{ $user->name }}</h1>
                    <div class="flex items-center space-x-3 text-sm mt-1">
                        <span class="text-gray-500 flex items-center"><i class="fa-solid fa-shield-halved mr-1.5"></i> {{ $user->role == 'admin' ? 'Quản trị viên hệ thống' : ($user->role == 'manager' ? 'Quản lý' : 'Nhân viên') }}</span>
                        <span class="text-gray-300">•</span>
                        @if($user->status)
                            <span class="text-green-600 font-medium flex items-center"><span class="w-2 h-2 rounded-full bg-green-500 mr-1.5"></span> Hoạt động</span>
                        @else
                            <span class="text-red-600 font-medium flex items-center"><span class="w-2 h-2 rounded-full bg-red-500 mr-1.5"></span> Đã khóa</span>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Actions Right -->
            <div class="pb-2 flex space-x-3">
                <a href="{{ route('admin.profile.edit') }}" class="px-4 py-2 bg-[#006699] text-white text-sm font-medium rounded hover:bg-[#005580] transition flex items-center shadow-sm">
                    <i class="fa-solid fa-pen mr-2"></i> Chỉnh sửa thông tin
                </a>
                <button class="px-4 py-2 bg-white text-gray-700 text-sm font-medium border border-gray-300 rounded hover:bg-gray-50 transition flex items-center shadow-sm">
                    <i class="fa-solid fa-share-nodes mr-2"></i> Xuất hồ sơ
                </button>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Cột Trái (2 phần) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- THÔNG TIN CÁ NHÂN -->
            <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wide flex items-center mb-4 pb-3 border-b border-gray-100">
                    <i class="fa-regular fa-user text-[#006699] text-lg mr-2"></i> THÔNG TIN CÁ NHÂN
                </h2>
                
                <div class="grid grid-cols-2 gap-y-6 gap-x-8">
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Họ và Tên</div>
                        <div class="font-medium text-gray-900">{{ $user->name }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Ngày sinh</div>
                        <div class="text-gray-900">{{ $user->dob ? \Carbon\Carbon::parse($user->dob)->format('d/m/Y') : '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Giới tính</div>
                        <div class="text-gray-900">{{ $user->gender ?: '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Số CCCD</div>
                        <div class="text-gray-900">{{ $user->id_card ?: '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Số điện thoại</div>
                        <div class="text-gray-900">{{ $user->phone ?: '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 mb-1">Địa chỉ</div>
                        <div class="text-gray-900">{{ $user->address ?: '—' }}</div>
                    </div>
                </div>
            </div>

            <!-- HOẠT ĐỘNG GẦN ĐÂY -->
            <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wide flex items-center">
                        <i class="fa-solid fa-clock-rotate-left text-[#006699] text-lg mr-2"></i> HOẠT ĐỘNG GẦN ĐÂY
                    </h2>
                    <a href="#" class="text-sm text-[#006699] hover:underline">Xem tất cả</a>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fa-solid fa-right-to-bracket text-sm"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm text-gray-900">Đăng nhập từ trình duyệt Chrome (macOS)</div>
                            <div class="text-xs text-gray-500 mt-0.5">10:45 AM, Hôm nay • IP: 113.161.x.x</div>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded bg-orange-50 text-orange-500 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fa-solid fa-file-contract text-sm"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm text-gray-900">Cập nhật thông tin hợp đồng Kiosk #K-102</div>
                            <div class="text-xs text-gray-500 mt-0.5">08:20 AM, Hôm qua</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Cột Phải (2 phần) -->
        <div class="space-y-6">
            
            <!-- CÔNG VIỆC & TÀI KHOẢN -->
            <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wide flex items-center mb-4 pb-3 border-b border-gray-100">
                    <i class="fa-solid fa-briefcase text-[#006699] text-lg mr-2"></i> CÔNG VIỆC & TÀI KHOẢN
                </h2>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-1">
                        <span class="text-sm text-gray-500">Mã nhân viên</span>
                        <span class="text-sm font-semibold text-[#006699]">{{ $user->employee_code ?: '—' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-t border-gray-50">
                        <span class="text-sm text-gray-500">Email công việc</span>
                        <span class="text-sm text-gray-900">{{ $user->email }}</span>
                    </div>
                    @if($user->personal_email)
                    <div class="flex justify-between items-center py-1 border-t border-gray-50">
                        <span class="text-sm text-gray-500">Email cá nhân</span>
                        <span class="text-sm text-gray-900">{{ $user->personal_email }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between items-center py-1 border-t border-gray-50">
                        <span class="text-sm text-gray-500">Phòng ban</span>
                        <span class="text-sm text-gray-900">{{ $user->department ?: '—' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-t border-gray-50">
                        <span class="text-sm text-gray-500">Ngày gia nhập</span>
                        <span class="text-sm text-gray-900">
                            {{ $user->join_date ? \Carbon\Carbon::parse($user->join_date)->format('d/m/Y') : '—' }}
                            @if($user->join_date)
                                <span class="text-gray-400">({{ \Carbon\Carbon::parse($user->join_date)->diffInYears(now()) }} năm)</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- BẢO MẬT & QUYỀN RIÊNG TƯ -->
            <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wide flex items-center mb-4 pb-3 border-b border-gray-100">
                    <i class="fa-solid fa-shield text-[#006699] text-lg mr-2"></i> BẢO MẬT & QUYỀN RIÊNG TƯ
                </h2>
                
                <div class="space-y-2">
                    <a href="{{ route('admin.profile.password') }}" class="flex items-center justify-between p-3 hover:bg-gray-50 rounded transition-colors group">
                        <div class="flex items-center text-gray-700 group-hover:text-[#006699]">
                            <i class="fa-solid fa-clock-rotate-left w-6 text-center text-gray-400 group-hover:text-[#006699]"></i>
                            <span class="text-sm font-medium ml-2">Đổi mật khẩu</span>
                        </div>
                        <i class="fa-solid fa-chevron-right text-gray-300 text-xs"></i>
                    </a>
                    
                    <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded transition-colors group cursor-pointer">
                        <div class="flex items-center text-gray-700">
                            <i class="fa-solid fa-mobile-screen w-6 text-center text-gray-400"></i>
                            <span class="text-sm font-medium ml-2">Xác thực 2 yếu tố (2FA)</span>
                        </div>
                        <span class="text-[10px] bg-green-100 text-green-700 font-bold px-2 py-0.5 rounded uppercase">Đã Bật</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 hover:bg-red-50 rounded transition-colors group cursor-pointer">
                        <div class="flex items-center text-red-600">
                            <i class="fa-solid fa-right-from-bracket w-6 text-center text-red-400 group-hover:text-red-600"></i>
                            <span class="text-sm font-medium ml-2">Đăng xuất khỏi các thiết bị</span>
                        </div>
                        <i class="fa-solid fa-chevron-right text-red-300 text-xs"></i>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection