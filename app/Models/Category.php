<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Category extends Model
{
 // Kolom yang boleh diisi secara mass assignment
 protected $fillable = ['name'];
 /**
 * Relasi antara Category dan Product.
 * Satu category dapat memiliki banyak product.
 */
 public function products()
 {
 return $this->hasMany(Product::class);
 }
}
