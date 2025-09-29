<?php 
session_start();
require 'audit_log.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="./images/logo/5thFighterWing-logo.png">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./stylesheet/login.css">
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
        <form autocomplete="off" action="login.php" method="POST" class="login-form">
          <h2>Login</h2>
          
          <label for="email">Email:</label>
          <input type="text" id="email" name="email" required>
          
          <label for="password">Password:</label>
          <div class="password-wrapper">
            <input type="password" id="password" name="password" required>
            <i class="fas fa-eye" id="togglePassword"></i>
          </div>
          
          <a href="#" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Forgot Password?</a>
          
          <button type="submit" class="login-btn">Login</button>
        </form>
      </div>

      <!-- Forgot Password Modal -->
      <div class="modal fade" id="forgotPasswordModal" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            
            <div class="modal-header custom-modal-header text-white">
              <h5 class="modal-title">Forgot Password</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
              <form action="forgot_password.php" method="POST">
                <div class="mb-3">
                  <label for="resetEmail" class="form-label">Enter your email address</label>
                  <input type="email" class="form-control" id="resetEmail" name="email" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
              </form>
            </div>
            
          </div>
        </div>
      </div>

      <!-- Login Error Modal -->
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
        <?php unset($_SESSION['login_error']); ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Audit Logging Script -->
  <script src="./scripts/loginpage.js"></script>
</body>
</html>
