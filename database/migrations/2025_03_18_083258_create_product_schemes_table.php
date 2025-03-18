<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('product_schemes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->integer('org_id');
            $table->string('scheme_name');
            $table->enum('type', ['discount', 'subscription', 'installment', 'bundle', 'cashback']);
            $table->decimal('value', 10, 2)->nullable(); // Discount %, cashback amount, etc.
            $table->integer('duration')->nullable(); // Duration in days (for subscriptions)
            $table->json('bundle_products')->nullable(); // For bundle schemes
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('product_schemes');
    }
};
