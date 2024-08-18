<?php
// Start the session
session_start();

// Include the config file
include 'config.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

// Retrieve user details from session
$name = $_SESSION['name'] ?? '';
$email = $_SESSION['email'] ?? '';
$class = $_SESSION['class'] ?? '';
$role = $_SESSION['role'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengumuman - Website E-Learning P-Balap</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="custom-cursor site-wide">
        <div class="pointer"></div>
    </div>
    <div class="header-nav-container">
        <header>
            <h1><a href="index.php"><img src="./image/text-header.png" alt="Header Image"></a></h1>
        </header>
        <nav>
            <ul>
                <li><a href="profil.php">Profil Anda</a></li>
                <li><a href="materi.php">Materi Pelajaran</a></li>
                <li><a href="tugas.php">Tugas</a></li>
                <li><a href="pengumuman.php">Pengumuman</a></li>
                <li><a href="forum.php">Forum</a></li>
                <li><a href="credit.php">Credit</a></li>
            </ul>
        </nav>
    </div>
    
    <?php include 'sidebar1.php'; ?>
    
    <main>
        <section class="intro">
            <h3>Pengumuman</h3>
            <p><b>Berita Tentang ini bla bla bla<br><br></b>
            bla bla bla bla bla bla bla bla</p>
            <p><b>Berita Tentang itu bla bla bla<br><br></b>
                bla bla bla bla bla bla bla bla</p>
            <p><b>Berita Tentang apalah bla bla bla<br><br></b>
                bla bla bla bla bla bla bla bla</p>
        </section>
    </main>
    
    <footer>
        <p><a>Design by Kelompok P-Balap</a></p>
    </footer>
    
    <script src="js/customCursor.js"></script>
    <script src="js/sidebarToggle.js"></script>
</body>
</html>
