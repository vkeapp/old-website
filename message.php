<?php
// message.php

// Start the session and check if the user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

// Include the database connection
$conn = new mysqli("localhost", "user", "pass", "db"); // Replace with your database credentials

// Check if the database connection was successful
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get the logged-in user's ID
$loggedInUserID = $_SESSION['user_id'];

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Get the submitted form data
  $receiverID = $_POST['receiver_id'];
  $message = $_POST['message'];

  // Send the message
  $insertSql = "INSERT INTO messages (sender_id, receiver_id, content, created_at) VALUES ('$loggedInUserID', '$receiverID', '$message', NOW())";
  if ($conn->query($insertSql) === TRUE) {
    // Redirect back to the messages page
    header("Location: messages.php?person_id=$receiverID");
    exit();
  } else {
    echo "Error: " . $insertSql . "<br>" . $conn->error;
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Send Message</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- Add your own custom stylesheets or scripts here -->
</head>
<body>
  <div class="container">
    <div class="row">
      <div class="col-md-6 mx-auto">
        <h4>Send Message</h4>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
          <div class="form-group">
            <input type="hidden" name="receiver_id" value="<?php echo $_GET['person_id']; ?>">
            <textarea class="form-control" name="message" rows="5" placeholder="Type your message" required></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Send Message</button>
        </form>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <!-- Add your own custom scripts here -->
</body>
</html>
