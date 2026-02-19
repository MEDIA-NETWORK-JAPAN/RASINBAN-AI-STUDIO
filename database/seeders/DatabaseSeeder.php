<?php

namespace Database\Seeders;

use App\Models\DifyApp;
use App\Models\MonthlyApiUsage;
use App\Models\Plan;
use App\Models\PlanLimit;
use App\Models\Team;
use App\Models\User;
use App\Models\UserApiKey;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. プラン ──────────────────────────────────────
        $planFree = Plan::create([
            'name' => 'フリー',
            'code' => 'free',
            'description' => '月100回まで無料で利用可能',
            'is_active' => true,
        ]);
        PlanLimit::create(['plan_id' => $planFree->id, 'endpoint' => '/chat-messages',       'limit_count' => 100]);
        PlanLimit::create(['plan_id' => $planFree->id, 'endpoint' => '/completion-messages', 'limit_count' => 100]);

        $planStandard = Plan::create([
            'name' => 'スタンダード',
            'code' => 'standard',
            'description' => '月5,000回まで利用可能',
            'is_active' => true,
        ]);
        PlanLimit::create(['plan_id' => $planStandard->id, 'endpoint' => '/chat-messages',       'limit_count' => 5000]);
        PlanLimit::create(['plan_id' => $planStandard->id, 'endpoint' => '/completion-messages', 'limit_count' => 5000]);
        PlanLimit::create(['plan_id' => $planStandard->id, 'endpoint' => '/workflows/run',       'limit_count' => 1000]);

        $planPro = Plan::create([
            'name' => 'プロ',
            'code' => 'pro',
            'description' => '月50,000回まで利用可能',
            'is_active' => true,
        ]);
        PlanLimit::create(['plan_id' => $planPro->id, 'endpoint' => '/chat-messages',       'limit_count' => 50000]);
        PlanLimit::create(['plan_id' => $planPro->id, 'endpoint' => '/completion-messages', 'limit_count' => 50000]);
        PlanLimit::create(['plan_id' => $planPro->id, 'endpoint' => '/workflows/run',       'limit_count' => 10000]);

        // ── 2. 管理者ユーザー (id=1 必須) ────────────────────
        $admin = User::create([
            'name' => 'システム管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'plan_id' => null,
            'email_verified_at' => now(),
        ]);
        // 管理者用 personal_team
        $adminTeam = Team::create([
            'user_id' => $admin->id,
            'name' => 'システム管理者\'s Team',
            'personal_team' => true,
        ]);
        $admin->update(['current_team_id' => $adminTeam->id]);

        // ── 3. Dify アプリ ────────────────────────────────
        $appChat = DifyApp::create([
            'name' => 'カスタマーサポートBot',
            'slug' => 'support-bot',
            'api_key' => 'app-'.Str::random(32),
            'base_url' => 'https://api.dify.ai',
            'description' => 'お客様からの問い合わせに自動回答するチャットBot',
            'is_active' => true,
        ]);
        $appDoc = DifyApp::create([
            'name' => '文書要約ツール',
            'slug' => 'doc-summary',
            'api_key' => 'app-'.Str::random(32),
            'base_url' => 'https://api.dify.ai',
            'description' => '長文ドキュメントを自動的に要約するツール',
            'is_active' => true,
        ]);
        $appWorkflow = DifyApp::create([
            'name' => '業務フロー自動化',
            'slug' => 'workflow-auto',
            'api_key' => 'app-'.Str::random(32),
            'base_url' => 'https://api.dify.ai',
            'description' => '定型業務をワークフローで自動化',
            'is_active' => false,
        ]);

        // ── 4. 拠点ユーザー + チーム + APIキー + 利用実績 ────
        $teamsData = [
            ['name' => '東京本社',     'plan' => $planPro,      'usage_chat' => 4200, 'usage_complete' => 800],
            ['name' => '大阪支社',     'plan' => $planStandard, 'usage_chat' => 4800, 'usage_complete' => 0],
            ['name' => '名古屋支社',   'plan' => $planStandard, 'usage_chat' => 1200, 'usage_complete' => 300],
            ['name' => '福岡支社',     'plan' => $planFree,     'usage_chat' => 90,   'usage_complete' => 10],
            ['name' => '札幌出張所',   'plan' => $planFree,     'usage_chat' => 0,    'usage_complete' => 0],
        ];

        $currentMonth = now()->format('Y-m');

        foreach ($teamsData as $i => $data) {
            // ユーザー作成
            $user = User::create([
                'name' => $data['name'].'担当者',
                'email' => 'user'.($i + 1).'@example.com',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'plan_id' => $data['plan']->id,
                'email_verified_at' => now(),
            ]);

            // チーム作成（personal_team=false が拠点）
            $team = Team::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'personal_team' => false,
            ]);
            $user->update(['current_team_id' => $team->id]);

            // チームメンバーとして追加
            $team->users()->attach($user, ['role' => 'editor']);

            // ユーザーAPIキー
            $plainKey = 'key_'.Str::random(59);
            UserApiKey::create([
                'user_id' => $user->id,
                'name' => 'default',
                'key_hash' => hash('sha256', $plainKey),
                'key_encrypted' => encrypt($plainKey),
                'is_active' => true,
            ]);

            // 月次利用実績（chat-messages）
            if ($data['usage_chat'] > 0) {
                MonthlyApiUsage::create([
                    'user_id' => $user->id,
                    'team_id' => $team->id,
                    'dify_app_id' => $appChat->id,
                    'usage_month' => $currentMonth,
                    'endpoint' => '/chat-messages',
                    'request_count' => $data['usage_chat'],
                    'last_request_at' => now()->subHours(rand(1, 24)),
                ]);
            }

            // 月次利用実績（completion-messages）
            if ($data['usage_complete'] > 0) {
                MonthlyApiUsage::create([
                    'user_id' => $user->id,
                    'team_id' => $team->id,
                    'dify_app_id' => $appDoc->id,
                    'usage_month' => $currentMonth,
                    'endpoint' => '/completion-messages',
                    'request_count' => $data['usage_complete'],
                    'last_request_at' => now()->subHours(rand(1, 48)),
                ]);
            }
        }
    }
}
