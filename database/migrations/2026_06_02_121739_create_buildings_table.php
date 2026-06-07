<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['學習','工作','運動','社交','休息','興趣創作']);
            $table->unsignedTinyInteger('slot')->default(0);
            $table->unsignedTinyInteger('level')->default(0);
            $table->string('name', 30)->default('基礎房子');
            $table->string('svg_file', 50)->default('01_基礎房子.svg');
            $table->unsignedSmallInteger('completed_count')->default(0);
            $table->unsignedTinyInteger('grid_x')->nullable();
            $table->unsignedTinyInteger('grid_y')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'type', 'slot']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
