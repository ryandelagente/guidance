<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['article','video','hotline','pdf','link','contact']);
            $table->string('category', 80)->index();   // mental_health, academic, career, financial, crisis, etc.
            $table->string('url', 500)->nullable();
            $table->string('file_path', 500)->nullable();
            $table->string('contact_number', 80)->nullable();
            $table->string('available_hours', 200)->nullable();
            $table->boolean('is_emergency')->default(false);
            $table->boolean('is_published')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(100);
            $table->unsignedInteger('view_count')->default(0);
            $table->timestamps();

            $table->index(['category', 'is_published']);
            $table->index('is_emergency');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
