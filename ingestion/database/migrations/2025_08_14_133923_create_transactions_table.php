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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('Invoice');
            $table->string('StockCode');
            $table->string('Description')->nullable();
            $table->integer('Quantity');
            $table->string('InvoiceDate');
            $table->decimal('Price', 10, 2);
            $table->string('CustomerID')->nullable();
            $table->string('Country');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
