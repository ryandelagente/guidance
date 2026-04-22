<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('psychological_tests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('test_type', ['iq', 'personality', 'career_aptitude', 'interest', 'mental_health', 'other']);
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('total_items')->nullable();
            $table->string('publisher')->nullable();
            $table->year('edition_year')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('psychological_tests');
    }
};
