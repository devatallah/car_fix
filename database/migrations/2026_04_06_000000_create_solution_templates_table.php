<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSolutionTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solution_templates', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('script_uuid'); // ربط بالسكريبت الأصلي
            $table->string('name'); // اسم القالب
            $table->text('description')->nullable(); // وصف
            $table->json('patterns')->nullable(); // أنماط أو قواعد للتطبيق
            $table->text('template_file')->nullable(); // ملف القالب أو المحتوى
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('solution_templates');
    }
}