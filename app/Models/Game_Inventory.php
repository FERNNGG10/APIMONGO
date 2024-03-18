<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game_Inventory extends Model
{
    protected $table = 'game_inventory';
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'game_id',
        'stock',
        'price'
    ];

    public function game(){
        return $this->belongsTo(Game::class);
    }
    
}
