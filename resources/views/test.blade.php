<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ollama テスト</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 2rem;
        }
        .main-container {
            max-width: 900px;
        }
        .question-card {
            margin-bottom: 2rem;
        }
        .answer-card {
            margin-top: 2rem;
        }
        .answer-content {
            white-space: pre-wrap;
            line-height: 1.6;
            min-height: 100px;
        }
        textarea {
            resize: vertical;
        }
    </style>
</head>
<body>
    <div class="container main-container">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-2 text-center">Ollama LLM動作テスト</h1>
                <p class="text-center text-muted mb-4">
                    <span class="badge bg-secondary">使用モデル: {{ config('ollama.model') }}</span>
                </p>

                <div class="card question-card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">質問を入力</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="/">
                            @csrf
                            <div class="mb-3">
                                <label for="question" class="form-label">質問内容</label>
                                <textarea
                                    class="form-control"
                                    id="question"
                                    name="question"
                                    rows="10">{{ old('question', !empty($question) ? $question : "## 質問\n\n## 前提条件\n\n## 回答形式\n\n## 補足") }}</textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" id="submitBtn" class="btn btn-primary btn-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-send me-2" viewBox="0 0 16 16">
                                        <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm-4.522-.59L13.713 1.48l-4.338 2.761L2.114 9.48Z"/>
                                    </svg>
                                    <span id="submitText">送信</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                @if (isset($answer) && !empty($answer))
                    <div class="card answer-card shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">回答</h5>
                        </div>
                        <div class="card-body">
                            <div class="answer-content">{{ $answer }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelector('form').addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');

            submitBtn.disabled = true;
            submitText.textContent = '送信中...';
            submitBtn.classList.add('opacity-75');
        });
    </script>
</body>
</html>
