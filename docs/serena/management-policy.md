# Serena Management Policy

## Goal
Serena 関連情報を「再現可能に引き継げる形」で公開しつつ、ローカル実行時の生成物や機密情報は Git に含めない。

## Scope
この文書は Serena 固有の管理方針を定義する。  
AI 全体の責任分界や共通運用は `AGENTS.md` / `docs/ai-usage-policy.md` に委譲する。

## Versioned Files (Git 管理する)
- `docs/serena/*.md`（Serena 運用ルール、判断理由、移行手順）
- `.serena/project.yml`（プロジェクト共通で再現したい Serena 設定）
- `.serena/.gitignore`（Serena 配下の除外ルール）
- `docs/serena/memories/*.md`（公開用に選別・整形した知識メモ）

## Ignored Files (Git 管理しない)
- `.serena/cache/`
- `.serena/logs/`
- `.serena/sessions/`
- `.serena/tmp/`
- `.serena/project.local.yml`
- `.serena/memories/`（実行時に増える生メモ置き場。公開用は `docs/serena/memories/` へ転記）

## Operating Rules
1. Serena の恒久ルール・方針は `docs/serena/` を正とする。
2. `.serena/memories/` は作業用の一時領域として扱い、原則コミットしない。
3. 共有価値があるメモのみ `docs/serena/memories/` に転記し、公開前に機密情報（トークン、個人情報、内部 URL、秘密鍵断片）がないことを確認する。
4. ローカル上書き設定は `.serena/project.local.yml` に限定し、チーム共通設定へ混入させない。
5. Serena の新しいファイル種別が増えた場合は、先に `versioned/ignored` を決めた上でこの文書と `.gitignore` を同時更新する。

## Definition of Done (Serena 運用確定の完了条件)
- この文書の方針と `.gitignore` の実態が一致している。
- 新規参加者が `project.yml` と `docs/serena/` を読むことで Serena 運用を再現できる。
- 公開物に機密情報が含まれていないことをレビューで確認済みである。
