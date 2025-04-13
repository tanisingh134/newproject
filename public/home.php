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
    $error = '';

    if (!empty($username) && !empty($password)) {
        $conn = getDbConnection();
        
        // Check if the user exists
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Valid user, set session and redirect
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_id'] = $user['id'];
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Invalid password. Please try again.';
            }
        } else {
            // User does not exist, redirect to registration
            header('Location: register.php?error=not_registered');
            exit();
        }
        
        $stmt->close();
        $conn->close();
    } else {
        $error = 'Please enter both username and password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Virtual Personal Trainer - Login</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <?php if (isset($_SESSION['username'])): ?>
    <?php include 'includes/navigation.php'; ?>
  <?php endif; ?>
  <main>
    <div class="container <?php echo isset($_SESSION['username']) ? '' : 'login-container'; ?>">
      <?php if (!isset($_SESSION['username'])): ?>
        <h1>Welcome to Virtual Personal Trainer</h1>
        <p>Your personalized fitness journey starts here. Track your workouts, monitor progress, and get adaptive plans tailored to your goals.</p>
        
        <?php if (isset($error)): ?>
          <div class="error-message">
            <?php echo $error; ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="home.php" class="login-form">
          <div class="form-group">
            <input type="text" name="username" placeholder="Username" required>
          </div>
          <div class="form-group">
            <input type="password" name="password" placeholder="Password" required>
          </div>
          <button type="submit">Login</button>
          <p class="register-link">Don't have an account? <a href="register.php">Register here</a></p>
        </form>
      <?php else: ?>
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p>You are already logged in. <a href="dashboard.php">Go to Dashboard</a></p>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>