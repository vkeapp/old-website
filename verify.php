<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "GET") {
  $email = $_GET["email"];

  // Display the verification form
  echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Email Verification</title>
      <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
      <style>
        body {
          background-image: url(https://data.1freewallpapers.com/download/night-city-city-lights-architecture-sydney-australia.jpg);
          background-size: cover;
        }

        .card {
          margin-top: 50vh;
          transform: translateY(-50%);
          background-color: #333;
          color: #fff;
        }

        .card-title {
          font-size: 24px;
        }

        .form-control {
          margin-bottom: 10px;
        }
      </style>
    </head>
    <body>
      <div class="container">
        <div class="row">
          <div class="col-md-6 mx-auto">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Email Verification</h5>
                <form method="POST" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">
                  <input type="hidden" name="email" value="' . $email . '">
                  <div class="form-group">
                    <input type="text" class="form-control" name="verification_code" placeholder="Verification Code Required">
                  </div>
                  <button type="submit" class="btn btn-primary">Verify</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

      <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>
  ';
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST["email"];
  $verificationCode = $_POST["verification_code"];

  // Retrieve the stored verification code for the email
  $conn = new mysqli("localhost", "user", "pass", "db");
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $selectSql = "SELECT verification_code FROM users WHERE email = '$email'";
  $selectResult = $conn->query($selectSql);

  if ($selectResult->num_rows > 0) {
    $row = $selectResult->fetch_assoc();
    $storedVerificationCode = $row["verification_code"];

    // Check if the entered verification code matches the stored one
    if ($verificationCode == $storedVerificationCode) {
      // Update the user's email verification status
      $updateSql = "UPDATE users SET email_verified = 1 WHERE email = '$email'";
      if ($conn->query($updateSql) === TRUE) {
        echo "<script>alert('Email verification successful'); window.location.href = 'login.php';</script>";
      } else {
        echo "Error updating email verification status: " . $conn->error;
      }
    } else {
      echo "Invalid verification code";
    }
  } else {
    echo "Invalid email";
  }

  $conn->close();
}
?>
