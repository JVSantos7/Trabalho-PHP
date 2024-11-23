<?php
    session_start();
    require_once 'config.php';

    $db = new Database();
    $conn = $db->getConnection();
    $user_id = $_SESSION['user_id'];

    $action = $_GET['action'] ?? $_POST['action'];

    switch ($action) {
        case 'list':
            $query = $conn->prepare('SELECT * FROM todos WHERE user_id = :user_id ORDER BY created_at DESC');
            $query->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
            $result = $query->execute();
            
            $todos = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $todos[] = $row;
            }
            
            header('Content-Type: application/json');
            echo json_encode($todos);
            break;

        case 'add':
            $title = $_POST['title'];
            
            if (!empty($title)) {
                $query = $conn->prepare('INSERT INTO todos (user_id, title) VALUES (:user_id, :title)');
                $query->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
                $query->bindValue(':title', $title, SQLITE3_TEXT);
                
                if ($query->execute()) {
                    http_response_code(201);
                    echo json_encode(['message' => 'Todo criado com sucesso']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Erro ao criar todo']);
                }
            }
            break;

        case 'toggle':
            $id = $_POST['id'];
            $completed = $_POST['completed'];
            
            if ($id) {
                $query = $conn->prepare('UPDATE todos SET completed = :completed WHERE id = :id AND user_id = :user_id');
                $query->bindValue(':completed', $completed, SQLITE3_INTEGER);
                $query->bindValue(':id', $id, SQLITE3_INTEGER);
                $query->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
                
                if ($query->execute()) {
                    echo json_encode(['message' => 'Todo atualizado com sucesso']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Erro ao atualizar todo']);
                }
            }
            break;

        case 'delete':
            $id = $_POST['id'];
            
            if ($id) {
                $query = $conn->prepare('DELETE FROM todos WHERE id = :id AND user_id = :user_id');
                $query->bindValue(':id', $id, SQLITE3_INTEGER);
                $query->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
                
                if ($query->execute()) {
                    echo json_encode(['message' => 'Todo deletado com sucesso']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Erro ao deletar todo']);
                }
            }
            break;

            case 'update':
                $id = $_POST['id'];
                $title = $_POST['title'];
                
                if ($id && $title) {
                    $query = $conn->prepare('UPDATE todos SET title = :title WHERE id = :id AND user_id = :user_id');
                    $query->bindValue(':title', $title, SQLITE3_TEXT);
                    $query->bindValue(':id', $id, SQLITE3_INTEGER);
                    $query->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
                    
                    if ($query->execute()) {
                        echo json_encode(['message' => 'Todo atualizado com sucesso']);
                    } else {
                        http_response_code(500);
                        echo json_encode(['error' => 'Erro ao atualizar todo']);
                    }
                }
                break;
    }