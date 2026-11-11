
<?php
session_start();
include 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "
        SELECT * FROM t_users
        WHERE email = '$email'
        LIMIT 1
    ");

    if (mysqli_num_rows($query) > 0) {

        $user = mysqli_fetch_assoc($query);

        if (password_verify($password, $user['password'])) {

            $role = strtolower(trim($user['role']));

            // Hanya 2 role: admin dan guru
            if ($role == 'admin' || $role == 'guru') {

                // CEK STATUS AKTIF GURU
                if ($role == 'guru') {

                    $id_user = $user['id'];

                    $cek_guru = mysqli_query($koneksi, "
                        SELECT status_aktif
                        FROM t_guru
                        WHERE user_id = '$id_user'
                        LIMIT 1
                    ");

                    if (mysqli_num_rows($cek_guru) > 0) {

                        $guru = mysqli_fetch_assoc($cek_guru);

                        if ($guru['status_aktif'] != 1) {

                            $_SESSION['pesan_error'] = "Akun Anda tidak aktif. Silakan hubungi admin.";

                            header("Location: login.php");
                            exit;
                        }

                    } else {

                        $_SESSION['pesan_error'] = "Data guru tidak ditemukan.";

                        header("Location: login.php");
                        exit;
                    }
                }

                // LOGIN BERHASIL
                session_regenerate_id(true);

                $_SESSION['login'] = true;
                $_SESSION['id'] = $user['id'];
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $role;

                header("Location: dashboard.php");
                exit;

            } else {

                $_SESSION['pesan_error'] = "Anda tidak memiliki hak akses.";

                header("Location: login.php");
                exit;
            }

        } else {

            $_SESSION['pesan_error'] = "Email atau password salah.";

            header("Location: login.php");
            exit;
        }

    } else {

        $_SESSION['pesan_error'] = "Email atau password salah.";

        header("Location: login.php");
        exit;
    }

} else {

    header("Location: login.php");
    exit;
}
