<?php
    require_once 'config.php';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];
    
        $db = new Database();
        $conn = $db->getConnection();
    
        // Verifica se o usuário já existe
        $query = $conn->prepare('SELECT id FROM users WHERE username = ?');
        $query->bindValue(1, $username);
        $result = $query->execute();
    
        if($result->fetchArray()) {
            echo "<script>
                    alert('Nome do usuário já existe')
                    window.location.href='register.html';
                </script>";
        }
    
        // Hash da senha
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
        // Insere um novo usuário
        $query = $conn->prepare('INSERT INTO users (username, password) VALUES (?,?)');
        $query->bindValue(1, $username);
        $query->bindValue(2, $hashed_password);
        
        //o execute() quando usado em querys que não retornam dados,(INSERT, UPDATE, DELETE), retorna um booleano
        if ($query->execute()) {
            header('Location: index.html');
            exit;
        } 

        echo "<script>
                alert('Falha no registro');
                window.location.href='register.html';
            </script>";
    }