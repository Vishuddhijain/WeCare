<?php
$conn = mysqli_connect('localhost', 'root', '', 'contact_db') or die('Connection failed');

$data = json_decode(file_get_contents("php://input"), true);
$doctor_id = intval($data['doctor_id']);
$patient_name = mysqli_real_escape_string($conn, $data['patient_name']);
$review = mysqli_real_escape_string($conn, $data['review']);
$rating = intval($data['rating']);

$query = "INSERT INTO doctor_reviews (doctor_id, patient_name, review, rating) 
          VALUES ($doctor_id, '$patient_name', '$review', $rating)";
$result = mysqli_query($conn, $query);

echo json_encode(['success' => $result]);
?>
