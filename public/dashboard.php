<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: home.php');
    exit();
}

require_once '../includes/db.php';

$username = $_SESSION['username'];
$site_name = "Virtual Personal Trainer"; // Define site name

// Fetch user's recent workouts
$conn = getDbConnection();
$stmt = $conn->prepare("SELECT * FROM workouts WHERE username = ? ORDER BY date DESC LIMIT 5");
$stmt->bind_param("s", $username);
$stmt->execute();
$recent_workouts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch user's recent progress
$stmt = $conn->prepare("SELECT * FROM progress WHERE username = ? ORDER BY date DESC LIMIT 5");
$stmt->bind_param("s", $username);
$stmt->execute();
$recent_progress = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - <?php echo $site_name; ?></title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <?php include 'includes/navigation.php'; ?>
  <main>
    <div class="container">
      <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
      
      <div class="dashboard-grid">
        <div class="dashboard-section">
          <h3>Recent Workouts</h3>
          <?php if (empty($recent_workouts)): ?>
            <p>No workouts logged yet.</p>
          <?php else: ?>
            <ul class="workout-list">
              <?php foreach ($recent_workouts as $workout): ?>
                <li>
                  <strong><?php echo htmlspecialchars($workout['exercise_name']); ?></strong>
                  <span class="date"><?php echo date('M j, Y', strtotime($workout['date'])); ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>

        <div class="dashboard-section">
          <h3>Recent Progress</h3>
          <?php if (empty($recent_progress)): ?>
            <p>No progress entries yet.</p>
          <?php else: ?>
            <ul class="progress-list">
              <?php foreach ($recent_progress as $progress): ?>
                <li>
                  <strong>Weight: <?php echo $progress['weight']; ?> kg</strong>
                  <span class="date"><?php echo date('M j, Y', strtotime($progress['date'])); ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </main>
</body>
</html>