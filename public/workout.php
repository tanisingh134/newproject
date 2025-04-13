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
    
    $exerciseName = $_POST['exerciseName'] ?? '';
    $sets = $_POST['sets'] ?? 0;
    $reps = $_POST['reps'] ?? 0;
    $weight = $_POST['weight'] ?? 0;
    
    if (!empty($exerciseName)) {
        if (addWorkout($username, [
            'exerciseName' => $exerciseName,
            'sets' => $sets,
            'reps' => $reps,
            'weight' => $weight
        ])) {
            $success = 'Workout added successfully!';
        } else {
            $error = 'Failed to add workout. Please try again.';
        }
    } else {
        $error = 'Please enter an exercise name';
    }
}

// Fetch user's workouts
require_once '../includes/db.php';
$workouts = getUserWorkouts($username);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Workout Plan - Virtual Personal Trainer</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <?php include 'includes/navigation.php'; ?>
  <main>
    <div class="container">
      <h2>Workout Plan</h2>
      
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

      <form method="POST" action="workout.php" class="workout-form">
        <div class="form-group">
          <label for="exerciseName">Exercise Name</label>
          <input type="text" name="exerciseName" id="exerciseName" placeholder="Enter exercise name" required>
        </div>
        
        <div class="form-group">
          <label for="sets">Sets</label>
          <input type="number" name="sets" id="sets" placeholder="Number of sets" required>
        </div>
        
        <div class="form-group">
          <label for="reps">Reps</label>
          <input type="number" name="reps" id="reps" placeholder="Number of reps" required>
        </div>
        
        <div class="form-group">
          <label for="weight">Weight (kg)</label>
          <input type="number" name="weight" id="weight" placeholder="Weight in kilograms" required>
        </div>
        
        <button type="submit">Add Workout</button>
      </form>

      <h2>Your Workouts</h2>
      <div class="workouts-list">
        <?php foreach ($workouts as $workout): ?>
          <div class="workout-item">
            <h3><?php echo htmlspecialchars($workout['exercise_name']); ?></h3>
            <p>Sets: <?php echo htmlspecialchars($workout['sets']); ?></p>
            <p>Reps: <?php echo htmlspecialchars($workout['reps']); ?></p>
            <p>Weight: <?php echo htmlspecialchars($workout['weight']); ?> kg</p>
            <p class="date">Date: <?php echo htmlspecialchars($workout['date']); ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </main>
</body>
</html> 