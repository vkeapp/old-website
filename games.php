<?php
session_start();

if (!isset($_SESSION["user_id"])) {
  header("Location: index.php");
  exit();
}

// Get logged-in user information
$loggedInUserId = $_SESSION["user_id"];
$conn = new mysqli("localhost", "user", "pass", "db");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT username FROM users WHERE id = $loggedInUserId";
$loggedInUserResult = $conn->query($sql);
$loggedInUser = $loggedInUserResult->fetch_assoc();

$isStaff = false;
$sql = "SELECT isstaff FROM users WHERE id = $loggedInUserId";
$userResult = $conn->query($sql);
$user = $userResult->fetch_assoc();

if ($user["isstaff"] == 1) {
  $isStaff = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VKE Games</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

  <style>
    .sidebar {
      background-color: #f5f8fa;
      position: sticky;
      padding: 20px;
      border-radius: 10px;
    }

    .game-icon {
      width: 100px;
      height: 100px;
      margin: 10px;
      background-color: #eee;
      border-radius: 5px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
    }

    .game-icon i {
      font-size: 48px;
    }

    .game-container {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.8);
      z-index: 9999;
      overflow: hidden;
    }

    .game-container iframe {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 80%;
      height: 80%;
      border: none;
    }
  </style>
</head>
<body>
  <br><br>
  <div class="container">
    <div class="row">
      <div class="col-md-3">
        <!-- Sidebar -->
        <div class="sticky-top">
          <br>
          <div class="sidebar">
            <div class="logo">
              <strong>VKE</strong>.app
            </div>
            <h3>Hello, @<?php echo $loggedInUser["username"]; ?></h3>
            <hr>
            <h5>More:</h5>
            <ul>
              <a href="new.php"><li>New Posts</li></a>
              <a href="https://shortr.zip/"><li>URL Shortener</li></a>
              <li><a href="https://web.vke.app/profile.php/<?php echo $loggedInUser["username"]; ?>">Profile</a></li>
              <a href="https://web.vke.app/shows.php"><li>VKE Shows <span class="badge badge-info">Beta Feature</span></li></a>
              <?php if ($isStaff) { ?>
                <li><a href="panel.php">Staff Panel</a></li>
              <?php } ?>
            </ul>
            <br>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tweetModal">
              <i class="fas fa-feather"></i> Compose
            </button>
            <br><br>
            <p>&copy; 2023 Mapler Services operating as VKE</p>
          </div>
          <br>
        </div>
      </div>
      <div class="col-md-9">
        <!-- Main Content -->
        <h1>Games</h1>
        <div class="row">
          <div class="col-md-4">
            <div class="game-icon" onclick="openGame('https://example.com/game1')">
              <i class="fas fa-gamepad"></i>
            </div>
          </div>
          <div class="col-md-4">
            <div class="game-icon" onclick="openGame('https://example.com/game2')">
              <i class="fas fa-gamepad"></i>
            </div>
          </div>
          <div class="col-md-4">
            <div class="game-icon" onclick="openGame('https://example.com/game3')">
              <i class="fas fa-gamepad"></i>
            </div>
          </div>
          <!-- Add more game icons here -->
        </div>
      </div>
    </div>
  </div>

  <!-- Game Container -->
  <div class="game-container" id="gameContainer">
    <iframe src="" frameborder="0"></iframe>
  </div>

  <!-- Tweet modal -->
  <div class="modal fade" id="tweetModal" tabindex="-1" role="dialog" aria-labelledby="tweetModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tweetModalLabel">Compose a post</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="new.php" method="POST">
            <div class="form-group">
              <textarea class="form-control" name="tweet_text" rows="3" required></textarea>
            </div>
            <div class="form-group">
              <label for="tweet_image">Image Link (optional): </label>
              <input type="text" class="form-control" name="tweet_image" id="tweet_image">
            </div>
            <button type="submit" class="btn btn-primary">Compose!</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>