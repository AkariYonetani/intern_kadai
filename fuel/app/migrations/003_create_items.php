<?php

namespace Fuel\Migrations;

class Create_items
{
    public function up()
    {
        \DB::query("
            CREATE TABLE items (
              id INT UNSIGNED NOT NULL AUTO_INCREMENT,
              category_id INT UNSIGNED NOT NULL,
              name VARCHAR(100) NOT NULL,
              desire_level TINYINT(1) NOT NULL DEFAULT 3,
              reason VARCHAR(500) NULL,
              url VARCHAR(2048) NULL,
              price INT NULL,
              image_path VARCHAR(255) NULL,
              created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (id),
              CONSTRAINT fk_items_category FOREIGN KEY (category_id) REFERENCES categories (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ")->execute();
    }

    public function down()
    {
        \DB::query("DROP TABLE IF EXISTS items")->execute();
    }
}
