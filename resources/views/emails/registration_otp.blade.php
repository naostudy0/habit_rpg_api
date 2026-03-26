<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>会員登録用ワンタイムパスワード</title>
</head>
<body>
<p>会員登録の確認コードです。</p>
<p>ワンタイムパスワード: <strong>{{ $otp_code }}</strong></p>
<p>有効期限: {{ $expires_at->format('Y-m-d H:i:s') }}</p>
<p>このコードに心当たりがない場合は破棄してください。</p>
</body>
</html>
