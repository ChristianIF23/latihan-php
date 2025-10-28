<!DOCTYPE html>
<html>
<head>
    <title>PHP - Aplikasi Todolist</title>
    <link href="/assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container-fluid p-5">
    <div class="card">
        <div class="card-body">

            <!-- ALERT NOTIFIKASI -->
            <?php if (isset($_GET['error']) && $_GET['error'] === 'duplicate'): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    Todo dengan judul tersebut sudah ada! Silakan gunakan judul lain.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif (isset($_GET['success']) && $_GET['success'] === 'created'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Todo berhasil ditambahkan!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif (isset($_GET['error']) && $_GET['error'] === 'failed'): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Gagal menambahkan todo. Silakan coba lagi.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- FILTER DAN SEARCH -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <form class="d-flex" method="GET" action="index.php">
                        <input type="hidden" name="filter" value="<?= htmlspecialchars($_GET['filter'] ?? 'all') ?>">
                        <input class="form-control me-2" name="search" placeholder="Cari todo..." 
                               value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        <button class="btn btn-outline-primary" type="submit">Cari</button>
                    </form>
                </div>
                <div>
                    <a href="?filter=all" class="btn btn-secondary btn-sm">Semua</a>
                    <a href="?filter=finished" class="btn btn-success btn-sm">Selesai</a>
                    <a href="?filter=unfinished" class="btn btn-danger btn-sm">Belum Selesai</a>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTodo">Tambah</button>
                </div>
            </div>

            <hr />
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Judul</th>
                        <th>Status</th>
                        <th>Tanggal Dibuat</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($todos)): ?>
                    <?php foreach ($todos as $i => $todo): ?>
                    <tr data-id="<?= $todo['id'] ?>">
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($todo['title'] ?? $todo['activity']) ?></td>
                        <td>
                            <?php if (!empty($todo['is_finished']) && $todo['is_finished'] == true): ?>
                                <span class="badge bg-success">Selesai</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Belum Selesai</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d F Y - H:i', strtotime($todo['created_at'])) ?></td>
                        <td>
                            <button class="btn btn-sm btn-info"
                                onclick="window.location='?page=detail&id=<?= $todo['id'] ?>'">
                                Detail
                            </button>
                            <button class="btn btn-sm btn-warning"
                                onclick="showModalEditTodo(<?= $todo['id'] ?>, 
                                '<?= htmlspecialchars(addslashes($todo['title'] ?? $todo['activity'])) ?>',
                                <?= $todo['is_finished'] ?? 0 ?>)">
                                Ubah
                            </button>
                            <button class="btn btn-sm btn-danger"
                                onclick="showModalDeleteTodo(<?= $todo['id'] ?>, 
                                '<?= htmlspecialchars(addslashes($todo['title'] ?? $todo['activity'])) ?>')">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">Belum ada data tersedia!</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL ADD TODO -->
<div class="modal fade" id="addTodo" tabindex="-1" aria-labelledby="addTodoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTodoLabel">Tambah Data Todo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="?page=create" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="inputTitle" class="form-label">Judul</label>
                        <input type="text" name="title" class="form-control" id="inputTitle"
                            placeholder="Contoh: Belajar membuat aplikasi website sederhana" required>
                    </div>
                    <div class="mb-3">
                        <label for="inputDescription" class="form-label">Deskripsi</label>
                        <textarea name="description" id="inputDescription" rows="3" class="form-control"
                            placeholder="Tuliskan deskripsi singkat..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT TODO -->
<div class="modal fade" id="editTodo" tabindex="-1" aria-labelledby="editTodoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTodoLabel">Ubah Data Todo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="?page=update" method="POST">
                <input name="id" type="hidden" id="inputEditTodoId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="inputEditTitle" class="form-label">Judul</label>
                        <input type="text" name="title" class="form-control" id="inputEditTitle" required>
                    </div>
                    <div class="mb-3">
                        <label for="inputEditDescription" class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" id="inputEditDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="selectEditStatus" class="form-label">Status</label>
                        <select class="form-select" name="is_finished" id="selectEditStatus">
                            <option value="0">Belum Selesai</option>
                            <option value="1">Selesai</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL DELETE TODO -->
<div class="modal fade" id="deleteTodo" tabindex="-1" aria-labelledby="deleteTodoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteTodoLabel">Hapus Data Todo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Kamu akan menghapus todo <strong class="text-danger" id="deleteTodoTitle"></strong>. Apakah kamu yakin?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a id="btnDeleteTodo" class="btn btn-danger">Ya, Hapus</a>
            </div>
        </div>
    </div>
</div>

<script src="/assets/vendor/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
function showModalEditTodo(id, title, is_finished) {
    document.getElementById("inputEditTodoId").value = id;
    document.getElementById("inputEditTitle").value = title;
    document.getElementById("selectEditStatus").value = is_finished ? 1 : 0;
    var modal = new bootstrap.Modal(document.getElementById("editTodo"));
    modal.show();
}

function showModalDeleteTodo(id, title) {
    document.getElementById("deleteTodoTitle").innerText = title;
    document.getElementById("btnDeleteTodo").setAttribute("href", `?page=delete&id=${id}`);
    var modal = new bootstrap.Modal(document.getElementById("deleteTodo"));
    modal.show();
}

const tbody = document.querySelector("tbody");
if (tbody) {
    new Sortable(tbody, {
        animation: 150,
        onEnd: function() {
            const ids = [...tbody.querySelectorAll("tr")].map(row => row.dataset.id);
            fetch("?page=reorder", {
                method: "POST",
                body: JSON.stringify(ids)
            });
        }
    });
}
</script>
</body>
</html>
