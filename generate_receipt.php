<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Receipt</title>
    <style>
        /* General body styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }

        /* Center the receipt container */
        .receipt-table {
            width: 70%;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            box-sizing: border-box;
        }

        /* Title styles */
        h1 {
            text-align: center;
            color: #0056b3;
            font-size: 24px;
            margin-bottom: 30px;
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        /* Table header */
        th {
            background-color: #0056b3;
            color: white;
            padding: 12px 15px;
            text-align: left;
        }

        /* Table cell styling */
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
        }

        /* Styling for the strong labels */
        td strong {
            color: #0056b3;
        }

        /* Thank you message styling */
        .thank-you {
            text-align: center;
            font-size: 16px;
            font-style: italic;
            color: #555;
            margin-top: 30px;
        }

        /* Download buttons */
        .download-btn {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #16a085;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            width: 200px;
        }

        .download-btn:hover {
            background-color: #33d9b2;
        }

        /* Responsive design */
        @media screen and (max-width: 768px) {
            .receipt-table {
                width: 90%;
            }

            h1 {
                font-size: 20px;
            }

            table {
                font-size: 14px;
            }

            td, th {
                padding: 8px;
            }
        }
    </style>
</head>

<body>
<?php
    // Enable error reporting for debugging
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'contact_db');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get appointment ID from URL
    if (!isset($_GET['appointment_id']) || !is_numeric($_GET['appointment_id'])) {
        die("Invalid appointment ID.");
    }
    $appointment_id = $_GET['appointment_id'];

    // Fetch appointment and doctor details
    $query = "SELECT c.*, d.name AS doctor_name, d.specialization, d.fees, ds.slot_time
              FROM contact_form c
              JOIN doctors d ON c.doctor_id = d.id
              JOIN doctor_slots ds ON c.slot_id = ds.slot_id
              WHERE c.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("Appointment not found.");
    }
    $data = $result->fetch_assoc();

    // Create HTML content for receipt
    $receiptHtml = "<div class='receipt-table'>
                        <h1>Appointment Receipt</h1>
                        <table>
                            <tr><td><strong>Patient Name</strong></td><td>" . htmlspecialchars($data['name']) . "</td></tr>
                            <tr><td><strong>Patient Age</strong></td><td>" . htmlspecialchars($data['age']) . "</td></tr>
                            <tr><td><strong>Doctor Name</strong></td><td>" . htmlspecialchars($data['doctor_name']) . "</td></tr>
                            <tr><td><strong>Specialization</strong></td><td>" . htmlspecialchars($data['specialization']) . "</td></tr>
                            <tr><td><strong>Appointment Date</strong></td><td>" . htmlspecialchars($data['date']) . "</td></tr>
                            <tr><td><strong>Slot Time</strong></td><td>" . htmlspecialchars($data['slot_time']) . "</td></tr>
                            <tr><td><strong>Fees</strong></td><td>₹" . htmlspecialchars($data['fees']) . "</td></tr>
                        </table>
                        <p class='thank-you'><i>Thank you for choosing our website!</i></p>
                    </div>";

    // Display the appointment details
    echo $receiptHtml;
    ?>

    <!-- Download HTML File Button -->
    <a href="javascript:void(0);" id="download-html" class="download-btn">Download receipt</a>

    <script>
        // Download as HTML File
        document.getElementById('download-html').addEventListener('click', function () {
            const receiptHtml = `<?php echo addslashes($receiptHtml); ?>`;  // PHP-generated HTML for download

            // Create a Blob with the HTML content
            const blob = new Blob([receiptHtml], { type: 'text/html' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'appointment_receipt.html';  // Filename to download
            link.click();  // Trigger the download
        });
    </script>

</body>
</html>