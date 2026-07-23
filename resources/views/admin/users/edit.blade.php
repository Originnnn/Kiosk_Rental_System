@extends('layouts.admin')

@section('title', 'Chỉnh sửa người dùng - Bến Xe Huế')

@section('content')
<div class="bg-gray-50 min-h-screen p-6 font-sans">
    
    <!-- Breadcrumb & Top Bar -->
    <div class="mb-6">
        <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.users.index') }}" class="hover:text-gray-900">Quản lý người dùng</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right text-[10px] mx-2"></i>
                        <span class="text-gray-900 font-medium">Chỉnh sửa người dùng</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">Chỉnh sửa: {{ $user->name }}</h1>
            <div class="flex space-x-3">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded font-medium text-sm hover:bg-gray-50 shadow-sm transition-colors">
                    Hủy
                </a>
                <button type="submit" form="editUserForm" class="px-4 py-2 bg-[#006699] text-white rounded font-medium text-sm hover:bg-[#005580] shadow-sm transition-colors flex items-center">
                    <i class="fa-regular fa-floppy-disk mr-2"></i> Lưu thay đổi
                </button>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-4 mb-6 text-sm font-medium border border-green-200 rounded">
            {{ session('success') }}
        </div>
    @endif
    
    @if($errors->any())
        <div class="bg-red-50 text-red-700 p-4 mb-6 text-sm font-medium border border-red-200 rounded">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex gap-6 w-full">
        <!-- Cột Trái (Main Form) - 70% -->
        <div class="w-[70%] space-y-6">
            <form id="editUserForm" action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Card Thông tin cơ bản -->
                <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100">Thông tin cơ bản</h2>
                    
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Họ và tên <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full border border-gray-300 rounded px-3 py-2.5 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-gray-900">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Số điện thoại <span class="text-red-500">*</span></label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}" class="w-full border border-gray-300 rounded px-3 py-2.5 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-gray-900">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Email (Tài khoản đăng nhập)</label>
                        <input type="email" value="{{ $user->email }}" disabled class="w-full border border-gray-300 bg-gray-50 rounded px-3 py-2.5 text-sm text-gray-500 cursor-not-allowed">
                        <p class="text-xs text-gray-400 mt-1.5">Email không thể thay đổi sau khi tạo tài khoản.</p>
                    </div>
                </div>

                <!-- Card Phân quyền -->
                <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100">Phân quyền</h2>
                    
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Vai trò <span class="text-red-500">*</span></label>
                            <select name="role" required class="w-full border border-gray-300 rounded px-3 py-2.5 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-900">
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Quản trị viên</option>
                                <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>Lãnh đạo</option>
                                <option value="employee" {{ old('role', $user->role) == 'employee' ? 'selected' : '' }}>Nhân viên điều hành</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Trạng thái hoạt động</label>
                            <div class="flex items-center space-x-6 mt-2">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="status" value="1" {{ old('status', $user->status) ? 'checked' : '' }} class="w-4 h-4 text-[#006699] border-gray-300 focus:ring-[#006699]">
                                    <span class="ml-2 text-sm text-gray-900">Hoạt động</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="status" value="0" {{ !old('status', $user->status) ? 'checked' : '' }} class="w-4 h-4 text-[#006699] border-gray-300 focus:ring-[#006699]">
                                    <span class="ml-2 text-sm text-gray-900">Tạm ngưng</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Cột Phải (Widgets) - 30% -->
        <div class="w-[30%] space-y-6">
            
            <!-- Bảo mật -->
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                <h3 class="text-sm font-bold text-gray-900 mb-3 pb-2 border-b border-gray-100">Bảo mật</h3>
                <p class="text-sm text-gray-600 mb-4 leading-relaxed">Mật khẩu mới sẽ được gửi về email của người dùng nếu bạn thực hiện đặt lại.</p>
                <button type="button" class="w-full px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded font-medium text-sm hover:bg-gray-50 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-arrow-rotate-right mr-2"></i> Đặt lại mật khẩu
                </button>
            </div>
            
            <!-- Khu vực nguy hiểm -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 shadow-sm">
                <h3 class="text-sm font-bold text-red-700 mb-2 flex items-center">
                    <i class="fa-solid fa-triangle-exclamation mr-2"></i> Khu vực nguy hiểm
                </h3>
                <p class="text-sm text-red-600 mb-4 leading-relaxed">Hành động này sẽ ngăn chặn người dùng truy cập vào hệ thống. Bạn có thể mở khóa lại sau.</p>
                
                <form action="{{ route('admin.users.toggleStatus', $user->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full px-4 py-2 {{ $user->status ? 'bg-red-500 hover:bg-red-600 text-white' : 'bg-green-500 hover:bg-green-600 text-white' }} rounded font-medium text-sm transition-colors flex items-center justify-center">
                        <i class="fa-solid {{ $user->status ? 'fa-ban' : 'fa-unlock' }} mr-2"></i> {{ $user->status ? 'Khóa tài khoản' : 'Mở khóa tài khoản' }}
                    </button>
                </form>
            </div>

            <!-- Lịch sử hệ thống -->
            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Lịch sử hệ thống</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Tạo lúc:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-4 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Bởi:</span>
                        <span class="text-sm font-medium text-gray-900">Super Admin</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Cập nhật cuối:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $user->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
