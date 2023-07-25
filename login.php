<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Get the submitted username and password
  $username = $_POST["login_username"];
  $password = $_POST["login_password"];

  // Validate and sanitize the inputs (you can add more validation rules)
  $username = filter_var($username, FILTER_SANITIZE_STRING);

  // Check if the user exists in the database
  $conn = new mysqli("localhost", "user", "pass", "db");
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $sql = "SELECT id, password FROM users WHERE username = '$username'";
  $result = $conn->query($sql);

  if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $hashedPassword = $row["password"];

    // Verify the password
    if (password_verify($password, $hashedPassword)) {
      // Password is correct, create a session for the user
      $_SESSION["user_id"] = $row["id"];
      header("Location: https://web.vke.app/new.php");
      exit();
    } else {
      echo "Invalid password";
    }
  } else {
    echo "User not found";
  }

  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login to VKE</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      background-image: url(https://images8.alphacoders.com/476/476849.jpg);
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

    .login-link {
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
            <h5 class="card-title">Login to VKE</h5>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
              <div class="form-group">
                <input type="text" class="form-control" name="login_username" placeholder="Username" required>
              </div>
              <div class="form-group">
                <input type="password" class="form-control" name="login_password" placeholder="Password" required>
              </div>
              <button type="submit" class="btn btn-primary">Login</button>
            </form>
            <a href="register.php" class="login-link">Trying to register?</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
