<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('order_status_updates')->default(true);
            $table->boolean('promotional_emails')->default(true);
            $table->boolean('newsletter_emails')->default(true);
            $table->boolean('security_alerts')->default(true);
            $table->boolean('product_recommendations')->default(true);
            $table->boolean('inventory_alerts')->default(false);
            $table->boolean('price_drop_alerts')->default(false);
            $table->boolean('review_reminders')->default(true);
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(false);
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
