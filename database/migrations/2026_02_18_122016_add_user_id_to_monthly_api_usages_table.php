<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monthly_api_usages', function (Blueprint $table) {
            // team_idの直後にuser_idを追加（既存データなし前提でNOT NULL）
            $table->foreignId('user_id')->after('team_id')->constrained()->onDelete('cascade');
            $table->index('user_id');

            // UNIQUE制約をteam_id基準からuser_id基準に変更
            $table->dropUnique('unique_monthly_usage');
            $table->unique(['user_id', 'dify_app_id', 'usage_month', 'endpoint'], 'unique_monthly_usage_user');
        });
    }

    public function down(): void
    {
        Schema::table('monthly_api_usages', function (Blueprint $table) {
            $table->dropUnique('unique_monthly_usage_user');
            $table->unique(['team_id', 'dify_app_id', 'usage_month', 'endpoint'], 'unique_monthly_usage');
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
