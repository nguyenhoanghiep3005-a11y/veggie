<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ShippingAddress;
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

        return view('clients.pages.account', compact('user', 'addresses', 'orders'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'ltn__name' => 'required|string|max:255',
            'ltn__phone_number' => 'nullable|string|max:15',
            'ltn__address' => 'nullable|string|max:255',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Cập nhật thông tin cá nhân trong tab tài khoản.
        $user->name = $request->input('ltn__name');
        $user->phone_number = $request->input('ltn__phone_number');
        $user->address = $request->input('ltn__address');
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thông tin thành công',
        ]);
    }

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
                'new_password.required' => 'Mật khẩu mới không được để trống.',
                'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
                'confirm_new_password.required' => 'Vui lòng nhập lại mật khẩu mới.',
                'confirm_new_password.same' => 'Mật khẩu nhập lại không khớp.',
            ]
        );

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Kiểm tra mật khẩu hiện tại trước khi lưu mật khẩu mới.
        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'errors' => [
                    'current_password' => ['Mật khẩu hiện tại không đúng!'],
                ],
            ], 422);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json([
            'success' => true,
            'message' => 'Đổi mật khẩu thành công',
        ]);
    }

    public function addAddress(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|min:3|max:255',
            'phone' => 'required|string|regex:/^[0-9]{10,11}$/',
            'address' => 'required|string|min:5|max:255',
            'province_id' => 'required|integer',
            'district_id' => 'required|integer',
            'ward_id' => 'required|string|max:50',
            'province_name' => 'required|string|max:100',
            'district_name' => 'required|string|max:100',
            'ward_name' => 'required|string|max:100',
        ]);

        // GHN cần mã tỉnh/quận/phường để trang checkout tính phí ship.
        $provinceName = trim($request->province_name);
        $districtName = trim($request->district_name);
        $wardName = trim($request->ward_name);

        $isFirstAddress = ! ShippingAddress::where('user_id', Auth::id())->exists();
        $isDefault = $request->has('default') || $isFirstAddress;

        // Nếu chọn mặc định thì bỏ mặc định ở các địa chỉ cũ.
        if ($isDefault) {
            ShippingAddress::where('user_id', Auth::id())->update(['default' => 0]);
        }

        ShippingAddress::create([
            'user_id' => Auth::id(),
            'full_name' => trim($request->full_name),
            'phone' => trim($request->phone),
            'address' => trim($request->address).', '.$wardName.', '.$districtName,
            'city' => $provinceName,
            'province_id' => $request->province_id,
            'district_id' => $request->district_id,
            'ward_id' => $request->ward_id,
            'default' => $isDefault ? 1 : 0,
        ]);

        return back()->with('success', 'Địa chỉ đã được thêm');
    }

    public function updatePrimaryAddress($id)
    {
        $address = ShippingAddress::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        // Chỉ có một địa chỉ mặc định cho mỗi tài khoản.
        ShippingAddress::where('user_id', Auth::id())->update(['default' => 0]);
        $address->update(['default' => 1]);

        toastr()->success('Địa chỉ mặc định đã được cập nhật');

        return back();
    }

    public function deleteAddress($id)
    {
        $address = ShippingAddress::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $wasDefault = $address->default;

        $address->delete();

        // Nếu xóa địa chỉ mặc định thì lấy địa chỉ còn lại làm mặc định.
        if ($wasDefault) {
            $nextAddress = ShippingAddress::where('user_id', Auth::id())->first();

            if ($nextAddress) {
                $nextAddress->update(['default' => 1]);
            }
        }

        toastr()->success('Địa chỉ đã được xóa');

        return back();
    }
}
