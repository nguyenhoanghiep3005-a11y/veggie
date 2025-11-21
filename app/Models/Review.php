<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
   protected $fillable = ['user_id','product_id','rating','comment'];

   public function product()
   {
    $this->belongsTo(Product::class);
   }

     public function user()
   {
    $this->belongsTo(User::class);
   }
   
}
