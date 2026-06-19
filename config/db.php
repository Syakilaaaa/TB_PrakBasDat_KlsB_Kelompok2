<?php
$conn = new mysqli("localhost", "root", "", "nyammeow_db");
if ($conn->connect_error) {
    die("Koneksi gagal meow~: " . $conn->connect_error);
}
?>