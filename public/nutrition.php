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
$site_name = "Virtual Personal Trainer"; // Define site name

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'] ?? date('Y-m-d');
    $meal_type = $_POST['meal_type'] ?? '';
    $food_name = $_POST['food_name'] ?? '';
    $calories = $_POST['calories'] ?? 0;
    $protein = $_POST['protein'] ?? 0;
    $carbs = $_POST['carbs'] ?? 0;
    $fats = $_POST['fats'] ?? 0;
    $notes = $_POST['notes'] ?? '';

    if (!empty($food_name) && $calories > 0) {
        $conn = getDbConnection();
        $stmt = $conn->prepare("INSERT INTO nutrition_log (user_id, date, meal_type, food_name, calories, protein, carbs, fats, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssiddds", $user_id, $date, $meal_type, $food_name, $calories, $protein, $carbs, $fats, $notes);
        
        if ($stmt->execute()) {
            $success = 'Meal logged successfully!';
        } else {
            $error = 'Failed to log meal. Please try again.';
        }
        
        $stmt->close();
        $conn->close();
    } else {
        $error = 'Please enter at least the food name and calories';
    }
}

// Fetch today's nutrition log
$conn = getDbConnection();
$stmt = $conn->prepare("SELECT * FROM nutrition_log WHERE user_id = ? AND date = CURDATE() ORDER BY meal_type, created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$meals = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

// Calculate daily totals
$daily_totals = [
    'calories' => 0,
    'protein' => 0,
    'carbs' => 0,
    'fats' => 0
];

foreach ($meals as $meal) {
    $daily_totals['calories'] += $meal['calories'];
    $daily_totals['protein'] += $meal['protein'];
    $daily_totals['carbs'] += $meal['carbs'];
    $daily_totals['fats'] += $meal['fats'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nutrition Tracker - <?php echo $site_name; ?></title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <?php include 'includes/navigation.php'; ?>
  <main>
    <div class="container">
      <h2>Nutrition Tracker</h2>
      
      <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
      <?php endif; ?>
      
      <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
      <?php endif; ?>

      <div class="daily-totals">
        <h3>Today's Nutrition Summary</h3>
        <div class="totals-grid">
          <div class="total-item">
            <h4>Calories</h4>
            <p><?php echo $daily_totals['calories']; ?></p>
          </div>
          <div class="total-item">
            <h4>Protein</h4>
            <p><?php echo $daily_totals['protein']; ?>g</p>
          </div>
          <div class="total-item">
            <h4>Carbs</h4>
            <p><?php echo $daily_totals['carbs']; ?>g</p>
          </div>
          <div class="total-item">
            <h4>Fats</h4>
            <p><?php echo $daily_totals['fats']; ?>g</p>
          </div>
        </div>
      </div>

      <div class="nutrition-form">
        <h3>Log a Meal</h3>
        <form method="POST" action="">
          <div class="form-row">
            <div class="form-group">
              <label for="date">Date</label>
              <input type="date" id="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="form-group">
              <label for="meal_type">Meal Type</label>
              <select id="meal_type" name="meal_type" class="form-control" required>
                <option value="breakfast">Breakfast</option>
                <option value="lunch">Lunch</option>
                <option value="dinner">Dinner</option>
                <option value="snack">Snack</option>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="food_name">Food Name</label>
              <input type="text" id="food_name" name="food_name" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="calories">Calories</label>
              <input type="number" id="calories" name="calories" class="form-control" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="protein">Protein (g)</label>
              <input type="number" id="protein" name="protein" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="carbs">Carbs (g)</label>
              <input type="number" id="carbs" name="carbs" class="form-control" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="fats">Fats (g)</label>
              <input type="number" id="fats" name="fats" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="notes">Notes (optional)</label>
              <input type="text" id="notes" name="notes" class="form-control">
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Log Meal</button>
        </form>
      </div>

      <div class="meals-list">
        <h3>Today's Meals</h3>
        <?php if (empty($meals)): ?>
          <p>No meals logged for today.</p>
        <?php else: ?>
          <?php foreach ($meals as $meal): ?>
            <div class="meal-item">
              <h4><?php echo ucfirst($meal['meal_type']); ?></h4>
              <p class="food-name"><?php echo htmlspecialchars($meal['food_name']); ?></p>
              <div class="meal-nutrition">
                <span><?php echo $meal['calories']; ?> calories</span>
                <span><?php echo $meal['protein']; ?>g protein</span>
                <span><?php echo $meal['carbs']; ?>g carbs</span>
                <span><?php echo $meal['fats']; ?>g fats</span>
              </div>
              <?php if (!empty($meal['notes'])): ?>
                <p class="notes"><?php echo htmlspecialchars($meal['notes']); ?></p>
              <?php endif; ?>
              <p class="time">Logged at <?php echo date('g:i A', strtotime($meal['created_at'])); ?></p>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </main>
</body>
</html> 