<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpectedFileSizeToScriptsTable extends Migration
{
    public function up()
    {
        Schema::table('scripts', function (Blueprint $table) {
            // حجم الملف المتوقع بالـ bytes — يُستخدم لمطابقة ملف المستخدم
            $table->unsignedBigInteger('expected_file_size')->nullable()->after('ecu_uuid');
            $table->index('expected_file_size');
        });
    }

    public function down()
    {
        Schema::table('scripts', function (Blueprint $table) {
            $table->dropIndex(['expected_file_size']);
            $table->dropColumn('expected_file_size');
        });
    }
}
