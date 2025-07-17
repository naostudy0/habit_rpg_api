<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Ollama テスト</title>
</head>
<body>
    <h1>Ollama に質問する</h1>

    <form method="POST" action="/">
        @csrf
        <textarea name="question" rows="4" cols="60" placeholder="ここに質問を入力">{{ old('question', $question ?? '') }}</textarea><br><br>
        <button type="submit">送信</button>
    </form>

    @if(isset($answer))
        <h2>回答:</h2>
        <div style="white-space: pre-wrap; border: 1px solid #ccc; padding: 1em;">
            {{ $answer }}
        </div>
    @endif
</body>
</html>
