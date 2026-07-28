<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Hiển thị danh sách tài khoản khách hàng.
    public function index()
    {
        $users = User::with('role')
            ->whereHas('role', function ($query) {
                $query->where('name', 'customer');
            })
            ->orderBy('id', 'desc')
            ->paginate(9);

        return view('admin.pages.users', compact('users'));
    }

    // Chặn hoặc bỏ chặn tài khoản khách hàng.
    public function updateStatus(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:active,banned',
        ]);

        $user = User::whereKey($data['user_id'])
            ->whereHas('role', function ($query) {
                $query->where('name', 'customer');
            })
            ->first();

        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy người dùng.',
            ]);
        }

        $user->status = $data['status'];
        $user->save();

        $message = 'Đã bỏ chặn tài khoản khách hàng.';
        if ($user->status == 'banned') {
            $message = 'Đã chặn tài khoản khách hàng.';
        }

        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => [
                'status' => $user->status,
            ],
        ]);
    }
}
