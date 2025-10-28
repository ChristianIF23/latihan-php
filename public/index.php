<?php
include('../controllers/TodoController.php');

$todoController = new TodoController();

// Tentukan halaman aktif
$page = $_GET['page'] ?? 'index';

// Routing halaman
switch ($page) {
    case 'index':
        $todoController->index();
        break;
    case 'create':
        $todoController->create();
        break;
    case 'update':
        $todoController->update();
        break;
    case 'delete':
        $todoController->delete();
        break;
    case 'detail': // halaman detail todo
        $todoController->detail();
        break;
    case 'reorder': // drag & drop sorting
        $todoController->reorder();
        break;
    default:
        $todoController->index();
        break;
}
