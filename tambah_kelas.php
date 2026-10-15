
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

$halaman = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kelas</title>

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
                       class="nav-link text-white">
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
                       class="nav-link active">
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
                    <a href="logout.php" class="nav-link text-white">
                        Logout
                    </a>
                </li>

            </ul>
        </div>

        <!-- KONTEN TAMBAH KELAS -->
        <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

            <!-- HEADER HALAMAN -->
            <div class="bg-white rounded shadow-sm p-4 mb-4">

                <h2 class="fw-bold">Tambah Kelas</h2>

                <p class="text-secondary mb-0">
                    Tambahkan data kelas baru ke dalam sistem.
                </p>

            </div>

            <!-- FORM TAMBAH KELAS -->
            <div class="card shadow-sm border-0">

                <div class="card-header bg-white p-3">
                    <h5 class="mb-0 fw-bold">Form Tambah Kelas</h5>
                </div>

                <div class="card-body p-4">

                    <form action="proses_tambah_kelas.php" method="POST">

                        <div class="row g-3">

                            <!-- NAMA KELAS -->
                            <div class="col-12">
                                <label for="nama" class="form-label">
                                    Nama Kelas
                                </label>

                                <input type="text"
                                       id="nama"
                                       name="nama"
                                       class="form-control"
                                       placeholder="Nama Kelas"
                                       maxlength="100"
                                       required>
                            </div>

                            <!-- TINGKAT -->
                            <div class="col-md-6">
                                <label for="tingkat" class="form-label">
                                    Tingkat
                                </label>

                                <select id="tingkat"
                                        name="tingkat"
                                        class="form-select"
                                        required>

                                    <option value="">Pilih Tingkat</option>
                                    <option value="X">X</option>
                                    <option value="XI">XI</option>
                                    <option value="XII">XII</option>

                                </select>
                            </div>

                            <!-- JURUSAN -->
                            <div class="col-md-6">
                                <label for="jurusan" class="form-label">
                                    Jurusan
                                </label>

                                <input type="text"
                                       id="jurusan"
                                       name="jurusan"
                                       class="form-control"
                                       placeholder="Jurusan"
                                       maxlength="100"
                                       required>
                            </div>

                            <!-- STATUS AKTIF -->
                            <div class="col-md-6">
                                <label for="status_aktif" class="form-label">
                                    Status Aktif
                                </label>

                                <select id="status_aktif"
                                        name="status_aktif"
                                        class="form-select"
                                        required>

                                    <option value="1" selected>Aktif</option>
                                    <option value="0">Tidak Aktif</option>

                                </select>
                            </div>

                        </div>

                        <hr class="my-4">

                        <!-- TOMBOL -->
                        <div class="d-flex justify-content-between align-items-center">

                            <a href="data_kelas.php"
                               class="btn btn-secondary">
                                Kembali
                            </a>

                            <button type="submit"
                                    name="simpan"
                                    class="btn btn-primary">
                                Simpan Kelas
                            </button>

                        </div>

                    </form>

                </div>
            </div>

        </main>

    </div>
</div>

<!-- JAVASCRIPT BOOTSTRAP -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>

</body>
</html>
