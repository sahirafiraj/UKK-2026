
<?php
session_start();

include 'config/koneksi.php';

// Cek login dan hak akses admin
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

// Mengambil data dari form
$nip = mysqli_real_escape_string($koneksi, $_POST['nip']);
$nama = mysqli_real_escape_string($koneksi, $_POST['nama_guru']);
$email = mysqli_real_escape_string($koneksi, $_POST['email']);
$password = $_POST['password'];
$konfirmasi_password = $_POST['konfirmasi_password'];
$status = $_POST['status'];

// Memeriksa konfirmasi password
if ($password != $konfirmasi_password) {
    echo "<script>
            alert('Konfirmasi password tidak cocok!');
            window.location='tambah_guru.php';
          </script>";
    exit;
}

// Memeriksa data wajib
if (
    empty($nip) ||
    empty($nama) ||
    empty($email) ||
    empty($password) ||
    empty($konfirmasi_password)
) {
    echo "<script>
            alert('Semua data wajib diisi!');
            window.location='tambah_guru.php';
          </script>";
    exit;
}

// Memeriksa format email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>
            alert('Format email tidak valid!');
            window.location='tambah_guru.php';
          </script>";
    exit;
}

// Memeriksa status
if ($status != 'aktif' && $status != 'tidak aktif') {
    echo "<script>
            alert('Silakan pilih status guru!');
            window.location='tambah_guru.php';
          </script>";
    exit;
}

// Mengubah status menjadi angka
if ($status == 'aktif') {
    $status_aktif = 1;
} else {
    $status_aktif = 0;
}

// Memeriksa NIP agar tidak duplikat
$cek_nip = mysqli_query(
    $koneksi,
    "SELECT id FROM t_guru WHERE nip = '$nip'"
);

if (mysqli_num_rows($cek_nip) > 0) {
    echo "<script>
            alert('NIP sudah terdaftar!');
            window.location='tambah_guru.php';
          </script>";
    exit;
}

// Memeriksa email agar tidak duplikat
$cek_email = mysqli_query(
    $koneksi,
    "SELECT id FROM t_users WHERE email = '$email'
     UNION
     SELECT id FROM t_guru WHERE email = '$email'"
);

if (mysqli_num_rows($cek_email) > 0) {
    echo "<script>
            alert('Email sudah digunakan!');
            window.location='tambah_guru.php';
          </script>";
    exit;
}

// HASH PASSWORD
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Memulai transaksi database
mysqli_begin_transaction($koneksi);

try {

    // 1. Menyimpan akun ke tabel t_users
    $query_user = mysqli_query(
        $koneksi,
        "INSERT INTO t_users (name, email, password, role)
         VALUES ('$nama', '$email', '$password_hash', 'guru')"
    );

    if (!$query_user) {
        throw new Exception("Gagal menyimpan akun pengguna.");
    }

    // Mengambil ID akun yang baru dibuat
    $user_id = mysqli_insert_id($koneksi);

    // 2. Menyimpan data guru ke tabel t_guru
    $query_guru = mysqli_query(
        $koneksi,
        "INSERT INTO t_guru
        (nip, nama, email, status_aktif, user_id)
        VALUES
        ('$nip', '$nama', '$email', '$status_aktif', '$user_id')"
    );

    if (!$query_guru) {
        throw new Exception("Gagal menyimpan data guru.");
    }

    // Menyimpan kedua data jika berhasil
    mysqli_commit($koneksi);

    echo "<script>
            alert('Data guru dan akun berhasil ditambahkan!');
            window.location='data_guru.php';
          </script>";
    exit;

} catch (Exception $e) {

    // Membatalkan kedua proses jika terjadi kegagalan
    mysqli_rollback($koneksi);

    echo "<script>
            alert('Gagal menambahkan data guru. Periksa kembali database.');
            window.location='tambah_guru.php';
          </script>";
    exit;
}
?>
