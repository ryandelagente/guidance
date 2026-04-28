<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('case_note_pin_hash')->nullable()->after('password');
            $table->timestamp('case_note_pin_set_at')->nullable()->after('case_note_pin_hash');
            $table->string('calendar_feed_token', 64)->nullable()->unique()->after('case_note_pin_set_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['case_note_pin_hash', 'case_note_pin_set_at', 'calendar_feed_token']);
        });
    }
};
