<?php
// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'contact_db') or die('connection failed');

// Check if doctor_id is set in the GET request
if (isset($_GET['doctor_id'])) {
    $doctor_id = $_GET['doctor_id'];

    // Prepare query to fetch available slots
    $query = "SELECT slot_id, slot_time FROM doctor_slots WHERE doctor_id = ? AND is_available = 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the slots and store them in an array
    $slots = [];
    while ($row = $result->fetch_assoc()) {
        $slots[] = [
            "slot_id" => $row['slot_id'],
            "slot_time" => $row['slot_time']
        ];
    }

    // Return the slots as a JSON response
    echo json_encode(["slots" => $slots]);

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    // If doctor_id is not set in the request, return an empty array
    echo json_encode(["slots" => []]);
}
?>
