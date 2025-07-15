<?php
// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'contact_db') or die('Connection failed');

// Check if form is submitted for making a new appointment
if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $number = mysqli_real_escape_string($conn, $_POST['number']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $age = intval($_POST['age']);
    $disease = mysqli_real_escape_string($conn, $_POST['disease']);
    $doctor_id = intval($_POST['doctor']);
    $slot_id = intval($_POST['slot']);

    // File upload handling
    $upload_dir = "uploads/"; // Directory to save uploaded files
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Create the directory if it doesn't exist
    }

    $lab_report_path = null; // Initialize as null in case no file is uploaded

    if (isset($_FILES['lab_report']) && $_FILES['lab_report']['error'] === 0) {
        $file_tmp_name = $_FILES['lab_report']['tmp_name'];
        $file_name = $_FILES['lab_report']['name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

        // Validate file type (allow only PDF)
        if ($file_ext === 'pdf') {
            $unique_name = uniqid() . '_' . $file_name; // Generate unique file name
            $upload_path = $upload_dir . $unique_name;

            // Move file to the target directory
            if (move_uploaded_file($file_tmp_name, $upload_path)) {
                $lab_report_path = $upload_path; // Save file path
            } else {
                $message[] = 'Failed to upload the file.';
            }
        } else {
            $message[] = 'Invalid file type. Only PDF files are allowed.';
        }
    }

    // Check if the slot is already booked for the selected date
    $checkSlotQuery = "SELECT * FROM contact_form WHERE slot_id = $slot_id AND date = '$date'";
    $checkSlot = mysqli_query($conn, $checkSlotQuery);

    if (mysqli_num_rows($checkSlot) > 0) {
        $message[] = 'Selected slot is already booked for the chosen date.';
    } else {
        // Insert appointment into contact_form with lab_report path
        $insertQuery = "INSERT INTO contact_form (name, email, number, age, date, disease, doctor_id, slot_id, lab_report) 
                        VALUES ('$name', '$email', '$number', $age, '$date', '$disease', $doctor_id, $slot_id, '$lab_report_path')";
        $insert = mysqli_query($conn, $insertQuery);

        if ($insert) {
            $message[] = 'Appointment made successfully!';
        } else {
            $message[] = 'Appointment failed. Please try again.';
        }
    }
}


// Check if a delete request was made
if (isset($_POST['delete_appointment_id'])) {
    $appointment_id = intval($_POST['delete_appointment_id']);
    $slot_id = intval($_POST['slot_id']);
    $date = mysqli_real_escape_string($conn, $_POST['appointment_date']);

    // Delete the appointment
    $deleteQuery = "DELETE FROM contact_form WHERE id = $appointment_id";
    $delete = mysqli_query($conn, $deleteQuery);

    if ($delete) {
        $message[] = 'Appointment deleted successfully!';
    } else {
        $message[] = 'Failed to delete appointment.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WeCare</title>
    <!-- Link the modal.css file here -->
    <link rel="stylesheet" href="modal.css">
    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="styles.css">

</head>

<body>

    <!-- header section starts  -->
    <?php session_start(); ?>
    <header class="header">

        <a href="#" class="logo"> <i class="fas fa-heartbeat"></i> <strong>We</strong>Care </a>

        <nav class="navbar">
            <a href="#home" class="nav-item" onclick="selectTab(event)">home</a>
            <a href="#about" class="nav-item" onclick="selectTab(event)">about</a>
            <a href="#services" class="nav-item" onclick="selectTab(event)">services</a>
            <a href="#doctors" class="nav-item" onclick="selectTab(event)">doctors</a>
            <a href="#appointment" class="nav-item" onclick="checkLoginStatus(event)">appointment</a>
            <a href="#review" class="nav-item" onclick="selectTab(event)">review</a>
            <a href="#blogs" class="nav-item" onclick="selectTab(event)">blogs</a>

            <!-- Profile and Logout -->
            <?php if (isset($_SESSION['patient_id'])): ?>
                <a href="profile.php" class="nav-item">Profile</a>
                <a href="logout.php" class="nav-item">Logout</a>
            <?php else: ?>
                <a href="#" class="nav-item" onclick="openModal()">Login/Sign Up</a>
            <?php endif; ?>
        </nav>


        <div id="menu-btn" class="fas fa-bars"></div>

    </header>

    <!-- Modal for selection -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Select User Type</h2>
            <button onclick="redirectTo('patient')">Patient</button>
            <button onclick="redirectTo('doctor')">Doctor</button>
        </div>
    </div>
    <script>
        // Open modal
        function openModal() {
            document.getElementById('modal').style.display = 'block';
        }

        // Close modal
        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }

        // Redirect to respective login/signup page
        function redirectTo(userType) {
            if (userType === 'patient') {
                window.location.href = 'login.php';
            } else if (userType === 'doctor') {
                window.location.href = 'doctor_login.php';
            }
        }
    </script>
    <!-- header section ends -->

    <!-- home section starts  -->

    <section class="home" id="home">

        <div class="image">
            <img src="images/home-img.svg" alt="">
        </div>

        <div class="content">
            <h3>Your health, Our Priority</h3>
            <p> WeCare—your health, connected. We're transforming healthcare with a secure, user-friendly platform that puts your health journey first.</p>
            <a href="#appointment" class="btn"  onclick="checkLoginStatus(event)"> Book appointment <span class="fas fa-chevron-right"></span> </a>
        </div>

    </section>


    <!-- home section ends -->

    <!-- icons section starts  -->

    <section class="icons-container">

        <div class="icons">
            <i class="fas fa-user-md"></i>
            <h3>150+</h3>
            <p>doctors at work</p>
        </div>

        <div class="icons">
            <i class="fas fa-users"></i>
            <h3>1030+</h3>
            <p>satisfied patients</p>
        </div>

        <div class="icons">
            <i class="fas fa-procedures"></i>
            <h3>490+</h3>
            <p>bed facility</p>
        </div>

        <div class="icons">
            <i class="fas fa-hospital"></i>
            <h3>70+</h3>
            <p>available hospitals</p>
        </div>

    </section>

    <!-- icons section ends -->

    <!-- about section starts  -->

    <section class="about" id="about">

        <h1 class="heading"> <span>about</span> us </h1>

        <div class="row">

            <div class="image">
                <img src="images/about-img.svg" alt="">
            </div>

            <div class="content">
                <h3>Receive the Care You Deserve </h3>
                <p>At WeCare, we connect you with top-tier healthcare providers, ensuring that your treatment is always personalized, efficient, and accessible. Our platform brings convenience and compassion together to support every step of your health journey.</p>
                <p>From scheduling appointments to accessing medical records, we make it easy for you to stay on top of your well-being—all within a secure and user-friendly platform.</p>
                <a href="#" class="btn"> Learn more <span class="fas fa-chevron-right"></span> </a>
            </div>


        </div>

    </section>

    <!-- about section ends -->

    <!-- services section starts  -->

    <section class="services" id="services">

        <h1 class="heading"> our <span>services</span> </h1>

        <div class="box-container">

            <div class="box">
                <i class="fas fa-notes-medical"></i>
                <h3>free checkups</h3>
                <p>"Start your wellness journey with our complimentary health checkups."</p>
            </div>

            <div class="box">
                <i class="fas fa-ambulance"></i>
                <h3>24/7 ambulance</h3>
                <p>"Emergency care is just a call away, any time of day."</p>
            </div>

            <div class="box">
                <i class="fas fa-user-md"></i>
                <h3>expert doctors</h3>
                <p>"Our doctors are here to provide exceptional care, every time."</p>
            </div>

            <div class="box">
                <i class="fas fa-pills"></i>
                <h3>medicines</h3>
                <p>"Quality medicines delivered with trust and care."</p>
            </div>

            <div class="box">
                <i class="fas fa-procedures"></i>
                <h3>bed facility</h3>
                <p>"Comfortable, clean, and safe—our facilities are ready for you."</p>
            </div>

            <div class="box">
                <i class="fas fa-heartbeat"></i>
                <h3>total care</h3>
                <p>"From start to finish, we're here for your complete health needs."</p>
            </div>

        </div>

    </section>
    <!-- services section ends -->

    <!-- doctors section starts  -->

    <section class="doctors" id="doctors">

        <h1 class="heading"> our <span>doctors</span> </h1>

        <div class="box-container">

            <div class="box">
                <a href="doctor_profileuser.php?doctor=1">
                    <img src="images/doc-1.jpg" alt="">
                    <h3>Dr. Ananya Sharma </h3>
                    <span> Cardiologist</span>
                    <div class="share">
                        <a href="https://www.facebook.com" class="fab fa-facebook-f" target="_blank"></a>
                        <a href="https://twitter.com" class="fab fa-twitter" target="_blank"></a>
                        <a href="https://www.instagram.com" class="fab fa-instagram" target="_blank"></a>
                        <a href="https://www.linkedin.com" class="fab fa-linkedin" target="_blank"></a>
                    </div>

            </div>

            <div class="box">
                <a href="doctor_profileuser.php?doctor=2">
                    <img src="images/doc-2.jpg" alt="">
                    <h3>Dr. Rajesh Verma</h3>
                    <span>Orthopedic Surgeon</span>
                    <div class="share">
                        <a href="https://www.facebook.com" class="fab fa-facebook-f" target="_blank"></a>
                        <a href="https://twitter.com" class="fab fa-twitter" target="_blank"></a>
                        <a href="https://www.instagram.com" class="fab fa-instagram" target="_blank"></a>
                        <a href="https://www.linkedin.com" class="fab fa-linkedin" target="_blank"></a>
                    </div>
            </div>

            <div class="box">
                <a href="doctor_profileuser.php?doctor=3">
                    <img src="images/doc-3.jpg" alt="">
                    <h3>Dr. Leena Kapoor</h3>
                    <span>Pediatrician</span>
                    <div class="share">
                        <a href="https://www.facebook.com" class="fab fa-facebook-f" target="_blank"></a>
                        <a href="https://twitter.com" class="fab fa-twitter" target="_blank"></a>
                        <a href="https://www.instagram.com" class="fab fa-instagram" target="_blank"></a>
                        <a href="https://www.linkedin.com" class="fab fa-linkedin" target="_blank"></a>
                    </div>
            </div>

            <div class="box">
                <a href="doctor_profileuser.php?doctor=4">
                    <img src="images/doc-4.jpg" alt="">
                    <h3>Dr. Priya Menon</h3>
                    <span>Dermatologist</span>
                    <div class="share">
                        <a href="https://www.facebook.com" class="fab fa-facebook-f" target="_blank"></a>
                        <a href="https://twitter.com" class="fab fa-twitter" target="_blank"></a>
                        <a href="https://www.instagram.com" class="fab fa-instagram" target="_blank"></a>
                        <a href="https://www.linkedin.com" class="fab fa-linkedin" target="_blank"></a>
                    </div>
            </div>

            <div class="box">
                <a href="doctor_profileuser.php?doctor=5">
                    <img src="images/doc-5.jpg" alt="">
                    <h3>Dr. Amit Patel </h3>
                    <span>Neurologist</span>
                    <div class="share">
                        <a href="https://www.facebook.com" class="fab fa-facebook-f" target="_blank"></a>
                        <a href="https://twitter.com" class="fab fa-twitter" target="_blank"></a>
                        <a href="https://www.instagram.com" class="fab fa-instagram" target="_blank"></a>
                        <a href="https://www.linkedin.com" class="fab fa-linkedin" target="_blank"></a>
                    </div>
            </div>

            <div class="box">
                <a href="doctor_profileuser.php?doctor=6">
                    <img src="images/doc-6.jpg" alt="">
                    <h3>Dr. Naveen Singh</h3>
                    <span>General Surgeon</span>
                    <div class="share">
                        <a href="https://www.facebook.com" class="fab fa-facebook-f" target="_blank"></a>
                        <a href="https://twitter.com" class="fab fa-twitter" target="_blank"></a>
                        <a href="https://www.instagram.com" class="fab fa-instagram" target="_blank"></a>
                        <a href="https://www.linkedin.com" class="fab fa-linkedin" target="_blank"></a>
                    </div>
            </div>
            <div class="box">
                <a href="doctor_profileuser.php?doctor=7">
                    <img src="images/doc-7.jpg" alt="">
                    <h3>Dr. Maya Rao</h3>
                    <span>Endocrinologist</span>
                    <div class="share">
                        <a href="https://www.facebook.com" class="fab fa-facebook-f" target="_blank"></a>
                        <a href="https://twitter.com" class="fab fa-twitter" target="_blank"></a>
                        <a href="https://www.instagram.com" class="fab fa-instagram" target="_blank"></a>
                        <a href="https://www.linkedin.com" class="fab fa-linkedin" target="_blank"></a>
                    </div>
            </div>
            <div class="box">
                <a href="doctor_profileuser.php?doctor=8">
                    <img src="images/doc-8.jpg" alt="">
                    <h3>Dr. Arjun Khanna</h3>
                    <span>Gastroenterologist</span>
                    <div class="share">
                        <a href="https://www.facebook.com" class="fab fa-facebook-f" target="_blank"></a>
                        <a href="https://twitter.com" class="fab fa-twitter" target="_blank"></a>
                        <a href="https://www.instagram.com" class="fab fa-instagram" target="_blank"></a>
                        <a href="https://www.linkedin.com" class="fab fa-linkedin" target="_blank"></a>
                    </div>
            </div>
            <div class="box">
                <a href="doctor_profileuser.php?doctor=9">
                    <img src="images/doc-9.jpg" alt="">
                    <h3>Dr. Sameer Das</h3>
                    <span>Oncologist</span>
                    <div class="share">
                        <a href="https://www.facebook.com" class="fab fa-facebook-f" target="_blank"></a>
                        <a href="https://twitter.com" class="fab fa-twitter" target="_blank"></a>
                        <a href="https://www.instagram.com" class="fab fa-instagram" target="_blank"></a>
                        <a href="https://www.linkedin.com" class="fab fa-linkedin" target="_blank"></a>
                    </div>
            </div>

        </div>

    </section>

    <!-- doctors section ends -->

    <!-- appointmenting section starts   -->
    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    ?>
    <section class="appointment" id="appointment">
        <?php if (isset($_SESSION['patient_id'])): // Check if the patient is logged in 
        ?>
            <h1 class="heading"><span>Appointment</span> Now</h1>
            <div class="row">
                <div class="image">
                    <img src="images/appointment-img.svg" alt="">
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
                    <?php
                    if (isset($message)) {
                        foreach ($message as $message) {
                            echo '<p class="message">' . $message . '</p>';
                        }
                    }
                    ?>
                    <h3>Book Appointment</h3>
                    <!-- Pre-fill name and number from session data -->
                    <input type="text" name="name" value="<?php echo $_SESSION['patient_name']; ?>" class="box" readonly>
                    <input type="number" name="number" value="<?php echo $_SESSION['patient_number']; ?>" class="box" readonly>
                    <input type="number" name="age" value="<?php echo $_SESSION['patient_age']; ?>" class="box" readonly>
                    <input type="email" name="email" value="<?php echo $_SESSION['patient_email']; ?>" class="box" readonly>
                    <input type="text" name="disease" placeholder="disease or reason" class="box">
                    <select name="doctor" id="doctorSelect" class="box">
                        <option value="" disabled selected>Choose doctor</option>
                        <option value="1">Dr. Ananya Sharma (Cardiologist)</option>
                        <option value="2">Dr. Rajesh Verma (Orthopedic Surgeon)</option>
                        <option value="3">Dr. Leena Kapoor (Pediatrician)</option>
                        <option value="4">Dr. Priya Menon (Dermatologist)</option>
                        <option value="5">Dr. Amit Patel (Neurologist)</option>
                        <option value="6">Dr. Naveen Singh (General Surgeon)</option>
                        <option value="7">Dr. Maya Rao (Endocrinologist)</option>
                        <option value="8">Dr. Arjun Khanna (Gastroenterologist)</option>
                        <option value="9">Dr. Sameer Das (Oncologist)</option>
                    </select>
                    <select name="slot" id="slotSelect" class="box" required>
                        <option value="" disabled selected>Choose time slot</option>
                    </select>
                    <input type="date" name="date" class="box" id="appointmentDate">
                    <input type="file" name="lab_report" accept="application/pdf" class="box">
                    <input type="submit" name="submit" value="Book Appointment" class="btn">

                    <script>
                        // Ensure the date picker is set to today or future only
                        window.onload = function() {
                            var today = new Date();
                            var dd = String(today.getDate()).padStart(2, '0');
                            var mm = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
                            var yyyy = today.getFullYear();
                            today = yyyy + '-' + mm + '-' + dd; // Format as YYYY-MM-DD
                            document.getElementById("appointmentDate").setAttribute("min", today);
                        };
                    </script>
                </form>
            </div>
        <?php endif; ?>
    </section>

    <!-- Your Appointments Section -->
    <?php if (isset($_SESSION['patient_name']) && isset($_SESSION['patient_number'])): ?>
        <section class="appointment-list">
            <h1 class="heading"><span>Your Appointments</span></h1>
            <?php
            $patient_name = $_SESSION['patient_name'];
            $patient_number = $_SESSION['patient_number'];

            $result = mysqli_query($conn, "SELECT c.*, d.slot_time 
                                       FROM contact_form c 
                                       JOIN doctor_slots d ON c.slot_id = d.slot_id 
                                       WHERE c.name = '$patient_name' AND c.number = '$patient_number'");

            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<div class='appointment-item'>";
                    echo "<p><strong>Patient Name:</strong> " . $row['name'] . "</p>";
                    echo "<p><strong>Age:</strong> " . $row['age'] . "</p>";
                    echo "<p><strong>Doctor:</strong> " . $row['doctor_id'] . "</p>";
                    echo "<p><strong>Date:</strong> " . $row['date'] . "</p>";
                    echo "<p><strong>Slot:</strong> " . $row['slot_time'] . "</p>";
                    echo "<form method='post' action=''>";
                    echo "<input type='hidden' name='delete_appointment_id' value='" . $row['id'] . "'>";
                    echo "<input type='hidden' name='slot_id' value='" . $row['slot_id'] . "'>";
                    echo "<button type='submit' class='delete-btn'>Delete Appointment</button>";
                    echo "</form>";
                    echo "<br>";
                    echo "<a href='generate_receipt.php?appointment_id=" . $row['id'] . "' class='view-receipt-btn'>View Receipt</a>";
                    echo "</div>";
                }
            } else {
                echo "<p>No appointments found.</p>";
            }
            ?>
        </section>
    <?php endif; ?>

    <script>
        document.getElementById('doctorSelect').addEventListener('change', function() {
            const doctorId = this.value;
            const slotSelect = document.getElementById('slotSelect');

            // Clear existing options in slotSelect
            slotSelect.innerHTML = '<option value="" disabled selected>Choose time slot</option>';

            // Fetch available slots for the selected doctor
            fetch(`get_slots.php?doctor_id=${doctorId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.slots && data.slots.length > 0) {
                        // Populate slotSelect with available slots
                        data.slots.forEach(slot => {
                            const option = document.createElement('option');
                            option.value = slot.slot_id;
                            option.textContent = slot.slot_time;
                            slotSelect.appendChild(option);
                        });
                    } else {
                        // If no slots are available, show a message
                        const option = document.createElement('option');
                        option.value = "";
                        option.disabled = true;
                        option.textContent = "No available slots";
                        slotSelect.appendChild(option);
                    }
                })
                .catch(error => console.error('Error fetching slots:', error));
        });
    </script>
    <script>
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const appointmentId = this.getAttribute('data-appointment-id'); // Ensure this is valid

                console.log('Appointment ID:', appointmentId); // Add logging to check the ID value

                if (confirm('Are you sure you want to delete this appointment?')) {
                    fetch('delete_appointment.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                id: appointmentId
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not OK');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                alert('Appointment deleted successfully');
                                location.reload();
                            } else {
                                alert(`Deleted appointment.`);
                            }
                        })
                        .catch(error => {
                            console.error('Alert:', error);
                            alert('Deleted appointment.');
                        });
                }
            });
        });
    </script>
    <script>
        function checkLoginStatus(event) {
            <?php if (!isset($_SESSION['patient_id'])): ?>
                event.preventDefault(); // Prevent the default navigation
                window.location.href = 'login.php';
            <?php endif; ?>
        }
    </script>


    <!-- appointmenting section ends -->

    <!-- review section starts  -->

    <section class="review" id="review">

        <h1 class="heading"> client's <span>review</span> </h1>

        <div class="box-container">

            <div class="box">
                <img src="images/img-1.jpg" alt="">
                <h3>Radhika Gupta</h3>
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <p class="text">"I can't thank WeCare enough for the exceptional care I received! Dr. Ananya Sharma took the time to understand my concerns, and her expertise made all the difference in my recovery. The team was compassionate, attentive, and professional every step of the way. I felt genuinely cared for, and the results speak for themselves. Highly recommend!"</p>
            </div>

            <div class="box">
                <img src="images/img-2.jpg" alt="">
                <h3>Mohit Jain</h3>
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <p class="text">"WeCare truly lives up to its name! From the seamless booking process to the attentive follow-up after my treatment, everything was smooth and easy. Dr. Naveen Singh was knowledgeable, friendly, and really took time to explain my procedure. This kind of patient-focused care is rare. Five stars all the way!"</p>
            </div>

            <div class="box">
                <img src="images/img-3.jpg" alt="">
                <h3>Tushar Mundra</h3>
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <p class="text">"WeCare is a gem! Dr. Priya Menon and her team provided me with the most comforting experience I've ever had at a clinic. The staff was professional yet warm, and I left feeling confident about my health. I appreciate the little details they take care of- from prompt responses to truly personalized care. WeCare feels like family to me now!"</p>
            </div>

        </div>

    </section>

    <!-- review section ends -->

    <!-- blogs section starts  -->

    <section class="blogs" id="blogs">

        <h1 class="heading"> our <span>blogs</span> </h1>

        <div class="box-container">

            <div class="box">
                <div class="image">
                    <img src="images/blog-1.jpg" alt="">
                </div>
                <div class="content">
                    <div class="icon">
                        <a href="#"> <i class="fas fa-calendar"></i> 05 November, 2023 </a>
                        <a href="#"> <i class="fas fa-user"></i> by WeCare </a>
                    </div>
                    <h3>5 Simple Habits for a Healthier Life</h3>
                    <p>Discover five easy, everyday habits that can significantly improve your health and well-being. Start making these changes today for a happier, healthier life.</p>
                    <a href="#" class="btn"> learn more <span class="fas fa-chevron-right"></span> </a>
                </div>
            </div>

            <div class="box">
                <div class="image">
                    <img src="images/blog-2.jpg" alt="">
                </div>
                <div class="content">
                    <div class="icon">
                        <a href="#"> <i class="fas fa-calendar"></i> 18 October, 2023 </a>
                        <a href="#"> <i class="fas fa-user"></i> by WeCare </a>
                    </div>
                    <h3>Understanding Preventive Healthcare: Why It Matters</h3>
                    <p>Preventive care is essential for maintaining health and avoiding major health issues. Learn about regular screenings, vaccinations, and lifestyle choices that keep you healthy.</p>
                    <a href="#" class="btn"> learn more <span class="fas fa-chevron-right"></span> </a>
                </div>
            </div>

            <div class="box">
                <div class="image">
                    <img src="images/blog-3.jpg" alt="">
                </div>
                <div class="content">
                    <div class="icon">
                        <a href="#"> <i class="fas fa-calendar"></i> 30 September, 2023 </a>
                        <a href="#"> <i class="fas fa-user"></i> by WeCare </a>
                    </div>
                    <h3>Top 10 Superfoods for Boosting Immunity</h3>
                    <p>Explore these ten powerful superfoods that can strengthen your immune system and keep you resilient against illnesses all year round.</p>
                    <a href="#" class="btn"> learn more <span class="fas fa-chevron-right"></span> </a>
                </div>
            </div>

            <div class="box">
                <div class="image">
                    <img src="images/blog-4.jpg" alt="">
                </div>
                <div class="content">
                    <div class="icon">
                        <a href="#"> <i class="fas fa-calendar"></i> 15 August, 2023 </a>
                        <a href="#"> <i class="fas fa-user"></i> by WeCare </a>
                    </div>
                    <h3>How to Manage Stress for Better Health</h3>
                    <p>Stress can take a toll on your physical and mental health. Learn simple and effective ways to reduce stress and improve your quality of life.</p>
                    <a href="#" class="btn"> learn more <span class="fas fa-chevron-right"></span> </a>
                </div>
            </div>

            <div class="box">
                <div class="image">
                    <img src="images/blog-5.jpg" alt="">
                </div>
                <div class="content">
                    <div class="icon">
                        <a href="#"> <i class="fas fa-calendar"></i> 22 July, 2023 </a>
                        <a href="#"> <i class="fas fa-user"></i> by WeCare </a>
                    </div>
                    <h3>Debunking Common Health Myths</h3>
                    <p>There are many misconceptions about health and wellness. This article clears up some of the most common myths so you can make informed health choices.</p>
                    <a href="#" class="btn"> learn more <span class="fas fa-chevron-right"></span> </a>
                </div>
            </div>

            <div class="box">
                <div class="image">
                    <img src="images/blog-6.jpg" alt="">
                </div>
                <div class="content">
                    <div class="icon">
                        <a href="#"> <i class="fas fa-calendar"></i> 10 June, 2023 </a>
                        <a href="#"> <i class="fas fa-user"></i> by WeCare </a>
                    </div>
                    <h3>Benefits of Regular Exercise for All Ages</h3>
                    <p>Exercise has immense health benefits for everyone, regardless of age. Discover how staying active can boost your health at any stage of life.</p>
                    <a href="#" class="btn"> learn more <span class="fas fa-chevron-right"></span> </a>
                </div>
            </div>

        </div>

    </section>

    <!-- blogs section ends -->

    <!-- footer section starts  -->

    <section class="footer">

        <div class="box-container">
            <div class="box">
                <h3>quick links</h3>
                <a href="#home"> <i class="fas fa-chevron-right"></i> home </a>
                <a href="#about"> <i class="fas fa-chevron-right"></i> about </a>
                <a href="#services"> <i class="fas fa-chevron-right"></i> services </a>
                <a href="#doctors"> <i class="fas fa-chevron-right"></i> doctors </a>
                <a href="#appointment"> <i class="fas fa-chevron-right"></i> appointment </a>
                <a href="#review"> <i class="fas fa-chevron-right"></i> review </a>
                <a href="#blogs"> <i class="fas fa-chevron-right"></i> blogs </a>
            </div>

            <div class="box">
                <h3>our services</h3>
                <p> <i class="fas fa-chevron-right"></i> dental care </p>
                <p> <i class="fas fa-chevron-right"></i> massage therapy </p>
                <p> <i class="fas fa-chevron-right"></i> cardiology </p>
                <p> <i class="fas fa-chevron-right"></i> diagnosis </p>
                <p> <i class="fas fa-chevron-right"></i> ambulance service </p>
            </div>

            <div class="box">
                <h3>Contact info</h3>
                <p> <i class="fas fa-phone"></i> +8801688238801 </p>
                <p> <i class="fas fa-phone"></i> +8801782546978 </p>
                <p> <i class="fas fa-envelope"></i> WeCare9@gmail.com </p>
                <p> <i class="fas fa-envelope"></i> abc@gmail.com </p>
                <p> <i class="fas fa-map-marker-alt"></i> Noida, India </p>
            </div>

            <div class="box">
                <h3>follow us</h3>
                <a href="https://twitter.com" target="_blank"> <i class="fab fa-twitter"></i> Twitter </a>
                <a href="https://www.instagram.com" target="_blank"> <i class="fab fa-instagram"></i> Instagram </a>
                <a href="https://www.linkedin.com" target="_blank"> <i class="fab fa-linkedin"></i> LinkedIn </a>
                <a href="https://www.pinterest.com" target="_blank"> <i class="fab fa-pinterest"></i> Pinterest </a>
            </div>



        </div>

        <div class="credit"> created by <span>WeCare</span> | all rights reserved </div>

    </section>

    <!-- footer section ends -->


    <!-- js file link  -->
    <script src="script.js"></script>

</body>

</html>