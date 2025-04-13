<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: home.php');
    exit();
}

$username = $_SESSION['username'];
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../includes/db.php';
    
    $weight = $_POST['weight'] ?? 0;
    $bodyFat = $_POST['bodyFat'] ?? 0;
    
    if ($weight > 0) {
        if (addProgress($username, [
            'weight' => $weight,
            'body_fat' => $bodyFat
        ])) {
            $success = 'Progress added successfully!';
        } else {
            $error = 'Failed to add progress. Please try again.';
        }
    } else {
        $error = 'Please enter a valid weight';
    }
}

// Fetch user's progress
require_once '../includes/db.php';
$progress = getUserProgress($username);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Progress Tracker - Virtual Personal Trainer</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <?php include 'includes/navigation.php'; ?>
  <main>
    <div class="container">
      <h2>Progress Tracker</h2>
      
      <?php if ($error): ?>
        <div class="error-message">
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>
      
      <?php if ($success): ?>
        <div class="success-message">
          <?php echo htmlspecialchars($success); ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="progress.php" class="progress-form">
        <div class="form-group">
          <label for="weight">Weight (kg)</label>
          <input type="number" name="weight" id="weight" placeholder="Enter your weight in kilograms" required step="0.1">
        </div>
        
        <div class="form-group">
          <label for="bodyFat">Body Fat (%)</label>
          <input type="number" name="bodyFat" id="bodyFat" placeholder="Enter your body fat percentage" required step="0.1">
        </div>
        
        <button type="submit">Add Progress</button>
      </form>

      <h2>Your Progress</h2>
      <div class="progress-list">
        <?php foreach ($progress as $entry): ?>
          <div class="progress-item">
            <h3>Progress Entry</h3>
            <p>Weight: <?php echo htmlspecialchars($entry['weight']); ?> kg</p>
            <p>Body Fat: <?php echo htmlspecialchars($entry['body_fat']); ?>%</p>
            <p class="date">Date: <?php echo htmlspecialchars($entry['date']); ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </main>
</body>
</html> 