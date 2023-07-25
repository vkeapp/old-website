<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION["user_id"])) {
  header("Location: index.php");
  exit();
}

// Function to get the price from CoinGecko API
function getCryptoPrice($symbol) {
  $url = "https://api.coingecko.com/api/v3/simple/price?ids=$symbol&vs_currencies=usd";
  $data = file_get_contents($url);
  $result = json_decode($data, true);
  return $result[$symbol]["usd"];
}

// Get logged-in user information
$loggedInUserId = $_SESSION["user_id"];

// Fetch user information from the database
$conn = new mysqli("localhost", "user", "pass", "db");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, username, location, bio, profile_picture, banner_image, displayname, otherusers FROM users WHERE id = $loggedInUserId";
$loggedInUserResult = $conn->query($sql);
$loggedInUser = $loggedInUserResult->fetch_assoc();

// Get the username from the URL
$uri = $_SERVER["REQUEST_URI"];
$username = substr($uri, strrpos($uri, '/') + 1);

// Check if the username exists in the "otherusers" field
$sql = "SELECT id, username, location, bio, profile_picture, displayname, banner_image, otherusers FROM users WHERE username = '$username' OR FIND_IN_SET('$username', otherusers)";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
  echo "User not found";
  exit();
}

$user = $result->fetch_assoc();

// Update profile information
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["location"]) && isset($_POST["bio"]) && isset($_POST["profile_picture"]) && isset($_POST["banner_image"])) {
  $location = htmlspecialchars($_POST["location"], ENT_QUOTES, 'UTF-8');
  $bio = htmlspecialchars($_POST["bio"], ENT_QUOTES, 'UTF-8');
  $displayname = htmlspecialchars($_POST["displayname"], ENT_QUOTES, 'UTF-8');
  $profilePicture = htmlspecialchars($_POST["profile_picture"], ENT_QUOTES, 'UTF-8');
  $bannerImage = htmlspecialchars($_POST["banner_image"], ENT_QUOTES, 'UTF-8');

  $sql = "UPDATE users SET location = '$location', bio = '$bio', profile_picture = '$profilePicture', displayname = '$displayname', banner_image = '$bannerImage' WHERE id = $loggedInUserId";
  $conn->query($sql);
}

// Insert a new tweet
if (isset($_POST["tweet_text"])) {
  $tweetText = $_POST["tweet_text"];
  $tweetImage = $_POST["tweet_image"]; // Retrieve the image link

  // Insert the tweet into the database
  $sql = "INSERT INTO tweets (tweet_text, tweet_image, user_id, created_at) VALUES ('$tweetText', '$tweetImage', $loggedInUserId, NOW())";
  $conn->query($sql);

  header("Location: https://web.vke.app/new.php"); // Redirect to the tweets page
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
  <style>
    .sidebar {
      flex-basis: 250px;
      padding: 20px;
      background-color: #f5f8fa;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .profile-content {
      padding: 20px;
      flex-basis: 80%;
    }

    .profile-container {
      display: flex;
      justify-content: center;
      align-items: flex-start;
    }

    .profile-card {
      background-color: #f5f8fa;
      border: 1px solid #e6ecf0;
      border-radius: 5px;
      padding: 20px;
      margin-bottom: 20px;
    }

    .profile-card .profile-banner {
      width: 100%;
      height: 250px;
      object-fit: cover;
      border-radius: 5px;
      margin-bottom: 20px;
    }

    .profile-card .profile-picture {
      width: 150px;
      height: 150px;
      object-fit: cover;
      border-radius: 50%;
      border: 3px solid #fff;
      margin: -75px auto 0;
      box-shadow: 0 0 0 7px #f5f8fa;
    }

    .profile-card .profile-info {
      margin-top: 20px;
    }

    .edit-profile-form input,
    .edit-profile-form textarea {
      width: 100%;
    }

    .logo {
      font-size: 24px;
      margin-bottom: 20px;
    }

    .navbar {
      background-color: #f5f8fa;
      border-bottom: 1px solid #e6ecf0;
      padding: 20px;
      margin-bottom: 20px;
    }

    .navbar ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .navbar ul li {
      display: inline-block;
      margin-right: 10px;
    }

    .navbar ul li a {
      text-decoration: none;
      color: #000;
      padding: 5px 10px;
      border-radius: 5px;
    }

    .navbar ul li a:hover {
      background-color: #e6ecf0;
    }

    .also-known-as {
      font-size: 12px;
      color: #999;
      margin-top: -5px;
    }

    @media (max-width: 768px) {
      .profile-container {
        flex-direction: column;
      }

      .sidebar, .profile-content {
        flex-basis: 100%;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="profile-container">
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
            <a href="https://web.vke.app/new.php"><li>New Posts</li></a>
            <a href="https://web.vke.app/fedi.php"><li>Federated Posts</li></a>
            <a href="https://shortr.zip/"><li>URL Shortener</li></a>
            <li><a href="https://web.vke.app/profile.php/<?php echo $loggedInUser["username"]; ?>">Profile</a>
</li>
            <a href="https://web.vke.app/shows.php"><li>VKE Shows <span class="badge badge-info">Beta Feature</span></li></a>
          </ul>
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tweetModal">
            <i class="fas fa-feather"></i> Compose
          </button>
        </div>
      </div>

      <div class="profile-content">
        <div class="profile-card">
          <img class="profile-banner" src="<?php echo $user["banner_image"] ? $user["banner_image"] : 'https://d2kf8ptlxcina8.cloudfront.net/AJ92CTCUJG-preview.png'; ?>" alt="Profile Banner">
          <img class="profile-picture" src="<?php echo $user["profile_picture"] ? $user["profile_picture"] : 'https://i.pinimg.com/222x/57/70/f0/5770f01a32c3c53e90ecda61483ccb08.jpg'; ?>" alt="Profile Picture">
          <div class="profile-info">
            <h2><?php echo $user["displayname"]; ?></h2>
            <h5>@<?php echo $user["username"]; ?></h5>
            <?php if (!empty($user["otherusers"])) {
                $otherUsernames = explode(',', $user["otherusers"]);
                $alsoKnownAs = '';
                foreach ($otherUsernames as $otherUsername) {
                    $otherUsername = htmlspecialchars(trim($otherUsername), ENT_QUOTES, "UTF-8");
                    $alsoKnownAs .= "@$otherUsername, ";
                }
                $alsoKnownAs = rtrim($alsoKnownAs, ', ');
                echo "<p class='also-known-as'>Also known as $alsoKnownAs</p>";
            }
            ?>
            <p>Location: <?php echo htmlspecialchars($user["location"], ENT_QUOTES, "UTF-8"); ?></p>
            <p>Bio: <?php echo htmlspecialchars($user["bio"], ENT_QUOTES, "UTF-8"); ?></p>
            <?php if ($loggedInUser["id"] == $user["id"]) { ?>
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editProfileModal">
                Edit Profile
              </button>
            <?php } ?>
          </div>
        </div>
        <?php if ($user["username"] === "crypto") { ?>
          <div class="row">
            <div class="col-md-6">
              <h3>Cryptocurrency Prices</h3>
              <div class="card">
                <div class="card-body">
                  <p class="card-text">Ethereum (ETH): $<?php echo getCryptoPrice("ethereum"); ?></p>
                  <p class="card-text">Bitcoin (BTC): $<?php echo getCryptoPrice("bitcoin"); ?></p>
                  <p class="card-text">Litecoin (LTC): $<?php echo getCryptoPrice("litecoin"); ?></p>
                  <p class="card-text">Monero (XMR): $<?php echo getCryptoPrice("monero"); ?></p>
                  <p class="card-text">Tether (USDT): $<?php echo getCryptoPrice("tether"); ?></p>
                  <p class="card-text">Solana (SOL): $<?php echo getCryptoPrice("solana"); ?></p>
                </div>
              </div>
            </div>
          </div>
        <?php } else { ?>
          <div class="row">
            <div class="col-md-6">
              <h3>Most Recent Posts</h3>
              <ul class="list-group">
                <?php
                $userId = $user["id"];
                $sql = "SELECT tweet_text FROM tweets WHERE user_id = $userId ORDER BY created_at DESC LIMIT 5";
                $thoughtsResult = $conn->query($sql);

                if ($thoughtsResult->num_rows > 0) {
                  while ($row = $thoughtsResult->fetch_assoc()) {
                    ?>
                    <li class="list-group-item"><?php echo htmlspecialchars($row["tweet_text"], ENT_QUOTES, "UTF-8"); ?></li>
                    <br>
                    <?php
                  }
                } else {
                  echo "<li class=\"list-group-item\">No posts found.</li>";
                }
                ?>
              </ul>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="editProfileModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form class="edit-profile-form" method="POST" action="">
                            <div class="form-group">
                <label for="location">Display Name</label>
                <input type="text" class="form-control" id="displayname" name="displayname" value="<?php echo $loggedInUser["displayname"]; ?>">
              </div>
              <div class="form-group">
                <label for="location">Location</label>
                <input type="text" class="form-control" id="location" name="location" value="<?php echo $loggedInUser["location"]; ?>">
              </div>
              <div class="form-group">
                <label for="bio">Bio</label>
                <textarea class="form-control" id="bio" name="bio" rows="3"><?php echo $loggedInUser["bio"]; ?></textarea>
              </div>
              <div class="form-group">
                <label for="profilePicture">Profile Picture Image URL</label>
                <input type="text" class="form-control" id="profilePicture" name="profile_picture" value="<?php echo $loggedInUser["profile_picture"]; ?>">
              </div>
              <div class="form-group">
                <label for="bannerImage">Banner Image URL</label>
                <input type="text" class="form-control" id="bannerImage" name="banner_image" value="<?php echo $loggedInUser["banner_image"]; ?>">
              </div>
              <button type="submit" class="btn btn-primary">Save</button>
            </form>
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
                <form action="https://web.vke.app/new.php" method="POST">
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
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  </div>
</body>
</html>