<?php
// Start the session
session_start();

// Include the database connection file
include 'config.php';

// Initialize variables for storing form data and error messages
$student_id = $password = "";
$student_id_err = $password_err = "";

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if student ID and password fields are filled
    if (empty(trim($_POST["student_id"]))) {
        $student_id_err = "Please enter your Student ID.";
    } else {
        $student_id = trim($_POST["student_id"]);
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($student_id_err) && empty($password_err)) {

        // Prepare the SQL query to include 'role'
        $sql = "SELECT student_id, password, name, email, class, role FROM users WHERE student_id = ?";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_student_id);

            // Set parameters
            $param_student_id = $student_id;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Store result
                $stmt->store_result();

                // Check if student ID exists, if yes, then verify password
                if ($stmt->num_rows == 1) {
                    // Bind result variables
                    $stmt->bind_result($student_id, $hashed_password, $name, $email, $class, $role);
                    if ($stmt->fetch()) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, start a new session and save the user information
                            $_SESSION["loggedin"] = true;
                            $_SESSION["student_id"] = $student_id;
                            $_SESSION["name"] = $name;
                            $_SESSION["email"] = $email;
                            $_SESSION["class"] = $class;
                            $_SESSION["role"] = $role;

                            // Redirect user to the index page
                            header("location: index.php");
                            exit();
                        } else {
                            // Display an error message if the password is not valid
                            $password_err = "The password you entered is incorrect.";
                        }
                    }
                } else {
                    // Display an error message if the student ID doesn't exist
                    $student_id_err = "No account found with that Student ID.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label>Student ID</label>
        <input type="text" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">
        <span><?php echo $student_id_err; ?></span><br>

        <label>Password</label>
        <input type="password" name="password">
        <span><?php echo $password_err; ?></span><br>
        <input type="submit" value="Login">
        <p>Belum punya akun? <a href="register.php">Daftar disini.</a></p>
    </form>
</body>
</html>
