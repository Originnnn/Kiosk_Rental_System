<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,manager,employee',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => true,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Tạo tài khoản thành công!');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        
        // Không cho phép khóa tài khoản admin chính đang đăng nhập hoặc các admin khác tùy rule, 
        // ở đây tạm thời cho phép nhưng nên cảnh báo nếu tự khóa mình.
        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'Bạn không thể khóa tài khoản của chính mình!');
        }

        $user->status = !$user->status;
        $user->save();

        $action = $user->status ? 'Mở khóa' : 'Khóa';
        return redirect()->back()->with('success', "$action tài khoản thành công!");
    }
}
