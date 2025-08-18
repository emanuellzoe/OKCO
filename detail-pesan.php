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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $nik = $_POST['nik'];
    $id_tiket = $_POST['id_tiket'];

    // Update the invoice data
    $sql = "UPDATE invoice SET nama='$nama', email='$email', no_whatsapp='$no_hp' WHERE nik='$nik' AND id_tiket='$id_tiket'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>window.location.href='pesan.php';</script>";
        exit; // Pastikan untuk keluar dari script setelah redirect
    } else {
        echo "Error updating record: " . $conn->error;
    }
} else {
    // Fetch the current data to populate the form
    $sql = "SELECT * FROM invoice WHERE nik='$nik' AND id_tiket='$id_tiket'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "No record found";
        exit;
    }
}
$search = ""; // Inisialisasi variabel $search
if(isset($_GET['search'])) {
    $search = $_GET['search'];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Okco Ticket - Edit</title>
    <link rel="icon" type="image/png" href="assets/okco.png">
    <link rel="stylesheet" href="detail-pesan.css">
</head>
<body>
    <header>
        <nav>
            <a href="index.php"><img id="okco" src="assets/okco-png.png" alt="okco"></a>
            <form method="GET" action="index.php">
                <div class="search">
                    <input class="search-input" type="search" name="search" placeholder="Search" value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="search-icon">
                        <img src="assets/search.png" alt="Search">
                    </button>
                </div>
            </form>
            <a href="pesan.php" class="pesan">Pemesanan</a>
        </nav>
    </header>
    <main>
        <div class="form-container">
            <h1>Edit Data Diri</h1>
            <form action="detail-pesan.php" method="POST">
                <div class="form-group">
                    <label for="nama">Nama:</label>
                    <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($row['nama']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="no_hp">No HP:</label>
                    <input type="text" id="no_hp" name="no_hp" value="<?php echo htmlspecialchars($row['no_whatsapp']); ?>" required>
                </div>
                <input type="hidden" name="nik" value="<?php echo htmlspecialchars($row['nik']); ?>">
                <input type="hidden" name="id_tiket" value="<?php echo htmlspecialchars($row['id_tiket']); ?>">
                <button type="submit" class="form-button">Update</button>
            </form>
        </div>
    </main>
    <footer>
        <p><a href="#"><img id="okco1" src="assets/okco-png.png" alt="okco"></a></p>
        <p>&#169; 2024 Okco Ticket.</p> 
        <p><a class="about" href="#">About Us</a></p> 
    </footer>
</body>
</html>