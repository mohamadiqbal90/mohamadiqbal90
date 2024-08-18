<?php
session_start();
include 'config.php';

// Initialize error messages array in session
if (!isset($_SESSION['error_messages'])) {
    $_SESSION['error_messages'] = [];
}

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    $_SESSION['error_messages'][] = "You need to log in first.";
    header("location: login.php");
    exit;
}

// Fetch user information from the database
$user_id = $_SESSION['student_id'];
$name = $_SESSION['name'];
$email = $_SESSION['email'];
$class = $_SESSION['class'];
$role = $_SESSION['role'];

$sql = "SELECT student_id, name, nickname, email, class, birth_place, birth_date, gender, religion, address, phone, role FROM users WHERE student_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($student_id, $name, $nickname, $email, $class, $birth_place, $birth_date, $gender, $religion, $address, $phone, $role);
    $stmt->fetch();
    $stmt->close();
} else {
    $_SESSION['error_messages'][] = "Error fetching user data.";
    exit;
}
// Photo upload handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_photo'])) {
    $targetDir = "uploads/profile_photos/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    $targetFile = $targetDir . str_replace(' ', '_', basename($_FILES["profile_photo"]["name"]));
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if the file is an actual image
    $check = getimagesize($_FILES["profile_photo"]["tmp_name"]);
    if ($check === false) {
        $_SESSION['error_messages'][] = "File is not an image.";
        $uploadOk = 0;
    }

    // Allow only certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        $_SESSION['error_messages'][] = "Sorry, only JPG, JPEG, & PNG files are allowed.";
        $uploadOk = 0;
    }

    // Check if everything is ok before uploading
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $targetFile)) {
            $student_id = $_SESSION['student_id'];

            // Update the photo_path in the database
            $sql = "UPDATE users SET photo_path = ? WHERE student_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $targetFile, $student_id);
            if ($stmt->execute()) {
                $_SESSION['error_messages'][] = "Profile photo updated successfully.";
            } else {
                $_SESSION['error_messages'][] = "Error updating profile photo: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $_SESSION['error_messages'][] = "Sorry, there was an error uploading your file.";
        }
    }
}

// Fetch user information including photo_path
$sql = "SELECT student_id, name, nickname, email, class, birth_place, birth_date, gender, religion, address, phone, photo_path FROM users WHERE student_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($student_id, $name, $nickname, $email, $class, $birth_place, $birth_date, $gender, $religion, $address, $phone, $photo_path);
    $stmt->fetch();
    $stmt->close();
} else {
    $_SESSION['error_messages'][] = "Error fetching user data.";
    exit;
}

// Delete sticker handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_sticker_id'])) {
    $sticker_id = $_POST['delete_sticker_id'];

    $sql = "DELETE FROM profile_stickers WHERE id = ? AND student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $sticker_id, $student_id);
    $stmt->execute();
    $stmt->close();

    // Optionally delete sticker file from server
    $sql = "SELECT sticker_path FROM profile_stickers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $sticker_id);
    $stmt->execute();
    $stmt->bind_result($sticker_path);
    $stmt->fetch();
    $stmt->close();

    if (file_exists($sticker_path)) {
        unlink($sticker_path);
    }
}

// Sticker upload handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['sticker'])) {
    $targetDir = "uploads/stickers/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    $targetFile = $targetDir . str_replace(' ', '_', basename($_FILES["sticker"]["name"]));
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $z_index = isset($_POST['z_index']) ? intval($_POST['z_index']) : 1;

    $check = getimagesize($_FILES["sticker"]["tmp_name"]);
    if ($check === false) {
        $_SESSION['error_messages'][] = "File is not an image.";
        $uploadOk = 0;
    }

    if ($imageFileType != "png") {
        $_SESSION['error_messages'][] = "Sorry, only PNG files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        $_SESSION['error_messages'][] = "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["sticker"]["tmp_name"], $targetFile)) {
            $student_id = $_SESSION['student_id'];
            $pos_x = 0;
            $pos_y = 0;
            $width = 100;
            $height = 100;

            $sql = "INSERT INTO profile_stickers (student_id, sticker_path, pos_x, pos_y, width, height, z_index)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issiiii", $student_id, $targetFile, $pos_x, $pos_y, $width, $height, $z_index);
            $stmt->execute();
            $stmt->close();
        } else {
            $_SESSION['error_messages'][] = "Sorry, there was an error uploading your file.";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['sticker_id']) && isset($_POST['sticker_width']) && isset($_POST['sticker_height']) && isset($_POST['sticker_z_index'])) {
        $sticker_id = $_POST['sticker_id'];
        $sticker_width = $_POST['sticker_width'];
        $sticker_height = $_POST['sticker_height'];
        $sticker_z_index = $_POST['sticker_z_index'];

        $query = "UPDATE profile_stickers SET width = ?, height = ?, z_index = ? WHERE id = ?";

        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param('iiii', $sticker_width, $sticker_height, $sticker_z_index, $sticker_id);
            if ($stmt->execute()) {
                $_SESSION['error_messages'][] = "Sticker updated successfully.";
                
            } else {
                $_SESSION['error_messages'][] = "Error updating sticker: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $_SESSION['error_messages'][] = "Error preparing statement: " . $conn->error;
        }
    }
}

// Fetch and display stickers
$student_id = $_SESSION['student_id'];
$sql = "SELECT id, sticker_path, pos_x, pos_y, width, height, z_index FROM profile_stickers WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->bind_result($sticker_id, $sticker_path, $pos_x, $pos_y, $width, $height, $z_index);

$stickers = [];
while ($stmt->fetch()) {
    $stickers[] = [
        'id' => $sticker_id,
        'path' => $sticker_path,
        'x' => $pos_x,
        'y' => $pos_y,
        'width' => $width,
        'height' => $height,
        'z_index' => $z_index
    ];
}

$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Anda - Website E-Learning P-Balap</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        @font-face {
            font-family: ballpoint;
            src: url(font/Ballpoint.otf);
        }
        .profile-container {
            font-family: ballpoint;
            font-size: 20px;
            z-index: 3;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            gap: 30px;
            background-color: #ffffff94;
            padding: 20px;
            margin: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.027);
        }

        .profile-photo {
            flex: 1;
            text-align: center;
        }

        .profile-photo img {
            width: 300px;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .profile-photo h3 {
            margin-top: 10px;
            font-family: 'bulpenheader';
        }

        .profile-info {
            flex: 2;
        }

        .profile-info h3 {
            margin-bottom: 20px;
            font-family: 'bulpenheader';
        }

        .profile-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .profile-info table th,
        .profile-info table td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        .profile-info table th {
            width: 40%;
            font-weight: bold;
        }

        .profile-info table td {
            width: 60%;
        }

        .profile-info .edit-button a:hover {
            cursor: none;
            background-color: #e24304;
        }
        .sticker {
            position: absolute;
            cursor: move;
        }
        .edit-mode-toggle {
            display: block;
            margin-top: 20px;
            padding: 10px;
            background-color: #f7690b;
            color: white;
            border: none;
            cursor: none;
            border-radius: 5px;
            max-width:50%; 
            margin: 0 auto;
            text-align: center;
        }

        #edit-mode {
            display: none;
        }
    </style>
</head>
<body>
    <div class="custom-cursor site-wide">
        <div class="pointer"></div>
    </div>
    <div class="header-nav-container">
        <header>
            <h1><a href="index.php"><img src="./image/text-header.png" style="cursor:none;"></a></h1>
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
            <h3>Profil Anda</h3>
            <section class="profile" style="padding: 10px;">
                <div class="profile-container">
                <form method="POST" enctype="multipart/form-data" action="profil.php">
                    <div class="profile-photo" style="text-align: center;">
                        <h2 style="margin-bottom: 10px;">Foto Pribadi</h2>
                        <img id="profile-photo" src="<?php echo htmlspecialchars($photo_path); ?>" alt="Profil Foto" 
                            style="max-width: 100%; height: auto; border-radius: 15px; border: 2px solid #ccc; display: inline-block; margin-bottom: 30px;">
                        <br>
                        <input type="file" id="file-input" name="profile_photo" accept="image/png, image/jpeg" 
                            style="display: inline-block; border-radius: 5px; padding: 5px; margin-top: 10px;">
                        <br>
                        <button type="submit" name="upload_photo" 
                            style="max-width:20%; padding: 10px 20px; background-color: #f7690b; color: white; border: none; border-radius: 5px; margin-top: 20px;">Upload Photo</button>
                    </div>
                </form>


                    <div class="profile-info">
                        <h2>Biodata <?php echo htmlspecialchars($name); ?></h2>
                        <table>
                            <tr>
                                <th>Nama</th>
                                <td><?php echo htmlspecialchars($name); ?></td>
                            </tr>
                            <tr>
                                <th>Nickname</th>
                                <td><?php echo htmlspecialchars($nickname); ?></td>
                            </tr>
                            <tr>
                                <th>Tempat Lahir</th>
                                <td><?php echo htmlspecialchars($birth_place); ?></td>
                            </tr>
                            <tr>
                                <th>Tanggal Lahir</th>
                                <td><?php echo htmlspecialchars($birth_date); ?></td>
                            </tr>
                            <tr>
                                <th>Jenis Kelamin</th>
                                <td><?php echo htmlspecialchars($gender); ?></td>
                            </tr>
                            <tr>
                                <th>Agama</th>
                                <td><?php echo htmlspecialchars($religion); ?></td>
                            </tr>
                            <tr>
                                <th>Alamat</th>
                                <td><?php echo htmlspecialchars($address); ?></td>
                            </tr>
                            <tr>
                                <th>No Telepon</th>
                                <td><?php echo htmlspecialchars($phone); ?></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td><?php echo htmlspecialchars($email); ?></td>
                            </tr>
                            <tr>
                                <th>Kelas</th>
                                <td><?php echo htmlspecialchars($class); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </section>
        </section>
    </main>

    <main>
        <section class="intro">
            <h3 style="text-align: center;">Mading</h3>
            <section class="profile" style="padding: 10px;">
                <div class="profile-container">
                </div>
            </section>
        </section>
        <section id="profile-stickers">
            <?php foreach ($stickers as $sticker): ?>
                <img src="<?php echo htmlspecialchars($sticker['path']); ?>" 
                     alt="Sticker" 
                     class="sticker" 
                     data-sticker-id="<?php echo $sticker['id']; ?>"
                     style="left: <?php echo $sticker['x']; ?>px; top: <?php echo $sticker['y']; ?>px; width: <?php echo $sticker['width']; ?>px; height: <?php echo $sticker['height']; ?>px; z-index: <?php echo $sticker['z_index']; ?>;">
            <?php endforeach; ?>
            <div class="button-container">
                <!-- Button to enable/disable edit mode -->
                <button id="toggle-edit-mode">Nyalakan Mode Edit</button>

                <!-- Edit Mode Section -->
                <section id="edit-mode">
                    <h3>Mode Edit</h3>
                    <a style='font: size 6px;'>Dalam mode edit kalian bisa Upload Sticker dengan memilih file gambar png di "Chosen File" ketika sudah klik Upload Sticker. Awal Sticker akan muncul di ujung kiri atas web kalian bisa menggeserkanya jika Mode Edit di nyalakan. Ketika Sudah Menggeserkan sticker di tempat yang cocok klik "Perbarui Posisi Sticker".</a><br>
                    <form action="profil.php" method="post" enctype="multipart/form-data"><br>
                        <input type="hidden" name="z_index" value="1">
                        <input type="file" id="file-input" name="sticker" accept="image/png, image/jpeg" 
                        style="display: inline-block; border-radius: 5px; padding: 5px; margin-left: 150px;">
                        <button type="submit" name="upload" class="edit-mode-toggle" style="max-width:50%; margin: 0 auto;">Upload Sticker</button>
                    </form><br><br>
                    <a>Ketika sudah selesai menggeser sticker klik perbarui stiker di bawah</a> 
                    <!-- Save Stickers Button -->
                    <button id="save-stickers" class="edit-mode-toggle" >Perbarui Posisi Sticker</button>
                    <section id="stickers-list">
                        <h3>Daftar Sticker</h3>
                        <p>Ketika merefresh halaman akan ada sticker duplikat harap di hapus.<br><br>Silahkan edit ukuran dan z-index dibawah berikut (z-index bisa sampai angka minus tapi ketika sticker ada di belakang container kamu tidak bisa mendrag sticker tersebut walaupun dalam mode edit)</p>
                        <ul style="list-style-type: none; padding: 0;">
                            <?php foreach ($stickers as $sticker): ?>
                            <li style="margin-bottom: 10px; display: flex; align-items: center;">
                                <img src="<?php echo htmlspecialchars($sticker['path']); ?>" 
                                    alt="Sticker" 
                                    style="width: 50px; height: auto; margin-right: 10px;">
                                <form action="profil.php" method="post" style="display: inline; margin-right: 10px;">
                                    <input type="hidden" name="delete_sticker_id" value="<?php echo $sticker['id']; ?>">
                                    <button type="submit" class="delete-sticker-button">Hapus</button>
                                </form>
                                <form action="profil.php" method="post" style="display: flex; align-items: center; gap: 10px;">
                                    <input type="hidden" name="sticker_id" value="<?php echo $sticker['id']; ?>">
                                    <label for="sticker_width_<?php echo $sticker['id']; ?>">Width:</label>
                                    <input type="number" id="sticker_width_<?php echo $sticker['id']; ?>" name="sticker_width" min="10" value="<?php echo $sticker['width']; ?>" style="width: 60px;">
                                    <label for="sticker_height_<?php echo $sticker['id']; ?>">Height:</label>
                                    <input type="number" id="sticker_height_<?php echo $sticker['id']; ?>" name="sticker_height" min="10" value="<?php echo $sticker['height']; ?>" style="width: 60px;">
                                    <label for="sticker_z_index_<?php echo $sticker['id']; ?>">Z-Index:</label>
                                    <input type="number" id="sticker_z_index_<?php echo $sticker['id']; ?>" name="sticker_z_index" value="<?php echo $sticker['z_index']; ?>" style="width: 50px;">
                                    <button type="submit" class="delete-sticker-button">Perbarui</button>
                                </form>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </section>

                </section>
            </div>

    </main>

    <div id="error-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); color: white; padding: 20px; z-index: 50;">
        <div style="background: #333; padding: 20px; border-radius: 8px; max-width: 500px; margin: 100px auto; position: relative;">
            <button id="close-modal" style="position: absolute; top: 10px; right: 10px; background: red; border: none; color: white; padding: 5px 10px; cursor: pointer;">Close</button>
            <h2>Error</h2>
            <ul id="error-messages">
                <!-- Error messages will be injected here -->
            </ul>
        </div>
    </div>

    <footer>
        <p><a>Design by Kelompok P-Balap</a></p>
    </footer>
    <script>
let isEditMode = false;
let stickers = [];

document.addEventListener('DOMContentLoaded', () => {
    // Populate the stickers array with all sticker elements
    stickers = Array.from(document.querySelectorAll('.sticker'));

    document.getElementById('toggle-edit-mode').addEventListener('click', function() {
        isEditMode = !isEditMode;
        document.getElementById('edit-mode').style.display = isEditMode ? 'block' : 'none';
        this.textContent = isEditMode ? 'Matikan Mode Edit' : 'Nyalakan Mode';

        stickers.forEach(sticker => {
            if (isEditMode) {
                // Add event listeners for dragging
                sticker.addEventListener('mousedown', onStickerMouseDown);
            } else {
                // Remove event listeners to prevent dragging
                sticker.removeEventListener('mousedown', onStickerMouseDown);
            }
        });
    });

    function onStickerMouseDown(e) {
        if (!isEditMode) return;

        let sticker = e.target;
        let shiftX = e.clientX - sticker.getBoundingClientRect().left;
        let shiftY = e.clientY - sticker.getBoundingClientRect().top;

        function moveAt(pageX, pageY) {
            sticker.style.left = pageX - shiftX + 'px';
            sticker.style.top = pageY - shiftY + 'px';
        }

        function onMouseMove(e) {
            moveAt(e.pageX, e.pageY);
        }

        document.addEventListener('mousemove', onMouseMove);

        sticker.addEventListener('mouseup', function() {
            document.removeEventListener('mousemove', onMouseMove);
        });
    }

    stickers.forEach(sticker => {
        sticker.ondragstart = function() {
            return false;
        };
    });
});

        document.getElementById('save-stickers').addEventListener('click', function() {
            let stickerData = [];

            stickers.forEach(sticker => {
                stickerData.push({
                    id: sticker.getAttribute('data-sticker-id'),
                    x: parseInt(sticker.style.left),
                    y: parseInt(sticker.style.top),
                    width: parseInt(sticker.style.width),
                    height: parseInt(sticker.style.height),
                    z_index: parseInt(sticker.style.zIndex)
                });
            });

            fetch('save_stickers.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(stickerData)
            })
            .then(response => response.json())
            .then(data => {
                console.log('Stickers saved successfully', data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
        document.getElementById('file-input').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profile-photo').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
        document.addEventListener('DOMContentLoaded', () => {
            // Check if there are error messages in the session
            const errorMessages = <?php echo json_encode(isset($_SESSION['error_messages']) ? $_SESSION['error_messages'] : []); ?>;

            if (errorMessages.length > 0) {
                const errorMessagesList = document.getElementById('error-messages');
                errorMessages.forEach(message => {
                    const li = document.createElement('li');
                    li.textContent = message;
                    errorMessagesList.appendChild(li);
                });

                document.getElementById('error-modal').style.display = 'block';
            }

            document.getElementById('close-modal').addEventListener('click', () => {
                document.getElementById('error-modal').style.display = 'none';
            });
        });
        document.getElementById('close-modal').addEventListener('click', function() {
            // Hide the modal
            document.getElementById('error-modal').style.display = 'none';

            // Send an AJAX request to clear the session error messages
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'clear_errors.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send('action=clear_errors');
        });

    </script>
    <script src="js/customCursor.js"></script> 
    <script src="js/sidebarToggle.js"></script>
    <script src="js/sticker.js"></script>
</body>
</html>
