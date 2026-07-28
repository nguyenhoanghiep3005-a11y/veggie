<?php

return [
    'enabled' => env('CLOUDINARY_ENABLED', true),
    'cloud_name' => env('CLOUDINARY_CLOUD_NAME', 'fmlmichm'),
    'api_key' => env('CLOUDINARY_API_KEY', '522984566368411'),
    'api_secret' => env('CLOUDINARY_API_SECRET'),
    'verify_ssl' => env('CLOUDINARY_VERIFY_SSL', true),
    'folders' => [
        'order_returns' => env('CLOUDINARY_ORDER_RETURN_FOLDER', 'Đổi trả'),
        'damage_slips' => env('CLOUDINARY_DAMAGE_SLIP_FOLDER', 'damage-slips'),
        'warehouse_damages' => env('CLOUDINARY_WAREHOUSE_DAMAGE_FOLDER', 'warehouse-damages'),
    ],
];
