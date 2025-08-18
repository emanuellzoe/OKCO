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

$search = isset($_GET['search']) ? $_GET['search'] : '';
$id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : '';

if ($id_user) {
    // Query to fetch invoices for the logged-in user with search functionality
    $sql = "SELECT t.judul_tiket, i.nama, i.email, i.nik, i.no_whatsapp, i.id_tiket
            FROM invoice i
            JOIN tiket t ON i.id_tiket = t.id_tiket
            WHERE i.id_user = '$id_user'
            AND (t.judul_tiket LIKE '%$search%' 
            OR i.nama LIKE '%$search%' 
            OR i.email LIKE '%$search%')";

    $result = $conn->query($sql);
} else {
    $result = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Okco Ticket</title>
    <link rel="icon" type="image/png" href="assets/okco.png">
    <link rel="stylesheet" href="pesan.css">
    <script>
        function confirmDelete(nik, id_tiket) {
            if (confirm("Are you sure you want to delete this item?")) {
                window.location.href = "delete_invoice.php?nik=" + nik + "&id_tiket=" + id_tiket;
            }
        }
    </script>
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
            <div class="nav-links">
                <a href="pesan.php" class="pesan">Pemesanan</a>
                <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                    <span class="username"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="logout.php" class="navi">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="navi">Login</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <main>
        <table class="tabel">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>No. WhatsApp</th>
                    <th>Email</th>
                    <th>Judul Tiket</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row["nama"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["no_whatsapp"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["judul_tiket"]) . "</td>";
                        echo "<td>
                                <a href='detail-pesan.php?nik=" . htmlspecialchars($row["nik"]) . "&id_tiket=" . htmlspecialchars($row["id_tiket"]) . "' class='edit-button'>Edit</a>
                                <a href='#' onclick='confirmDelete(\"" . htmlspecialchars($row["nik"]) . "\", \"" . htmlspecialchars($row["id_tiket"]) . "\")' class='delete-button'>Delete</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No results found</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </main>
    <footer>
        <p><a href="#"><img id="okco1" src="assets/okco-png.png" alt="okco"></a></p>
        <p>&#169; 2024 Okco Ticket.</p> 
        <p><a class="about" href="#">About Us</a></p> 
    </footer>
</body>
</html>