<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_services', function (Blueprint $table) {
            $table->id();

            $table->text('image')->nullable();
            $table->string('name');
            $table->string('slug');
            $table->text('description');
            $table->float('price');
            $table->float('time');
            $table->string('time_type');
            $table->float('discount');
            $table->enum('discount_type', ['percent', 'fixed'])->default('percent');
            $table->float('net_price');
            $table->string('default_image')->nullable();
            $table->enum('status',['active', 'deactive'])->default('active');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_services');
    }
}
