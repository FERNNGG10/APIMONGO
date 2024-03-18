<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Console_Inventory extends Model
{
    protected $table = 'console_inventory';
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'console_id',
        'stock',
        'price'
    ];

    public function console(){
        return $this->belongsTo(Console::class);
    }
}
