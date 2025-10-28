<?php
require_once(__DIR__ . '/../models/TodoModel.php');

class TodoController {
    public function index() {
        $todoModel = new TodoModel();
        $filter = $_GET['filter'] ?? 'all';
        $search = $_GET['search'] ?? '';
        $todos = $todoModel->getTodos($filter, $search);
        include(__DIR__ . '/../views/TodoView.php');
    }

public function create() {
    require_once(__DIR__ . '/../models/TodoModel.php');
    $todoModel = new TodoModel();

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    // Jalankan fungsi createTodo di model
    $result = $todoModel->createTodo($title, $description);

    if ($result === "duplicate") {
        // Kirimkan notifikasi error lewat parameter GET
        header("Location: index.php?error=duplicate");
        exit;
    } elseif ($result) {
        header("Location: index.php?success=created");
        exit;
    } else {
        header("Location: index.php?error=failed");
        exit;
    }
}



    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $title = $_POST['title'];
            $description = $_POST['description'];
            $is_finished = isset($_POST['is_finished']) ? $_POST['is_finished'] : 0;
            $todoModel = new TodoModel();
            $todoModel->updateTodo($id, $title, $description, $is_finished);
        }
        header('Location: index.php');
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $todoModel = new TodoModel();
            $todoModel->deleteTodo($_GET['id']);
        }
        header('Location: index.php');
    }

    public function detail() {
        if (isset($_GET['id'])) {
            $todoModel = new TodoModel();
            $todo = $todoModel->getTodoById($_GET['id']);
            include(__DIR__ . '/../views/TodoDetail.php');
        }
    }

    public function reorder() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $positions = json_decode(file_get_contents('php://input'), true);
            $todoModel = new TodoModel();
            $todoModel->updatePositions($positions);
        }
    }
}
