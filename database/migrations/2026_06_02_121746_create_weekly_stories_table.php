<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('week');             // ISO 週次
            $table->unsignedSmallInteger('year');            // 年份
            $table->json('task_summary');                    // JSON, 當週任務摘要陣列
            $table->text('prev_story_tail')->nullable();     // TEXT, 上週結尾段落（首週 null）
            $table->text('story_text')->nullable();          // TEXT, AI 生成的故事本文
            $table->timestamp('generated_at')->nullable();   // TIMESTAMP, AI 生成時間
            $table->timestamps();

            // 同一個 user 同一週只有一篇故事
            $table->unique(['user_id', 'week', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_stories');
    }
};
