<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Product extends Model
{
 protected $fillable = ['category_id', 'code', 'name', 'stock', 'minimum_stock',];
 public function category()
 {
 return $this->belongsTo(Category::class);
 }
 public function stockTransactions()
 {
 return $this->hasMany(StockTransaction::class);
 }
 public function getStockStatusAttribute()
 {
 return $this->stock < 10 ? 'Akan Habis' : 'Aman';
 }
}