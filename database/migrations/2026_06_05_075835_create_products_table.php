<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
 public function up(): void
 {
 Schema::create('products', function (Blueprint $table) {
 $table->id();
 $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
 $table->string('code')->unique();
 $table->string('name');
 $table->integer('stock')->default(0);
 $table->integer('minimum_stock')->default(10);
 $table->timestamps();
 });
 }
 public function down(): void
 {
 Schema::dropIfExists('products');
 }
};