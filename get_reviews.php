<?php
$conn = mysqli_connect('localhost', 'root', '', 'contact_db') or die('Connection failed');

$doctor_id = intval($_GET['doctor_id']);
$query = "SELECT * FROM doctor_reviews WHERE doctor_id = $doctor_id ORDER BY review_date DESC";
$result = mysqli_query($conn, $query);

$reviews = [];
while ($row = mysqli_fetch_assoc($result)) {
    $reviews[] = $row;
}

echo json_encode(['reviews' => $reviews]);
?>
