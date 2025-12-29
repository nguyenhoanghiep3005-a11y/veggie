<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ShippingAddress;
use Illuminate\Container\Attributes\Auth as AttributesAuth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $addresses = ShippingAddress::where('user_id', Auth::id())->get();
        $orders = Order::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        // dd($orders);
        return view('clients.pages.account', compact('user', 'addresses', 'orders'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'ltn__name'        => 'required|string|max:255',
            'ltn__phone_number' => 'nullable|string|max:15',
            'ltn__address'     => 'nullable|string|max:255',
            'avatar'          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Xử lý ảnh đại diện (avatar)
        if ($request->hasFile('avatar')) {
            // Xóa ảnh cũ nếu có
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            // Lấy file mới
            $file = $request->file('avatar');
            // Tạo tên file mới 
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            // Lưu file vào thư mục storage/app/public/uploads/users
            $avatarPath = $file->storeAs('uploads/users', $filename, 'public');
            // Cập nhật đường dẫn avatar trong DB
            $user->avatar = $avatarPath;
        }

        
        $user->name = $request->input('ltn__name');
        $user->phone_number = $request->input('ltn__phone_number');
        $user->address = $request->input('ltn__address');

        $user->save();
        return response()->json([
            'success' => true,
            'message' => 'cập nhật thông tin thành công',
            'avatar' => asset('storage/' . $user->avatar)
        ]);
    }

    //doi mat khau
    public function changePassword(Request $request)
    {

        $request->validate(
            [
                'current_password' => 'required',
                'new_password' => 'required|min:6',
                'confirm_new_password' => 'required|same:new_password',

            ],
            [
                'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
                'new_password.required'  => 'Mật khẩu mới không được để trống.',
                'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
                'confirm_new_password.required' => 'Vui lòng nhập lại mật khẩu mới.',
                'confirm_new_password.same' => 'Mật khẩu nhập lại không khớp.',
            ]
        );
        /** @var \App\Models\User $user */
        $user = Auth::user();
        //kiemtrachinhxacmatkhau
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['errors' => ['current_password' => ['Mật khẩu hiện tại không đúng!']]], 422);
        }
        //neu thanh cong update password
        $user->update(['password' => Hash::make($request->new_password)]);
        return response()->json([
            'success' => true,
            'message' => 'Đổi mật khẩu thành công',
        ]);
    }
    public function addAddress(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
        ]);

        //neu dia chi moi set mac dinh thi update
        if ($request->has('default')) {
            ShippingAddress::where('user_id', Auth::id())->update(['default' => 0]);
        }

        ShippingAddress::create([
            'user_id' => Auth::id(),
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'default' => $request->has('default') ? 1 : 0
        ]);
        return back()->with('success', 'Địa chỉ đã được thêm');
    }
    public function updatePrimaryAddress($id)
    {
        $addresses = ShippingAddress::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        //set lai dia chi 
        ShippingAddress::where('user_id', Auth::id())->update(['default' => 0]);
        //update dia chi thanh 1
        $addresses->update(['default' => 1]);
        toastr()->success('Địa chỉ mặt định đã được cập nhật');
        return back();
    }

    public function deleteAddress($id)
    {
        ShippingAddress::where('id', $id)->where('user_id', Auth::id())->delete();
         toastr()->success('Địa chỉ đã được xóa');
        return back();
    }
}
