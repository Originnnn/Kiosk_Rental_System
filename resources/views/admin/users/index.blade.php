@extends('layouts.admin')

@section('title', 'Quản lý người dùng - Bến Xe Huế')

@section('content')
<div x-data="{ 
        openModal: false, 
        showPanel: false, 
        selectedUser: null,
        openPanel(user) {
            this.selectedUser = user;
            this.showPanel = true;
        }
    }" 
    class="bg-gray-50 min-h-screen p-6 font-sans relative overflow-hidden"
    :class="{'overflow-hidden': showPanel}">
    
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Quản lý người dùng</h1>
            <p class="text-sm text-gray-500">Danh sách tài khoản quản trị hệ thống.</p>
        </div>
        
        <div>
            <button @click="openModal = true" class="bg-[#006699] hover:bg-[#005580] text-white px-4 py-2 rounded font-medium flex items-center text-sm transition-colors shadow-sm">
                <i class="fa-solid fa-plus mr-2"></i> Thêm người dùng
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-4 mb-4 text-sm font-medium border border-green-200 rounded mx-0 mt-4">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-50 text-red-700 p-4 mb-4 text-sm font-medium border border-red-200 rounded mx-0 mt-4">
            {{ session('error') }}
        </div>
    @endif
    
    @if($errors->any())
        <div class="bg-red-50 text-red-700 p-4 mb-4 text-sm font-medium border border-red-200 rounded mx-0 mt-4">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Bảng dữ liệu -->
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
        
        <!-- Toolbar -->
        <div class="p-4 border-b border-gray-200 bg-white rounded-t-lg">
            <form action="{{ route('admin.users.index') }}" method="GET" class="w-[300px] relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                </div>
                <input type="text" name="q" value="{{ request('q') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" placeholder="Tìm kiếm người dùng...">
            </form>
        </div>

        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-4 text-sm font-semibold text-gray-700">Họ tên / Email</th>
                    <th class="px-6 py-4 text-sm font-semibold text-gray-700">Vai trò</th>
                    <th class="px-6 py-4 text-sm font-semibold text-gray-700">Trạng thái</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50 cursor-pointer transition-colors" @click="openPanel({{ json_encode([
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role == 'admin' ? 'Quản trị viên' : ($user->role == 'manager' ? 'Lãnh đạo' : 'Nhân viên quầy'),
                        'role_raw' => $user->role,
                        'status' => $user->status,
                        'created_at' => $user->created_at->format('d/m/Y'),
                        'edit_url' => route('admin.users.edit', $user->id)
                    ]) }})">
                        <td class="px-6 py-4 flex items-center">
                            <div class="h-10 w-10 rounded-full bg-[#006699] text-white flex items-center justify-center font-bold text-sm">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            @if($user->role == 'admin')
                                Quản trị viên
                            @elseif($user->role == 'manager')
                                Lãnh đạo
                            @else
                                Nhân viên quầy
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($user->status)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold text-green-700 bg-green-100 uppercase tracking-wider">
                                    Hoạt động
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold text-red-700 bg-red-100 uppercase tracking-wider">
                                    Đã khóa
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                            Không tìm thấy người dùng nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between rounded-b-lg">
            <div class="text-sm text-gray-500">
                Hiển thị {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} của {{ $users->total() }} người dùng
            </div>
            <div>
                {{ $users->links('pagination::tailwind') }}
            </div>
        </div>
    </div>

    <!-- Off-canvas / Slide-over Panel -->
    <div x-show="showPanel" style="display: none;" class="fixed inset-0 z-40 overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <div class="absolute inset-0 overflow-hidden">
            <!-- Background overlay -->
            <div x-show="showPanel" 
                 x-transition:enter="ease-in-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in-out duration-300" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                 @click="showPanel = false"></div>

            <div class="fixed inset-y-0 right-0 max-w-md w-full flex">
                <div x-show="showPanel" 
                     x-transition:enter="transform transition ease-in-out duration-300" 
                     x-transition:enter-start="translate-x-full" 
                     x-transition:enter-end="translate-x-0" 
                     x-transition:leave="transform transition ease-in-out duration-300" 
                     x-transition:leave-start="translate-x-0" 
                     x-transition:leave-end="translate-x-full" 
                     class="w-full h-full bg-white shadow-2xl flex flex-col">
                    
                    <!-- Panel Header -->
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-gray-900" id="slide-over-title">Chi tiết người dùng</h2>
                        <button @click="showPanel = false" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>

                    <!-- Panel Content -->
                    <div class="px-6 py-6 flex-1 overflow-y-auto">
                        <template x-if="selectedUser">
                            <div>
                                <!-- Avatar & Basic Info -->
                                <div class="flex items-start justify-between mb-8">
                                    <div class="flex items-center">
                                        <div class="h-14 w-14 rounded bg-[#0099cc] text-white flex items-center justify-center font-bold text-xl uppercase" x-text="selectedUser.name.substring(0, 2)"></div>
                                        <div class="ml-4">
                                            <h3 class="text-xl font-bold text-gray-900 leading-tight" x-text="selectedUser.name"></h3>
                                            <p class="text-sm text-gray-500 mb-1" x-text="selectedUser.email"></p>
                                            
                                            <template x-if="selectedUser.status">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold text-green-700 bg-green-100 uppercase tracking-wider">Hoạt động</span>
                                            </template>
                                            <template x-if="!selectedUser.status">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold text-red-700 bg-red-100 uppercase tracking-wider">Đã khóa</span>
                                            </template>
                                        </div>
                                    </div>
                                    <a :href="selectedUser.edit_url" class="p-2 text-gray-400 hover:text-gray-600 border border-gray-200 rounded hover:bg-gray-50">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                </div>

                                <!-- System Info -->
                                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Thông tin hệ thống</h4>
                                <div class="grid grid-cols-2 gap-y-6 gap-x-4 mb-8">
                                    <div>
                                        <div class="text-sm text-gray-500 mb-1">Vai trò</div>
                                        <div class="text-sm font-medium text-gray-900" x-text="selectedUser.role"></div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-500 mb-1">Ngày tạo</div>
                                        <div class="text-sm font-medium text-gray-900" x-text="selectedUser.created_at"></div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-500 mb-1">Đăng nhập lần cuối</div>
                                        <div class="text-sm font-medium text-gray-900">14:30, Hôm nay</div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-500 mb-1">Khu vực quản lý</div>
                                        <div class="text-sm font-medium text-gray-900">Tất cả</div>
                                    </div>
                                </div>

                                <!-- Activity History (Mock) -->
                                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Lịch sử hoạt động gần đây</h4>
                                <ul class="relative border-l border-gray-200 ml-2 space-y-6">
                                    <li class="pl-4 relative">
                                        <div class="absolute w-2 h-2 bg-[#006699] rounded-full -left-[4.5px] top-1.5"></div>
                                        <p class="text-sm font-medium text-gray-900">Cập nhật hợp đồng HD-2023-045</p>
                                        <p class="text-xs text-gray-500 mt-0.5">10:15, Hôm nay</p>
                                    </li>
                                    <li class="pl-4 relative">
                                        <div class="absolute w-2 h-2 bg-gray-300 rounded-full -left-[4.5px] top-1.5"></div>
                                        <p class="text-sm font-medium text-gray-700">Đăng nhập hệ thống</p>
                                        <p class="text-xs text-gray-500 mt-0.5">08:00, Hôm nay</p>
                                    </li>
                                    <li class="pl-4 relative">
                                        <div class="absolute w-2 h-2 bg-gray-300 rounded-full -left-[4.5px] top-1.5"></div>
                                        <p class="text-sm font-medium text-gray-700">Khóa Kiosk K-003</p>
                                        <p class="text-xs text-gray-500 mt-0.5">16:45, Hôm qua</p>
                                    </li>
                                </ul>
                                <a href="#" class="inline-block mt-4 text-sm font-medium text-[#006699] hover:underline">Xem toàn bộ lịch sử</a>
                            </div>
                        </template>
                    </div>

                    <!-- Panel Footer Actions -->
                    <div class="p-6 border-t border-gray-200 space-y-3 bg-white">
                        <button type="button" class="w-full px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded font-medium text-sm hover:bg-gray-50 flex items-center justify-center">
                            <i class="fa-solid fa-arrow-rotate-right mr-2"></i> Reset mật khẩu
                        </button>
                        <form x-show="selectedUser" :action="'/users/' + selectedUser.id + '/toggle-status'" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full px-4 py-2 bg-red-50 text-red-600 rounded font-medium text-sm hover:bg-red-100 flex items-center justify-center border border-red-200 transition-colors">
                                <i class="fa-solid fa-ban mr-2"></i> <span x-text="selectedUser.status ? 'Khóa tài khoản' : 'Mở khóa tài khoản'"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Thêm mới User -->
    <div x-show="openModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl w-[500px] flex flex-col" @click.away="openModal = false">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 rounded-t-lg">
                <h2 class="text-lg font-bold text-gray-900">Thêm người dùng mới</h2>
                <button @click="openModal = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Họ Tên <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-1 focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-1 focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Mật khẩu <span class="text-red-500">*</span></label>
                        <input type="password" name="password" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-1 focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Vai trò <span class="text-red-500">*</span></label>
                        <select name="role" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-1 focus:ring-primary focus:border-primary bg-white">
                            <option value="employee">Nhân viên quầy</option>
                            <option value="manager">Lãnh đạo</option>
                            <option value="admin">Quản trị viên</option>
                        </select>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50 rounded-b-lg">
                    <button type="button" @click="openModal = false" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded font-medium text-sm hover:bg-gray-100">
                        Hủy bỏ
                    </button>
                    <button type="submit" class="px-4 py-2 bg-[#006699] text-white rounded font-bold text-sm hover:bg-[#005580]">
                        <i class="fa-regular fa-floppy-disk mr-2"></i> Lưu người dùng
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
