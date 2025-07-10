<?php

declare(strict_types=1);

use App\Http\Controllers\Addresses\AddressController;
use App\Http\Controllers\Admin\AdminNewsletterController;
use App\Http\Controllers\Analytics\AnalyticsController;
use App\Http\Controllers\Banners\BannerController;
use App\Http\Controllers\Carts\CartController;
use App\Http\Controllers\Categories\CategoryController;
use App\Http\Controllers\Checkout\CheckoutController;
use App\Http\Controllers\Coupons\CouponController;
use App\Http\Controllers\Images\ImageController;
use App\Http\Controllers\Newsletter\NewsletterController;
use App\Http\Controllers\Notifications\NotificationController;
use App\Http\Controllers\Orders\OrderController;
use App\Http\Controllers\Pages\AboutPageController;
use App\Http\Controllers\Pages\ContactController;
use App\Http\Controllers\Products\ProductController;
use App\Http\Controllers\Reviews\ReviewController;
use App\Http\Controllers\Users\AuthController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Wishlists\WishlistController;
use App\Http\Middleware\CheckRoleMiddleware;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:5,1');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1');

    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
        ->middleware('throttle:5,1');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->middleware('throttle:5,1');

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
        Route::get('about', [AboutPageController::class, 'getAboutPage']);
        Route::get('contact', [ContactController::class, 'getContactInfo']);
        Route::post('contact/send', [ContactController::class, 'sendContactForm']);
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

        Route::prefix('notifications')->group(function () {
            Route::get('preferences', [NotificationController::class, 'getNotificationPreferences']);
            Route::put('preferences', [NotificationController::class, 'updateNotificationPreferences']);
        });
    });

    Route::middleware('auth:sanctum')->prefix('addresses')->group(function () {
        Route::get('', [AddressController::class, 'getUserAddresses']);
        Route::post('create', [AddressController::class, 'createAddress']);
        Route::get('{addressId}', [AddressController::class, 'getAddressById']);
        Route::put('{addressId}', [AddressController::class, 'updateAddress']);
        Route::delete('{addressId}', [AddressController::class, 'deleteAddress']);
        Route::patch('{addressId}/set-default', [AddressController::class, 'setDefaultAddress']);
    });
});

Route::middleware('auth:sanctum')->prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'getCart']);
    Route::post('/add', [CartController::class, 'addToCart']);
    Route::patch('/update/{item_id}', [CartController::class, 'updateCartItem']);
    Route::delete('/remove/{item_id}', [CartController::class, 'removeFromCart']);
    Route::delete('/clear', [CartController::class, 'clearCart']);
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
        Route::get('/', [OrderController::class, 'getAllOrders']);
        Route::get('/{orderId}', [OrderController::class, 'getOrderById']);
        Route::patch('/{orderId}/status', [OrderController::class, 'updateOrderStatus']);
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
    Route::get('/', [WishlistController::class, 'getWishlist']);
    Route::post('/add', [WishlistController::class, 'addToWishlist']);
    Route::delete('/remove/{productId}', [WishlistController::class, 'removeFromWishlist']);
    Route::get('/check/{productId}', [WishlistController::class, 'checkInWishlist']);
    Route::delete('/clear', [WishlistController::class, 'clearWishlist']);
});

Route::middleware(['auth:sanctum', CheckRoleMiddleware::class . ':admin|moderator'])
    ->prefix('admin/pages')
    ->group(function () {
        Route::prefix('about')->group(function () {
            Route::get('', [AboutPageController::class, 'getAllAboutPages']);
            Route::post('create', [AboutPageController::class, 'createAboutPage']);
            Route::put('{id}', [AboutPageController::class, 'updateAboutPage']);
            Route::delete('{id}', [AboutPageController::class, 'deleteAboutPage']);
            Route::get('{id}', [AboutPageController::class, 'getAboutPageById']);
        });

        Route::prefix('contact')->group(function () {
            Route::get('', [ContactController::class, 'getContactInfo']);
            Route::put('update', [ContactController::class, 'updateContactInfo']);
        });
    });

Route::middleware(['auth:sanctum', CheckRoleMiddleware::class . ':admin|moderator'])
    ->prefix('admin/analytics')
    ->group(function () {
        Route::get('/dashboard', [AnalyticsController::class, 'getDashboardData']);
        Route::get('/sales', [AnalyticsController::class, 'getSalesAnalytics']);
        Route::get('/customers', [AnalyticsController::class, 'getCustomerAnalyticsDetails']);
        Route::get('/products', [AnalyticsController::class, 'getProductAnalyticsDetails']);
        Route::get('/inventory', [AnalyticsController::class, 'getInventoryAnalyticsDetails']);
    });

Route::middleware('auth:sanctum')->prefix('coupons')->group(function () {
    Route::post('validate', [CouponController::class, 'validateCoupon']);
    Route::get('active', [CouponController::class, 'getActiveCoupons']);
});

Route::middleware(['auth:sanctum', CheckRoleMiddleware::class . ':admin|moderator'])
    ->prefix('admin/coupons')
    ->group(function () {
        Route::get('/', [CouponController::class, 'getAllCoupons']);
        Route::post('create', [CouponController::class, 'createCoupon']);
        Route::get('{couponId}', [CouponController::class, 'getCouponById']);
        Route::put('{couponId}', [CouponController::class, 'updateCoupon']);
        Route::delete('{couponId}', [CouponController::class, 'deleteCoupon']);
        Route::get('{couponId}/usage-stats', function (int $couponId, App\Services\CouponService $couponService) {
            return response()->json($couponService->getCouponUsageStats($couponId));
        });
    });

Route::prefix('newsletter')->group(function () {
    Route::post('subscribe', [NewsletterController::class, 'subscribe']);
    Route::post('unsubscribe', [NewsletterController::class, 'unsubscribeAPI']);
});

Route::prefix('admin/newsletter')
    ->middleware(['auth:sanctum', CheckRoleMiddleware::class . ':admin|moderator'])
    ->group(function () {
        Route::get('stats', [AdminNewsletterController::class, 'getSubscriberStats']);
        Route::get('subscribers', [AdminNewsletterController::class, 'getSubscribers']);
        Route::post('send', [AdminNewsletterController::class, 'sendNewsletter']);
    });
