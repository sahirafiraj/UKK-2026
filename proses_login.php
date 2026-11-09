
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