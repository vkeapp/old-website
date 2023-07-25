<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (!isset($_SESSION["user_id"])) {
    echo "User not logged in";
    exit();
  }

  // Get the submitted tweet text
  $tweetText = strip_tags($_POST["tweet_text"]);

  // Validate and process the image URL
  $imageUrl = $_POST["image_url"];
  $allowedFormats = ["png", "jpg", "jpeg", "gif"];
  $maxSize = 1000; // Maximum image size in pixels

  // Check if the image URL is provided
  if (!empty($imageUrl)) {
    // Get the image dimensions
    $imageSize = getimagesize($imageUrl);
    if ($imageSize === false) {
      echo "Invalid image URL";
      exit();
    }

    $width = $imageSize[0];
    $height = $imageSize[1];

    // Check if the image size exceeds the maximum limit
    if ($width > $maxSize || $height > $maxSize) {
      echo "Image size exceeds the maximum limit of 1000x1000 pixels";
      exit();
    }

    // Check if the image format is allowed
    $fileExtension = strtolower(pathinfo($imageUrl, PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $allowedFormats)) {
      echo "Invalid image format. Only PNG, JPG, JPEG, and GIF formats are allowed";
      exit();
    }
  }

  // Save the tweet to the database
  $conn = new mysqli("localhost", "dbinfo", "dbinfo", "dbinfo");
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $userId = $_SESSION["user_id"];
  $sql = "INSERT INTO tweets (user_id, tweet_text, image_url) VALUES ($userId, '$tweetText'', '$imageUrl')";
  if ($conn->query($sql) === TRUE) {
    echo "Tweet saved successfully";
    header("Location: https://web.vke.app/temp/new.php");
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }

  $conn->close();
}
?>