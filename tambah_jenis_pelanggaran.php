
<?php
session_start();
include "config/koneksi.php";

// Cek login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// Cek hak akses
if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama_login = $_SESSION['name'] ?? 'Admin';
$pesan = "";

// Proses tambah data
if (isset($_POST['simpan'])) {
    $kode = mysqli_real_escape_string($koneksi, $_POST['kode']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $kategori = (int) $_POST['kategori'];
    $poin = (int) $_POST['poin'];
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $status = (int) $_POST['status'];

    $query = mysqli_query($koneksi, "
        INSERT INTO t_pelanggaran
        (kode, nama, pelanggaran_kategori_id, poin, deskripsi, aktif)
        VALUES
        ('$kode', '$nama', '$kategori', '$poin', '$deskripsi', '$status')
    ");

    if ($query) {
        header("Location: data_jenis_pelanggaran.php");
        exit;
    } else {
        $pesan = "Gagal menyimpan data: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Jenis Pelanggaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
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
                    <a href="data_guru.php" class="nav-link text-white">Data Guru</a>
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
                    <a href="data_jenis_pelanggaran.php" class="nav-link active">Jenis Pelanggaran</a>
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

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

            <!-- HEADER -->
            <div class="bg-white rounded shadow-sm p-4 mb-4">
                <h2 class="fw-bold">Tambah Jenis Pelanggaran</h2>

                <p class="text-secondary mb-0">
                    Selamat datang,
                    <b><?= htmlspecialchars($nama_login); ?></b>
                </p>
            </div>

            <!-- FORM TAMBAH -->
            <div class="card shadow-sm border-0">

                <div class="card-header bg-white">
                    <h5 class="fw-bold mb-0">Form Jenis Pelanggaran</h5>
                </div>

                <div class="card-body">

                    <?php if ($pesan != "") { ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($pesan); ?>
                        </div>
                    <?php } ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label">Kode Pelanggaran</label>
                            <input type="text" name="kode"
                                   class="form-control"
                                   placeholder="Contoh: PLG-051"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Pelanggaran</label>
                            <input type="text" name="nama"
                                   class="form-control"
                                   placeholder="Contoh: Tidak membawa buku"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kategori Pelanggaran</label>

                            <select name="kategori" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>

                                <?php
                                $kategori_query = mysqli_query(
                                    $koneksi,
                                    "SELECT * FROM t_pelanggaran_kategori ORDER BY id DESC"
                                );

                                if ($kategori_query) {
                                    while ($kat = mysqli_fetch_assoc($kategori_query)) {
                                        $nama_kategori = $kat['nama']
                                            ?? $kat['kategori']
                                            ?? $kat['nama_kategori']
                                            ?? 'Kategori ' . $kat['id'];
                                ?>
                                    <option value="<?= $kat['id']; ?>">
                                        <?= htmlspecialchars($nama_kategori); ?>
                                    </option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Poin Pelanggaran</label>
                            <input type="number" name="poin"
                                   class="form-control"
                                   min="0" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Masukkan deskripsi pelanggaran"
                                      required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>

                            <select name="status" class="form-select" required>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>

                        <button type="submit" name="simpan"
                                class="btn btn-primary">
                            Simpan
                        </button>

                        <a href="data_jenis_pelanggaran.php"
                           class="btn btn-secondary">
                            Kembali
                        </a>

                    </form>

                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>