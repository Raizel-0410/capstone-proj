<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href=".\stylesheet\login.css">
</head>
<body>
  <div class="login-wrapper">
    <div class="login-card">
      
      <!-- Left Side -->
      <div class="left-panel">
        <h1>Welcome Back!</h1>
      </div>

      <!-- Right Side -->
      <div class="right-panel">
        <form action="login.php" method="POST" class="login-form">
          <h2>Login</h2>
          
          <label for="email">Email:</label>
          <input type="text" id="email" name="email" required>
          
          <label for="password">Password:</label>
          <input type="password" id="password" name="password" required>
          
          <a href="#" class="forgot">Forgot Password?</a>
          
          <button type="submit" class="login-btn">Login</button>
        </form>
      </div>

      <?php if (!empty($_SESSION['login_error'])): ?>
  <div class="modal fade" id="loginErrorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header custom-modal-header text-white">
          <h5 class="modal-title">Login Failed</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <?= htmlspecialchars($_SESSION['login_error']); ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
<?php 
   unset($_SESSION['login_error']); // clear error after showing once
endif; 
?>
    </div>
  </div>
  <script>
  document.addEventListener("DOMContentLoaded", function () {
    var loginErrorModal = document.getElementById("loginErrorModal");
    if (loginErrorModal) {
      var modal = new bootstrap.Modal(loginErrorModal);
      modal.show();
    }
  });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
