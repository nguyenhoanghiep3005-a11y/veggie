<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->paginate(9);
        return view('admin.pages.users', compact('users'));
    }
    // nâng cấp user thành nhân viên
    public function upgrade(Request $request)
    {
        $userId = $request->user_id;
        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'status' => false,
                 'message' => 'Không tìm thấy người dùng']);
        }
        $user->role_id = 2; //  2 là ID của admin
        $user->save();
        return response()->json(['status' => true, 'message' => 'Đã update thành NV']);
    }
    public function updateStatus(Request $request)
    {
        $userId = $request->user_id;
        $status = $request->status;
        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'status' => false,
                 'message' => 'Không tìm thấy người dùng']);
        }
        $user->status = $status; // Đảo ngược trạng thái hiện tại
        $user->save();
        return response()->json(['status' => true, 'message' => 'Đã cập nhật trạng thái người dùng']);
    }
}
