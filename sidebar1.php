<?php
// Ensure user data is available
if (!isset($name) || !isset($email) || !isset($class)) {
    $name = $email = $class = $role ='';
}
?>

<div id="sidebar1" class="sidebar1">
    <h2>User Info</h2>
    <p>Nama: <?php echo htmlspecialchars($name); ?></p>
    <p>Email: <?php echo htmlspecialchars($email); ?></p>
    <p>Kelas: <?php echo htmlspecialchars($class); ?></p>
    <p>Role: <?php echo htmlspecialchars($role); ?></p>
    <!-- Logout Button -->
    <div class="logout-button">
        <a href="logout.php">Logout</a>
    </div>
</div>


<div id="sidebarToggle" class="sidebar-toggle"></div>
