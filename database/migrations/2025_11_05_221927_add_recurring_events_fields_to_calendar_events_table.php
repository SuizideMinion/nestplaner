<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('calendar_events', function (Blueprint $table) {
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_type')->nullable(); // yearly, monthly, weekly, etc.
            $table->date('recurrence_date')->nullable();   // z.B. 2025-11-01
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calendar_events', function (Blueprint $table) {
            //
        });
    }
};
