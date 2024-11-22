<?php
    class Database {
        private $db;

        public function __construct() {
                $this->db = new SQLite3(__DIR__ . '/todo.db');
                $this->createTables();
        }

        private function createTables() {
            $this->db->exec('
                CREATE TABLE IF NOT EXISTS users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    username TEXT NOT NULL UNIQUE,
                    password TEXT NOT NULL
                );
                CREATE TABLE IF NOT EXISTS todos (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER,
                    title TEXT NOT NULL,
                    completed BOOLEAN DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id)
                );
            ');
        }

        public function getConnection() {
            return $this->db;
        }
    }
?>