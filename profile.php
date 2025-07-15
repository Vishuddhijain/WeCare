<?php
session_start();
$conn = mysqli_connect('localhost', 'root', '', 'contact_db') or die('Connection failed');

if (!isset($_SESSION['patient_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

$patient_id = $_SESSION['patient_id'];

// Fetch patient data
$query = "SELECT * FROM patients WHERE id = $patient_id";
$result = mysqli_query($conn, $query);
if ($result && mysqli_num_rows($result) > 0) {
    $patient = mysqli_fetch_assoc($result);
}

// Handle profile update
if (isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $number = mysqli_real_escape_string($conn, $_POST['number']);
    $age = intval($_POST['age']);

    $update_query = "UPDATE patients SET name='$name', email='$email', number='$number', age='$age' WHERE id=$patient_id";
    if (mysqli_query($conn, $update_query)) {
        $_SESSION['patient_name'] = $name;
        $_SESSION['patient_number'] = $number;
        $_SESSION['patient_email'] = $email;
        $_SESSION['patient_age'] = $age;
        $success = "Profile updated successfully!";
        // Refresh patient data after updating
        $query = "SELECT * FROM patients WHERE id = $patient_id";
        $result = mysqli_query($conn, $query);
        $patient = mysqli_fetch_assoc($result);
    } else {
        $error = "Failed to update profile. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Profile</title>
    <style>
    body {
    font-family: Arial, sans-serif;
    background: #f0f0f0;
    margin: 0;
    padding: 0;
}

.profile-container {
    width: 50%;
    margin: 50px auto;
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

h1 {
    text-align: center;
    color: #16a085;
}

form {
    display: flex;
    flex-direction: column;
}

label {
    margin: 10px 0 5px;
    font-weight: bold;
}

input {
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    background: #f9f9f9; /* Light background for readonly fields */
}

input[readonly] {
    cursor: not-allowed;
    color: #666; /* Gray text for readonly fields */
}

button {
    padding: 10px;
    margin-top: 10px;
    background: #16a085;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    display: inline-block;
    width: 100px;
    text-align: center;
}

button:hover {
    background: #33d9b2;
}

button#update-button {
    background: #16a085;
}

button#update-button:hover {
    background: #33d9b2;
}

.editable input {
    background: #fff; /* White background for editable fields */
    color: #000;
}

    </style>
    <script>
        // Enable editing mode
        function enableEditing() {
            document.getElementById('edit-profile-form').classList.add('editable');
            document.getElementById('edit-button').style.display = 'none'; // Hide Edit button
            document.getElementById('update-button').style.display = 'inline-block'; // Show Update button
            const fields = document.querySelectorAll('#edit-profile-form input');
            fields.forEach(field => field.removeAttribute('readonly'));
        }

        // Disable editing mode (initial view)
        function disableEditing() {
            document.getElementById('edit-profile-form').classList.remove('editable');
            document.getElementById('edit-button').style.display = 'inline-block'; // Show Edit button
            document.getElementById('update-button').style.display = 'none'; // Hide Update button
            const fields = document.querySelectorAll('#edit-profile-form input');
            fields.forEach(field => field.setAttribute('readonly', 'readonly'));
        }
    </script>
</head>
<body onload="disableEditing()">
    <div class="profile-container">
        <h1>My Profile</h1>
        <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>

        <!-- Profile form -->
        <form action="" method="post" id="edit-profile-form">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="<?php echo $patient['name']; ?>" readonly>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?php echo $patient['email']; ?>" readonly>

            <label for="number">Phone Number:</label>
            <input type="text" name="number" id="number" value="<?php echo $patient['number']; ?>" readonly>

            <label for="age">Age:</label>
            <input type="number" name="age" id="age" value="<?php echo $patient['age']; ?>" readonly>

            <!-- Buttons -->
            <button type="button" id="edit-button" onclick="enableEditing()">Edit</button>
            <button type="submit" name="update_profile" id="update-button" style="display: none;">Update</button>
        </form>
    </div>
</body>
</html>
