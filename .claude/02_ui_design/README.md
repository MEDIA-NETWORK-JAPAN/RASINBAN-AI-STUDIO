# UI Design Documents

このフォルダには画面設計に関するすべてのドキュメントが含まれています。

## ディレクトリ構造

```
02_ui_design/
├── README.md              # このファイル（目次）
├── _design_tokens.md      # デザイントークン（色、角丸、シャドウ等）
├── components/            # 共通コンポーネント仕様
│   ├── _index.md          # コンポーネント一覧
│   ├── layout.md          # レイアウト系
│   ├── navigation.md      # ナビゲーション系
│   ├── data-display.md    # データ表示系
│   ├── forms.md           # フォーム系
│   ├── buttons.md         # ボタン系
│   ├── modals.md          # モーダル系
│   └── feedback.md        # フィードバック系
├── screens/               # 画面別仕様
│   ├── _index.md          # 画面一覧・遷移図
│   └── A01〜A09.md        # 各画面の仕様
└── mocks/                 # HTMLモック（参照用）
    └── *.html
```

## AIエージェントへの指示方法

### 画面実装を依頼する場合

```
A02 拠点一覧画面を実装してください。

参照:
- 画面仕様: .claude/02_ui_design/screens/A02_team_list.md
- モック: .claude/02_ui_design/mocks/A02_team_list.html
```

### コンポーネント実装を依頼する場合

```
Buttonコンポーネントを実装してください。

参照:
- コンポーネント仕様: .claude/02_ui_design/components/buttons.md
- デザイントークン: .claude/02_ui_design/_design_tokens.md
```

### 複数画面にまたがる実装

```
管理者用の拠点管理機能を実装してください。

参照:
- 画面一覧: .claude/02_ui_design/screens/_index.md
- 対象画面: A02, A03, A04
```

## ファイル命名規則

- `_` で始まるファイル: インデックス・設定ファイル
- `A01`〜`A09`: 管理者画面
- `U01`〜: 一般ユーザー画面（今後追加予定）
- `G01`〜: 共通画面（ログイン等、今後追加予定）

## 関連ドキュメント

- 要件定義: `.claude/01_development_docs/01_要件定義書.md`
- 画面機能一覧: `.claude/01_development_docs/02_画面一覧・機能一覧定義書.md`
