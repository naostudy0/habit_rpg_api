# Domain Layer

Domain層は、アプリケーションの中核となる  
**フレームワーク非依存のビジネスルール** を保持します。

この層は Laravel や Eloquent、HTTP などの技術的詳細に依存せず、  
純粋な業務ロジックのみを表現することを目的としています。

## ディレクトリ構成

- `Entities`
  - 業務上の概念を表すエンティティ
  - 状態とビジネスルールを持つが、永続化の責務は持たない

- `Repositories`
  - エンティティを永続化・取得するためのインターフェース
  - 実装は Infrastructure層に委譲する

## 設計方針

- Domain層はフレームワークに依存しない
- Entity は自ら保存処理を行わない
- 永続化の責務は Repository に委ねる
- Repository Interface は Domain層に置く
