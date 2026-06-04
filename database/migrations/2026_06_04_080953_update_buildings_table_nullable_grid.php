<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buildings', function (Blueprint $table) {
            // grid_x/grid_y 改為 nullable（選位置前先為 null）
            $table->unsignedTinyInteger('grid_x')->nullable()->change();
            $table->unsignedTinyInteger('grid_y')->nullable()->change();

            // 同一個 user 不能有兩棟建築在同一格
            $table->unique(['user_id', 'grid_x', 'grid_y'], 'buildings_user_grid_unique');
        });
    }

    public function down(): void
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->dropUnique('buildings_user_grid_unique');
            $table->unsignedTinyInteger('grid_x')->nullable(false)->change();
            $table->unsignedTinyInteger('grid_y')->nullable(false)->change();
        });
    }
};