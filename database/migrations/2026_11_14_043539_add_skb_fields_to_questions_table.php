<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->string('exam_type', 8)->default('skd')->after('id')->index();
            $table->foreignId('formation_id')->nullable()->after('subtest_id')->constrained()->nullOnDelete();
            $table->foreignId('subtest_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['formation_id']);
            $table->dropColumn(['formation_id', 'exam_type']);
            $table->foreignId('subtest_id')->nullable(false)->change();
        });
    }
};
