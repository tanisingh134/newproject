<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: home.php');
    exit();
}

require_once '../includes/db.php';

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $goal_type = $_POST['goal_type'] ?? '';
    $target_value = $_POST['target_value'] ?? '';
    $target_date = $_POST['target_date'] ?? '';
    $notes = $_POST['notes'] ?? '';

    if (!empty($goal_type)) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("INSERT INTO goals (user_id, goal_type, target_value, start_date, target_date, notes) VALUES (?, ?, ?, CURDATE(), ?, ?)");
        $stmt->bind_param("isdss", $user_id, $goal_type, $target_value, $target_date, $notes);
        
        if ($stmt->execute()) {
            $success = 'Goal added successfully!';
        } else {
            $error = 'Failed to add goal. Please try again.';
        }
        
        $stmt->close();
        $conn->close();
    } else {
        $error = 'Please select a goal type';
    }
}

// Fetch user's goals
$conn = getDbConnection();
$stmt = $conn->prepare("SELECT * FROM goals WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$goals = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Goals - Virtual Personal Trainer</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <?php include 'includes/navigation.php'; ?>
  <main>
    <div class="container">
      <h2>My Goals</h2>
      
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

      <form method="POST" action="goals.php" class="goal-form">
        <div class="form-group">
          <label for="goal_type">Goal Type</label>
          <select name="goal_type" id="goal_type" required>
            <option value="">Select a goal</option>
            <option value="weight_loss">Weight Loss</option>
            <option value="muscle_gain">Muscle Gain</option>
            <option value="endurance">Endurance</option>
            <option value="flexibility">Flexibility</option>
            <option value="strength">Strength</option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="target_value">Target Value</label>
          <input type="number" name="target_value" id="target_value" step="0.1" placeholder="e.g., 10 kg or 5 reps">
        </div>
        
        <div class="form-group">
          <label for="target_date">Target Date</label>
          <input type="date" name="target_date" id="target_date">
        </div>
        
        <div class="form-group">
          <label for="notes">Notes</label>
          <textarea name="notes" id="notes" rows="3" placeholder="Add any additional details about your goal"></textarea>
        </div>
        
        <button type="submit">Add Goal</button>
      </form>

      <h2>Current Goals</h2>
      <div class="goals-list">
        <?php foreach ($goals as $goal): ?>
          <div class="goal-item">
            <h3><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $goal['goal_type']))); ?></h3>
            <?php if ($goal['target_value']): ?>
              <p>Target: <?php echo htmlspecialchars($goal['target_value']); ?></p>
            <?php endif; ?>
            <?php if ($goal['target_date']): ?>
              <p>Target Date: <?php echo htmlspecialchars($goal['target_date']); ?></p>
            <?php endif; ?>
            <?php if ($goal['notes']): ?>
              <p>Notes: <?php echo htmlspecialchars($goal['notes']); ?></p>
            <?php endif; ?>
            <p class="status">Status: <?php echo htmlspecialchars(ucfirst($goal['status'])); ?></p>
            <p class="date">Created: <?php echo htmlspecialchars($goal['created_at']); ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </main>
</body>
</html> 