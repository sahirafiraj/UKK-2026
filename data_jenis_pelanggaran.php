
<?php
session_start();

include "config/koneksi.php";

// Cek login dan hak akses admin
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

// Nama pengguna
$nama_login = $_SESSION['name'] ?? 'Admin';

// Mengambil data jenis pelanggaran
$query = mysqli_query($koneksi, "
    SELECT *
    FROM t_pelanggaran
    ORDER BY id DESC
");

if (!$query) {
    die("Gagal mengambil data: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kelola Jenis Pelanggaran</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet"
          href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css">

    <!-- Mengatur kolom dan tombol aksi -->
    <style>
        #tabelPelanggaran {
            width: 100%;
        }

        #tabelPelanggaran th:last-child,
        #tabelPelanggaran td:last-child {
            min-width: 125px;
            white-space: nowrap;
        }

        #tabelPelanggaran .aksi {
            display: flex;
            flex-wrap: nowrap;
            align-items: center;
            gap: 5px;
        }

        #tabelPelanggaran .aksi .btn {
            flex-shrink: 0;
        }
    </style>
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
                    <a href="dashboard.php" class="nav-link text-white">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_siswa.php" class="nav-link text-white">
                        Data Siswa
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_guru.php" class="nav-link text-white">
                        Data Guru
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_kelas.php" class="nav-link text-white">
                        Data Kelas
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_tahun_ajaran.php" class="nav-link text-white">
                        Tahun Ajaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_penempatan_siswa.php" class="nav-link text-white">
                        Penempatan Siswa
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_wali_kelas.php" class="nav-link text-white">
                        Data Wali Kelas
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_kategori_pelanggaran.php" class="nav-link text-white">
                        Kategori Pelanggaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_jenis_pelanggaran.php" class="nav-link active">
                        Jenis Pelanggaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="laporan.php" class="nav-link text-white">
                        Laporan
                    </a>
                </li>

                <li class="nav-item">
                    <a href="about_me.php" class="nav-link text-white">
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

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

            <!-- HEADER -->
            <div class="bg-white rounded shadow-sm p-4 mb-4">

                <h2 class="fw-bold">Kelola Jenis Pelanggaran</h2>

                <p class="text-secondary mb-0">
                    Selamat datang,
                    <b><?= htmlspecialchars($nama_login); ?></b>
                </p>

            </div>

            <!-- DAFTAR JENIS PELANGGARAN -->
            <div class="card shadow-sm border-0">

                <div class="card-header bg-white py-3">

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                        <h5 class="mb-0 fw-bold">
                            Daftar Jenis Pelanggaran
                        </h5>

                        <div class="d-flex gap-2">

                            <a href="dashboard.php"
                               class="btn btn-secondary btn-sm">
                                Kembali ke Dashboard
                            </a>

                            <a href="tambah_jenis_pelanggaran.php"
                               class="btn btn-primary btn-sm">
                                Tambah Jenis Pelanggaran
                            </a>

                        </div>

                    </div>

                </div>

                <!-- TABEL -->
                <div class="card-body">

                    <div class="table-responsive">

                        <table id="tabelPelanggaran"
                               class="table table-bordered table-striped table-hover align-middle w-100">

                            <thead class="table-primary">
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Nama Pelanggaran</th>
                                    <th>Kategori</th>
                                    <th>Poin</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php
                                $no = 1;

                                while ($data = mysqli_fetch_assoc($query)) {
                                ?>

                                <tr>

                                    <td><?= $no++; ?></td>

                                    <td>
                                        <?= htmlspecialchars($data['kode'] ?? ''); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($data['nama'] ?? ''); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $data['pelanggaran_kategori_id'] ?? ''
                                        ); ?>
                                    </td>

                                    <td>
                                        <span class="badge bg-info text-dark">
                                            <?= htmlspecialchars($data['poin'] ?? '0'); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($data['deskripsi'] ?? ''); ?>
                                    </td>

                                    <!-- STATUS -->
                                    <td>
                                        <?php
                                        if (($data['status_aktif'] ?? 0) == 1) {
                                        ?>
                                            <span class="badge bg-success">
                                                Aktif
                                            </span>
                                        <?php
                                        } else {
                                        ?>
                                            <span class="badge bg-secondary">
                                                Tidak Aktif
                                            </span>
                                        <?php
                                        }
                                        ?>
                                    </td>

                                    <!-- AKSI -->
                                    <td>
                                        <div class="aksi">

                                            <a href="edit_jenis_pelanggaran.php?id=<?= (int) $data['id']; ?>"
                                               class="btn btn-warning btn-sm">
                                                Edit
                                            </a>

                                            <a href="hapus_jenis_pelanggaran.php?id=<?= (int) $data['id']; ?>"
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Yakin ingin menghapus jenis pelanggaran ini?')">
                                                Hapus
                                            </a>

                                        </div>
                                    </td>

                                </tr>

                                <?php } ?>

                            </tbody>

                        </table>

                    </div>
                </div>
            </div>

        </main>

    </div>
</div>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JavaScript -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<!-- Mengaktifkan DataTables -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    new DataTable('#tabelPelanggaran', {

        // Jumlah data per halaman
        pageLength: 10,

        // Pilihan jumlah data
        lengthMenu: [5, 10, 25, 50, 100],

        // Urutan awal mengikuti query PHP
        order: [],

        // Posisi pencarian, pilihan jumlah data, dan pagination
        layout: {
            topStart: 'pageLength',
            topEnd: 'search',
            bottomStart: 'info',
            bottomEnd: 'paging'
        },

        // Bahasa Indonesia
        language: {
            search: 'Cari:',
            searchPlaceholder: 'Cari jenis pelanggaran...',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data',
            infoFiltered: '(difilter dari _MAX_ total data)',
            zeroRecords: 'Data tidak ditemukan',
            emptyTable: 'Belum ada data jenis pelanggaran',

            paginate: {
                first: 'Pertama',
                last: 'Terakhir',
                next: 'Berikutnya',
                previous: 'Sebelumnya'
            }
        },

        // Kolom Aksi tidak ikut pencarian dan pengurutan
        columnDefs: [
            {
                targets: 7,
                orderable: false,
                searchable: false
            }
        ]

    });

});
</script>

</body>
</html>