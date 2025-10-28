<?php
require_once(__DIR__ . '/../config.php');

class TodoModel {
    private $conn;

    public function __construct() {
        $this->conn = pg_connect(
            'host=' . DB_HOST . ' port=' . DB_PORT . ' dbname=' . DB_NAME .
            ' user=' . DB_USER . ' password=' . DB_PASSWORD
        );
        if (!$this->conn) die('Koneksi database gagal');
    }

    // ✅ Ambil data Todo dengan filter dan pencarian
    public function getTodos($filter = 'all', $search = '') {
        $query = "SELECT * FROM todo";
        $conditions = [];

        if ($filter === 'finished') {
            $conditions[] = "is_finished = TRUE";
        } elseif ($filter === 'unfinished') {
            $conditions[] = "is_finished = FALSE";
        }

        if (!empty($search)) {
            $conditions[] = "LOWER(title) LIKE LOWER('%$search%')";
        }

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " ORDER BY id ASC";
        $result = pg_query($this->conn, $query);

        $todos = [];
        if ($result) {
            while ($row = pg_fetch_assoc($result)) {
                // Pastikan hasilnya boolean, bukan string “t” / “f”
                $row['is_finished'] = ($row['is_finished'] === 't' || $row['is_finished'] === true);
                $todos[] = $row;
            }
        }
        return $todos;
    }

    // ✅ Tambah Todo baru
    public function createTodo($title, $description) {
        $dupCheck = pg_query_params($this->conn, "SELECT * FROM todo WHERE LOWER(title)=LOWER($1)", [$title]);
        if (pg_num_rows($dupCheck) > 0) return "duplicate";

        $query = "INSERT INTO todo (title, description, is_finished, created_at, updated_at, position) 
                  VALUES ($1, $2, FALSE, NOW(), NOW(), 0)";
        return pg_query_params($this->conn, $query, [$title, $description]) !== false;
    }

    // ✅ Update Todo (judul, deskripsi, status)
    public function updateTodo($id, $title, $description, $is_finished) {
        // Pastikan nilai boolean dikonversi dengan benar
        $is_finished = ($is_finished === '1' || $is_finished === 1 || $is_finished === true) ? 'TRUE' : 'FALSE';
        $query = "UPDATE todo 
                  SET title=$1, description=$2, is_finished=$3::BOOLEAN, updated_at=NOW() 
                  WHERE id=$4";
        return pg_query_params($this->conn, $query, [$title, $description, $is_finished, $id]) !== false;
    }

    // ✅ Hapus Todo
    public function deleteTodo($id) {
        return pg_query_params($this->conn, "DELETE FROM todo WHERE id=$1", [$id]) !== false;
    }

    // ✅ Reorder Todo
    public function updatePositions($positions) {
        foreach ($positions as $index => $id) {
            pg_query_params($this->conn, "UPDATE todo SET position=$1 WHERE id=$2", [$index + 1, $id]);
        }
        return true;
    }

    // ✅ Ambil satu Todo
    public function getTodoById($id) {
        $result = pg_query_params($this->conn, "SELECT * FROM todo WHERE id=$1", [$id]);
        return pg_fetch_assoc($result);
    }
}
