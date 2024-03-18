<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dlc extends Model
{
    protected $table = 'dlcs';
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'name',
        'game_id',
        
    ];


    public function game(){
        return $this->belongsTo(Game::class);
    }
}
