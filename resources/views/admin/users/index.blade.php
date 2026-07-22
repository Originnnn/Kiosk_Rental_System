@extends('layouts.admin')

@section('title', 'Quản lý người dùng - Bến Xe Huế')

@section('content')
<div x-data="{ openModal: false }" class="bg-gray-50 min-h-screen p-6 font-sans">
    
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Quản lý người dùng</h1>
            <p class="text-sm text-gray-500">Quản lý danh sách tài khoản và phân quyền hệ thống.</p>
        </div>
        
        <div>
            <button @click="openModal = true" class="bg-[#006699] hover:bg-[#005580] text-white px-4 py-2 rounded font-medium flex items-center text-sm transition-colors shadow-sm">
                <i class="fa-solid fa-plus mr-2"></i> Thêm người dùng
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-4 mb-4 text-sm font-medium border border-green-200 rounded mx-4 mt-4">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-50 text-red-700 p-4 mb-4 text-sm font-medium border border-red-200 rounded mx-4 mt-4">
            {{ session('error') }}
        </div>
    @endif
    
    @if($errors->any())
        <div class="bg-red-50 text-red-700 p-4 mb-4 text-sm font-medium border border-red-200 rounded mx-4 mt-4">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Bảng dữ liệu -->
    <div class="bg-white border border-gray-200 rounded overflow-x-auto mx-4 shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Họ Tên</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Vai trò</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Trạng thái</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-bold text-gray-900">
                            {{ $user->name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($user->role == 'admin')
                                <span class="inline-flex items-center px-2 py-1 text-xs font-bold rounded-full text-purple-700 bg-purple-100">ADMIN</span>
                            @elseif($user->role == 'manager')
                                <span class="inline-flex items-center px-2 py-1 text-xs font-bold rounded-full text-blue-700 bg-blue-100">MANAGER</span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 text-xs font-bold rounded-full text-gray-700 bg-gray-100">EMPLOYEE</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($user->status)
                                <span class="inline-flex items-center px-2 py-1 text-xs font-bold rounded-full text-green-700 bg-green-100">
                                    <i class="fa-solid fa-circle text-[8px] mr-1"></i> ACTIVE
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 text-xs font-bold rounded-full text-red-700 bg-red-100">
                                    <i class="fa-solid fa-circle text-[8px] mr-1"></i> LOCKED
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('admin.users.toggleStatus', $user->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-sm px-3 py-1 rounded border {{ $user->status ? 'border-red-500 text-red-500 hover:bg-red-50' : 'border-green-500 text-green-500 hover:bg-green-50' }} transition">
                                    @if($user->status)
                                        <i class="fa-solid fa-lock mr-1"></i> Khoá
                                    @else
                                        <i class="fa-solid fa-lock-open mr-1"></i> Mở khoá
                                    @endif
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            Không tìm thấy người dùng nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-6 py-4 mx-4 mt-0 border border-t-0 border-gray-200 bg-white flex items-center justify-between rounded-b">
        <div class="text-sm text-gray-500">
            Hiển thị {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} trong số {{ $users->total() }} người dùng
        </div>
        <div>
            {{ $users->links('pagination::tailwind') }}
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
                            <option value="employee">Employee</option>
                            <option value="manager">Manager</option>
                            <option value="admin">Admin</option>
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
