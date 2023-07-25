<!-- tweets.php -->
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
  header("Location: index.php");
  exit();
}

// Fetch tweets from the database
$conn = new mysqli("localhost", "user", "pass", "db");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT t.tweet_text, u.username FROM tweets t INNER JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC";
$result = $conn->query($sql);

// Get logged-in user information
$loggedInUserId = $_SESSION["user_id"];
$sql = "SELECT username FROM users WHERE id = $loggedInUserId";
$loggedInUserResult = $conn->query($sql);
$loggedInUser = $loggedInUserResult->fetch_assoc();

// Display tweets
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thoughts</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.min.css">
  
  <style>
    .sidebar {
      background-color: #f5f8fa;
      padding: 20px;
      border-radius: 10px;
    }

    .tweet-card {
      margin-bottom: 20px;
      padding: 15px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .tweet-card .card-title {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
    }

    .tweet-card .card-title a {
      font-weight: bold;
      color: #333;
      text-decoration: none;
      margin-right: 5px;
    }

    .tweet-card .card-title i {
      font-size: 14px;
      color: #007bff;
    }

    .tweet-card .card-text {
      color: #555;
    }
  </style>
</head>
<body>
  <br><br>
  <div class="container">
    <div class="row">
      <div class="col-md-3">
        <!-- Sidebar -->
        <div class="sidebar">
          <h5>Logged-in as:</h5>
          <p>@<?php echo $loggedInUser["username"]; ?></p>
          <hr>
          <h5>More:</h5>
          <ul>
            <a href="tweets.php"><li>Explore</li></a>
            <li><a href="profile.php?username=<?php echo $loggedInUser["username"]; ?>">Profile</a></li>
          </ul>
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tweetModal">
            Compose Thought
          </button>
        </div>
      </div>
      <div class="col-md-9">
        <!-- Main Content -->
        <h1>Thoughts</h1>

        <!-- Tweet modal -->
        <div class="modal fade" id="tweetModal" tabindex="-1" role="dialog" aria-labelledby="tweetModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="tweetModalLabel">Compose Thought</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form action="tweet.php" method="POST">
                  <div class="form-group">
                    <textarea class="form-control" name="tweet_text" rows="3" required></textarea>
                  </div>
                  <button type="submit" class="btn btn-primary">Think it!</button>
                </form>
              </div>
            </div>
          </div>
        </div>
<!-- Display tweets -->
<?php while ($row = $result->fetch_assoc()) { ?>
  <div class="card tweet-card">
    <?php
    // Array of usernames to be verified
    $verifiedUsers = ["thoughts", "liam"];
    $ogUsers = ["1"];
    $staff = ["liam"];
    ?>

    <!-- Inside the loop to display user profiles -->
    <div class="card-title">
      <a href="profile.php?username=<?php echo $row["username"]; ?>">@<?php echo $row["username"]; ?></a>
      <?php if (in_array($row["username"], $verifiedUsers)) { ?>
        <i style="color: blue;" class="fas fa-check-circle ml-1" onclick="showVerifiedUserAlert()"></i>
      <?php } ?>
      <?php if (in_array($row["username"], $ogUsers)) { ?>
        <i style="color: gold;" class="fas fa-check-circle ml-1" onclick="showOGUserAlert()"></i>
      <?php } ?>
      <?php if (in_array($row["username"], $staff)) { ?>
        <i style="color: purple;" class="fas fa-hammer ml-1" onclick="showStaffAlert()"></i>
      <?php } ?>
    </div>
    <div class="card-text"><?php echo $row["tweet_text"]; ?></div>
  </div>
<?php } ?>

<!-- JavaScript code for Bootbox.js alerts -->
<script>
  function showVerifiedUserAlert() {
    bootbox.alert({
      message: '<i class="fas fa-check-circle fa-4x" style="color: blue;"></i><h4 class="mt-3">Verified User</h4><p>User is verified as they could be subscribed to Thoughts+ or a notable figure.</p>',
      size: 'large'
    });
  }

  function showOGUserAlert() {
    bootbox.alert({
      message: '<i class="fas fa-check-circle fa-4x" style="color: gold;"></i><h4 class="mt-3">OG User</h4><p>User is verified as their username is OG.</p>',
      size: 'large'
    });
  }

  function showStaffAlert() {
    bootbox.alert({
      message: '<i class="fas fa-hammer fa-4x" style="color: purple;"></i><h4 class="mt-3">Staff</h4><p>User is a staff member at Thoughts.</p>',
      size: 'large'
    });
  }
</script>

      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.min.js"></script>
</body>
</html>
