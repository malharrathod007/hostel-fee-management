<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('persons')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->tinyInteger('fee_month'); // 1-12
            $table->year('fee_year');
            $table->enum('status', ['pending', 'paid', 'partial'])->default('pending');
            $table->date('paid_date')->nullable();
            $table->string('payment_mode')->nullable(); // cash, upi, bank
            $table->string('receipt_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['person_id', 'fee_month', 'fee_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};
