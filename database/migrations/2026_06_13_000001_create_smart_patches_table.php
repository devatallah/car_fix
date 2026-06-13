<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmartPatchesTable extends Migration
{
    public function up()
    {
        Schema::create('smart_patches', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('ecu_uuid');
            $table->string('module_uuid');
            $table->unsignedBigInteger('file_size');
            $table->longText('patch_map');       // JSON: clusters with search/replace + wildcards
            $table->unsignedInteger('patches_count')->default(0);
            $table->unsignedInteger('wildcard_count')->default(0);
            $table->unsignedInteger('context_size')->default(16);
            $table->unsignedInteger('gap_tolerance')->default(30);
            $table->timestamps();
            $table->softDeletes();

            $table->index('ecu_uuid');
            $table->index('module_uuid');
            $table->index('file_size');
        });
    }

    public function down()
    {
        Schema::dropIfExists('smart_patches');
    }
}
