<?php

return [
    [
        'key'   => 'account',
        'name'  => 'shop::app.layouts.my-account',
        'route' =>'customer.profile.index',
        'sort'  => 1,
    ], [
        'key'   => 'account.profile',
        'name'  => 'shop::app.layouts.profile',
        'route' =>'customer.profile.index',
        'sort'  => 1,
    ], [
        'key'   => 'account.tracking',
        'name'  => 'Tracking Hub',
        'route' => 'hws.customer.account.tracking',
        'sort'  => 2,
    ], [
        'key'   => 'account.address',
        'name'  => 'shop::app.layouts.address',
        'route' =>'customer.address.index',
        'sort'  => 3,
    ], [
        'key'   => 'account.reviews',
        'name'  => 'shop::app.layouts.reviews',
        'route' =>'customer.reviews.index',
        'sort'  => 4,
    ], [
        'key'   => 'account.wishlist',
        'name'  => 'shop::app.layouts.wishlist',
        'route' =>'customer.wishlist.index',
        'sort'  => 5,
    ], [
        'key'   => 'account.compare',
        'name'  => 'shop::app.customer.compare.text',
        'route' =>'velocity.customer.product.compare',
        'sort'  => 6,
    ], [
        'key'   => 'account.orders',
        'name'  => 'shop::app.layouts.orders',
        'route' =>'customer.orders.index',
        'sort'  => 7,
    ], [
        'key'   => 'account.downloadables',
        'name'  => 'shop::app.layouts.downloadable-products',
        'route' =>'customer.downloadable_products.index',
        'sort'  => 8,
    ]
];

?>
