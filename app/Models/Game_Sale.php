<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Game_Sale extends Model
{
    protected $table = 'game_sales';
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'game_id',
        'quantity',
        'total',
        'user_id',
        'payment_method_id'


    ];

    public function game()
    {
        return $this->belongsTo(Game::class);   
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
