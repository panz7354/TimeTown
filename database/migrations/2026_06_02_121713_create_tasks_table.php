<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title', 100);
            $table->enum('type', ['學習', '工作', '運動', '社交', '休息', '興趣創作']);
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->date('date');
            $table->unsignedTinyInteger('week');
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
