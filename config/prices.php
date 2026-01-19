<?php
// Liwonde Sun Hotel - Price Configuration File
// This file allows administrators to easily update room prices without changing code

// Room Pricing Configuration
$roomPrices = [
    'standard' => [
        'name' => 'Standard Room',
        'price' => 120, // Price in USD per night
        'currency_symbol' => '$',
        'description' => 'Comfortable accommodation with all essential amenities',
        'features' => [
            'King-size bed',
            'Free WiFi',
            'Flat-screen TV',
            'Daily breakfast',
            'Air conditioning'
        ]
    ],
    'deluxe' => [
        'name' => 'Deluxe Room',
        'price' => 180, // Price in USD per night
        'currency_symbol' => '$',
        'description' => 'Spacious room with premium amenities and scenic views',
        'features' => [
            'King-size bed',
            'Free WiFi',
            'Smart TV with streaming',
            'Mini bar & coffee maker',
            'Scenic view',
            'Luxury toiletries'
        ]
    ],
    'suite' => [
        'name' => 'Executive Suite',
        'price' => 280, // Price in USD per night
        'currency_symbol' => '$',
        'description' => 'Luxurious suite with separate living area and premium services',
        'features' => [
            'King-size bed',
            'Separate living area',
            'Free WiFi',
            'Two flat-screen TVs',
            'Wet bar',
            'Priority service',
            'Private terrace'
        ]
    ],
    'family' => [
        'name' => 'Family Suite',
        'price' => 220, // Price in USD per night
        'currency_symbol' => '$',
        'description' => 'Spacious accommodation with separate sleeping areas for parents and children',
        'features' => [
            'King-size bed + twin beds',
            'Free WiFi',
            'TV in each bedroom',
            'Kids-friendly amenities',
            'Children activities',
            'Kitchenette'
        ]
    ]
];

// Special Offers Configuration
$specialOffers = [
    [
        'title' => 'Early Bird Special',
        'discount' => '20%',
        'description' => 'Book 30 days in advance and save 20% on your stay',
        'conditions' => 'Valid for stays booked at least 30 days in advance'
    ],
    [
        'title' => 'Weekend Getaway',
        'discount' => '3rd Night Free',
        'description' => 'Enjoy a relaxing weekend with our special weekend package deal',
        'conditions' => 'Book a minimum of 3 nights on weekends'
    ],
    [
        'title' => 'Loyalty Package',
        'discount' => '15% Off',
        'description' => 'Exclusive benefits for our loyalty program members',
        'conditions' => 'Available to registered loyalty members only'
    ]
];

// Seasonal Pricing Multiplier (optional)
$seasonalMultiplier = [
    'peak' => 1.2,    // 20% increase during peak season
    'shoulder' => 1.0, // Regular price during shoulder season
    'off' => 0.8      // 20% discount during off-season
];

// Contact Information
$contactInfo = [
    'address' => 'Liwonde National Park, Malawi',
    'phone' => '+265 123 456 789',
    'email' => 'info@liwondesunhotel.com',
    'hours' => 'Open 24/7'
];

// Social Media Links
$socialLinks = [
    'facebook' => '#',
    'twitter' => '#',
    'instagram' => '#',
    'linkedin' => '#'
];

// Function to get room price by type
function getRoomPrice($roomType) {
    global $roomPrices;
    if (isset($roomPrices[$roomType])) {
        return $roomPrices[$roomType]['price'];
    }
    return 0;
}

// Function to get room name by type
function getRoomName($roomType) {
    global $roomPrices;
    if (isset($roomPrices[$roomType])) {
        return $roomPrices[$roomType]['name'];
    }
    return ucfirst($roomType) . ' Room';
}

// Function to get all room types
function getRoomTypes() {
    global $roomPrices;
    return array_keys($roomPrices);
}

// Function to get room details
function getRoomDetails($roomType) {
    global $roomPrices;
    if (isset($roomPrices[$roomType])) {
        return $roomPrices[$roomType];
    }
    return null;
}

// Function to format price with currency
function formatPrice($price) {
    global $roomPrices;
    $currencySymbol = $roomPrices['standard']['currency_symbol']; // Use standard room's currency as default
    return $currencySymbol . number_format($price, 0);
}
?>