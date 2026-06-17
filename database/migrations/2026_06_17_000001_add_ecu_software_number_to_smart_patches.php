<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEcuSoftwareNumberToSmartPatches extends Migration
{
    public function up()
    {
        Schema::table('smart_patches', function (Blueprint $table) {
            $table->string('ecu_software_number')->nullable()->after('module_uuid');
            $table->index('ecu_software_number');
        });
    }

    public function down()
    {
        Schema::table('smart_patches', function (Blueprint $table) {
            $table->dropIndex(['ecu_software_number']);
            $table->dropColumn('ecu_software_number');
        });
    }
}
