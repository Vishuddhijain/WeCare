<?php
header("Content-Type: application/json");

$conn = mysqli_connect('localhost', 'root', '', 'contact_db');

if (!$conn) {
    echo json_encode(["success" => false, "error" => "Database connection failed"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

// Log the incoming data to verify it is as expected
error_log(print_r($data, true));

if (isset($data['id']) && is_numeric($data['id'])) {
    $appointment_id = intval($data['id']);

    // Log the ID to verify it's correct
    error_log("Appointment ID: " . $appointment_id);

    $delete_query = "DELETE FROM contact_form WHERE id = ?";
    $stmt = $conn->prepare($delete_query);

    if ($stmt) {
        $stmt->bind_param("i", $appointment_id);
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "Failed to delete appointment in the database"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "Failed to prepare delete query"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Invalid appointment ID"]);
}

$conn->close();
?>
