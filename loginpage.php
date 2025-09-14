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
    </div>
  </div>
</body>
</html>
