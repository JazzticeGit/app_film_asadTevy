<?php
include "koneksi.php";

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_film  = $_POST['nama_film'];
    $genre      = $_POST['genre'];
    $sutradara  = $_POST['sutradara'];
    $durasi     = $_POST['durasi'];
    $sinopsis   = $_POST['sinopsis'];
    $filter     = $_POST['filter'];

    // upload poster
    $poster = "";
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] == 0) {
        $targetDir = "uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . "_" . basename($_FILES["poster"]["name"]);
        $targetFile = $targetDir . $fileName;
        if (move_uploaded_file($_FILES["poster"]["tmp_name"], $targetFile)) {
            $poster = $fileName; // simpan nama file saja
        }
    }

    $query = "INSERT INTO film (nama_film, genre, sutradara, durasi, poster, sinopsis, filter) 
              VALUES ('$nama_film', '$genre', '$sutradara', '$durasi', '$poster', '$sinopsis', '$filter')";

    if (mysqli_query($koneksi, $query)) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
        exit();
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}


// ambil data
$result = mysqli_query($koneksi, "SELECT * FROM film ORDER BY id_film DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>formulir</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
    <div class="nav-bg">
            <h2>FilmBre</h2>
        <div class="navlink">
            <ul>
                <li><a href="film.php">Daftar Film</a></li>
                <li><a href="index.php">Home</a></li>
            </ul>
        </div>
    </div>
    </nav>

    <!-- forolir -->
    <br><br><br>
    <form action="" method="POST" enctype="multipart/form-data">
        <h2>Form Tambah Film</h2>

        <label>Nama Film:</label><br>
        <input type="text" name="nama_film" required><br><br>

        <label>Genre:</label><br>
        <input type="text" name="genre" required><br><br>

        <label>Sutradara:</label><br>
        <input type="text" name="sutradara"><br><br>

        <label>Durasi (satuan menit):</label><br>
        <input type="text" name="durasi" placeholder="durasi dalam menit"><br><br>

        <label>Poster (URL atau upload file):</label><br>
        <input type="file" name="poster" placeholder="poster.jpg"><br><br>
        <!-- Jika mau upload file, bisa ganti dengan: <input type="file" name="poster"> -->

        <label>Sinopsis:</label><br>
        <textarea name="sinopsis" rows="5" cols="40"></textarea><br><br>

        <label>Status Film:</label><br>
        <select name="filter" required>
            <option value="upcoming">Upcoming</option>
            <option value="now_playing">Now Playing</option>
        </select><br><br>

        <button type="submit">Simpan</button>
    </form><br><br><br>

    <h2>Daftar Film</h2>

<div class="card-container">
<?php while($row = mysqli_fetch_assoc($result)): ?>
    <div class="card">
    <?php if(!empty($row['poster'])): ?>
        <img src="uploads/<?php echo $row['poster']; ?>" alt="Poster" width="120">
    <?php else: ?>
        <p><i>Tidak ada poster</i></p>
    <?php endif; ?>



        <div class="card-content">
            <h3><?php echo $row['nama_film']; ?></h3>
            <p><b>Genre:</b> <?php echo $row['genre']; ?></p>
            <p><b>Sutradara:</b> <?php echo $row['sutradara']; ?></p>
            <p><b>Durasi:</b> <?php echo $row['durasi']; ?> menit</p>
            <p><b>Sinopsis:</b> <?php echo $row['sinopsis']; ?></p>
            <span class="status <?php echo $row['filter']; ?>">
                <?php echo ucfirst(str_replace("_"," ",$row['filter'])); ?>
            </span>
        </div>
    </div>
<?php endwhile; ?>
</div>


</body>
</html>