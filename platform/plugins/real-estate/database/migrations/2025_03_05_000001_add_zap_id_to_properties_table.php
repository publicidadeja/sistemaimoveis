<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddZapIdToPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('re_properties', function (Blueprint $table) {
            if (!Schema::hasColumn('re_properties', 'zap_id')) {
                $table->string('zap_id', 100)->nullable()->after('author_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('re_properties', function (Blueprint $table) {
            if (Schema::hasColumn('re_properties', 'zap_id')) {
                $table->dropColumn('zap_id');
            }
        });
    }
}