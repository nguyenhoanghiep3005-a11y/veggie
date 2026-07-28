<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    // Hiển thị danh sách voucher khách có thể lấy hoặc đã lấy.
    public function index()
    {
        $user = Auth::user();
        $claimedIds = [];
        $usedIds = [];

        if ($user) {
            $userCoupons = $user->coupons()->get();

            foreach ($userCoupons as $coupon) {
                if (empty($coupon->pivot->used_at)) {
                    $claimedIds[] = $coupon->id;
                } else {
                    $usedIds[] = $coupon->id;
                }
            }
        }

        $allCoupons = Coupon::where('is_active', true)
            ->orderBy('id', 'desc')
            ->get();

        $coupons = [];

        foreach ($allCoupons as $coupon) {
            if ($coupon->expires_at != null && $coupon->expires_at < now()) {
                continue;
            }

            if ($coupon->usage_limit != null && $coupon->used_count >= $coupon->usage_limit) {
                continue;
            }

            if ($coupon->apply_type == Coupon::APPLY_CUSTOMER) {
                if (! $user) {
                    continue;
                }

                $isAssigned = DB::table('coupon_user')
                    ->where('coupon_id', $coupon->id)
                    ->where('user_id', $user->id)
                    ->exists();

                if (! $isAssigned) {
                    continue;
                }
            }

            $coupons[] = $coupon;
        }

        return view('clients.pages.vouchers', compact('coupons', 'claimedIds', 'usedIds'));
    }

    // Lưu voucher khách chọn vào tài khoản để dùng ở checkout.
    public function claim(Coupon $coupon)
    {
        $error = $coupon->validateForUser(Auth::id());

        if ($error) {
            return back()->with('error', $error);
        }

        Auth::user()->coupons()->syncWithoutDetaching([
            $coupon->id => ['claimed_at' => now()],
        ]);

        return back()->with('success', 'Đã lấy mã '.$coupon->code.'.');
    }
}
