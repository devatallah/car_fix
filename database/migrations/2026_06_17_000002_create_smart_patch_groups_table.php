<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmartPatchGroupsTable extends Migration
{
    public function up()
    {
        Schema::create('smart_patch_groups', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('ecu_uuid');
            $table->string('module_uuid');
            $table->timestamps();
            $table->softDeletes();

            $table->index('ecu_uuid');
            $table->index('module_uuid');
        });
    }

    public function down()
    {
        Schema::dropIfExists('smart_patch_groups');
    }
}
