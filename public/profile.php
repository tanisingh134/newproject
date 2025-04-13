<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: home.php');
    exit();
}

require_once '../includes/db.php';
require_once '../includes/goals.php';

$username = $_SESSION['username'];
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'change_password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
            if ($new_password === $confirm_password) {
                $conn = getDbConnection();
                
                // Verify current password
                $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                
                if (password_verify($current_password, $user['password'])) {
                    // Update password
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
                    $stmt->bind_param("ss", $hashed_password, $username);
                    
                    if ($stmt->execute()) {
                        $success = 'Password updated successfully!';
                    } else {
                        $error = 'Failed to update password. Please try again.';
                    }
                } else {
                    $error = 'Current password is incorrect.';
                }
                
                $stmt->close();
                $conn->close();
            } else {
                $error = 'New passwords do not match.';
            }
        } else {
            $error = 'Please fill in all fields.';
        }
    }
}

// Fetch user's recent activity
$conn = getDbConnection();
$recent_workouts = [];
$recent_progress = [];
$recent_goals = [];

try {
    // Get recent workouts
    $stmt = $conn->prepare("SELECT * FROM workouts WHERE username = ? ORDER BY date DESC LIMIT 5");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $recent_workouts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Get recent progress
    $stmt = $conn->prepare("SELECT * FROM progress WHERE username = ? ORDER BY date DESC LIMIT 5");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $recent_progress = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Get recent goals
    $recent_goals = getRecentGoals($username, 5);
} catch (Exception $e) {
    // Log the error but don't show it to the user
    error_log("Error fetching user activity: " . $e->getMessage());
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile - Virtual Personal Trainer</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <?php include 'includes/navigation.php'; ?>
  <main>
    <div class="container">
      <h2>Profile</h2>
      
      <?php if ($error): ?>
        <div class="alert alert-danger">
          <?php echo $error; ?>
        </div>
      <?php endif; ?>
      
      <?php if ($success): ?>
        <div class="alert alert-success">
          <?php echo $success; ?>
        </div>
      <?php endif; ?>

      <div class="profile-section">
        <h3>Account Information</h3>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
        <p><strong>Member Since:</strong> <?php echo date('F j, Y', strtotime($_SESSION['created_at'] ?? 'now')); ?></p>
      </div>

      <div class="profile-section">
        <h3>Change Password</h3>
        <form method="POST" action="profile.php" class="profile-form">
          <input type="hidden" name="action" value="change_password">
          <div class="form-group">
            <label for="current_password">Current Password</label>
            <input type="password" id="current_password" name="current_password" required>
          </div>
          <div class="form-group">
            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password" required>
          </div>
          <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
          </div>
          <button type="submit" class="btn btn-primary">Update Password</button>
        </form>
      </div>

      <div class="profile-section">
        <h3>Current Goals</h3>
        <?php if (empty($recent_goals)): ?>
          <p>No current goals</p>
        <?php else: ?>
          <div class="goals-list">
            <?php foreach ($recent_goals as $goal): ?>
              <div class="goal-item">
                <h4><?php echo htmlspecialchars($goal['goal_type']); ?></h4>
                <p><strong>Target:</strong> <?php echo htmlspecialchars($goal['target_value']); ?></p>
                <p><strong>Target Date:</strong> <?php echo date('Y-m-d', strtotime($goal['deadline'])); ?></p>
                <?php if (!empty($goal['notes'])): ?>
                  <p><strong>Notes:</strong> <?php echo htmlspecialchars($goal['notes']); ?></p>
                <?php endif; ?>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($goal['status'] ?? 'Active'); ?></p>
                <p><strong>Created:</strong> <?php echo date('Y-m-d H:i:s', strtotime($goal['created_at'])); ?></p>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <div class="profile-section">
        <h3>Recent Activity</h3>
        
        <div class="activity-grid">
          <div class="activity-section">
            <h4>Recent Workouts</h4>
            <?php if (empty($recent_workouts)): ?>
              <p>No recent workouts</p>
            <?php else: ?>
              <ul class="activity-list">
                <?php foreach ($recent_workouts as $workout): ?>
                  <li>
                    <strong><?php echo htmlspecialchars($workout['exercise_name']); ?></strong>
                    <span class="date"><?php echo date('M j, Y', strtotime($workout['date'])); ?></span>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>

          <div class="activity-section">
            <h4>Recent Progress</h4>
            <?php if (empty($recent_progress)): ?>
              <p>No recent progress entries</p>
            <?php else: ?>
              <ul class="activity-list">
                <?php foreach ($recent_progress as $progress): ?>
                  <li>
                    <strong>Weight: <?php echo htmlspecialchars($progress['weight']); ?> kg</strong>
                    <span class="date"><?php echo date('M j, Y', strtotime($progress['date'])); ?></span>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </main>
</body>
</html> 