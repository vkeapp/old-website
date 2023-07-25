<?php
session_start();

// Database connection
$host = "localhost";
$username = "user";
$password = "pass";
$database = "db";

$connection = mysqli_connect($host, $username, $password, $database);

if (!$connection) {
  die("Database connection failed: " . mysqli_connect_error());
}

// Logic to check user's staff status (e.g., retrieve from session, user role, etc.)
$isStaff = true; // Set to true if the user is staff
// Example: Retrieve user's role from session
if (isset($_SESSION['role']) && $_SESSION['role'] === 'staff') {
  $isStaff = true;
}

// Retrieve the 10 most recent shows from the database
$query = "SELECT * FROM shows ORDER BY created_at DESC LIMIT 10";
$result = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Shows</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
  <style>
    #video-player {
      margin-bottom: 20px;
    }
        .sidebar {
      background-color: #f5f8fa;
      padding: 20px;
      border-radius: 10px;
    }
            .logo {
      font-size: 24px;
      margin-bottom: 20px;
    }
  </style>
  <script>
    // Video click handler
    $(document).on('click', '.show-link', function() {
      var videoId = $(this).data('video-id');
      var videoPlayer = '<iframe width="560" height="315" src="https://www.youtube.com/embed/' + videoId + '" frameborder="0" allowfullscreen></iframe>';
      $('#video-player').html(videoPlayer);
    });
  </script>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">VKE Shows <span class="badge badge-info">Beta Feature</span></a>
  </nav>

  <div class="container mt-4">
    <div class="row">
      <div class="col-md-3">
        <!-- Sidebar -->
        <div class="sidebar">
          <div class="logo">
            <strong>VKE</strong>.app
          </div>
          <hr>
          <h5>More:</h5>
          <ul>
            <a href="#"><li>VKE Originals</li></a>
            <a href="#"><li>VKE YouTube</li></a>
            <?php if ($isStaff) { ?>
              <li><a href="panel.php">Moderate Shows</a></li>
            <?php } ?>
          </ul>
          <button type="button" class="btn btn-link" data-toggle="modal" data-target="#plusModal">
            Become an uploader
          </button>
          <br>
          <button type="button" class="btn btn-link" data-toggle="modal" data-target="#logoutModal">
            Go back to VKE
        </button>
        </div>
        <!-- End Sidebar -->
    </div>
<div class="col-md-9">
<!-- Video Player -->
<div id="video-player"></div>
    <!-- Show List -->
    <div class="list-group">
      <?php
      while ($row = mysqli_fetch_assoc($result)) {
        $videoId = $row['video_id'];
        $title = $row['title'];
        $description = $row['description'];
        $createdBy = $row['created_by'];
        $createdAt = $row['created_at'];
        $formattedDate = date('M d, Y', strtotime($createdAt));
        ?>

        <a href="#" class="list-group-item list-group-item-action show-link" data-video-id="<?php echo $videoId; ?>">
          <div class="d-flex w-100 justify-content-between">
            <h5 class="mb-1"><?php echo $title; ?></h5>
            <small><?php echo $formattedDate; ?></small>
          </div>
          <p class="mb-1"><?php echo $description; ?></p>
        </a>

      <?php } ?>
    </div>
  </div>
</div>
  </div>
  <!-- Plus Modal -->
  <div class="modal fade" id="plusModal" tabindex="-1" role="dialog" aria-labelledby="plusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="plusModalLabel">Upload to VKE</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>This feature is currently under development.</p>
          <p>Please check back later.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Logout Modal -->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="logoutModalLabel">Go Back</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to go back to VKE?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
          <a href="https://web.vke.app/new.php/" class="btn btn-primary">Yes</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>