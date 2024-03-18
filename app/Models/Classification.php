<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classification extends Model
{
    protected $table = 'classifications';
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'classification',
        'status',
    ];

    public function games(){
        return $this->hasMany(Game::class);
    }
}
