<?php
return [
    'api_url' => env('GHN_API_URL', 'https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee'),
    'token' => env('GHN_TOKEN'),
    'shop_id' => env('GHN_SHOP_ID'),
    'from_district_id' => env('GHN_FROM_DISTRICT_ID'),
    'from_ward_id' => env('GHN_FROM_WARD_ID'),
    'service_id' => env('GHN_SERVICE_ID'),
    'service_type_id' => env('GHN_SERVICE_TYPE_ID'),
    'default_weight' => env('GHN_DEFAULT_WEIGHT', 500), 
    'default_length' => env('GHN_DEFAULT_LENGTH', 20),
    'default_width' => env('GHN_DEFAULT_WIDTH', 15),
    'default_height' => env('GHN_DEFAULT_HEIGHT', 5),
    'fallback_fee' => env('GHN_FALLBACK_FEE', 25000),
];