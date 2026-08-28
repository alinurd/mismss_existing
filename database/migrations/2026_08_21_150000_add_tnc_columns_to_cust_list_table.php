<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cust_list', function (Blueprint $table) {
            $table->string('tnc_version_sent', 20)->nullable()->after('link_ref');
            $table->timestamp('tnc_sent_at')->nullable()->after('tnc_version_sent');
        });
    }

    public function down(): void
    {
        Schema::table('cust_list', function (Blueprint $table) {
            $table->dropColumn(['tnc_version_sent', 'tnc_sent_at']);
        });
    }
};
