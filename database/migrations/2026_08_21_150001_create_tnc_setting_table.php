<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tnc_setting', function (Blueprint $table) {
            $table->id();
            $table->string('version', 20)->default('1.0');
            $table->string('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        DB::table('tnc_setting')->insert([
            'id' => 1,
            'version' => '1.0',
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tnc_setting');
    }
};
