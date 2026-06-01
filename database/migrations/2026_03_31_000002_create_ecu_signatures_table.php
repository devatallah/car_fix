<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEcuSignaturesTable extends Migration
{
    public function up()
    {
        Schema::create('ecu_signatures', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('ecu_uuid');                    // FK to ecus.uuid
            $table->string('ecu_file_uuid')->nullable();   // FK to ecu_files.uuid
            $table->unsignedBigInteger('file_size');        // expected binary file size (e.g., 2097152)
            $table->string('signature_offset')->nullable(); // hex offset to check (e.g., "0x40000")
            $table->string('signature_bytes')->nullable();  // expected hex bytes at offset
            $table->string('car_make')->nullable();         // e.g., BMW, VW, Mercedes
            $table->string('car_model')->nullable();        // e.g., Golf 6, E90
            $table->string('year_range')->nullable();       // e.g., 2009-2013
            $table->string('ecu_type')->nullable();         // e.g., Bosch EDC17C46
            $table->string('hw_sw_number')->nullable();     // hardware/software number
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('file_size');
            $table->index('ecu_uuid');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ecu_signatures');
    }
}
