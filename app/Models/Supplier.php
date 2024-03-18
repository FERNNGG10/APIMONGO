<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'suppliers';
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'status',
    ];

    public function games(){
        return $this->hasMany(Game::class);
    }
    public function consoles(){
        return $this->hasMany(Console::class);
    }
    public function gadgets(){
        return $this->hasMany(Gadget::class);
    }
}
