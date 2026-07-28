<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CouponController extends Controller
{
    // Hiển thị danh sách mã giảm giá và form thêm mới.
    public function index()
    {
        $coupons = Coupon::with('users')
            ->orderBy('id', 'desc')
            ->get();

        $customers = User::where('role_id', 3)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.pages.coupons', compact('coupons', 'customers'));
    }

    // Lưu mã giảm giá mới.
    public function store(Request $request)
    {
        $data = $this->validateStore($request);
        $customerIds = $data['customer_ids'];
        unset($data['customer_ids']);

        DB::transaction(function () use ($data, $customerIds) {
            $coupon = Coupon::create($data);
            $this->syncCustomers($coupon, $customerIds);
        });

        return back()->with('success', 'Thêm mã giảm giá thành công.');
    }

    // Cập nhật mã giảm giá đã có.
    public function update(Request $request, Coupon $coupon)
    {
        $data = $this->validateUpdate($request, $coupon);
        $customerIds = $data['customer_ids'];
        unset($data['customer_ids']);

        DB::transaction(function () use ($coupon, $data, $customerIds) {
            $coupon->update($data);
            $this->syncCustomers($coupon, $customerIds);
        });

        return back()->with('success', 'Cập nhật mã giảm giá thành công.');
    }

    // Xóa mã giảm giá, nếu đã phát sinh đơn thì chỉ khóa lại.
    public function destroy(Coupon $coupon)
    {
        if ($coupon->orders()->exists()) {
            $coupon->update(['is_active' => false]);

            return back()->with('success', 'Mã đã được khóa vì đã phát sinh đơn hàng.');
        }

        $coupon->delete();

        return back()->with('success', 'Xóa mã giảm giá thành công.');
    }

    // Kiểm tra dữ liệu khi thêm mã mới.
    private function validateStore($request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'discount_percent' => 'required|numeric|gt:0|lte:100',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'expires_at' => 'nullable|date',
            'usage_limit' => 'nullable|integer|min:1',
            'apply_type' => 'required|in:all,customer',
            'customer_ids' => 'nullable|array',
            'customer_ids.*' => 'integer|exists:users,id',
            'is_active' => 'nullable|boolean',
        ]);

        return $this->prepareData($request, $data);
    }

    // Kiểm tra dữ liệu khi sửa mã giảm giá.
    private function validateUpdate($request, $coupon)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,'.$coupon->id,
            'discount_percent' => 'required|numeric|gt:0|lte:100',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'expires_at' => 'nullable|date',
            'usage_limit' => 'nullable|integer|min:1',
            'apply_type' => 'required|in:all,customer',
            'customer_ids' => 'nullable|array',
            'customer_ids.*' => 'integer|exists:users,id',
            'is_active' => 'nullable|boolean',
        ]);

        return $this->prepareData($request, $data);
    }

    // Chuẩn hóa dữ liệu trước khi lưu mã giảm giá.
    private function prepareData($request, $data)
    {
        $data['code'] = strtoupper(trim($data['code']));

        if (empty($data['minimum_order_amount'])) {
            $data['minimum_order_amount'] = 0;
        }

        if (empty($data['max_discount_amount'])) {
            $data['max_discount_amount'] = null;
        }

        if (empty($data['usage_limit'])) {
            $data['usage_limit'] = null;
        }

        if (empty($data['apply_type'])) {
            $data['apply_type'] = Coupon::APPLY_ALL;
        }

        $data['is_active'] = $request->boolean('is_active');

        if (empty($data['customer_ids'])) {
            $data['customer_ids'] = [];
        }

        if ($data['apply_type'] == Coupon::APPLY_CUSTOMER && count($data['customer_ids']) == 0) {
            throw ValidationException::withMessages([
                'customer_ids' => 'Vui lòng chọn ít nhất một khách hàng nhận voucher riêng.',
            ]);
        }

        return $data;
    }

    // Đồng bộ danh sách khách hàng được nhận voucher riêng.
    private function syncCustomers($coupon, $customerIds)
    {
        DB::table('coupon_user')
            ->where('coupon_id', $coupon->id)
            ->whereNull('used_at')
            ->delete();

        if ($coupon->apply_type != Coupon::APPLY_CUSTOMER) {
            return;
        }

        $customers = User::whereIn('id', $customerIds)
            ->where('role_id', 3)
            ->get();

        foreach ($customers as $customer) {
            DB::table('coupon_user')->updateOrInsert(
                [
                    'coupon_id' => $coupon->id,
                    'user_id' => $customer->id,
                ],
                [
                    'claimed_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
