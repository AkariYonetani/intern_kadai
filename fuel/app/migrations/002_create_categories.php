<?php

namespace Fuel\Migrations;

class Create_categories
{
    public function up()
    {
        \DB::query("
            CREATE TABLE categories (
              id INT UNSIGNED NOT NULL AUTO_INCREMENT,
              user_id INT UNSIGNED NOT NULL,
              name VARCHAR(50) COLLATE utf8mb4_bin NOT NULL,
              is_default TINYINT(1) NOT NULL DEFAULT 0,
              created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (id),
              UNIQUE KEY uq_categories_user_name (user_id, name),
              CONSTRAINT fk_categories_user FOREIGN KEY (user_id) REFERENCES users (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ")->execute();
    }

    public function down()
    {
        \DB::query("DROP TABLE IF EXISTS categories")->execute();
    }
}
