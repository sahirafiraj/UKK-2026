
<?php
session_start();

include "config/koneksi.php";

// Cek hak akses admin
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$halaman = basename($_SERVER['PHP_SELF']);
$nama_login = $_SESSION['name'] ?? 'Admin';

// Mengambil ID siswa dari URL
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Mengambil data siswa dari database
$query = mysqli_query($koneksi, "
    SELECT * FROM t_siswa
    WHERE id = $id
    LIMIT 1
");

$data = mysqli_fetch_assoc($query);

// Cek apakah data siswa ditemukan
if (!$data) {
    echo "Data siswa tidak ditemukan.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Siswa</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
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
                    <a href="dashboard.php"
                       class="nav-link text-white">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_siswa.php"
                       class="nav-link active">
                        Data Siswa
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_guru.php"
                       class="nav-link text-white">
                        Data Guru
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_kelas.php"
                       class="nav-link text-white">
                        Data Kelas
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_tahun_ajaran.php"
                       class="nav-link text-white">
                        Tahun Ajaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_penempatan_siswa.php"
                       class="nav-link text-white">
                        Penempatan Siswa
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_wali_kelas.php"
                       class="nav-link text-white">
                        Data Wali Kelas
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_kategori_pelanggaran.php"
                       class="nav-link text-white">
                        Kategori Pelanggaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_jenis_pelanggaran.php"
                       class="nav-link text-white">
                        Jenis Pelanggaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="laporan.php"
                       class="nav-link text-white">
                        Laporan
                    </a>
                </li>

                <li class="nav-item">
                    <a href="about_me.php"
                       class="nav-link text-white">
                        About Me
                    </a>
                </li>

                <hr class="border-white">

                <li class="nav-item">
                    <a href="logout.php"
                       class="nav-link text-white">
                        Logout
                    </a>
                </li>

            </ul>
        </div>

        <!-- KONTEN EDIT SISWA -->
        <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

            <!-- JUDUL HALAMAN -->
            <div class="bg-white rounded shadow-sm p-4 mb-4">

                <h2 class="fw-bold">Edit Siswa</h2>

                <p class="text-secondary mb-0">
                    Perbarui data siswa yang sudah tersimpan di dalam sistem.
                </p>

            </div>

            <!-- NOTIFIKASI ERROR -->
            <?php if (isset($_SESSION['pesan_error'])) { ?>

                <div class="alert alert-danger alert-dismissible fade show shadow-sm"
                     role="alert">

                    <strong>Data belum berhasil diperbarui!</strong><br>

                    <?= htmlspecialchars($_SESSION['pesan_error']); ?>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert">
                    </button>

                </div>

            <?php
                unset($_SESSION['pesan_error']);
            } ?>

            <!-- FORM EDIT SISWA -->
            <div class="card shadow-sm border-0">

                <div class="card-header bg-white">
                    <h5 class="mb-0">Form Edit Siswa</h5>
                </div>

                <div class="card-body">

                    <form action="proses_edit_siswa.php" method="POST">

                        <!-- ID SISWA -->
                        <input type="hidden"
                               name="id"
                               value="<?= (int) $data['id']; ?>">

                        <!-- NIS -->
                        <div class="mb-3">
                            <label class="form-label">NIS</label>

                            <input type="text"
                                   name="nis"
                                   class="form-control"
                                   placeholder="Masukkan NIS"
                                   maxlength="10"
                                   value="<?= htmlspecialchars($data['nis']); ?>"
                                   required>
                        </div>

                        <!-- NISN -->
                        <div class="mb-3">
                            <label class="form-label">NISN</label>

                            <input type="text"
                                   name="nisn"
                                   class="form-control"
                                   placeholder="Masukkan NISN"
                                   maxlength="10"
                                   value="<?= htmlspecialchars($data['nisn']); ?>"
                                   required>
                        </div>

                        <!-- NAMA SISWA -->
                        <div class="mb-3">
                            <label class="form-label">Nama Siswa</label>

                            <input type="text"
                                   name="nama"
                                   class="form-control"
                                   placeholder="Masukkan nama siswa"
                                   value="<?= htmlspecialchars($data['nama']); ?>"
                                   required>
                        </div>

                        <!-- JENIS KELAMIN -->
                        <div class="mb-3">
                            <label class="form-label">Jenis Kelamin</label>

                            <select name="jenis_kelamin"
                                    class="form-select"
                                    required>

                                <option value="">Pilih Jenis Kelamin</option>

                                <option value="L"
                                    <?= $data['jenis_kelamin'] == 'L' ? 'selected' : ''; ?>>
                                    Laki-laki
                                </option>

                                <option value="P"
                                    <?= $data['jenis_kelamin'] == 'P' ? 'selected' : ''; ?>>
                                    Perempuan
                                </option>

                            </select>
                        </div>

                        <!-- TANGGAL LAHIR -->
                        <div class="mb-3">
                            <label class="form-label">Tanggal Lahir</label>

                            <input type="date"
                                   name="tanggal_lahir"
                                   class="form-control"
                                   value="<?= htmlspecialchars($data['tanggal_lahir']); ?>"
                                   required>
                        </div>

                        <!-- ALAMAT -->
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>

                            <textarea name="alamat"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Masukkan alamat siswa"
                                      required><?= htmlspecialchars($data['alamat']); ?></textarea>
                        </div>

                        <!-- STATUS AKTIF -->
                        <div class="col-12 mb-3">

                            <label class="form-label">Status Aktif</label>

                            <select name="status_aktif"
                                    class="form-select"
                                    required>

                                <option value="1"
                                    <?= $data['status_aktif'] == 1 ? 'selected' : ''; ?>>
                                    Aktif
                                </option>

                                <option value="0"
                                    <?= $data['status_aktif'] == 0 ? 'selected' : ''; ?>>
                                    Tidak Aktif
                                </option>

                            </select>

                        </div>

                        <!-- TOMBOL -->
                        <div class="d-flex gap-2">

                            <button type="submit"
                                    name="update"
                                    class="btn btn-primary">
                                Simpan Perubahan
                            </button>

                            <a href="data_siswa.php"
                               class="btn btn-secondary">
                                Kembali
                            </a>

                        </div>

                    </form>

                </div>
            </div>

        </main>

    </div>
</div>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"
        integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y"
        crossorigin="anonymous"></script>

</body>
</html>