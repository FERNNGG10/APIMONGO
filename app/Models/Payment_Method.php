<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment_Method extends Model
{
    protected $table = 'payment_methods';
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'method',
        'status'
    ];
    public function game_sales()
    {
        return $this->hasMany(Game_Sale::class);
    }
    public function console_sales()
    {
        return $this->hasMany(Console_Sale::class);
    }
    public function gadget_sales()
    {
        return $this->hasMany(Gadget_Sale::class);
    }
    

    
}
