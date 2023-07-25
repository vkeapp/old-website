<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Get the submitted username, password, and email
  $username = $_POST["register_username"];
  $password = $_POST["register_password"];
  $email = $_POST["register_email"];

  // Validate and sanitize the inputs
  $username = filter_var($username, FILTER_SANITIZE_STRING);
  $email = filter_var($email, FILTER_SANITIZE_EMAIL);

  // Check if the username is already taken
  $conn = new mysqli("localhost", "user", "pass", "db");
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  // Check if the username exists in the 'users' table
  $checkSql = "SELECT id FROM users WHERE username = '$username' OR otherusers = '$username'";
  $checkResult = $conn->query($checkSql);

  if ($checkResult->num_rows > 0) {
    echo "Username already taken";
    $conn->close();
    exit();
  }

  // Validate the username format
  if (!preg_match('/^[a-zA-Z0-9]+$/', $username)) {
    echo "Invalid username format. Only letters and numbers are allowed.";
    $conn->close();
    exit();
  }

  // Check the username length
  $usernameLength = strlen($username);
  if ($usernameLength < 3 || $usernameLength > 20) {
    echo "Username length must be between 3 and 20 characters.";
    $conn->close();
    exit();
  }

  // Check if the email is already registered
  $checkEmailSql = "SELECT id FROM users WHERE email = '$email'";
  $checkEmailResult = $conn->query($checkEmailSql);

  if ($checkEmailResult->num_rows > 0) {
    echo "Email already registered";
    $conn->close();
    exit();
  }

  // Hash the password
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  // Generate a verification code
  $verificationCode = mt_rand(100000, 999999);

  // Save the user to the database with verification code
  $insertSql = "INSERT INTO users (username, password, email, verification_code) VALUES ('$username', '$hashedPassword', '$email', '$verificationCode')";

  // Send verification email
  $to = $email;
  $subject = "Your email verification code is here!";
  $message = "Hi, thank you for registering an account on VKE. Your verification code is: $verificationCode. If you did not register an account please ignore this email.";
  $headers = "From: verify@vke.app"; // Replace with your email address or use a library like PHPMailer

  if (mail($to, $subject, $message, $headers)) {
    if ($conn->query($insertSql) === TRUE) {
      echo "<script>alert('Registration successful. Check your email for the verification code.'); window.location.href = 'verify.php?email=$email';</script>";
    } else {
      echo "Error: " . $insertSql . "<br>" . $conn->error;
    }
  } else {
    echo "Error sending verification email";
  }

  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register for VKE</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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

    .register-link {
      font-size: 14px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="row">
      <div class="col-md-6 mx-auto">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Register for VKE</h5>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
              <div class="form-group">
                <input type="text" class="form-control" name="register_username" placeholder="Username" pattern="[A-Za-z0-9]{3,20}" title="Only letters and numbers are allowed. Minimum 3 characters and maximum 20 characters." required>
              </div>
              <div class="form-group">
                <input type="password" class="form-control" name="register_password" placeholder="Password" required>
              </div>
              <div class="form-group">
                <input type="email" class="form-control" name="register_email" placeholder="Email" required>
              </div>
              <div class="form-group">
                <div class="g-recaptcha" data-sitekey="6LeK1PImAAAAAKjUdPVQS7MCaFTLiHcawwnu8EVY"></div>
              </div>
              <p>By registering an account and using VKE's service you agree to our <a href="https://legal.vke.app/tos">Terms of Service</a> and <a href="https://legal.vke.app/privacy">Privacy Policy</a></p>
              <button type="submit" class="btn btn-primary">Register</button>
            </form>
            <a href="login.php" class="register-link">Already have an account?</a>
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
