<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gadget_inventory',function(Blueprint $table){
            $table->id();
            $table->foreignId('gadget_id')
            ->constrained('gadgets')
            ->onDelete('cascade')
            ->onUpdate('cascade');
            $table->integer('stock');
            $table->decimal('price',8,2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gadget_inventory');
    }
};
