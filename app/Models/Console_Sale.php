<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Console_Sale extends Pivot
{
    protected $table = 'console_sales';
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'console_id',
        'user_id',
        'quantity',
        'total',
        'payment_method_id'
    ];

    public function console()
    {
        return $this->belongsTo(Console::class);   
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function paymentMethod()
    {
        return $this->belongsTo(Payment_Method::class);
    }

}
