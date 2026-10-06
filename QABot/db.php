<?php

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "qabot"
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->query(
    "CREATE TABLE IF NOT EXISTS faqs (
        id INT(11) NOT NULL AUTO_INCREMENT,
        question TEXT NOT NULL,
        answer TEXT NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
        PRIMARY KEY (id),
        UNIQUE KEY uq_question (question(255))
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
);
?>