<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gadget_Inventory extends Model
{
    protected $table = 'gadget_inventory';
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'gadget_id',
        'stock',
        'price'
    ];

    public function gadget(){
        return $this->belongsTo(Gadget::class);
    }
}
