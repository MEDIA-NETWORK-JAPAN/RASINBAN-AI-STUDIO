# Design Tokens

UIモック間で統一するデザイントークン（Tailwind CSSクラス）の定義です。

## カラーパレット

### Primary (Indigo)
| 用途 | クラス |
|------|--------|
| メインアクション | `bg-indigo-600 hover:bg-indigo-700` |
| アクティブ状態 | `bg-indigo-50 text-indigo-700` |
| フォーカスリング | `focus:ring-indigo-500 focus:border-indigo-500` |
| テキストリンク | `text-indigo-600 hover:text-indigo-800` |

### Danger (Red)
| 用途 | クラス |
|------|--------|
| 危険なアクション | `bg-red-600 hover:bg-red-700` |
| 警告テキスト | `text-red-600` |
| 警告背景 | `bg-red-50` |
| 超過表示 | `text-red-600 bg-red-50` |

### Success (Green)
| 用途 | クラス |
|------|--------|
| 成功状態 | `bg-green-600` |
| 成功テキスト | `text-green-600` |
| 増加表示 | `text-green-600` |

### Neutral (Gray)
| 用途 | クラス |
|------|--------|
| ページ背景 | `bg-gray-50` |
| カード背景 | `bg-white` |
| ボーダー | `border-gray-200` |
| セカンダリテキスト | `text-gray-500` |
| 無効状態 | `text-gray-400` |

## 角丸 (Border Radius)

| 要素 | クラス |
|------|--------|
| カード | `rounded-xl` |
| ボタン | `rounded-lg` |
| 入力フィールド | `rounded-md` |
| バッジ | `rounded-full` |
| プログレスバー | `rounded-full` |

## シャドウ (Box Shadow)

| 要素 | クラス |
|------|--------|
| カード（標準） | `shadow-sm` |
| モーダル | `shadow-2xl` |
| ボタン | `shadow-sm` |
| ドロップダウン | `shadow-lg` |

## スペーシング

### ページレイアウト
| 要素 | クラス |
|------|--------|
| メインコンテンツパディング | `p-4 lg:p-8` |
| セクション間マージン | `space-y-6` または `mb-8` |

### カード内部
| 要素 | クラス |
|------|--------|
| カードパディング | `p-6` |
| カードヘッダー | `px-6 py-4` |
| カードフッター | `px-6 py-4` |

### フォーム
| 要素 | クラス |
|------|--------|
| フィールド間 | `space-y-4` |
| ラベルとフィールド | `mb-1` |

## タイポグラフィ

### 見出し
| 要素 | クラス |
|------|--------|
| ページタイトル | `text-xl font-bold text-gray-800` |
| セクションタイトル | `text-base font-bold text-gray-800` |
| カードタイトル | `text-lg font-bold text-gray-900` |

### 本文
| 要素 | クラス |
|------|--------|
| 標準テキスト | `text-sm text-gray-600` |
| 補足テキスト | `text-xs text-gray-500` |
| ラベル | `text-xs font-medium text-gray-500 uppercase tracking-wider` |

### 数値表示
| 要素 | クラス |
|------|--------|
| KPI数値 | `text-3xl font-bold text-gray-900` |
| テーブル数値 | `text-sm font-mono` |

## カード標準スタイル

```html
<!-- 標準カード -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
  <!-- ヘッダー（オプション） -->
  <div class="px-6 py-4 border-b border-gray-100">
    <h2 class="text-base font-bold text-gray-800">タイトル</h2>
  </div>
  <!-- ボディ -->
  <div class="p-6">
    コンテンツ
  </div>
  <!-- フッター（オプション） -->
  <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
    アクション
  </div>
</div>
```

## テーブル標準スタイル

```html
<table class="min-w-full divide-y divide-gray-200">
  <thead class="bg-gray-50">
    <tr>
      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
        ヘッダー
      </th>
    </tr>
  </thead>
  <tbody class="bg-white divide-y divide-gray-200">
    <tr class="hover:bg-gray-50 transition-colors">
      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
        セル
      </td>
    </tr>
  </tbody>
</table>
```

## フォーム入力標準スタイル

```html
<!-- テキスト入力 -->
<input type="text"
  class="block w-full rounded-md border-gray-300 shadow-sm
         focus:border-indigo-500 focus:ring-indigo-500
         sm:text-sm py-2 px-3">

<!-- セレクト -->
<select class="block w-full rounded-md border-gray-300 shadow-sm
               focus:border-indigo-500 focus:ring-indigo-500
               sm:text-sm py-2 px-3 bg-white">
```

## トランジション

| 要素 | クラス |
|------|--------|
| 標準 | `transition-colors` |
| モーダル | `transition-all duration-300` |
| サイドバー | `transition-transform duration-300 ease-in-out` |
