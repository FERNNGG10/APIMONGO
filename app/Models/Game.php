<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $table = 'games';
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'name',
        'description',
        'status',
        'category_id',
        'developer_id',
        'classification_id',
        'supplier_id'
    ];

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function classification(){
        return $this->belongsTo(Classification::class);
    }

    public function developer(){
        return $this->belongsTo(Developer::class);
    }
    
    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }

    public function reviews(){
        return $this->hasMany(Review::class);
    }

    public function dlcs(){
        return $this->hasMany(Dlc::class);
    }

    public function game_inventory()
    {
        return $this->hasOne(Game_Inventory::class);
    }

    public function sales(){
        return $this->hasMany(Game_Sale::class);
    }
}
