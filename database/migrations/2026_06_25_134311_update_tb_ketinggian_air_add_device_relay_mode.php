<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_ketinggian_air', function (Blueprint $table) {
            $table->renameColumn('tinggi_air', 'tinggi');
            $table->string('device_id', 10)->default('001')->after('id');
            $table->boolean('relay')->default(false)->after('status');
            $table->string('mode', 10)->default('AUTO')->after('relay');
        });
    }

    public function down(): void
    {
        Schema::table('tb_ketinggian_air', function (Blueprint $table) {
            $table->renameColumn('tinggi', 'tinggi_air');
            $table->dropColumn(['device_id', 'relay', 'mode']);
        });
    }
};
