<?php
session_start();
$conn = mysqli_connect('localhost', 'root', '', 'contact_db') or die('Connection failed');

// Handle login
if (isset($_POST['login'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM patients WHERE name='$name' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $patient = mysqli_fetch_assoc($result);

        $_SESSION['patient_id'] = $patient['id'];
        $_SESSION['patient_name'] = $patient['name'];
        $_SESSION['patient_number'] = $patient['number'];
        $_SESSION['patient_age']=$patient['age'];
        $_SESSION['patient_email']=$patient['email'];

        header('Location: index.php'); // Redirect to main page
        exit();
    } else {
        $error = "Invalid login details. Please try again.";
    }
}
?>

<?php
if (isset($_POST['signup'])) {
    // Get and sanitize input values
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $number = mysqli_real_escape_string($conn, $_POST['number']);
    $age = mysqli_real_escape_string($conn, $_POST['age']); // New field: age
    $email = mysqli_real_escape_string($conn, $_POST['email']); // New field: email

    // Check if the user already exists
    $check_query = "SELECT * FROM patients WHERE name='$name' OR email='$email'"; // Check both name and email
    $check_result = mysqli_query($conn, $check_query);

    if ($check_result && mysqli_num_rows($check_result) > 0) {
        $error = "User already exists. Please log in.";
    } else {
        // Insert new patient into the database
        $insert_query = "INSERT INTO patients (name, password, number, age, email) 
                         VALUES ('$name', '$password', '$number', '$age', '$email')";
        if (mysqli_query($conn, $insert_query)) {
            $_SESSION['patient_id'] = mysqli_insert_id($conn);
            $_SESSION['patient_name'] = $name;
            $_SESSION['patient_number'] = $number;
            $_SESSION['patient_age'] = $age; // Storing age in session (optional)
            $_SESSION['patient_email'] = $email; // Storing email in session (optional)

            header('Location: index.php'); // Redirect to main page
            exit();
        } else {
            $error = "Signup failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Sign Up</title>
    <link rel="stylesheet" href="login1.css">
</head>
<body>

    <div class="wrapper">
        <div class="title-text">
            <div class="title login">Login Form</div>
            <div class="title signup">Signup Form</div>
        </div>
        <div class="form-container">
            <div class="slide-controls">
                <input type="radio" name="slide" id="login" checked>
                <input type="radio" name="slide" id="signup">
                <label for="login" class="slide login">Login</label>
                <label for="signup" class="slide signup">Signup</label>
                <div class="slider-tab"></div>
            </div>
            <div class="form-inner">
                <!-- Login Form -->
                <form action="#" method="post" class="login">
                    <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
                    <div class="field">
                        <input type="text" name="name" placeholder="Your Name" required>
                    </div>
                    <div class="field">
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="field btn">
                        <div class="btn-layer"></div>
                        <input type="submit" name="login" value="Login">
                    </div>
                    <div class="signup-link">Not a member? <a href="#">Signup now</a></div>
                </form>

                <!-- Signup Form -->
                <form action="#" method="post" class="signup">
                    <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
                    <div class="field">
                        <input type="text" name="name" placeholder="Your Name" required>
                    </div>
                    <div class="field">
                        <input type="email" name="email" placeholder="Your Email" required>
                    </div>
                    <div class="field">
                        <input type="number" name="age" placeholder="Your Age" required>
                    </div>
                    <div class="field">
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="field">
                        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    </div>
                    <div class="field">
                        <input type="text" name="number" placeholder="Your Number" required>
                    </div>
                    <div class="field btn">
                        <div class="btn-layer"></div>
                        <input type="submit" name="signup" value="Signup">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="login.js"></script>
</body>
</html>