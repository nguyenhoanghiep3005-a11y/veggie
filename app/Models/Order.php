<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_price',
        'status',
        'shipping_address_id',
        'shipping_address_data',
        'subtotal',
        'shipping_fee',
        'discount_amount',
        'coupon_id',
        'coupon_code',
        'completed_at',
        'canceled_by',
        'cancel_reason',
    ];

    protected $casts = [
        'shipping_address_data' => 'array',
        'subtotal' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    // Lấy thời điểm bắt đầu tính hạn đổi/trả hàng.
    public function returnStartedAt()
    {
        if ($this->completed_at) {
            return $this->completed_at;
        }

        if ($this->status == 'completed') {
            return $this->updated_at;
        }

        return null;
    }

    // Lấy hạn cuối được gửi yêu cầu đổi/trả hàng.
    public function returnDeadline()
    {
        $startedAt = $this->returnStartedAt();
        if (! $startedAt) {
            return null;
        }

        return $startedAt->copy()->addDays(3);
    }

    // Kiểm tra đơn hàng còn được gửi yêu cầu đổi/trả không.
    public function isReturnPeriodAvailable()
    {
        if ($this->status != 'completed') {
            return false;
        }

        if ($this->returnRequest) {
            return false;
        }

        $deadline = $this->returnDeadline();
        if (! $deadline) {
            return false;
        }

        return now()->lessThanOrEqualTo($deadline);
    }

    // Hiển thị hạn cuối được gửi yêu cầu đổi/trả.
    public function returnDeadlineLabel()
    {
        $deadline = $this->returnDeadline();
        if ($deadline) {
            return $deadline->format('d/m/Y H:i');
        }

        return '—';
    }

    // Lấy địa chỉ giao hàng từ dữ liệu đã lưu hoặc từ bảng địa chỉ.
    public function getShippingAddressAttribute()
    {
        if ($this->shipping_address_data) {
            if (is_string($this->shipping_address_data)) {
                $data = json_decode($this->shipping_address_data, true);
            } else {
                $data = $this->shipping_address_data;
            }

            $fullName = 'Khách vãng lai';
            $phone = '—';
            $address = '—';
            $city = '—';
            $provinceId = null;
            $districtId = null;
            $wardId = null;

            if (isset($data['full_name'])) {
                $fullName = $data['full_name'];
            } elseif (isset($data['guest_name'])) {
                $fullName = $data['guest_name'];
            }

            if (isset($data['phone'])) {
                $phone = $data['phone'];
            } elseif (isset($data['guest_phone'])) {
                $phone = $data['guest_phone'];
            }

            if (isset($data['address'])) {
                $address = $data['address'];
            } elseif (isset($data['guest_address'])) {
                $address = $data['guest_address'];
            }

            if (isset($data['city'])) {
                $city = $data['city'];
            } elseif (isset($data['guest_city'])) {
                $city = $data['guest_city'];
            }

            if (isset($data['province_id'])) {
                $provinceId = $data['province_id'];
            } elseif (isset($data['guest_province_id'])) {
                $provinceId = $data['guest_province_id'];
            }

            if (isset($data['district_id'])) {
                $districtId = $data['district_id'];
            } elseif (isset($data['guest_district_id'])) {
                $districtId = $data['guest_district_id'];
            }

            if (isset($data['ward_id'])) {
                $wardId = $data['ward_id'];
            } elseif (isset($data['guest_ward_id'])) {
                $wardId = $data['guest_ward_id'];
            }

            return new ShippingAddress([
                'full_name' => $fullName,
                'phone' => $phone,
                'address' => $address,
                'city' => $city,
                'province_id' => $provinceId,
                'district_id' => $districtId,
                'ward_id' => $wardId,
            ]);
        }

        if ($this->shipping_address_id) {
            $address = $this->getRelationValue('shippingAddress');
            if ($address) {
                return $address;
            }

            return $this->shippingAddress()->first();
        }

        return null;
    }

    // Cho phép gọi shippingAddress như thuộc tính để đơn guest lấy địa chỉ JSON.
    public function __get($key)
    {
        if ($key == 'shippingAddress' || $key == 'shipping_address') {
            return $this->getShippingAddressAttribute();
        }

        return parent::__get($key);
    }

    // Lấy danh sách sản phẩm trong đơn hàng.
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Lấy tài khoản đã đặt đơn.
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Lấy địa chỉ giao hàng đã lưu.
    public function shippingAddress()
    {
        return $this->belongsTo(ShippingAddress::class);
    }

    // Lấy thông tin thanh toán của đơn hàng.
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // Lấy mã giảm giá đã dùng cho đơn hàng.
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    // Lấy yêu cầu đổi/trả hàng của đơn.
    public function returnRequest()
    {
        return $this->hasOne(OrderReturn::class);
    }

    // Tên trạng thái mặc định của đơn hàng.
    public function statusLabel()
    {
        return $this->clientStatusLabel();
    }

    // Tên trạng thái hiển thị cho khách hàng.
    public function clientStatusLabel()
    {
        if ($this->status == 'canceled') {
            if ($this->canceled_by == 'admin') {
                return 'Đã hủy bởi quản trị viên';
            }

            return 'Đã hủy đơn hàng';
        }

        if ($this->status == 'pending') {
            return 'Chờ xác nhận';
        }

        if ($this->status == 'confirmed') {
            return 'Đã xác nhận';
        }

        if ($this->status == 'shipping') {
            return 'Đang giao hàng';
        }

        if ($this->status == 'completed') {
            return 'Đã giao hàng';
        }

        if ($this->status == 'return_requested') {
            return 'Chờ duyệt đổi/trả hàng lỗi';
        }

        if ($this->status == 'return_pickup') {
            return 'Chờ gửi hàng lỗi về cửa hàng';
        }

        if ($this->status == 'replacement_shipping') {
            return 'Đang giao sản phẩm đổi';
        }

        if ($this->status == 'replacement_completed') {
            return 'Hoàn tất yêu cầu đổi';
        }

        return $this->status;
    }

    // Tên trạng thái hiển thị cho admin.
    public function adminStatusLabel()
    {
        if ($this->status == 'canceled') {
            if ($this->canceled_by == 'admin') {
                return 'QTV hủy đơn';
            }

            return 'Khách hủy đơn';
        }

        if ($this->status == 'pending') {
            return 'Chờ xác nhận';
        }

        if ($this->status == 'confirmed') {
            return 'Đã xác nhận';
        }

        if ($this->status == 'shipping') {
            return 'Đang giao';
        }

        if ($this->status == 'completed') {
            return 'Đã giao';
        }

        if ($this->status == 'return_requested') {
            return 'Chờ duyệt hàng lỗi';
        }

        if ($this->status == 'return_pickup') {
            return 'Chờ nhận hàng lỗi';
        }

        if ($this->status == 'replacement_shipping') {
            return 'Đang giao sản phẩm đổi';
        }

        if ($this->status == 'replacement_completed') {
            return 'Hoàn tất yêu cầu đổi';
        }

        return $this->status;
    }

    // Class badge trạng thái cho admin.
    public function adminStatusClass()
    {
        if ($this->status == 'pending' || $this->status == 'return_requested') {
            return 'custom-badge badge badge-warning';
        }

        if ($this->status == 'confirmed') {
            return 'custom-badge badge badge-primary';
        }

        if ($this->status == 'shipping' || $this->status == 'return_pickup' || $this->status == 'replacement_shipping') {
            return 'custom-badge badge badge-info';
        }

        if ($this->status == 'completed' || $this->status == 'replacement_completed') {
            return 'custom-badge badge badge-success';
        }

        if ($this->status == 'canceled') {
            return 'custom-badge badge badge-danger';
        }

        return 'custom-badge badge badge-secondary';
    }

    // Class badge trạng thái cho khách hàng.
    public function clientStatusClass()
    {
        if ($this->status == 'pending' || $this->status == 'return_requested') {
            return 'bg-warning';
        }

        if ($this->status == 'confirmed') {
            return 'bg-primary';
        }

        if ($this->status == 'shipping' || $this->status == 'return_pickup' || $this->status == 'replacement_shipping') {
            return 'bg-info';
        }

        if ($this->status == 'completed' || $this->status == 'replacement_completed') {
            return 'bg-success';
        }

        if ($this->status == 'canceled') {
            return 'bg-danger';
        }

        return 'bg-secondary';
    }

    // Tên dòng phí vận chuyển.
    public function shippingFeeLabel()
    {
        return 'Phí vận chuyển';
    }

    // Tên dòng tổng tiền.
    public function totalLabel()
    {
        if ($this->status == 'replacement_shipping' || $this->status == 'replacement_completed') {
            return 'Tổng tiền đơn hàng gốc';
        }

        return 'Tổng tiền đơn hàng';
    }
}
