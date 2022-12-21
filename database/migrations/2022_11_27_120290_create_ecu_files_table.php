<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEcuFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ecu_files', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('fixed_file')->nullable();
            $table->text('origin_file')->nullable();
            $table->string('ecu_uuid')->nullable();
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
            Schema::dropIfExists('ecu_files');
    }
}
