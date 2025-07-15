<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header('Location: doctor_login.php'); // Redirect to login if not logged in
    exit();
}
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}

$conn = mysqli_connect('localhost', 'root', '', 'contact_db') or die('Connection failed');
$doctorId = $_SESSION['doctor_id'];
$doctorName = $_SESSION['doctor_name'];

// Fetch doctor details
$sqlDoctor = "SELECT * FROM doctors WHERE id = ?";
$stmtDoctor = $conn->prepare($sqlDoctor);
if (!$stmtDoctor) {
    die("SQL Error: " . $conn->error); // Output error if prepare fails
}
$stmtDoctor->bind_param("i", $doctorId);
$stmtDoctor->execute();
$doctorResult = $stmtDoctor->get_result();
$doctor = $doctorResult->fetch_assoc();

// Handle form submission
if (isset($_POST['update'])) {
    // Capture updated values from the form
    $name = $_POST['name'];
    $specialization = $_POST['specialization'];
    $experience = $_POST['experience'];
    $email = $_POST['email'];

    // Update the doctor's details in the database
    $sqlUpdate = "UPDATE doctors SET name = ?, specialization = ?, experience = ?, email = ? WHERE id = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("ssssi", $name, $specialization, $experience, $email, $doctorId);

    if ($stmtUpdate->execute()) {
        // Success message (optional)
        echo "<p class='message'>Your information has been updated successfully!</p>";
        // Optionally update session variables with new information
        $_SESSION['doctor_name'] = $name;
        $_SESSION['doctor_specialization'] = $specialization;
        $_SESSION['doctor_experience'] = $experience;
        $_SESSION['doctor_email'] = $email;
    } else {
        // Error message (optional)
        echo "<p class='message'>Error updating your information. Please try again.</p>";
    }
}
// Fetch appointments
$sqlAppointments = "SELECT * FROM contact_form WHERE doctor_id = ?";
$stmtAppointments = $conn->prepare($sqlAppointments);
if (!$stmtAppointments) {
    die("SQL Error: " . $conn->error); // Output error if prepare fails
}
$stmtAppointments->bind_param("i", $doctorId);
$stmtAppointments->execute();
$appointmentsResult = $stmtAppointments->get_result();

// Fetch reviews for the logged-in doctor
$sqlReviews = "SELECT doctor_reviews.patient_name, doctor_reviews.review, doctor_reviews.rating , doctor_reviews.review_date
               FROM doctor_reviews 
               WHERE doctor_reviews.doctor_id = ?";
$stmtReviews = $conn->prepare($sqlReviews);
if (!$stmtReviews) {
    die("SQL Error: " . $conn->error); // Output error if prepare fails
}
$stmtReviews->bind_param("i", $doctorId);
$stmtReviews->execute();
$resultReviews = $stmtReviews->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Main container styling */
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #f4f8fb;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            max-width: 900px;
            margin: 0 auto;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .doctor-profile {
            text-align: center;
            margin-bottom: 30px;
        }

        .doctor-profile img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-bottom: 15px;
            border: 5px solid #16a085;
        }

        .doctor-profile h1 {
            color: #16a085;
            font-size: 28px;
            font-weight: 700;
            margin: 10px 0;
        }

        .doctor-profile p {
            color: #555;
            font-size: 18px;
            margin: 5px 0;
        }

        h2 {
            font-size: 24px;
            color: #16a085;
            font-weight: 700;
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        table th,
        table td {
            padding: 15px;
            text-align: center;
            font-size: 16px;
            color: #333;
        }

        table th {
            background-color: #16a085;
            color: #fff;
            font-weight: 600;
            text-transform: uppercase;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #eaf8f3;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        table td {
            border: 1px solid #ddd;
        }

        /* Add responsiveness for mobile */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .doctor-profile h1 {
                font-size: 24px;
            }

            table th,
            table td {
                padding: 10px;
                font-size: 14px;
            }
        }

        .logout-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            color: #ffffff;
            background-color: #e74c3c;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .logout-button:hover {
            background-color: #c0392b;
            cursor: pointer;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #16a085;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }

        /* Form styling */
        form {
            margin-top: 20px;
        }

        input[type="text"],
        input[type="email"],
        input[type="number"],
        select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            background-color: #16a085;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #e74c3c;
        }

        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        label {
            margin-right: 10px;
            /* Space between label and input */
            width: 150px;
            /* Ensure label has consistent width */
            text-align: right;
            /* Align text to the right */
            margin-left: 0;
            /* Remove any left margin */
        }

        input {
            flex-grow: 1;
            /* The input will take up remaining space */
            padding: 5px;
            /* Adjust input padding if needed */
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .reviews-container {
            max-width: 800px;
            margin: auto;
        }

        .review {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }

        .review .patient-name {
            font-weight: bold;
            color: #16a085;
        }

        .review .rating {
            color: #ff9800;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="doctor-profile">
            <img src="<?php echo htmlspecialchars($doctor['image']); ?>" alt="Doctor Photo">
            <h1>Welcome, <?php echo htmlspecialchars($doctor['name']); ?></h1>
            <p>Specialty: <?php echo htmlspecialchars($doctor['specialization']); ?></p>
            <p>Experience: <?php echo htmlspecialchars($doctor['experience']); ?></p>
            <p>Email: <?php echo htmlspecialchars($doctor['email']); ?></p>
            <p>Fees: <?php echo htmlspecialchars($doctor['fees']); ?></p>


            <!-- Button to show the edit form -->
            <button onclick="toggleEditForm()" class="btn">
                Edit <i class="fas fa-pencil-alt"></i>
            </button>

            <!-- Edit form (initially hidden) -->
            <form id="editForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="display:none;">
                <h3>Edit Your Info</h3>
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($doctor['name']); ?>" class="box">
                </div>

                <div class="form-group">
                    <label for="specialization">Specialty:</label>
                    <input type="text" name="specialization" id="specialization" value="<?php echo htmlspecialchars($doctor['specialization']); ?>" class="box">
                </div>

                <div class="form-group">
                    <label for="experience">Experience:</label>
                    <input type="text" name="experience" id="experience" value="<?php echo htmlspecialchars($doctor['experience']); ?>" class="box">
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($doctor['email']); ?>" class="box">
                </div>
                <div class="form-group">
                    <label for="fees">Fees:</label>
                    <input type="fees" name="fees" id="fees" value="<?php echo htmlspecialchars($doctor['fees']); ?>" class="box">
                </div>

                <!-- Submit button to save the changes -->
                <input type="submit" name="update" value="Save Changes" class="btn">
            </form>
        </div>
    </div>

    <script>
        // Function to toggle the visibility of the edit form
        function toggleEditForm() {
            var form = document.getElementById('editForm');
            form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
        }
    </script>


    <h2>Your Appointments</h2>
    <table>
        <tr>
            <th>Appointment ID</th>
            <th>Patient Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Appointment Date</th>
            <th>Lab Report</th> <!-- New column for lab reports -->
        </tr>
        <?php while ($appointment = $appointmentsResult->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($appointment['id']); ?></td>
                <td><?php echo htmlspecialchars($appointment['name']); ?></td>
                <td><?php echo htmlspecialchars($appointment['email']); ?></td>
                <td><?php echo htmlspecialchars($appointment['number']); ?></td>
                <td><?php echo htmlspecialchars($appointment['date']); ?></td>
                <td>
                    <?php if (!empty($appointment['lab_report'])) { ?>
                        <!-- Provide a download link for the lab report -->
                        <a href="<?php echo htmlspecialchars($appointment['lab_report']); ?>" target="_blank">Download Report</a>
                    <?php } else { ?>
                        <!-- Indicate that no report is available -->
                        <span>No Report</span>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </table>
    <a href="?logout=true" class="logout-button">Logout</a>

    <div class="reviews-container">
        <h2>Patient Reviews</h2>
        <?php if ($resultReviews->num_rows > 0): ?>
            <?php while ($review = $resultReviews->fetch_assoc()): ?>
                <div class="review">
                    <p class="patient-name">Patient: <?php echo htmlspecialchars($review['patient_name']); ?></p>
                    <p class="rating">Rating: <?php echo htmlspecialchars($review['rating']); ?>/5</p>
                    <p>Review: <?php echo htmlspecialchars($review['review']); ?></p>
                    <p>Review Date: <?php echo htmlspecialchars($review['review_date']); ?></p>

                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align: center;">No reviews available.</p>
        <?php endif; ?>
    </div>
</body>

</html>