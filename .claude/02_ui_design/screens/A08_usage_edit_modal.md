# A08: 利用回数修正モーダル

## 基本情報

| 項目 | 値 |
|------|-----|
| 画面ID | A08 |
| 画面名 | 利用回数修正 |
| URL | (モーダル - A07から呼び出し) |
| モック | [mocks/A08_usage_edit_modal.html](../mocks/A08_usage_edit_modal.html) |

## 概要

API利用回数の誤りを手動で補正するためのモーダル。課金に関わる重要なデータ操作のため、Before/Afterの比較や理由の記録を重視。

## 使用コンポーネント

- `modals/Modal`
- `forms/TextInput` (number)
- `forms/Textarea`
- `buttons/Button`
- `buttons/PresetButton`
- `data-display/ProgressBar`
- `feedback/LimitWarning`

## 画面構成

### 1. 対象データ情報

読み取り専用で表示：
| 項目 | 内容 |
|------|------|
| 対象年月 | `year_month` |
| 契約プラン | `team.plan.name` + 上限値 |
| 拠点 (Team) | `team.name` |
| アプリ (App) | `dify_app.name` |

### 2. 修正操作エリア

| 要素 | 説明 |
|------|------|
| 数値入力 | 中央配置、大きめフォント |
| +/- ボタン | 100単位で増減 |
| 差分表示 | +500 / -1,200 のような差分 |

### 3. シミュレーション表示

Before/Afterのプログレスバー比較：
- 現在: グレーのバー + 現在値
- 修正後: 色付きバー + 修正後値
- 超過時は警告メッセージ表示

### 4. 修正理由入力

| 要素 | 説明 |
|------|------|
| プリセットボタン | よく使う理由をワンクリック入力 |
| テキストエリア | 詳細な理由を記入（必須） |
| 注記 | 「操作ログに記録されます」 |

プリセット理由：
- テスト利用分除外
- システム障害補填
- 初期設定ミス

## Livewire実装

```php
// app/Livewire/Admin/UsageEditModal.php
class UsageEditModal extends Component
{
    public MonthlyApiUsage $usage;

    public $editValue;
    public $reason = '';

    protected $rules = [
        'editValue' => 'required|integer|min:0',
        'reason' => 'required|string|min:10|max:500',
    ];

    public function mount(MonthlyApiUsage $usage)
    {
        $this->usage = $usage;
        $this->editValue = $usage->count;
    }

    public function getDiffProperty()
    {
        return $this->editValue - $this->usage->count;
    }

    public function getAfterPercentageProperty()
    {
        $limit = $this->usage->monthly_limit;
        if ($limit === 0) return 0;
        return min(100, round(($this->editValue / $limit) * 100));
    }

    public function increment($amount = 100)
    {
        $this->editValue += $amount;
    }

    public function decrement($amount = 100)
    {
        $this->editValue = max(0, $this->editValue - $amount);
    }

    public function applyPreset($preset)
    {
        $presets = [
            'test' => 'テスト利用分の除外',
            'incident' => 'システム障害による補填',
            'config' => '初期設定ミスによる修正',
        ];

        $this->reason = $presets[$preset] ?? '';
    }

    public function save()
    {
        $this->validate();

        if ($this->diff === 0) {
            return;
        }

        // 変更履歴を記録
        UsageAuditLog::create([
            'monthly_api_usage_id' => $this->usage->id,
            'user_id' => auth()->id(),
            'old_value' => $this->usage->count,
            'new_value' => $this->editValue,
            'reason' => $this->reason,
        ]);

        // 値を更新
        $this->usage->update(['count' => $this->editValue]);

        $this->dispatch('close-modal');
        $this->dispatch('toast', type: 'success', message: '利用回数を修正しました');
        $this->dispatch('refresh-list');
    }
}
```

## バリデーション

| フィールド | ルール |
|-----------|--------|
| 修正後の値 | required, integer, min:0 |
| 修正理由 | required, string, min:10, max:500 |

## 監査ログ

修正時は以下を記録：
- 対象レコードID
- 操作者
- 変更前の値
- 変更後の値
- 修正理由
- 操作日時

```php
// database/migrations/xxxx_create_usage_audit_logs_table.php
Schema::create('usage_audit_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('monthly_api_usage_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained();
    $table->integer('old_value');
    $table->integer('new_value');
    $table->text('reason');
    $table->timestamps();
});
```

## 注意事項

- 差分が0の場合は保存ボタンを無効化
- 理由が空の場合は保存ボタンを無効化
- 超過する値を設定する場合は警告表示（保存は可能）
