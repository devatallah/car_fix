<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVerificationFieldsToScriptsTable extends Migration
{
    public function up()
    {
        Schema::table('scripts', function (Blueprint $table) {
            // حقول التحقق — تُستخدم لتمييز سكريبتات بنفس الـ ECU والحجم
            // يملأها الأدمن عند رفع السكريبت بناءً على الملف الأصلي
            $table->string('part_number')->nullable()->after('expected_file_size');
            $table->string('calibration_id')->nullable()->after('part_number');
            $table->string('sw_version')->nullable()->after('calibration_id');
            $table->string('hw_version')->nullable()->after('sw_version');

            $table->index('part_number');
            $table->index('calibration_id');
        });
    }

    public function down()
    {
        Schema::table('scripts', function (Blueprint $table) {
            $table->dropIndex(['part_number']);
            $table->dropIndex(['calibration_id']);
            $table->dropColumn(['part_number', 'calibration_id', 'sw_version', 'hw_version']);
        });
    }
}
