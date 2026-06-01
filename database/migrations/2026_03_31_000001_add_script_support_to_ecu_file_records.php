<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScriptSupportToEcuFileRecords extends Migration
{
    public function up()
    {
        Schema::table('ecu_file_records', function (Blueprint $table) {
            $table->longText('script_content')->nullable()->after('file');
            $table->string('patch_method')->default('binary')->after('script_content');
            // 'binary' = existing byte comparison method (untouched)
            // 'script' = .magicsscript patch method (new)
        });
    }

    public function down()
    {
        Schema::table('ecu_file_records', function (Blueprint $table) {
            $table->dropColumn(['script_content', 'patch_method']);
        });
    }
}
