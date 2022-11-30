<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFixesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fixes', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('broken_file')->nullable();
            $table->text('fixed_file')->nullable();
            $table->string('solution_uuid')->nullable();
            $table->string('brand_uuid')->nullable();
            $table->string('ecu_uuid')->nullable();
            $table->string('ownerable_uuid')->nullable();
            $table->string('ownerable_type')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fixes');
    }
}
