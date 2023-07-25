<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Get the submitted username and password
  $username = $_POST["register_username"];
  $password = $_POST["register_password"];

  // Validate and sanitize the inputs
  $username = filter_var($username, FILTER_SANITIZE_STRING);

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

  // Hash the password
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  // Save the user to the database
  $insertSql = "INSERT INTO users (username, password) VALUES ('$username', '$hashedPassword')";

  // reCAPTCHA Verification
  $recaptchaResponse = $_POST['g-recaptcha-response'];
  $recaptchaSecretKey = '6LeK1PImAAAAAPzOEIyOAp62kGOtKkFhlojvd1Vs';
  $recaptchaVerifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
  $recaptchaData = [
    'secret' => $recaptchaSecretKey,
    'response' => $recaptchaResponse,
    'remoteip' => $_SERVER['REMOTE_ADDR'],
  ];

  $recaptchaOptions = [
    'http' => [
      'method' => 'POST',
      'content' => http_build_query($recaptchaData),
      'header' => 'Content-Type: application/x-www-form-urlencoded',
    ],
  ];

  $recaptchaContext = stream_context_create($recaptchaOptions);
  $recaptchaResult = file_get_contents($recaptchaVerifyUrl, false, $recaptchaContext);
  $recaptchaResult = json_decode($recaptchaResult);

  if (!$recaptchaResult->success) {
    echo 'reCAPTCHA verification failed. Please try again.';
    $conn->close();
    exit();
  }

  if ($conn->query($insertSql) === TRUE) {
    echo "<script>alert('Registration successful'); window.location.href = 'login.php';</script>";
  } else {
    echo "Error: " . $insertSql . "<br>" . $conn->error;
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