<?php
header("Content-Type: application/json");

$conn = mysqli_connect('localhost', 'root', '', 'contact_db');
if (!$conn) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Database connection failed."]);
    exit;
}

// Decode the JSON request payload
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['doctor_id'], $data['slot_id'], $data['name'], $data['email'], $data['number'], $data['date'], $data['disease'], $data['age'])) {
    $doctor_id = $data['doctor_id'];
    $slot_id = $data['slot_id'];
    $patient_name = $data['name'];
    $age = filter_var($data['age'], FILTER_VALIDATE_INT);
    $patient_email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    $patient_number = preg_match('/^\d{10}$/', $data['number']) ? $data['number'] : null;
    $appointment_date = $data['date'];
    $disease = $data['disease'];

    // Validate fields
    if (!$patient_email || !$patient_number || !$age || $age <= 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => "Invalid input data (email, phone, or age)."]);
        exit;
    }

    $conn->begin_transaction();

    try {
        // Check slot availability
        $check_query = "SELECT is_available FROM doctor_slots WHERE slot_id = ? AND doctor_id = ? AND is_available = 1";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("ii", $slot_id, $doctor_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows === 0) {
            throw new Exception("Slot is no longer available.");
        }

        // Insert the appointment
        $insert_query = "INSERT INTO contact_form (name, age, email, number, date, disease, doctor_id, slot_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("sissssii", $patient_name, $age, $patient_email, $patient_number, $appointment_date, $disease, $doctor_id, $slot_id);
        $stmt->execute();

        // Update slot availability
        $update_query = "UPDATE doctor_slots SET is_available = 0 WHERE slot_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("i", $slot_id);
        $update_stmt->execute();

        $conn->commit();
        echo json_encode(["success" => true]);
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Booking error: " . $e->getMessage());
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }

    $check_stmt->close();
    $stmt->close();
    $update_stmt->close();
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Invalid input data."]);
}

$conn->close();
?>
