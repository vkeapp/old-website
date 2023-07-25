<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

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

$sql = "SELECT t.tweet_text, t.tweet_image, t.created_at, u.username, u.displayname FROM tweets t INNER JOIN users u ON t.user_id = u.id WHERE t.deleted = 0 ORDER BY t.created_at DESC";
$result = $conn->query($sql);

// Get logged-in user information
$loggedInUserId = $_SESSION["user_id"];
$sql = "SELECT username, displayname FROM users WHERE id = $loggedInUserId";
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

// Insert a new tweet
if (isset($_POST["tweet_text"])) {
  $tweetText = $_POST["tweet_text"];
  $tweetImage = $_POST["tweet_image"]; // Retrieve the image link

  // Insert the tweet into the database
  $sql = "INSERT INTO tweets (tweet_text, tweet_image, user_id, created_at) VALUES ('$tweetText', '$tweetImage', $loggedInUserId, NOW())";
  $conn->query($sql);

  header("Location: new.php"); // Redirect to the tweets page
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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

  <style>
    .sidebar {
      background-color: #f5f8fa;
      position: sticky;
      padding: 20px;
      border-radius: 10px;
    }

    .tweet-card {
      margin-bottom: 20px;
      padding: 15px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    
    .user-image {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      object-fit: cover;
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
  <div class="sidebar">
    <div class="logo">
      <img src="banner.png" alt="VKE Banner" width="65" height="65" style="margin-bottom: 10px;">
    </div>
    <h3>Hello, @<?php echo $loggedInUser["username"]; ?></h3>
    <hr>
      <a href="new.php">New Posts</a>
      <br>
      <a href="fedi.php">Federated Posts</a>
      <br>
      <a href="https://web.vke.app/profile.php/<?php echo $loggedInUser["username"]; ?>">Profile</a>
      <br>
      <a href="https://web.vke.app/shows.php">VKE Shows <span class="badge badge-info">Beta Feature</span></a>
      <br><br>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tweetModal">
      <i class="fas fa-feather"></i> Compose
    </button>
    <br>  <br>
    <p>&copy; 2023 VKE Platforms & Apps</p>
  </div>
  <br>
</div>
</div>

      <div class="col-md-9">
        <!-- Main Content -->
        <h1>Posts</h1>
                <form action="new.php" method="POST">
                  <div class="form-group">
                    <textarea class="form-control" name="tweet_text" placeholder="What's going on, <?php echo $loggedInUser["displayname"]; ?>?" rows="3" required></textarea>
                  </div>
                  <button type="submit" class="btn btn-primary">Submit Post</button>
                </form>
<br>
<div class="container">
  <ul class="nav nav-tabs">
    <li class="active"><a href="#">Feed</a></li>
    <li><a href="#">Following</a></li>
    <li><a href="#">Photos</a></li>
  </ul>
  <br>
</div>
        <!-- Plus modal -->
        <div class="modal fade" id="plusModal" tabindex="-1" role="dialog" aria-labelledby="plusModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="plusModalLabel">VKE Verified</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <h4>Verified <i style="color: blue;" class="fas fa-check-circle ml-1"></i></h4>
                <p>VKE Verified is a game-changing subscription model that redefines social media engagement.</p>
                <ul class="list-group">
                  <li class="list-group-item"><i style="color: blue;" class="fas fa-check-circle mr-2"></i> Verification (Once approved)</li>
                  <li class="list-group-item"><i class="fas fa-envelope mr-2"></i> Custom Email (vke.me)</li>
                  <li class="list-group-item"><i class="fas fa-chart-line mr-2"></i> Get Your Posts Recommended More</li>
                  <li class="list-group-item"><i class="fas fa-times-circle mr-2"></i> 0 Ads (Coming Soon)</li>
                  <li class="list-group-item"><i class="fas fa-user-tag mr-2"></i> Ability to use the username market (Coming Soon)</li>
                </ul>
                <br>
                <button class="btn btn-primary">Go get Verified: AUD$5.00 / month</button>
              </div>
            </div>
          </div>
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

    <!-- Display tweets -->
<?php
$staff = ["mapler", "vke"]; // Array of staff members
$verifiedUsers = ["mapler", "vke", "deadinside"]; // Array of staff members
$ogUsers = ["1", "satan", "egirl"]; // Array of staff members
$blockchain = ["crypto"]; // Array of staff members


while ($row = $result->fetch_assoc()) {
  // Retrieve the profile picture link from the database
  $sql = "SELECT profile_picture FROM users WHERE username = '" . $row["username"] . "'";
  $profilePictureResult = $conn->query($sql);
  $profilePictureRow = $profilePictureResult->fetch_assoc();
  $profilePicture = $profilePictureRow["profile_picture"];

  // Use a default profile picture if the user doesn't have one
  if (empty($profilePicture)) {
    $profilePicture = "https://i.pinimg.com/222x/57/70/f0/5770f01a32c3c53e90ecda61483ccb08.jpg";
  }
  ?>

  <div class="card tweet-card">
    <!-- Rest of the code for displaying tweets -->
    <div class="card-body">
      <div class="d-flex align-items-center">
        <img src="<?php echo $profilePicture; ?>" alt="Profile Picture" class="rounded-circle mr-2 user-image" width="40">
        <div>
          <a><?php echo $row["displayname"]; ?></a>
          <?php if (in_array($row["username"], $verifiedUsers)) { ?>
            <i style="color: blue;" class="fas fa-check-circle ml-1" onclick="showVerifiedUserAlert()"></i>
          <?php } ?>
          <?php if (in_array($row["username"], $ogUsers)) { ?>
            <i style="color: gold;" class="fas fa-check-circle ml-1" onclick="showOGUserAlert()"></i>
          <?php } ?>
          <?php if (in_array($row["username"], $staff)) { ?>
            <i style="color: purple;" class="fas fa-hammer ml-1" onclick="showStaffAlert()"></i>
          <?php } ?>
          <?php if (in_array($row["username"], $blockchain)) { ?>
            <i style="color: blue;" class="fas fa-link ml-1" onclick="showBlockchainAlert()"></i>
          <?php } ?>
          <br>
          <a href="https://web.vke.app/profile.php/<?php echo $row["username"]; ?>">@<?php echo $row["username"]; ?></a>
        </div>
      </div>
      <div class="card-text mt-2"><?php echo htmlspecialchars($row["tweet_text"], ENT_QUOTES, 'UTF-8'); ?></div>
      <?php if (!empty($row["tweet_image"])) { ?>
        <img src="<?php echo $row["tweet_image"]; ?>" alt="If you are the owner of this post, make sure your image has a file extension at the end of the link." class="img-fluid mt-3">
      <?php } ?>
    </div>
    <div class="card-footer small text-muted"><?php echo date('d F Y, H:i', strtotime($row["created_at"])); ?></div>
  </div>
<?php } ?>

        <!-- JavaScript code for Bootbox.js alerts -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js"></script>

        <script>
          // Function to show the verified user alert
          function showVerifiedUserAlert() {
            bootbox.alert({
              message: "This user is verified.",
              backdrop: true
            });
          }

          // Function to show the OG user alert
          function showOGUserAlert() {
            bootbox.alert({
              message: "This user has an OG username.",
              backdrop: true
            });
          }

          // Function to show the staff alert
          function showStaffAlert() {
            bootbox.alert({
              message: "This user is a staff member at VKE.",
              backdrop: true
            });
          }

          // Function to show the blockchain alert
          function showBlockchainAlert() {
            bootbox.alert({
              message: "This user posts information related to the blockchain.",
              backdrop: true
            });
          }

          // Display the SweetAlert popup for the first-time visit
          var visited = localStorage.getItem("updated3");
          if (!visited) {
            swal({
              title: "Updated to the newest version of VKE",
              text: "BETA v1.1.4 -> BETA v1.1.5",
              icon: "info"
            });
            localStorage.setItem("updated3", true);
          }
        </script>

      </div>
    </div>
  </div>
</body>
</html>