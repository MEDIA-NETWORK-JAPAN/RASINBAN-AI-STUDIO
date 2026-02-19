<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>二段階認証コード - Dify Gateway</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Noto Sans JP', sans-serif;
        }
        .container {
            max-width: 480px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        }
        .header {
            background: linear-gradient(135deg, #4f46e5, #4338ca);
            padding: 32px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 4px;
        }
        .header p {
            color: #c7d2fe;
            font-size: 13px;
            margin: 0;
        }
        .body {
            padding: 40px 32px;
            text-align: center;
        }
        .body p {
            color: #4b5563;
            font-size: 14px;
            margin: 0 0 24px;
        }
        .otp-box {
            display: inline-block;
            background-color: #f5f3ff;
            border: 2px solid #a5b4fc;
            border-radius: 12px;
            padding: 20px 40px;
            margin-bottom: 24px;
        }
        .otp-code {
            font-size: 42px;
            font-weight: 700;
            letter-spacing: 10px;
            color: #4338ca;
            font-family: 'Courier New', monospace;
        }
        .expiry {
            font-size: 12px;
            color: #6b7280;
            margin: 0 0 8px;
        }
        .footer {
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
            padding: 20px 32px;
            text-align: center;
        }
        .footer p {
            color: #9ca3af;
            font-size: 11px;
            margin: 0 0 4px;
        }
        .warning {
            color: #6b7280;
            font-size: 12px;
            background: #fef9c3;
            border: 1px solid #fde68a;
            border-radius: 8px;
            padding: 12px 16px;
            text-align: left;
            margin-top: 8px;
        }
        .warning strong { color: #92400e; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 二段階認証コード</h1>
            <p>Dify Gateway - rasinban-ai-studio</p>
        </div>
        <div class="body">
            <p>管理者ログインの二段階認証コードです。<br>以下のコードを認証画面に入力してください。</p>
            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
            </div>
            <p class="expiry">このコードは <strong>10分間</strong> 有効です。</p>
            <div class="warning">
                <strong>⚠️ セキュリティ上のご注意</strong><br>
                このコードは他人に教えないでください。<br>
                心当たりのないメールを受け取った場合は、すぐに管理者にお問い合わせください。
            </div>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} Dify Gateway - rasinban-ai-studio</p>
            <p>このメールは自動送信されています。返信は不要です。</p>
        </div>
    </div>
</body>
</html>
