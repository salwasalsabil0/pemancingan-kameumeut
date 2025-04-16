<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('queues', function (Blueprint $table) {
            $table->string('status_menunggu')->default('Menunggu')->after('served');
        });
    }

    public function down()
    {
        Schema::table('queues', function (Blueprint $table) {
            $table->dropColumn('status_menunggu');
        });
    }
};
