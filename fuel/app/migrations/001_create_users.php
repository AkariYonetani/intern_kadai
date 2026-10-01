<?php

namespace Fuel\Migrations;

class Create_users
{
    public function up()
    {
        \DB::query("
            CREATE TABLE users (
              id INT UNSIGNED NOT NULL AUTO_INCREMENT,
              name VARCHAR(50) NOT NULL,
              email VARCHAR(255) NOT NULL,
              password VARCHAR(255) NOT NULL,
              created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (id),
              UNIQUE KEY uq_users_email (email)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ")->execute();
    }

    public function down()
    {
        \DB::query("DROP TABLE IF EXISTS users")->execute();
    }
}
