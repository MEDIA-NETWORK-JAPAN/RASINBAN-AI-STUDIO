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
        Schema::create('monthly_api_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('dify_app_id')->constrained()->onDelete('cascade');
            $table->string('usage_month', 7);              // YYYY-MM形式
            $table->string('endpoint');                    // エンドポイントパターン
            $table->integer('request_count')->default(0);  // 月間リクエスト数
            $table->timestamp('last_request_at')->nullable();
            $table->timestamps();

            // 複合ユニーク制約
            $table->unique(['team_id', 'dify_app_id', 'usage_month', 'endpoint'], 'unique_monthly_usage');

            // インデックス
            $table->index('team_id');
            $table->index('dify_app_id');
            $table->index('usage_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_api_usages');
    }
};
