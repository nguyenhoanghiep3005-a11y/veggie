<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class CouponWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_usable_scope_only_returns_active_unexpired_coupons_with_remaining_uses(): void
    {
        $usable = $this->coupon('DUNGDUOC', [
            'expires_at' => now()->addDay(),
            'usage_limit' => 2,
            'used_count' => 1,
        ]);
        $unlimited = $this->coupon('KHONGGIOIHAN', [
            'expires_at' => null,
            'usage_limit' => null,
        ]);
        $this->coupon('HETHAN', ['expires_at' => now()->subMinute()]);
        $this->coupon('HETLUOT', ['usage_limit' => 1, 'used_count' => 1]);
        $this->coupon('DANGKHOA', ['is_active' => false]);

        $codes = Coupon::usable()->orderBy('id')->pluck('code')->all();

        $this->assertSame(['DUNGDUOC', 'KHONGGIOIHAN'], $codes);
        $this->assertTrue($usable->isUsable());
        $this->assertTrue($unlimited->isUsable());
    }

    public function test_customer_can_apply_a_usable_coupon_code(): void
    {
        $this->withoutMiddleware();
        config(['app.url' => 'http://localhost']);
        URL::forceRootUrl('http://localhost');
        $role = Role::create(['name' => 'customer']);
        $user = User::create([
            'name' => 'Khách kiểm thử mã giảm giá',
            'email' => 'coupon@example.test',
            'password' => bcrypt('password'),
            'status' => 'active',
            'role_id' => $role->id,
        ]);
        $coupon = $this->coupon('GG10');

        $this->actingAs($user)
            ->postJson(route('checkout.coupon.apply', [], false), ['code' => ' gg10 '])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'coupon' => 'GG10',
            ]);

        $this->assertSame($coupon->id, session('checkout_coupon.id'));
    }

    public function test_guest_coupon_request_returns_json_instead_of_login_redirect(): void
    {
        config(['app.url' => 'http://localhost']);
        URL::forceRootUrl('http://localhost');

        $this->postJson(route('checkout.coupon.apply', [], false), ['code' => 'GG10'])
            ->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để sử dụng mã giảm giá.',
            ]);
    }

    private function coupon(string $code, array $attributes = []): Coupon
    {
        return Coupon::create(array_merge([
            'code' => $code,
            'discount_percent' => 10,
            'expires_at' => now()->addDay(),
            'usage_limit' => 10,
            'used_count' => 0,
            'is_active' => true,
        ], $attributes));
    }
}
