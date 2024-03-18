<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Console extends Model
{
    protected $table = 'consoles';
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'name',
        'description',
        'status',
        'supplier_id'
    ];

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }

    public function console_inventory(){
        return $this->hasOne(Console_Inventory::class);
    }

    public function sales(){
        return $this->hasMany(Console_Sale::class);
    }
}
