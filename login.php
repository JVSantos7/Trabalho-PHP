<?php
    session_start();
    require_once 'config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $db = new Database();
        $conn = $db->getConnection();

        $query = $conn->prepare('SELECT id, password FROM users WHERE username = :username');
        $query->bindValue(':username', $username, SQLITE3_TEXT);
        $result = $query->execute();

        if ($user = $result->fetchArray(SQLITE3_ASSOC)) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header('Location: dashboard.html');
                exit;
            }
        }

        echo "<script>
                alert('Usuário ou senha inválidos')
                window.location.href='index.html';
            </script>
            ";
    }
