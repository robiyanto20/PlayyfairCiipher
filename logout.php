<?php
// Memulai sesi pengguna.
session_start();
// Mengakhiri sesi pengguna (logout).
session_destroy();
// Mengarahkan pengguna kembali ke halaman login setelah logout.
header("Location: login.php");
exit();
?>