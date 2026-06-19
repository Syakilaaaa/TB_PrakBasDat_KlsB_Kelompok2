<?php
session_start();
if (!isset($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {
    header("Location: login.php");
    exit;
}

include '../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$confirm = isset($_GET['confirm']) ? $_GET['confirm'] : '';

if ($id > 0) {
    if ($confirm === 'yes') {
        // Hapus detail pesanan terlebih dahulu
        $conn->query("DELETE FROM detail_pesanan WHERE pesanan_id = $id");
        
        // Hapus pesanan
        if ($conn->query("DELETE FROM pesanan WHERE id = $id")) {
            $_SESSION['message'] = "Pesanan #$id berhasil dihapus!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Gagal menghapus pesanan: " . $conn->error;
            $_SESSION['message_type'] = "error";
        }
    } else {
        // Redirect ke halaman konfirmasi
        header("Location: index.php?confirm_delete=$id");
        exit;
    }
}

header("Location: index.php");
exit;
?>