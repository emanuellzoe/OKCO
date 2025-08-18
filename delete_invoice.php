<?php
session_start();
include 'koneksi.php';
// $servername = "127.0.0.1";
// $username = "root";
// $password = "";
// $dbname = "progcoba2";

// // Create connection
// $conn = new mysqli($servername, $username, $password, $dbname);

// // Check connection
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

$nik = isset($_GET['nik']) ? $_GET['nik'] : '';
$id_tiket = isset($_GET['id_tiket']) ? $_GET['id_tiket'] : '';

if ($nik && $id_tiket) {
    $sql = "DELETE FROM invoice WHERE nik='$nik' AND id_tiket='$id_tiket'";

    if ($conn->query($sql) === TRUE) {
        header("Location: pesan.php");
        exit;
    } else {
        echo "Error deleting record: " . $conn->error;
    }
} else {
    echo "Invalid parameters";
}

$conn->close();
?> 