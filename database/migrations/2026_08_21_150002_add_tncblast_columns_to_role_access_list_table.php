<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('role_access_list', function (Blueprint $table) {
            $table->integer('tncblast_page')->default(0);
            $table->integer('tncblast_send')->default(0);
        });

        // Grant access to superadmin, admin, and marketing roles by default.
        DB::table('role_access_list')
            ->whereIn('role_id', [248576, 375698, 537469])
            ->update([
                'tncblast_page' => 1,
                'tncblast_send' => 1,
            ]);
    }

    public function down(): void
    {
        Schema::table('role_access_list', function (Blueprint $table) {
            $table->dropColumn(['tncblast_page', 'tncblast_send']);
        });
    }
};
