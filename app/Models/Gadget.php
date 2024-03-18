<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gadget extends Model
{
    protected $table = 'gadgets';
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

    public function gadget_inventory(){
        return $this->hasOne(Gadget_Inventory::class);
    }

    public function sales(){
        return $this->hasMany(Gadget_Sale::class);
    }
}
