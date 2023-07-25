<?php
session_start();

if (!isset($_SESSION["user_id"])) {
  header("Location: index.php");
  exit();
}

// Fetch the latest posts from the Mastodon API
$apiUrl = "https://mastodon.social/api/v1/timelines/public?limit=100";
$response = file_get_contents($apiUrl);

if ($response === false) {
  echo "Failed to fetch Mastodon posts.";
  exit();
}

$posts = json_decode($response, true);

// Fetch tweets from the database
$conn = new mysqli("localhost", "username", "pass", "db");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT t.tweet_text, t.tweet_image, u.username FROM tweets t INNER JOIN users u ON t.user_id = u.id WHERE t.deleted = 0 ORDER BY t.created_at DESC";
$result = $conn->query($sql);

// Get logged-in user information
$loggedInUserId = $_SESSION["user_id"];
$sql = "SELECT username FROM users WHERE id = $loggedInUserId";
$loggedInUserResult = $conn->query($sql);
$loggedInUser = $loggedInUserResult->fetch_assoc();

// Check if the logged-in user is banned
$sql = "SELECT banned FROM users WHERE id = $loggedInUserId";
$bannedResult = $conn->query($sql);
$bannedUser = $bannedResult->fetch_assoc();

if ($bannedUser["banned"] == 1) {
  header("Location: banned.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VKE</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.min.css">

  <style>
    .sidebar {
      background-color: #f5f8fa;
      position: sticky;
      padding: 20px;
      border-radius: 10px;
    }
    .custom-list-item {
      border: none;
      padding: 0.25rem 0;
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

    .logo {
      position: sticky;
      font-size: 24px;
      margin-bottom: 20px;
    }

    .motto {
      position: sticky;
      font-size: 10px;
      margin-bottom: 10px;
    }

    .row {
      margin-right: 0;
      margin-left: 0;
    }
  </style>
</head>
<body>
  <br><br>
  <div class="container">
    <div class="row">
      <div class="col-md-3">
        <!-- Sidebar -->
        <?php
        // Check if the logged-in user is a staff member
        $isStaff = false; // Default value

        // Retrieve the logged-in user's information from the database
        $sql = "SELECT isstaff FROM users WHERE id = $loggedInUserId";
        $userResult = $conn->query($sql);
        $user = $userResult->fetch_assoc();

        // Check if the user is a staff member
        if ($user["isstaff"] == 1) {
          $isStaff = true;
        }
        ?>
        <div class="sticky-top">
          <br>
          <!-- Sidebar -->
          <div class="sidebar">
            <div class="logo">
              <strong>VKE</strong>.app
            </div>
            <h3>Hello, @<?php echo $loggedInUser["username"]; ?></h3>
            <hr>
            <h5>More:</h5>
            <ul>
              <a href="new.php"><li>New Posts</li></a>
              <a href="fedi.php"><li>Federated Posts</li></a>
              <a href="https://shortr.zip/"><li>URL Shortener</li></a>
              <li><a href="https://web.vke.app/profile.php/<?php echo $loggedInUser["username"]; ?>">Profile</a></li>
              <a href="https://web.vke.app/shows.php"><li>VKE Shows <span class="badge badge-info">Beta Feature</span></li></a>
              <?php if ($isStaff) { ?>
                <li><a href="panel.php">Staff Panel</a></li>
              <?php } ?>
            </ul>
            <br>
            <p>&copy; 2023 Mapler Services operating as VKE</p>
          </div>
          <br>
        </div>
      </div>
      <div class="col-md-9">
        <!-- Main Content -->
        <h1>Federated Posts</h1>
        <p>This page uses the Mastodon network to load posts, which results in posts that are written in different languages. We are working to add a built in translator in the future.</p>

        <!-- Display tweets -->
        <?php foreach ($posts as $post) { ?>
          <div class="card tweet-card">
            <div class="card-title">
              <a href="#"><?php echo '@' . $post['account']['username']; ?></a>
            </div>
            <div class="card-text"><?php echo $post['content']; ?></div>
          </div>
        <?php } ?>
        <p>Refresh the page! More posts have been made since you last loaded this page.</p>

        <!-- JavaScript code for Bootbox.js alerts -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js"></script>
      </div>
    </div>
  </div>
</body>
</html>
