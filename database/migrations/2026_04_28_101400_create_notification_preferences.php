<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('notification_preferences')->nullable()->after('two_factor_enabled_at');
            $table->string('phone_number', 30)->nullable()->after('notification_preferences');
            $table->boolean('phone_verified')->default(false)->after('phone_number');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['notification_preferences', 'phone_number', 'phone_verified']);
        });
    }
};
