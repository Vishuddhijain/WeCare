<?php
session_start();
$conn = mysqli_connect('localhost', 'root', '', 'contact_db') or die('Connection failed');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $doctorId = $_POST['doctor_id'];
    $password = $_POST['password'];

    // Fetch doctor details
    $sql = "SELECT * FROM doctors WHERE id = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $doctorId, $password); // Use 'is' for integer and string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $doctor = $result->fetch_assoc();
        $_SESSION['doctor_id'] = $doctor['id'];
        $_SESSION['doctor_name'] = $doctor['name'];
        header('Location: doctor_profile.php'); // Redirect to a dashboard page
        exit();
    } else {
        $error = "Invalid ID or Password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Login Page</title>
</head>
<style>
    @import url('https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap');

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Poppins', sans-serif;
}

html, body {
  display: grid;
  height: 100%;
  width: 100%;
  place-items: center;
  background: linear-gradient(to right, #16a085, #1abc9c, #2ecc71, #27ae60);
}

::selection {
  background: #1abc9c;
  color: #fff;
}

.wrapper {
  overflow: hidden;
  max-width: 390px;
  background: #fff;
  padding: 30px;
  border-radius: 15px;
  box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.1);
}

.wrapper .title {
  font-size: 35px;
  font-weight: 600;
  text-align: center;
  color: #16a085;
}

.form-container {
  width: 100%;
}

.form-inner form {
  width: 100%;
}

.form-inner form .field {
  height: 50px;
  width: 100%;
  margin-top: 20px;
}

.form-inner form .field input {
  height: 100%;
  width: 100%;
  outline: none;
  padding-left: 15px;
  border-radius: 15px;
  border: 1px solid lightgrey;
  border-bottom-width: 2px;
  font-size: 17px;
  transition: all 0.3s ease;
}

.form-inner form .field input:focus {
  border-color: #1abc9c;
}

.form-inner form .field input::placeholder {
  color: #999;
  transition: all 0.3s ease;
}

.form-inner form .field input:focus::placeholder {
  color: #16a085;
}

.form-inner form .pass-link {
  margin-top: 5px;
  text-align: right;
}

.form-inner form .pass-link a {
  color: #1abc9c;
  text-decoration: none;
}

.form-inner form .pass-link a:hover {
  text-decoration: underline;
}

form .btn {
  height: 50px;
  width: 100%;
  border-radius: 15px;
  position: relative;
  overflow: hidden;
  margin-top: 20px;
}

form .btn .btn-layer {
  height: 100%;
  width: 300%;
  position: absolute;
  left: -100%;
  background: linear-gradient(to right, #16a085, #1abc9c, #2ecc71, #27ae60);
  border-radius: 15px;
  transition: all 0.4s ease;
}

form .btn:hover .btn-layer {
  left: 0;
}

form .btn input[type="submit"] {
  height: 100%;
  width: 100%;
  z-index: 1;
  position: relative;
  background: none;
  border: none;
  color: #fff;
  padding-left: 0;
  border-radius: 15px;
  font-size: 20px;
  font-weight: 500;
  cursor: pointer;
}

</style>
<body>
    <div class="wrapper">
        <div class="title">Login Form</div>
        <div class="form-container">
            <div class="form-inner">
                <form action="doctor_login.php" method="post">
                    <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
                    <div class="field">
                        <input type="text" name="doctor_id" placeholder="Doctor ID" required>
                    </div>
                    <div class="field">
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="btn">
                        <div class="btn-layer"></div>
                        <input type="submit" value="Login">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>