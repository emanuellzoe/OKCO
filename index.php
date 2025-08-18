<?php
session_start();
include 'koneksi.php';
// $servername = "127.0.0.1";
// $username = "root";
// $password = "";
// $dbname = "progcoba2";

// $conn = new mysqli($servername, $username, $password, $dbname);

// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT t.id_tiket, t.judul_tiket, t.gambar_tiket, t.tanggal_tiket, t.lokasi_tiket, t.artis_tiket, h.Tribun
        FROM tiket t 
        JOIN harga h ON t.id_harga = h.id_harga 
        WHERE t.judul_tiket LIKE '%$search%' 
        OR t.lokasi_tiket LIKE '%$search%' 
        OR t.artis_tiket LIKE '%$search%' 
        OR t.tanggal_tiket LIKE '%$search%'";

$result = $conn->query($sql);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Okco Ticket</title>
    <link rel="icon" type="image/png" href="assets/okco.png">
    <link rel="stylesheet" href="assets/style.css">
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
        <article class="boxes">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='box1'>";
                    echo "<a href='halaman2 copy.php?id=" . $row["id_tiket"] . "'>";
                    echo "<aside><img class='bannerkonser' src='" . $row["gambar_tiket"] . "' alt=''></aside>";
                    echo "<h2>" . htmlspecialchars($row["judul_tiket"]) . "</h2>";
                    echo "<p>Tanggal: " . htmlspecialchars($row["tanggal_tiket"]) . "</p>";
                    echo "<p>Lokasi: " . htmlspecialchars($row["lokasi_tiket"]) . "</p>";
                    echo "<p>Artis: " . htmlspecialchars($row["artis_tiket"]) . "</p>";
                    echo "Harga: <p>Rp. " . htmlspecialchars($row["Tribun"]) . "</p>";
                    echo "<button class='beli-button'>Beli</button>";
                    echo "</div></a></div>";
                }
            } else {
                echo "<p>No results found</p>";
            }
            $conn->close();
            ?>
        </article>
    </main>
    <footer>
        <p><a href="#"><img id="okco1" src="assets/okco-png.png" alt="okco"></a></p>
        <p>&#169; 2024 Okco Ticket.</p> 
        <p><a class="about" href="#">About Us</a></p> 
    </footer>
</body>
</html>