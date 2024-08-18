<?php
// Display errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

// Include the database connection file
include 'config.php';

// Initialize variables for storing form data and error messages
$name = $nickname = $student_id = $email = $class = $birth_place = $birth_date = $gender = $religion = $address = $phone = $password = $confirm_password = "";
$name_err = $nickname_err = $student_id_err = $email_err = $class_err = $birth_place_err = $birth_date_err = $gender_err = $religion_err = $address_err = $phone_err = $password_err = $confirm_password_err = "";

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate and sanitize inputs
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter your name.";
    } else {
        $name = trim($_POST["name"]);
    }

    if (empty(trim($_POST["nickname"]))) {
        $nickname_err = "Please enter your nickname.";
    } else {
        $nickname = trim($_POST["nickname"]);
    }

    if (empty(trim($_POST["student_id"]))) {
        $student_id_err = "Please enter your student ID.";
    } else {
        $student_id = trim($_POST["student_id"]);
    }

    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email format.";
    } else {
        $email = trim($_POST["email"]);
    }

    if (empty(trim($_POST["class"]))) {
        $class_err = "Please enter your class.";
    } else {
        $class = trim($_POST["class"]);
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm your password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    if (empty(trim($_POST["birth_place"]))) {
        $birth_place_err = "Please enter your place of birth.";
    } else {
        $birth_place = trim($_POST["birth_place"]);
    }

    if (empty(trim($_POST["birth_date"]))) {
        $birth_date_err = "Please enter your date of birth.";
    } else {
        $birth_date = trim($_POST["birth_date"]);
    }

    if (empty(trim($_POST["gender"]))) {
        $gender_err = "Please select your gender.";
    } else {
        $gender = trim($_POST["gender"]);
    }

    if (empty(trim($_POST["religion"]))) {
        $religion_err = "Please enter your religion.";
    } else {
        $religion = trim($_POST["religion"]);
    }

    if (empty(trim($_POST["address"]))) {
        $address_err = "Please enter your address.";
    } else {
        $address = trim($_POST["address"]);
    }

    if (empty(trim($_POST["phone"]))) {
        $phone_err = "Please enter your phone number.";
    } else {
        $phone = trim($_POST["phone"]);
    }


    if (empty($name_err) && empty($nickname_err) && empty($student_id_err) && empty($email_err) && empty($class_err) && empty($birth_place_err) && empty($birth_date_err) && empty($gender_err) && empty($religion_err) && empty($address_err) && empty($phone_err) && empty($password_err) && empty($confirm_password_err)) {

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Prepare and execute the SQL query to insert the user into the database
        $sql = "INSERT INTO users (student_id, name, nickname, email, class, birth_place, birth_date, gender, religion, address, phone, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssssssssssss", $param_student_id, $param_name, $param_nickname, $param_email, $param_class, $param_birth_place, $param_birth_date, $param_gender, $param_religion, $param_address, $param_phone, $param_password);

            $param_student_id = $student_id;
            $param_name = $name;
            $param_nickname = $nickname;
            $param_email = $email;
            $param_class = $class;
            $param_birth_place = $birth_place;
            $param_birth_date = $birth_date;
            $param_gender = $gender;
            $param_religion = $religion;
            $param_address = $address;
            $param_phone = $phone;
            $param_password = $hashed_password;

            if ($stmt->execute()) {
                // Redirect to login page after successful registration
                header("location: login.php");
            } else {
                echo "Something went wrong. Please try again later.";
            }

            // Close the statement
            $stmt->close();
        }
    }

    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h2>Register</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label>Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>">
        <span><?php echo $name_err; ?></span><br>

        <label>Nickname</label>
        <input type="text" name="nickname" value="<?php echo htmlspecialchars($nickname); ?>">
        <span><?php echo $nickname_err; ?></span><br>

        <label>Student ID</label>
        <input type="text" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">
        <span><?php echo $student_id_err; ?></span><br>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
        <span><?php echo $email_err; ?></span><br>

        <label>Class</label>
        <input type="text" name="class" value="<?php echo htmlspecialchars($class); ?>">
        <span><?php echo $class_err; ?></span><br>


        <label>Place of Birth</label>
        <input type="text" name="birth_place" value="<?php echo $birth_place; ?>">
        <span><?php echo $birth_place_err; ?></span><br>

        <label>Date of Birth</label>
        <input type="date" name="birth_date" value="<?php echo $birth_date; ?>">
        <span><?php echo $birth_date_err; ?></span><br>

        <label>Gender</label>
        <select name="gender">
            <option value="Male" <?php if($gender == "Male") echo "selected"; ?>>Male</option>
            <option value="Female" <?php if($gender == "Female") echo "selected"; ?>>Female</option>
        </select>
        <span><?php echo $gender_err; ?></span><br>

        <label>Religion</label>
        <input type="text" name="religion" value="<?php echo $religion; ?>">
        <span><?php echo $religion_err; ?></span><br>

        <label>Address</label>
        <textarea name="address"><?php echo $address; ?></textarea>
        <span><?php echo $address_err; ?></span><br>

        <label>Phone</label>
        <input type="text" name="phone" value="<?php echo $phone; ?>">
        <span><?php echo $phone_err; ?></span><br>

        <label>Password</label>
        <input type="password" name="password">
        <span><?php echo $password_err; ?></span><br>

        <label>Confirm Password</label>
        <input type="password" name="confirm_password">
        <span><?php echo $confirm_password_err; ?></span><br>

        <input type="submit" value="Register">
    </form>
</body>
</html>
