<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();

$conn = new mysqli("localhost", "user", "pass", "db");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in and is an admin
$username = $_SESSION["username"];
$sqlCheckAdmin = "SELECT isstaff FROM users WHERE username = '$username' AND isstaff = 1";
$resultCheckAdmin = $conn->query($sqlCheckAdmin);
if ($resultCheckAdmin->num_rows === 0) {
  header("Location: tweets.php");
  exit();
}

// Delete tweet
if (isset($_POST["delete_tweet"])) {
  $tweetId = $_POST["tweet_id"];
  $sql = "UPDATE tweets SET deleted = 1 WHERE id = $tweetId";
  $conn->query($sql);
  header("Location: panel.php");
  exit();
}

// Undo delete tweet
if (isset($_POST["undo_delete"])) {
  $tweetId = $_POST["tweet_id"];
  $sql = "UPDATE tweets SET deleted = 0 WHERE id = $tweetId";
  $conn->query($sql);
  header("Location: panel.php");
  exit();
}

// Ban user
if (isset($_POST["ban_user"])) {
  $userId = $_POST["user_id"];
  $sql = "UPDATE users SET banned = 1 WHERE id = $userId";
  $conn->query($sql);
  header("Location: panel.php");
  exit();
}

// Unban user
if (isset($_POST["unban_user"])) {
  $userId = $_POST["user_id"];
  $sql = "UPDATE users SET banned = 0 WHERE id = $userId";
  $conn->query($sql);
  header("Location: panel.php");
  exit();
}

// Fetch thoughts
$searchTweets = isset($_GET['search_tweets']) ? $_GET['search_tweets'] : '';
$sql = "SELECT tweets.*, users.username FROM tweets JOIN users ON tweets.user_id = users.id WHERE tweets.deleted = 0 AND tweets.tweet_text LIKE '%$searchTweets%'";
$result = $conn->query($sql);

// Fetch deleted thoughts
$searchDeletedTweets = isset($_GET['search_deleted_tweets']) ? $_GET['search_deleted_tweets'] : '';
$sqlDeleted = "SELECT tweets.*, users.username FROM tweets JOIN users ON tweets.user_id = users.id WHERE tweets.deleted = 1 AND tweets.tweet_text LIKE '%$searchDeletedTweets%'";
$resultDeleted = $conn->query($sqlDeleted);

// Fetch users
$searchUsers = isset($_GET['search_users']) ? $_GET['search_users'] : '';
$sqlUsers = "SELECT * FROM users WHERE banned = 0 AND username LIKE '%$searchUsers%'";
$resultUsers = $conn->query($sqlUsers);

// Fetch banned users
$searchBannedUsers = isset($_GET['search_banned_users']) ? $_GET['search_banned_users'] : '';
$sqlBannedUsers = "SELECT * FROM users WHERE banned = 1 AND username LIKE '%$searchBannedUsers%'";
$resultBannedUsers = $conn->query($sqlBannedUsers);

// Fetch stats
$sqlStats = "SELECT COUNT(*) AS total_tweets, SUM(deleted = 1) AS total_deleted, SUM(banned = 1) AS total_banned FROM tweets";
$resultStats = $conn->query($sqlStats);
$stats = $resultStats->fetch_assoc();
$totalTweets = $stats['total_tweets'];
$totalDeletedTweets = $stats['total_deleted'];
$totalBannedUsers = $stats['total_banned'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff Panel</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <style>
    body {
      padding-top: 20px;
    }

    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      width: 250px;
      background-color: #333;
      color: #fff;
      padding-top: 20px;
    }

    .sidebar h4 {
      text-align: center;
      margin-bottom: 30px;
    }

    .sidebar ul {
      list-style: none;
      padding-left: 0;
    }

    .sidebar ul li {
      margin-bottom: 10px;
    }

    .sidebar ul li a {
      color: #fff;
      text-decoration: none;
      display: flex;
      align-items: center;
    }

    .sidebar ul li a i {
      margin-right: 10px;
    }

    .content {
      margin-left: 250px;
      padding: 20px;
    }

    .search-form {
      margin-bottom: 20px;
    }

    .card-text {
      margin-bottom: 0;
    }

    .btn-delete {
      background-color: #dc3545;
      border-color: #dc3545;
    }

    .btn-undo-delete {
      background-color: #28a745;
      border-color: #28a745;
    }

    .btn-ban {
      background-color: #dc3545;
      border-color: #dc3545;
    }

    .btn-unban {
      background-color: #28a745;
      border-color: #28a745;
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <h4>Welcome, <?php echo $username; ?></h4>
    <ul>
      <li><a href="#thoughts"><i class="fas fa-comments"></i> Thoughts</a></li>
      <li><a href="#deleted-thoughts"><i class="fas fa-trash-alt"></i> Deleted Thoughts</a></li>
      <li><a href="#users"><i class="fas fa-users"></i> Users</a></li>
      <li><a href="#banned-users"><i class="fas fa-user-lock"></i> Banned Users</a></li>
      <li><a href="#stats"><i class="fas fa-chart-bar"></i> Stats</a></li>
    </ul>
  </div>

  <div class="content">
    <div id="thoughts">
      <h2>Thoughts</h2>
      <form class="search-form" method="GET">
        <div class="input-group">
          <input type="text" class="form-control" placeholder="Search for tweets" name="search_tweets">
          <div class="input-group-append">
            <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
          </div>
        </div>
      </form>
      <?php while ($row = $result->fetch_assoc()) { ?>
        <div class="card mb-3">
          <div class="card-body">
            <h5 class="card-title">Tweet ID: <?php echo $row["id"]; ?></h5>
            <p class="card-text"><?php echo $row["tweet_text"]; ?></p>
            <p class="card-text"><small class="text-muted">Posted by: <?php echo $row["username"]; ?></small></p>
            <form action="panel.php" method="POST">
              <input type="hidden" name="tweet_id" value="<?php echo $row["id"]; ?>">
              <button type="submit" class="btn btn-delete" name="delete_tweet"><i class="fas fa-trash-alt"></i> Delete</button>
            </form>
          </div>
        </div>
      <?php } ?>
    </div>

    <div id="deleted-thoughts">
      <h2>Deleted Thoughts</h2>
      <form class="search-form" method="GET">
        <div class="input-group">
          <input type="text" class="form-control" placeholder="Search for deleted tweets" name="search_deleted_tweets">
          <div class="input-group-append">
            <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
          </div>
        </div>
      </form>
      <?php while ($rowDeleted = $resultDeleted->fetch_assoc()) { ?>
        <div class="card mb-3">
          <div class="card-body">
            <h5 class="card-title">Tweet ID: <?php echo $rowDeleted["id"]; ?></h5>
            <p class="card-text"><?php echo $rowDeleted["tweet_text"]; ?></p>
            <p class="card-text"><small class="text-muted">Posted by: <?php echo $rowDeleted["username"]; ?></small></p>
            <form action="panel.php" method="POST">
              <input type="hidden" name="tweet_id" value="<?php echo $rowDeleted["id"]; ?>">
              <button type="submit" class="btn btn-undo-delete" name="undo_delete"><i class="fas fa-undo"></i> Undo Delete</button>
            </form>
          </div>
        </div>
      <?php } ?>
    </div>

    <div id="users">
      <h2>Users</h2>
      <form class="search-form" method="GET">
        <div class="input-group">
          <input type="text" class="form-control" placeholder="Search for users" name="search_users">
          <div class="input-group-append">
            <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
          </div>
        </div>
      </form>
      <?php while ($rowUser = $resultUsers->fetch_assoc()) { ?>
        <div class="card mb-3">
          <div class="card-body">
            <h5 class="card-title">User ID: <?php echo $rowUser["id"]; ?></h5>
            <p class="card-text"><?php echo $rowUser["username"]; ?></p>
            <form action="panel.php" method="POST">
              <input type="hidden" name="user_id" value="<?php echo $rowUser["id"]; ?>">
              <button type="submit" class="btn btn-ban" name="ban_user"><i class="fas fa-user-lock"></i> Ban User</button>
            </form>
          </div>
        </div>
      <?php } ?>
    </div>

    <div id="banned-users">
      <h2>Banned Users</h2>
      <form class="search-form" method="GET">
        <div class="input-group">
          <input type="text" class="form-control" placeholder="Search for banned users" name="search_banned_users">
          <div class="input-group-append">
            <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
          </div>
        </div>
      </form>
      <?php while ($rowBannedUser = $resultBannedUsers->fetch_assoc()) { ?>
        <div class="card mb-3">
          <div class="card-body">
            <h5 class="card-title">User ID: <?php echo $rowBannedUser["id"]; ?></h5>
            <p class="card-text"><?php echo $rowBannedUser["username"]; ?></p>
            <form action="panel.php" method="POST">
              <input type="hidden" name="user_id" value="<?php echo $rowBannedUser["id"]; ?>">
              <button type="submit" class="btn btn-unban" name="unban_user"><i class="fas fa-user-lock-open"></i> Unban User</button>
            </form>
          </div>
        </div>
      <?php } ?>
    </div>

    <div id="stats">
      <h2>Stats</h2>
      <p>Total Tweets: <?php echo $totalTweets; ?></p>
      <p>Total Deleted Tweets: <?php echo $totalDeletedTweets; ?></p>
      <p>Total Banned Users: <?php echo $totalBannedUsers; ?></p>
    </div>
  </div>
</body>
</html>