
<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$name = $_SESSION['name'];
$halaman = basename($_SERVER['PHP_SELF']);

$id = $_GET['id'] ?? '';

if ($id == '' || !is_numeric($id)) {
    echo "ID guru tidak valid";
    exit;
}

$id = (int) $id;

$query = mysqli_query($koneksi, "SELECT * FROM t_guru WHERE id = $id");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Data guru tidak ditemukan";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Guru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR ADMIN -->
        <div class="col-md-3 col-lg-2 position-fixed top-0 start-0 vh-100 p-3 overflow-auto"
             style="background-color: #172554;">

            <h4 class="text-white mb-4">Sistem Pelanggaran</h4>

            <ul class="nav nav-pills flex-column gap-1">

                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link text-white">Dashboard</a>
                </li>

                <li class="nav-item">
                    <a href="data_siswa.php" class="nav-link text-white">Data Siswa</a>
                </li>

                <li class="nav-item">
                    <a href="data_guru.php"
                       class="nav-link <?= $halaman == 'edit_guru.php' ? 'active' : 'text-white'; ?>">
                        Data Guru
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_kelas.php" class="nav-link text-white">Data Kelas</a>
                </li>

                <li class="nav-item">
                    <a href="data_tahun_ajaran.php" class="nav-link text-white">Tahun Ajaran</a>
                </li>

                <li class="nav-item">
                    <a href="data_penempatan_siswa.php" class="nav-link text-white">Penempatan Siswa</a>
                </li>

                <li class="nav-item">
                    <a href="data_wali_kelas.php" class="nav-link text-white">Data Wali Kelas</a>
                </li>

                <li class="nav-item">
                    <a href="data_kategori_pelanggaran.php" class="nav-link text-white">Kategori Pelanggaran</a>
                </li>

                <li class="nav-item">
                    <a href="data_jenis_pelanggaran.php" class="nav-link text-white">Jenis Pelanggaran</a>
                </li>

                <li class="nav-item">
                    <a href="laporan.php" class="nav-link text-white">Laporan</a>
                </li>

                <li class="nav-item">
                    <a href="about_me.php" class="nav-link text-white">About Me</a>
                </li>

                <hr class="border-white">

                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-white">Logout</a>
                </li>

            </ul>
        </div>

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

            <div class="bg-white rounded shadow-sm p-4 mb-4">
                <h2 class="fw-bold mb-2">Edit Data Guru</h2>
                <p class="text-secondary mb-0">
                    Ubah data guru yang sudah tersimpan.
                </p>
            </div>

            <div class="card shadow-sm border-0">

                <div class="card-header bg-white p-3">
                    <h5 class="mb-0 fw-bold">Form Edit Guru</h5>
                </div>

                <div class="card-body p-4">

                    <form action="proses_edit_guru.php" method="POST">

                        <input type="hidden" name="id"
                               value="<?= htmlspecialchars($data['id']); ?>">

                        <!-- NIP -->
                        <div class="mb-3">
                            <label for="nip" class="form-label">NIP</label>
                            <input type="text"
                                   id="nip"
                                   name="nip"
                                   class="form-control"
                                   maxlength="18"
                                   inputmode="numeric"
                                   placeholder="Masukkan NIP guru"
                                   value="<?= htmlspecialchars($data['nip']); ?>"
                                   required>
                        </div>

                        <!-- NAMA GURU -->
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Guru</label>
                            <input type="text"
                                   id="nama"
                                   name="nama"
                                   class="form-control"
                                   value="<?= htmlspecialchars($data['nama']); ?>"
                                   required>
                        </div>

                        <!-- EMAIL -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   class="form-control"
                                   value="<?= htmlspecialchars($data['email']); ?>"
                                   required>
                        </div>

                        <!-- STATUS AKTIF -->
                        <div class="mb-3">
                            <label for="status_aktif" class="form-label">Status Aktif</label>
                            <select id="status_aktif"
                                    name="status_aktif"
                                    class="form-select"
                                    required>
                                <option value="1" <?= $data['status_aktif'] == 1 ? 'selected' : ''; ?>>
                                    Aktif
                                </option>

                                <option value="0" <?= $data['status_aktif'] == 0 ? 'selected' : ''; ?>>
                                    Tidak Aktif
                                </option>
                            </select>
                        </div>

                        <!-- USER ID -->
                        <div class="mb-4">
                            <label for="user_id" class="form-label">User ID</label>
                            <input type="number"
                                   id="user_id"
                                   name="user_id"
                                   class="form-control"
                                   value="<?= htmlspecialchars($data['user_id']); ?>"
                                   required>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="data_guru.php" class="btn btn-secondary">
                                Kembali
                            </a>

                            <button type="submit"
                                    name="update"
                                    value="1"
                                    class="btn btn-primary">
                                Simpan Perubahan
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>