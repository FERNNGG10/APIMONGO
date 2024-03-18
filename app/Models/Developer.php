<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Developer extends Model
{
    protected $table = 'developers';
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'status'
    ];

    public function games(){
        return $this->hasMany(Game::class);
    }


}
