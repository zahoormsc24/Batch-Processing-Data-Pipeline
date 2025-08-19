<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('quarterly_sales', function (Blueprint $table) {
        $table->id();
        $table->integer('customer_id');
        $table->string('stock_code');
        $table->timestamp('invoice_date');
        $table->integer('total_quantity');
        $table->decimal('total_sales', 10, 2);
        $table->timestamp('created_at')->useCurrent();
        $table->string('quarter');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quarterly_sales');
    }
};
