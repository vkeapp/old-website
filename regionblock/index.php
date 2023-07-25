<!-- tweets.php -->
<?php
session_start();

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
      padding: 20px;
      border-radius: 10px;
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
  </style>
</head>
<body>
  <br><br>
  <div class="container">
    <div class="row">
      <div class="col-md-3">
        <!-- Sidebar -->
        <div class="sidebar">
          <h3>Hello, Guest</h5>
          <hr>
          <h5>More:</h5>
          <ul>
            <a href="#"><li>Explore</li></a>
            <li><a href="#">Profile</a></li>
          </ul>
          <button type="button" class="btn btn-primary">
            Compose
          </button>
        </div>
      </div>
      <div class="col-md-9">
        <!-- Main Content -->
        <h1>Posts</h1>

        <!-- Tweet modal -->
        <div class="modal fade" id="tweetModal" tabindex="-1" role="dialog" aria-labelledby="tweetModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="tweetModalLabel">Compose</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form action="tweet.php" method="POST">
                  <div class="form-group">
                    <textarea class="form-control" name="tweet_text" rows="3" required></textarea>
                  </div>
                  <button type="submit" class="btn btn-primary">Think it!</button>
                </form>
              </div>
            </div>
          </div>
        </div>
<!-- Display tweets -->
  <div class="card tweet-card">

    <!-- Inside the loop to display user profiles -->
    <div class="card-title">
      <h1 href="#">Oops</h1>
    </div>
    <div class="card-text">VKE is temporarily region locked to Australia. Please use a VPN to continue!
  </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.min.js"></script>
</body>
</html>
