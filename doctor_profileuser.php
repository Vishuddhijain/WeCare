<?php
// Start session
session_start();

// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'contact_db') or die('Connection failed');

// Check if the user is logged in
$loggedIn = isset($_SESSION['patient_name']) && isset($_SESSION['patient_number']);

// Fetch doctor details from the database based on the doctor ID in the query parameter
$doctorDetails = [];
if (isset($_GET['doctor'])) {
    $doctorId = intval($_GET['doctor']);
    $query = "SELECT * FROM doctors WHERE id = $doctorId";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $doctorDetails = mysqli_fetch_assoc($result);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Doctor Profile</title>
  <link rel="stylesheet" href="styles1.css" />
</head>

<body>
  <div class="container">
    <div class="doctor-profile">
      <?php if (!empty($doctorDetails)): ?>
        <img id="doctor-image" src="<?php echo htmlspecialchars($doctorDetails['image']); ?>" alt="Doctor Photo" />
        <h1 id="doctor-name"><?php echo htmlspecialchars($doctorDetails['name']); ?></h1>
        <p id="doctor-specialty"><?php echo htmlspecialchars($doctorDetails['specialization']); ?></p>
        <p id="doctor-experience">Experience: <?php echo htmlspecialchars($doctorDetails['experience']); ?></p>
        <p id="doctor-e">Email: <?php echo htmlspecialchars($doctorDetails['email']); ?></p>
        <p id="doctor-e">Fees: <?php echo htmlspecialchars($doctorDetails['fees']); ?></p>

        <h2>Available Time Slots</h2>
        <div class="time-slots" id="time-slots">
          <!-- Time slots will be dynamically inserted here -->
        </div>
      <?php else: ?>
        <h1>Doctor not found.</h1>
      <?php endif; ?>
    </div>

    <?php if ($loggedIn): ?>
      <h2>Leave a Review</h2>
      <form id="review-form">
        <input type="hidden" id="patient-name" value="<?php echo htmlspecialchars($_SESSION['patient_name']); ?>" />
        <textarea id="review-text" placeholder="Write your review here..." required></textarea>
        <label for="rating">Rating:</label>
        <select id="rating" required>
          <option value="1">1</option>
          <option value="2">2</option>
          <option value="3">3</option>
          <option value="4">4</option>
          <option value="5">5</option>
        </select>
        <button type="button" onclick="submitReview()">Submit Review</button>
      </form>

      <h2>Reviews</h2>
      <div id="reviews-container">
        <!-- Reviews will be dynamically loaded here -->
      </div>
    <?php else: ?>
      <p>Please <a href="login.php">log in</a> to view and submit reviews.</p>
    <?php endif; ?>
  </div>

  <script>
    const doctorId = <?php echo isset($_GET['doctor']) ? intval($_GET['doctor']) : 'null'; ?>;

    if (doctorId) {
      fetch(`get_slots.php?doctor_id=${doctorId}`)
        .then((response) => response.json())
        .then((data) => {
          const timeSlotsContainer = document.getElementById("time-slots");
          timeSlotsContainer.innerHTML = ""; // Clear existing slots
          data.slots.forEach((slot) => {
            const slotButton = document.createElement("button");
            slotButton.textContent = slot.slot_time; // Use slot directly
            slotButton.classList.add("slot", "available");
            timeSlotsContainer.appendChild(slotButton);
          });
        })
        .catch((error) => console.error("Error fetching slots:", error));
    } else {
      alert("No doctor specified in the URL.");
    }


    function fetchReviews(doctorId) {
      fetch(`get_reviews.php?doctor_id=${doctorId}`)
        .then((response) => response.json())
        .then((data) => {
          const reviewsContainer =
            document.getElementById("reviews-container");
          reviewsContainer.innerHTML = ""; // Clear existing reviews
          data.reviews.forEach((review) => {
            const reviewElement = document.createElement("div");
            reviewElement.classList.add("review");
            reviewElement.innerHTML = `
                    <strong>${review.patient_name}</strong> 
                    <span>(Rating: ${review.rating})</span>
                    <p>${review.review}</p>
                    <small>${new Date(
                      review.review_date
                    ).toLocaleDateString()}</small>
                `;
            reviewsContainer.appendChild(reviewElement);
          });
        })
        .catch((error) => console.error("Error fetching reviews:", error));
    }

    function submitReview() {
      const patientName = document.getElementById("patient-name").value;
      const reviewText = document.getElementById("review-text").value;
      const rating = document.getElementById("rating").value;

      fetch("submit_review.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({
            doctor_id: doctorId,
            patient_name: patientName,
            review: reviewText,
            rating: parseInt(rating),
          }),
        })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            alert("Review submitted successfully!");
            fetchReviews(doctorId); // Reload reviews
          } else {
            alert("Failed to submit review.");
          }
        })
        .catch((error) => console.error("Error submitting review:", error));
    }

    // Fetch reviews on page load
    if (doctorId && <?php echo json_encode($loggedIn); ?>) {
      fetchReviews(doctorId);
    }
  </script>
</body>

</html>
