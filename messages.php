<?php
// messages.php

// Start the session and check if the user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

// Include the database connection
$conn = new mysqli("localhost", "user", "pass", "db"); // Replace with your database credentials

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the logged-in user's ID
$loggedInUserID = $_SESSION['user_id'];

// Get the list of people the user has messaged
$people = getMessagedPeople($loggedInUserID);

// Get the selected person's ID from the query string if available
$selectedPersonID = null;
if (isset($_GET['person_id'])) {
  $selectedPersonID = $_GET['person_id'];
}

// Get the messages between the logged-in user and the selected person
$messages = [];
if ($selectedPersonID) {
  $messages = getMessages($loggedInUserID, $selectedPersonID);
  markMessagesAsRead($loggedInUserID, $selectedPersonID);
}

// Function to retrieve the list of people the user has messaged
function getMessagedPeople($userID) {
  global $conn;

  $sql = "SELECT DISTINCT sender_id, receiver_id FROM messages WHERE sender_id = '$userID' OR receiver_id = '$userID'";
  $result = $conn->query($sql);

  $people = [];
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $personID = $row['sender_id'] != $userID ? $row['sender_id'] : $row['receiver_id'];
      $people[] = getUser($personID);
    }
  }

  return $people;
}

// Function to retrieve a user by ID
function getUser($userID) {
  global $conn;

  $sql = "SELECT * FROM users WHERE id = '$userID'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    return $result->fetch_assoc();
  }

  return null;
}

// Function to retrieve the messages between two users
function getMessages($senderID, $receiverID) {
  global $conn;

  $sql = "SELECT * FROM messages WHERE (sender_id = '$senderID' AND receiver_id = '$receiverID') OR (sender_id = '$receiverID' AND receiver_id = '$senderID') ORDER BY created_at ASC";
  $result = $conn->query($sql);

  $messages = [];
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $messages[] = $row;
    }
  }

  return $messages;
}

// Function to mark messages as read
function markMessagesAsRead($senderID, $receiverID) {
  global $conn;

  $sql = "UPDATE messages SET is_read = 1 WHERE sender_id = '$senderID' AND receiver_id = '$receiverID' AND is_read = 0";
  $conn->query($sql);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Messages</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- Add your own custom stylesheets or scripts here -->
</head>
<body>
  <div class="container">
    <div class="row">
      <div class="col-md-3">
        <!-- Sidebar -->
        <h4>People You Have Messaged</h4>
        <ul class="list-group">
          <?php foreach ($people as $person) { ?>
            <li class="list-group-item <?php echo ($person['id'] == $selectedPersonID) ? 'active' : ''; ?>">
              <a href="messages.php?person_id=<?php echo $person['id']; ?>"><?php echo $person['username']; ?></a>
            </li>
          <?php } ?>
        </ul>
      </div>
      <div class="col-md-9">
        <!-- Messages -->
        <?php if ($selectedPersonID) { ?>
          <h4>Messages with <?php echo $people[$selectedPersonID]['username']; ?></h4>
          <?php if (count($messages) > 0) { ?>
            <ul class="list-group">
              <?php foreach ($messages as $message) { ?>
                <li class="list-group-item">
                  <strong><?php echo ($message['sender_id'] == $loggedInUserID) ? 'You' : $people[$message['sender_id']]['username']; ?>:</strong>
                  <?php echo $message['content']; ?>
                </li>
              <?php } ?>
            </ul>
          <?php } else { ?>
            <p>No messages yet.</p>
          <?php } ?>
          <!-- Message form -->
          <form method="POST" action="message.php">
            <div class="form-group">
              <input type="hidden" name="receiver_id" value="<?php echo $selectedPersonID; ?>">
              <textarea class="form-control" name="message" rows="5" placeholder="Type your message" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Message</button>
          </form>
        <?php } else { ?>
          <p>Select a person to start a conversation.</p>
        <?php } ?>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <!-- Add your own custom scripts here -->
</body>
</html>
