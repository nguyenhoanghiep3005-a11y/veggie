<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\OrderReturnController as AdminOrderReturnController;
use App\Http\Controllers\Clients\OrderController as ClientOrderController;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReturnWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_damaged_return_ships_replacement_from_product_stock(): void
    {
        Storage::fake('public');

        $role = Role::create(['name' => 'customer']);
        $user = User::create([
            'name' => 'Khách đổi trả',
            'email' => 'return@example.test',
            'password' => bcrypt('password'),
            'status' => 'active',
            'role_id' => $role->id,
        ]);

        $category = Category::create([
            'name' => 'Trái cây',
            'slug' => 'trai-cay',
            'status' => 1,
        ]);

        $product = Product::create([
            'name' => 'Táo đỏ',
            'slug' => 'tao-do-500g',
            'category_id' => $category->id,
            'description' => 'Táo đỏ kiểm thử',
            'price' => 45000,
            'stock' => 5,
            'status' => 'int_stock',
            'unit' => '500g',
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => 90000,
            'subtotal' => 90000,
            'shipping_fee' => 0,
            'discount_amount' => 0,
            'status' => 'completed',
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 45000,
        ]);

        $this->actingAs($user);
        Auth::login($user);

        $returnRequest = Request::create('/order/'.$order->id.'/return-request', 'POST', [
            'description' => 'Sản phẩm bị dập và mốc khi nhận hàng',
            'items' => [
                $orderItem->id => ['quantity' => 2],
            ],
        ], [], [
            'evidence' => [
                UploadedFile::fake()->image('return-damage.png'),
            ],
        ]);

        $clientResponse = app(ClientOrderController::class)->requestReturn($returnRequest, $order->id);
        $this->assertTrue($clientResponse->isRedirect());

        $order->refresh();
        $this->assertSame('return_requested', $order->status);
        $returnRequest = OrderReturn::firstOrFail();
        $this->assertSame(OrderReturn::TYPE_DAMAGED, $returnRequest->type);
        $this->assertCount(1, $returnRequest->media);

        $approveResponse = app(AdminOrderReturnController::class)->approve($returnRequest->id);
        $this->assertTrue($approveResponse->getData()->status);

        $order->refresh();
        $this->assertSame('return_pickup', $order->status);

        $receiveResponse = app(AdminOrderReturnController::class)->receive($returnRequest->id);
        $this->assertTrue($receiveResponse->getData()->status);

        $order->refresh();
        $product->refresh();

        $this->assertSame('replacement_shipping', $order->status);
        $this->assertSame(3, $product->stock);

        $returnRequest->refresh();
        $this->assertNotNull($returnRequest->received_at);
        $this->assertSame(2, $returnRequest->items[0]['quantity']);
        $this->assertArrayHasKey('replacement_allocations', $returnRequest->items[0]);

        $completeResponse = app(AdminOrderReturnController::class)->complete($returnRequest->id);
        $this->assertTrue($completeResponse->getData()->status);

        $order->refresh();
        $this->assertSame('replacement_completed', $order->status);
    }
}
