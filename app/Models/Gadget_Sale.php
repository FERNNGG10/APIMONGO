<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Gadget_Sale extends Pivot
{
    protected $table = 'gadget_sales';
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'gadget_id',
        'quantity',
        'user_id',
        'total',
        'payment_method_id'
    ];

    public function gadget(){
        return $this->belongsTo(Gadget::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function paymentMethod()
    {
        return $this->belongsTo(Payment_Method::class);
    }
}
