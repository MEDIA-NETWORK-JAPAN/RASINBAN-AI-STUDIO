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
        Schema::create('dify_apps', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // アプリ名
            $table->string('slug')->unique();          // URL識別子
            $table->text('api_key');                   // Dify APIキー（暗号化）
            $table->string('base_url')->nullable();    // 基底URL
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // インデックス
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dify_apps');
    }
};
