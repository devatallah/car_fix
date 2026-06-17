<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGroupUuidToSmartPatches extends Migration
{
    public function up()
    {
        Schema::table('smart_patches', function (Blueprint $table) {
            $table->string('group_uuid')->nullable()->after('uuid');
            $table->index('group_uuid');
        });
    }

    public function down()
    {
        Schema::table('smart_patches', function (Blueprint $table) {
            $table->dropIndex(['group_uuid']);
            $table->dropColumn('group_uuid');
        });
    }
}
