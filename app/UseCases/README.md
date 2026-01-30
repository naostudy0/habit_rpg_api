# UseCase Layer

UseCase層は、アプリケーション固有の処理を実行する層です。  
入力を受け取り、ドメインのルールに沿って処理し、結果を返します。

## 責務

- ユースケース単位の処理を組み立てる
- ドメインのエンティティやリポジトリを利用する
- 取引や永続化の流れを調整する

## 実装ルール

- HTTP / JSON / Request / Response に依存しない
- 入力は UseCase 専用の DTO として定義する
- 出力は Output にまとめ、Result で成功/失敗を表現する
- 例外は Result の失敗で表現し、UseCase 内で完結させる

## ディレクトリ構成

- `Inputs`
  - UseCase の入力DTO
- `Outputs`
  - UseCase の出力DTO
- `Results`
  - Result の共通定義
