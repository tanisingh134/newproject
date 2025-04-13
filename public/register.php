<?php
session_start();

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit();
}

require_once '../includes/db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $error = '';

    if (!empty($username) && !empty($password) && !empty($confirm_password)) {
        if ($password === $confirm_password) {
            $conn = getDbConnection();
            
            // Check if username already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                // Username is available, create new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
                $stmt->bind_param("ss", $username, $hashed_password);
                
                if ($stmt->execute()) {
                    $_SESSION['username'] = $username;
                    $_SESSION['user_id'] = $conn->insert_id;
                    header('Location: dashboard.php');
                    exit();
                } else {
                    $error = 'Registration failed. Please try again.';
                }
            } else {
                $error = 'Username already exists';
            }
            $stmt->close();
            $conn->close();
        } else {
            $error = 'Passwords do not match';
        }
    } else {
        $error = 'Please fill in all fields';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - Virtual Personal Trainer</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <?php if (isset($_SESSION['username'])): ?>
    <?php include 'includes/navigation.php'; ?>
  <?php endif; ?>
  <main>
    <div class="container <?php echo isset($_SESSION['username']) ? '' : 'login-container'; ?>">
      <?php if (!isset($_SESSION['username'])): ?>
        <h1>Create an Account</h1>
        <p>Join Virtual Personal Trainer and start your fitness journey today.</p>
        
        <?php if (isset($error)): ?>
          <div class="error-message">
            <?php echo $error; ?>
          </div>
        <?php endif; ?>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'not_registered'): ?>
          <div class="error-message">
            You are not registered. Please create an account to continue.
          </div>
        <?php endif; ?>

        <form method="POST" action="register.php" class="login-form">
          <div class="form-group">
            <input type="text" name="username" placeholder="Username" required minlength="3" maxlength="50">
          </div>
          <div class="form-group">
            <input type="password" name="password" placeholder="Password" required minlength="6">
          </div>
          <div class="form-group">
            <input type="password" name="confirm_password" placeholder="Confirm Password" required minlength="6">
          </div>
          <button type="submit">Register</button>
          <p class="register-link">Already have an account? <a href="home.php">Login here</a></p>
        </form>
      <?php else: ?>
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p>You are already logged in. <a href="dashboard.php">Go to Dashboard</a></p>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>