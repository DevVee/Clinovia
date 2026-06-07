<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_id')->constrained()->cascadeOnDelete();
            $table->enum('transaction_type', ['stock_in', 'stock_out', 'dispensed', 'adjustment']);
            $table->integer('quantity'); // positive = in, negative = out
            $table->unsignedInteger('before_quantity');
            $table->unsignedInteger('after_quantity');
            $table->nullableMorphs('reference'); // reference_id, reference_type
            $table->string('batch_number')->nullable();
            $table->date('expiration_date')->nullable();
            $table->string('supplier')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['medicine_id', 'transaction_type']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
