<?php

declare(strict_types=1);

use App\Http\Controllers\Banners\BannerController;
use App\Http\Controllers\Categories\CategoryController;
use App\Http\Controllers\Checkout\CheckoutController;
use App\Http\Controllers\Images\ImageController;
use App\Http\Controllers\Orders\OrderController;
use App\Http\Controllers\Products\ProductController;
use App\Http\Controllers\Reviews\ReviewController;
use App\Http\Controllers\Users\AuthController;
use App\Http\Controllers\Users\UserController;
use App\Http\Middleware\CheckRoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:5,1');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->prefix('user')->group(function () {
    Route::middleware(CheckRoleMiddleware::class . ':admin')->group(function () {
        Route::get('getAll', [UserController::class, 'getAll']);
        Route::delete('delete/{userId}', [UserController::class, 'deleteUser']);
        Route::post('create', [UserController::class, 'createUser']);
        Route::patch('edit/{userId}', [UserController::class, 'editUser']);
    });

    Route::get('{userId}', [UserController::class, 'getUserById']);
});

Route::middleware(['auth:sanctum', CheckRoleMiddleware::class . ':admin|moderator'])
    ->prefix('categories')
    ->group(function () {
        Route::get('getAll', [CategoryController::class, 'getAllCategories']);
        Route::post('create', [CategoryController::class, 'createCategory']);
        Route::get('{categoryId}', [CategoryController::class, 'getCategoryById']);
        Route::post('edit/{categoryId}', [CategoryController::class, 'editCategory']);
        Route::delete('delete/{categoryId}', [CategoryController::class, 'deleteCategory']);
    });

Route::middleware(['auth:sanctum', CheckRoleMiddleware::class . ':admin|moderator'])
    ->prefix('products')
    ->group(function () {
        Route::get('getAll', [ProductController::class, 'getAllProducts']);
        Route::post('create', [ProductController::class, 'createProduct']);
        Route::get('{productId}', [ProductController::class, 'getProductById']);
        Route::patch('edit/{productId}', [ProductController::class, 'editProduct']);
        Route::delete('delete/{productId}', [ProductController::class, 'deleteProduct']);
    });

Route::middleware(['auth:sanctum', CheckRoleMiddleware::class . ':admin|moderator'])
    ->prefix('images')
    ->group(function () {
        Route::get('{relatedId}', [ImageController::class, 'getImages']);
        Route::post('upload/{relatedId}', [ImageController::class, 'uploadImages']);
        Route::patch('set-primary/{imageId}', [ImageController::class, 'setPrimaryImage']);
        Route::delete('delete/{imageId}', [ImageController::class, 'deleteImage']);
    });

Route::middleware(['auth:sanctum', CheckRoleMiddleware::class . ':admin|moderator'])
    ->prefix('banners')
    ->group(function () {
        Route::get('getAll', [BannerController::class, 'getAllBanners']);
        Route::post('create', [BannerController::class, 'createBanner']);
        Route::get('{bannerId}', [BannerController::class, 'getBannerById']);
        Route::post('edit/{bannerId}', [BannerController::class, 'editBanner']);
        Route::delete('delete/{bannerId}', [BannerController::class, 'deleteBanner']);
    });

Route::prefix('public')->group(function () {
    Route::prefix('categories')->group(function () {
        Route::get('getAllActiveCategories', [CategoryController::class, 'getAllActiveCategories']);
    });

    Route::prefix('products')->group(function () {
        Route::get('getAllPopularActiveProducts', [ProductController::class, 'getAllPopularActiveProducts']);
        Route::get('getAllSearchableProducts', [ProductController::class, 'getAllSearchableProducts']);
        Route::get('getRelatedProducts', [ProductController::class, 'getRelatedProducts']);
        Route::get('getSaleProducts', [ProductController::class, 'getSaleProducts']);
        Route::get('suggestions', [ProductController::class, 'getProductSuggestions']);

        Route::prefix('{slug}')->group(function () {
            Route::get('', [ProductController::class, 'getProductBySlug']);
        });
    });

    Route::prefix('images')->group(function () {
        Route::prefix('{relatedId}')->group(function () {
            Route::get('', [ImageController::class, 'getImages']);
        });
    });

    Route::prefix('banners')->group(function () {
        Route::get('getAllActiveBanners', [BannerController::class, 'getAllActiveBanners']);
    });

    Route::prefix('pages')->group(function () {
        Route::get('about', [App\Http\Controllers\Pages\AboutPageController::class, 'getAboutPage']);
        Route::get('contact', [App\Http\Controllers\Pages\ContactController::class, 'getContactInfo']);
        Route::post('contact/send', [App\Http\Controllers\Pages\ContactController::class, 'sendContactForm']);
    });
});

Route::prefix('profile')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('', [UserController::class, 'getUserProfile']);
        Route::post('update', [UserController::class, 'updateUserProfile']);
        Route::post('change-password', [UserController::class, 'changePassword']);
        Route::post('update-image', [UserController::class, 'updateProfileImage']);
        Route::delete('remove-image', [UserController::class, 'removeProfileImage']);
        Route::get('products/{productId}/reviews', [ReviewController::class, 'getProductReviews']);
    });

    Route::middleware('auth:sanctum')->prefix('addresses')->group(function () {
        Route::get('', [App\Http\Controllers\Addresses\AddressController::class, 'getUserAddresses']);
        Route::post('create', [App\Http\Controllers\Addresses\AddressController::class, 'createAddress']);
        Route::get('{addressId}', [App\Http\Controllers\Addresses\AddressController::class, 'getAddressById']);
        Route::put('{addressId}', [App\Http\Controllers\Addresses\AddressController::class, 'updateAddress']);
        Route::delete('{addressId}', [App\Http\Controllers\Addresses\AddressController::class, 'deleteAddress']);
        Route::patch('{addressId}/set-default', [App\Http\Controllers\Addresses\AddressController::class, 'setDefaultAddress']);
    });
});

Route::middleware('auth:sanctum')->prefix('cart')->group(function () {
    Route::get('/', [App\Http\Controllers\Carts\CartController::class, 'getCart']);
    Route::post('/add', [App\Http\Controllers\Carts\CartController::class, 'addToCart']);
    Route::patch('/update/{item_id}', [App\Http\Controllers\Carts\CartController::class, 'updateCartItem']);
    Route::delete('/remove/{item_id}', [App\Http\Controllers\Carts\CartController::class, 'removeFromCart']);
    Route::delete('/clear', [App\Http\Controllers\Carts\CartController::class, 'clearCart']);
});

Route::prefix('checkout')->group(function () {
    Route::post('/initiate', [CheckoutController::class, 'initiateCheckout']);
    Route::post('/payment', [CheckoutController::class, 'processPayment']);
    Route::post('/webhook/stripe', [CheckoutController::class, 'handleStripeWebhook']);
});

Route::middleware('auth:sanctum')->prefix('checkout')->group(function () {
    Route::post('/initiate', [CheckoutController::class, 'initiateCheckout']);
    Route::post('/payment', [CheckoutController::class, 'processPayment']);
});

Route::post('/checkout/webhook/stripe', [CheckoutController::class, 'handleStripeWebhook']);

Route::middleware('auth:sanctum')->prefix('orders')->group(function () {
    Route::get('/', [OrderController::class, 'getUserOrders']);
    Route::get('/{orderId}', [OrderController::class, 'getOrderById']);
    Route::get('/number/{orderNumber}', [OrderController::class, 'getOrderByNumber']);
    Route::post('/{orderId}/cancel', [OrderController::class, 'cancelOrder']);
});

Route::middleware(['auth:sanctum', CheckRoleMiddleware::class . ':admin|moderator'])
    ->prefix('admin/orders')
    ->group(function () {
        Route::get('/', [App\Http\Controllers\Orders\OrderController::class, 'getAllOrders']);
        Route::get('/{orderId}', [App\Http\Controllers\Orders\OrderController::class, 'getOrderById']);
        Route::patch('/{orderId}/status', [App\Http\Controllers\Orders\OrderController::class, 'updateOrderStatus']);
    });

Route::middleware('auth:sanctum')->prefix('reviews')->group(function () {
    Route::get('/user', [ReviewController::class, 'getUserReviews']);
    Route::post('/create', [ReviewController::class, 'createReview']);
    Route::delete('/{reviewId}', [ReviewController::class, 'deleteReview']);
});

Route::middleware(['auth:sanctum', CheckRoleMiddleware::class . ':admin|moderator'])->prefix('admin/reviews')->group(function () {
    Route::get('/', [ReviewController::class, 'getAllReviews']);
    Route::get('/pending', [ReviewController::class, 'getPendingReviews']);
    Route::patch('/{reviewId}/status', [ReviewController::class, 'updateReviewStatus']);
    Route::delete('/{reviewId}', [ReviewController::class, 'deleteReview']);
});

Route::middleware('auth:sanctum')->prefix('wishlist')->group(function () {
    Route::get('/', [App\Http\Controllers\Wishlists\WishlistController::class, 'getWishlist']);
    Route::post('/add', [App\Http\Controllers\Wishlists\WishlistController::class, 'addToWishlist']);
    Route::delete('/remove/{productId}', [App\Http\Controllers\Wishlists\WishlistController::class, 'removeFromWishlist']);
    Route::get('/check/{productId}', [App\Http\Controllers\Wishlists\WishlistController::class, 'checkInWishlist']);
    Route::delete('/clear', [App\Http\Controllers\Wishlists\WishlistController::class, 'clearWishlist']);
});

Route::middleware(['auth:sanctum', CheckRoleMiddleware::class . ':admin|moderator'])
    ->prefix('admin/pages')
    ->group(function () {
        Route::prefix('about')->group(function () {
            Route::get('', [App\Http\Controllers\Pages\AboutPageController::class, 'getAllAboutPages']);
            Route::post('create', [App\Http\Controllers\Pages\AboutPageController::class, 'createAboutPage']);
            Route::put('{id}', [App\Http\Controllers\Pages\AboutPageController::class, 'updateAboutPage']);
            Route::delete('{id}', [App\Http\Controllers\Pages\AboutPageController::class, 'deleteAboutPage']);
            Route::get('{id}', [App\Http\Controllers\Pages\AboutPageController::class, 'getAboutPageById']);
        });

        Route::prefix('contact')->group(function () {
            Route::get('', [App\Http\Controllers\Pages\ContactController::class, 'getContactInfo']);
            Route::put('update', [App\Http\Controllers\Pages\ContactController::class, 'updateContactInfo']);
        });
    });
