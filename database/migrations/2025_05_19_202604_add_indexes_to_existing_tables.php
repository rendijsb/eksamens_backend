<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('role_id', 'users_role_id_index');
            $table->index('created_at', 'users_created_at_index');
            $table->index('phone', 'users_phone_index');
            $table->index(['email', 'role_id'], 'users_email_role_index');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->index('name', 'roles_name_index');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index('slug', 'categories_slug_index');
            $table->index('name', 'categories_name_index');
            $table->index('created_at', 'categories_created_at_index');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('category_id', 'products_category_id_index');
            $table->index('status', 'products_status_index');
            $table->index('stock', 'products_stock_index');
            $table->index('price', 'products_price_index');
            $table->index('sale_price', 'products_sale_price_index');
            $table->index('sale_ends_at', 'products_sale_ends_at_index');
            $table->index('sold', 'products_sold_index');
            $table->index('slug', 'products_slug_index');
            $table->index('name', 'products_name_index');
            $table->index('created_at', 'products_created_at_index');
            $table->index(['status', 'stock'], 'products_status_stock_index');
            $table->index(['category_id', 'status'], 'products_category_status_index');
            $table->index(['category_id', 'status', 'stock'], 'products_category_status_stock_index');
            $table->index(['status', 'sold'], 'products_status_sold_index');
            $table->index(['status', 'price'], 'products_status_price_index');
            $table->index(['status', 'sale_price'], 'products_status_sale_price_index');
            $table->index(['sale_price', 'sale_ends_at'], 'products_sale_price_ends_index');
            $table->index(['status', 'created_at'], 'products_status_created_index');
        });

        Schema::table('images', function (Blueprint $table) {
            $table->index('related_id', 'images_related_id_index');
            $table->index('type', 'images_type_index');
            $table->index('is_primary', 'images_is_primary_index');
            $table->index(['related_id', 'type'], 'images_related_type_index');
            $table->index(['related_id', 'type', 'is_primary'], 'images_related_type_primary_index');
            $table->index(['type', 'is_primary'], 'images_type_primary_index');
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->index('is_active', 'banners_is_active_index');
            $table->index('created_at', 'banners_created_at_index');
            $table->index(['is_active', 'created_at'], 'banners_active_created_index');
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->index('user_id', 'addresses_user_id_index');
            $table->index('is_default', 'addresses_is_default_index');
            $table->index('type', 'addresses_type_index');
            $table->index(['user_id', 'is_default'], 'addresses_user_default_index');
            $table->index(['user_id', 'type'], 'addresses_user_type_index');
            $table->index('created_at', 'addresses_created_at_index');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->index('user_id', 'carts_user_id_index');
            $table->index('updated_at', 'carts_updated_at_index');
            $table->index('created_at', 'carts_created_at_index');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->index('cart_id', 'cart_items_cart_id_index');
            $table->index('product_id', 'cart_items_product_id_index');
            $table->index('created_at', 'cart_items_created_at_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('user_id', 'orders_user_id_index');
            $table->index('order_number', 'orders_order_number_index');
            $table->index('status', 'orders_status_index');
            $table->index('payment_status', 'orders_payment_status_index');
            $table->index('customer_email', 'orders_customer_email_index');
            $table->index('created_at', 'orders_created_at_index');
            $table->index('coupon_id', 'orders_coupon_id_index');
            $table->index('shipping_address_id', 'orders_shipping_address_id_index');
            $table->index('billing_address_id', 'orders_billing_address_id_index');

            $table->index(['user_id', 'status'], 'orders_user_status_index');
            $table->index(['user_id', 'created_at'], 'orders_user_created_index');
            $table->index(['status', 'payment_status'], 'orders_status_payment_index');
            $table->index(['status', 'created_at'], 'orders_status_created_index');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->index('order_id', 'order_items_order_id_index');
            $table->index('product_id', 'order_items_product_id_index');
            $table->index(['order_id', 'product_id'], 'order_items_order_product_index');
        });

        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->index('order_id', 'payment_transactions_order_id_index');
            $table->index('transaction_id', 'payment_transactions_transaction_id_index');
            $table->index('status', 'payment_transactions_status_index');
            $table->index('payment_method', 'payment_transactions_method_index');
            $table->index('created_at', 'payment_transactions_created_at_index');
            $table->index(['order_id', 'status'], 'payment_transactions_order_status_index');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->index('product_id', 'reviews_product_id_index');
            $table->index('user_id', 'reviews_user_id_index');
            $table->index('is_approved', 'reviews_is_approved_index');
            $table->index('rating', 'reviews_rating_index');
            $table->index('created_at', 'reviews_created_at_index');

            $table->index(['product_id', 'is_approved'], 'reviews_product_approved_index');
            $table->index(['product_id', 'is_approved', 'rating'], 'reviews_product_approved_rating_index');
            $table->index(['user_id', 'is_approved'], 'reviews_user_approved_index');
            $table->index(['is_approved', 'created_at'], 'reviews_approved_created_index');
        });

        Schema::table('wishlist_items', function (Blueprint $table) {
            $table->index('user_id', 'wishlist_items_user_id_index');
            $table->index('product_id', 'wishlist_items_product_id_index');
            $table->index('created_at', 'wishlist_items_created_at_index');
        });

        Schema::table('about_pages', function (Blueprint $table) {
            $table->index('is_active', 'about_pages_is_active_index');
            $table->index('created_at', 'about_pages_created_at_index');
            $table->index(['is_active', 'created_at'], 'about_pages_active_created_index');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->index('email', 'contacts_email_index');
            $table->index('phone', 'contacts_phone_index');
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->index('code', 'coupons_code_index');
            $table->index('is_active', 'coupons_is_active_index');
            $table->index('starts_at', 'coupons_starts_at_index');
            $table->index('expires_at', 'coupons_expires_at_index');
            $table->index('type', 'coupons_type_index');
            $table->index('created_at', 'coupons_created_at_index');
            $table->index(['is_active', 'starts_at', 'expires_at'], 'coupons_active_dates_index');
            $table->index(['code', 'is_active'], 'coupons_code_active_index');
            $table->index(['type', 'is_active'], 'coupons_type_active_index');
            $table->index(['expires_at', 'is_active'], 'coupons_expires_active_index');
        });

        Schema::table('coupon_usages', function (Blueprint $table) {
            $table->index('coupon_id', 'coupon_usages_coupon_id_index');
            $table->index('user_id', 'coupon_usages_user_id_index');
            $table->index('order_id', 'coupon_usages_order_id_index');
            $table->index('created_at', 'coupon_usages_created_at_index');
            $table->index(['coupon_id', 'user_id'], 'coupon_usages_coupon_user_index');
        });

        Schema::table('newsletter_subscriptions', function (Blueprint $table) {
            $table->index('email', 'newsletter_subscriptions_email_index');
            $table->index('token', 'newsletter_subscriptions_token_index');
            $table->index('is_active', 'newsletter_subscriptions_is_active_index');
            $table->index('subscribed_at', 'newsletter_subscriptions_subscribed_at_index');
            $table->index('unsubscribed_at', 'newsletter_subscriptions_unsubscribed_at_index');
            $table->index(['is_active', 'subscribed_at'], 'newsletter_subscriptions_active_subscribed_index');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_id_index');
            $table->dropIndex('users_created_at_index');
            $table->dropIndex('users_phone_index');
            $table->dropIndex('users_email_role_index');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropIndex('roles_name_index');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_slug_index');
            $table->dropIndex('categories_name_index');
            $table->dropIndex('categories_created_at_index');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_category_id_index');
            $table->dropIndex('products_status_index');
            $table->dropIndex('products_stock_index');
            $table->dropIndex('products_price_index');
            $table->dropIndex('products_sale_price_index');
            $table->dropIndex('products_sale_ends_at_index');
            $table->dropIndex('products_sold_index');
            $table->dropIndex('products_slug_index');
            $table->dropIndex('products_name_index');
            $table->dropIndex('products_created_at_index');
            $table->dropIndex('products_status_stock_index');
            $table->dropIndex('products_category_status_index');
            $table->dropIndex('products_category_status_stock_index');
            $table->dropIndex('products_status_sold_index');
            $table->dropIndex('products_status_price_index');
            $table->dropIndex('products_status_sale_price_index');
            $table->dropIndex('products_sale_price_ends_index');
            $table->dropIndex('products_status_created_index');
        });

        Schema::table('images', function (Blueprint $table) {
            $table->dropIndex('images_related_id_index');
            $table->dropIndex('images_type_index');
            $table->dropIndex('images_is_primary_index');
            $table->dropIndex('images_related_type_index');
            $table->dropIndex('images_related_type_primary_index');
            $table->dropIndex('images_type_primary_index');
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->dropIndex('banners_is_active_index');
            $table->dropIndex('banners_created_at_index');
            $table->dropIndex('banners_active_created_index');
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->dropIndex('addresses_user_id_index');
            $table->dropIndex('addresses_is_default_index');
            $table->dropIndex('addresses_type_index');
            $table->dropIndex('addresses_user_default_index');
            $table->dropIndex('addresses_user_type_index');
            $table->dropIndex('addresses_created_at_index');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropIndex('carts_user_id_index');
            $table->dropIndex('carts_updated_at_index');
            $table->dropIndex('carts_created_at_index');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex('cart_items_cart_id_index');
            $table->dropIndex('cart_items_product_id_index');
            $table->dropIndex('cart_items_created_at_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_user_id_index');
            $table->dropIndex('orders_order_number_index');
            $table->dropIndex('orders_status_index');
            $table->dropIndex('orders_payment_status_index');
            $table->dropIndex('orders_customer_email_index');
            $table->dropIndex('orders_created_at_index');
            $table->dropIndex('orders_coupon_id_index');
            $table->dropIndex('orders_shipping_address_id_index');
            $table->dropIndex('orders_billing_address_id_index');
            $table->dropIndex('orders_user_status_index');
            $table->dropIndex('orders_user_created_index');
            $table->dropIndex('orders_status_payment_index');
            $table->dropIndex('orders_status_created_index');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('order_items_order_id_index');
            $table->dropIndex('order_items_product_id_index');
            $table->dropIndex('order_items_order_product_index');
        });

        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropIndex('payment_transactions_order_id_index');
            $table->dropIndex('payment_transactions_transaction_id_index');
            $table->dropIndex('payment_transactions_status_index');
            $table->dropIndex('payment_transactions_method_index');
            $table->dropIndex('payment_transactions_created_at_index');
            $table->dropIndex('payment_transactions_order_status_index');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('reviews_product_id_index');
            $table->dropIndex('reviews_user_id_index');
            $table->dropIndex('reviews_is_approved_index');
            $table->dropIndex('reviews_rating_index');
            $table->dropIndex('reviews_created_at_index');
            $table->dropIndex('reviews_product_approved_index');
            $table->dropIndex('reviews_product_approved_rating_index');
            $table->dropIndex('reviews_user_approved_index');
            $table->dropIndex('reviews_approved_created_index');
        });

        Schema::table('wishlist_items', function (Blueprint $table) {
            $table->dropIndex('wishlist_items_user_id_index');
            $table->dropIndex('wishlist_items_product_id_index');
            $table->dropIndex('wishlist_items_created_at_index');
        });

        Schema::table('about_pages', function (Blueprint $table) {
            $table->dropIndex('about_pages_is_active_index');
            $table->dropIndex('about_pages_created_at_index');
            $table->dropIndex('about_pages_active_created_index');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropIndex('contacts_email_index');
            $table->dropIndex('contacts_phone_index');
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->dropIndex('coupons_code_index');
            $table->dropIndex('coupons_is_active_index');
            $table->dropIndex('coupons_starts_at_index');
            $table->dropIndex('coupons_expires_at_index');
            $table->dropIndex('coupons_type_index');
            $table->dropIndex('coupons_created_at_index');
            $table->dropIndex('coupons_active_dates_index');
            $table->dropIndex('coupons_code_active_index');
            $table->dropIndex('coupons_type_active_index');
            $table->dropIndex('coupons_expires_active_index');
        });

        Schema::table('coupon_usages', function (Blueprint $table) {
            $table->dropIndex('coupon_usages_coupon_id_index');
            $table->dropIndex('coupon_usages_user_id_index');
            $table->dropIndex('coupon_usages_order_id_index');
            $table->dropIndex('coupon_usages_created_at_index');
            $table->dropIndex('coupon_usages_coupon_user_index');
        });

        Schema::table('newsletter_subscriptions', function (Blueprint $table) {
            $table->dropIndex('newsletter_subscriptions_email_index');
            $table->dropIndex('newsletter_subscriptions_token_index');
            $table->dropIndex('newsletter_subscriptions_is_active_index');
            $table->dropIndex('newsletter_subscriptions_subscribed_at_index');
            $table->dropIndex('newsletter_subscriptions_unsubscribed_at_index');
            $table->dropIndex('newsletter_subscriptions_active_subscribed_index');
        });
    }
};
