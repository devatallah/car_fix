<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSolutionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solutions', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('broken_file')->nullable();
            $table->text('fixed_file')->nullable();
            $table->string('module_uuid')->nullable();
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
        Schema::dropIfExists('solutions');
    }
}
