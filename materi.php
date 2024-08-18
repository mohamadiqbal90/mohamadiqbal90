<?php
include 'config.php';


// Fetch all subjects
$query = "SELECT DISTINCT subject FROM materials";
$result = mysqli_query($conn, $query);

// Store subjects in an array
$subjects = [];
while ($row = mysqli_fetch_assoc($result)) {
    $subjects[] = $row['subject'];
}
session_start();
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
    <title>Materi Pelajaran</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="custom-cursor site-wide">
        <div class="pointer"></div>
    </div>
    <div class="header-nav-container">
        <header>
            <h1><a href="index.php"><img src="./image/text-header.png"></a></h1>
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
        <div class="tugas-container">
            <aside class="sidebar">
                <h2 id="sidebar-title">Materi Pelajaran</h2>
                <ul id="subject-list">
                    <?php foreach ($subjects as $subject): ?>
                        <li><button class="subject-button" data-subject="<?php echo htmlspecialchars($subject); ?>">
                            <?php echo htmlspecialchars($subject); ?>
                        </button></li>
                    <?php endforeach; ?>
                </ul>
            </aside>
            <section id="content-section" class="tugas-content">
                <!-- Content will be dynamically loaded here -->
            </section>
        </div>
    </main>
    <footer>
        <p><a>Design by Kelompok P-Balap</a></p>
    </footer>
    <script src="js/customCursor.js"></script> 
    <script src="js/sidebarToggle.js"></script>

    <script>
        document.querySelectorAll('.subject-button').forEach(button => {
            button.addEventListener('click', function() {
                const subject = this.getAttribute('data-subject');
                document.getElementById('sidebar-title').textContent = subject;
                
                // Fetch bab for the selected subject
                fetchBab(subject);
            });
        });

        function fetchBab(subject) {
            fetch('fetch_bab.php?subject=' + encodeURIComponent(subject))
                .then(response => response.json())
                .then(data => {
                    const list = document.getElementById('subject-list');
                    list.innerHTML = '';
                    data.babs.forEach(bab => {
                        const li = document.createElement('li');
                        const button = document.createElement('button');
                        button.textContent = bab;
                        button.classList.add('bab-button');
                        button.addEventListener('click', () => fetchContent(subject, bab));
                        li.appendChild(button);
                        list.appendChild(li);
                    });

                    // Add "Back" button at the bottom
                    const backButton = document.createElement('button');
                    backButton.textContent = "Kembali";
                    backButton.classList.add('back-button', 'custom-back-button'); // Add custom class here
                    backButton.addEventListener('click', () => {
                        location.reload(); // Reload the page to go back to the Materi Pelajaran list
                    });
                    list.appendChild(backButton);
                });
        }



        function fetchContent(subject, bab) {
            fetch('fetch_content.php?subject=' + encodeURIComponent(subject) + '&bab=' + encodeURIComponent(bab))
                .then(response => response.json())
                .then(data => {
                    const section = document.getElementById('content-section');
                    section.innerHTML = `<h2>${bab}</h2><p>${data.content}</p>`;
                    
                    if (data.video_path) {
                        const video = document.createElement('video');
                        video.controls = true;
                        video.src = data.video_path;
                        section.appendChild(video);
                    }
                });
        }
    </script>

</body>
</html>
