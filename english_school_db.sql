-- =============================================================================
--  英会話学校予約システム テーブル定義
--
--  使い方: mysql -u ユーザー名 -p データベース名 < english_school_db.sql
--          続けて initial_data.sql で講師・コースの初期データを登録する
--  ※ テーブルを削除してから作り直すため、既存のデータは消えます
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------------------------
--  instructor : インストラクター
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `instructor`;
CREATE TABLE `instructor` (
  `id`        int          NOT NULL AUTO_INCREMENT COMMENT 'インストラクターID',
  `name`      varchar(32)  NOT NULL                COMMENT '氏名',
  `hometown`  varchar(64)  DEFAULT NULL            COMMENT '出身地',
  `biography` varchar(255) DEFAULT NULL            COMMENT '略歴',
  `details`   varchar(255) DEFAULT NULL            COMMENT '詳細',
  `hobby`     varchar(255) DEFAULT NULL            COMMENT '趣味',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='インストラクター';

-- -----------------------------------------------------------------------------
--  course : コース
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `course`;
CREATE TABLE `course` (
  `id`   int         NOT NULL AUTO_INCREMENT COMMENT 'コースID',
  `name` varchar(64) NOT NULL                COMMENT 'コース名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='コース';

-- -----------------------------------------------------------------------------
--  member : 会員
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `member`;
CREATE TABLE `member` (
  `id`              int          NOT NULL AUTO_INCREMENT COMMENT '会員ID',
  `mail_address`    varchar(255) NOT NULL                COMMENT 'メールアドレス（ログインID）',
  `password`        varchar(255) NOT NULL                COMMENT 'パスワード（password_hash() でハッシュ化）',
  `familyName`      varchar(32)  NOT NULL                COMMENT '姓',
  `givenName`       varchar(32)  NOT NULL                COMMENT '名',
  `familyName_kana` varchar(32)  NOT NULL                COMMENT 'セイ',
  `givenName_kana`  varchar(32)  NOT NULL                COMMENT 'メイ',
  `tel`             varchar(20)  NOT NULL                COMMENT '電話番号（ハイフンなし）',
  `zipcode`         varchar(8)   DEFAULT NULL            COMMENT '郵便番号（ハイフンなし）',
  `pref`            varchar(8)   DEFAULT NULL            COMMENT '都道府県',
  `city`            varchar(64)  DEFAULT NULL            COMMENT '市区町村',
  `address`         varchar(128) DEFAULT NULL            COMMENT '番地・建物名',
  `sex`             varchar(2)   DEFAULT NULL            COMMENT '性別（男 / 女）',
  `birthday`        date         DEFAULT NULL            COMMENT '生年月日',
  `mail_magazine`   tinyint      NOT NULL DEFAULT 0      COMMENT 'メールマガジン（1: 希望する / 0: 希望しない）',
  `created_at`      datetime     DEFAULT NULL            COMMENT '登録日時',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_member_mail_address` (`mail_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='会員';

-- -----------------------------------------------------------------------------
--  guest : ゲスト（会員登録せずに予約した人）
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `guest`;
CREATE TABLE `guest` (
  `id`           int          NOT NULL AUTO_INCREMENT COMMENT 'ゲストID',
  `name`         varchar(64)  NOT NULL                COMMENT '氏名（姓＋名）',
  `name_kana`    varchar(64)  NOT NULL                COMMENT 'フリガナ（セイ＋メイ）',
  `tel`          varchar(20)  NOT NULL                COMMENT '電話番号（ハイフンなし）',
  `mail_address` varchar(255) NOT NULL                COMMENT 'メールアドレス',
  `address`      varchar(255) DEFAULT NULL            COMMENT '住所（都道府県＋市区町村＋番地）',
  `sex`          varchar(2)   DEFAULT NULL            COMMENT '性別（男 / 女）',
  `birthday`     date         DEFAULT NULL            COMMENT '生年月日',
  `created_at`   datetime     DEFAULT NULL            COMMENT '登録日時',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='ゲスト';

-- -----------------------------------------------------------------------------
--  reserve : 予約
--    会員の予約は member_id、ゲストの予約は guest_id に値が入る
--    同じ会員・同じ講師で、同じ日時の予約は1件だけ（UNIQUE）
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `reserve`;
CREATE TABLE `reserve` (
  `id`            int         NOT NULL AUTO_INCREMENT COMMENT '予約ID（予約番号）',
  `member_id`     int         DEFAULT NULL            COMMENT '会員ID',
  `guest_id`      int         DEFAULT NULL            COMMENT 'ゲストID',
  `instructor_id` int         NOT NULL                COMMENT 'インストラクターID',
  `course_id`     int         NOT NULL                COMMENT 'コースID',
  `day`           date        NOT NULL                COMMENT '予約日',
  `week`          varchar(2)  NOT NULL                COMMENT '曜日',
  `time`          tinyint     NOT NULL                COMMENT '時間（9〜20時）',
  `opinion`       text        DEFAULT NULL            COMMENT 'ご要望・ご意見',
  `device`        varchar(32) DEFAULT NULL            COMMENT '予約した端末（PC / SP / FP / ROBOT）',
  `created_at`    datetime    DEFAULT NULL            COMMENT '予約日時',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_reserve_member`     (`member_id`, `day`, `time`),
  UNIQUE KEY `uq_reserve_instructor` (`instructor_id`, `day`, `time`),
  KEY `idx_reserve_day` (`day`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='予約';

SET FOREIGN_KEY_CHECKS = 1;
