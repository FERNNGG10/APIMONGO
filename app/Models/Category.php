<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
       'category',
       'status'
    ];

    public function games(){
        return $this->hasMany(Game::class);
    }
}
