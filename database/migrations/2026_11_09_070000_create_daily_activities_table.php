<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('activity_date');
            $table->unsignedSmallInteger('study_minutes')->default(0);
            $table->unsignedSmallInteger('questions_answered')->default(0);
            $table->unique(['user_id', 'activity_date']);
        });

        Schema::table('attempts', function (Blueprint $table) {
            $table->boolean('daily_activity_recorded')->default(false)->after('is_passed');
        });
    }

    public function down(): void
    {
        Schema::table('attempts', function (Blueprint $table) {
            $table->dropColumn('daily_activity_recorded');
        });

        Schema::dropIfExists('daily_activities');
    }
};
