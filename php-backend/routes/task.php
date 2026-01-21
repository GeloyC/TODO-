<?php 

function route(string $method, string $path, PDO $pdo): void {
    $path = rtrim($path, '/');

    if ($path === '' || $path === '/') {
        http_response_code(200);
        echo json_encode([
            "message" => "Todo API is running!",
            "endpoint" => [
                'POST /task' => 'Create new Task'
            ]
        ]);

        return;
    }


    if ($path === '/tasks') {
        if ($method === 'POST') {
            createTask($pdo);
            return;
        }

        if ($method === 'GET') {
            getAllTask($pdo);
            return;
        }

        http_response_code(405);
        echo json_encode([
            'error'=>'Method not allowed'
        ]);

        return;
    }

    if (preg_match('#^/tasks?/(\d+)$#', $path, $matches)) {
        $id = (int) $matches[1];

        if ($method === 'DELETE') {
            deleteTask($pdo, $id);
            return;
        }

        if ($method === 'PATCH') {
            updateTask($pdo, $id);
            return;
        }

        http_response_code(405);
        echo json_encode([
            'error'=>'Method not allowed'
        ]);

        return;
    }


    http_response_code(404);
    echo json_encode([
        'error' => 'Not Found',
        'path' => $path,
        'method' => $method
    ]);
};


function createTask(PDO $pdo): void {
    try {
        $input = json_decode(file_get_contents('php://input'), true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($input)) {
            http_response_code(400);
            echo json_encode(['error'=>'Invalid JSON']);

            return;
        }

        $title = trim($input['title'] ?? '');

        if ($title === ''){
            http_response_code(400);
            echo json_encode(['error' => 'Title is required!']);

            return;
        }

        $stmt = $pdo->prepare("
            INSERT INTO tasks (title, active, date_created)
            VALUES (?, false, NOW());	
        ");
        $stmt->execute([$title]);
        $new_id = $pdo->lastInsertId();

        http_response_code(201);

        echo json_encode([
            'success' => true,
            'id' => (int) $new_id,
            'title' => $title,
            'active' => true
        ]);

    } catch (Exception $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode([
            'error' => 'Server error',
            'message' => $e->getMessage()
        ]);
    }
}


function getAllTask(PDO $pdo): void {
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM tasks
        "); 

        $stmt->execute();
        $tasks = $stmt->fetchAll();

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'tasks' => $tasks,
            'count' => count($tasks)
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Server error',
            'message' => $e->getMessage()
        ]);
    }
}

function deleteTask(PDO $pdo, int $id): void {
    try {
        $stmt = $pdo->prepare("
            DELETE FROM tasks WHERE id = ?
        ");
        $stmt->execute([$id]);

        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode([
                'error'=>'Task not found'
            ]);

            return;
        }

        http_response_code(204);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'error' => 'Server Error'
        ]);
    }
}


function updateTask(PDO $pdo, int $id): void {
    try {
        $stmt = $pdo->prepare("
            UPDATE tasks SET active = 1 WHERE id = ?;
        ");

        $stmt->execute([$id]);
        http_response_code(204);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'error' => 'Server error'
        ]);
    }
}
?>