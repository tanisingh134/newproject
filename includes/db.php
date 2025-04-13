<?php
// Database configuration
$db_host = 'localhost:3307';
$db_user = 'root';
$db_pass = '';
$db_name = 'fitness_tracker';

// Create database connection
function getDbConnection() {
    global $db_host, $db_user, $db_pass, $db_name;
    
    // Check if MySQL is running
    if (!extension_loaded('mysqli')) {
        die("MySQLi extension is not loaded. Please check your PHP configuration.");
    }
    
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error . 
            "\nPlease check:\n" .
            "1. Is MySQL running in XAMPP?\n" .
            "2. Is the database 'fitness_tracker' created?\n" .
            "3. Are the credentials correct?");
    }
    
    return $conn;
}

// Workout functions
function addWorkout($username, $workout) {
    $conn = getDbConnection();
    
    $stmt = $conn->prepare("INSERT INTO workouts (username, exercise_name, sets, reps, weight, date) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssiii", $username, $workout['exerciseName'], $workout['sets'], $workout['reps'], $workout['weight']);
    
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

function getUserWorkouts($username) {
    $conn = getDbConnection();
    
    $stmt = $conn->prepare("SELECT * FROM workouts WHERE username = ? ORDER BY date DESC");
    $stmt->bind_param("s", $username);
    
    $stmt->execute();
    $result = $stmt->get_result();
    $workouts = $result->fetch_all(MYSQLI_ASSOC);
    
    $stmt->close();
    $conn->close();
    
    return $workouts;
}

function getRecentWorkouts($username, $limit = 3) {
    $conn = getDbConnection();
    
    $stmt = $conn->prepare("SELECT * FROM workouts WHERE username = ? ORDER BY date DESC LIMIT ?");
    $stmt->bind_param("si", $username, $limit);
    
    $stmt->execute();
    $result = $stmt->get_result();
    $workouts = $result->fetch_all(MYSQLI_ASSOC);
    
    $stmt->close();
    $conn->close();
    
    return $workouts;
}

// Progress functions
function addProgress($username, $progress) {
    $conn = getDbConnection();
    
    $stmt = $conn->prepare("INSERT INTO progress (username, weight, body_fat, date) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sdd", $username, $progress['weight'], $progress['body_fat']);
    
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

function getUserProgress($username) {
    $conn = getDbConnection();
    
    $stmt = $conn->prepare("SELECT * FROM progress WHERE username = ? ORDER BY date DESC");
    $stmt->bind_param("s", $username);
    
    $stmt->execute();
    $result = $stmt->get_result();
    $progress = $result->fetch_all(MYSQLI_ASSOC);
    
    $stmt->close();
    $conn->close();
    
    return $progress;
}

function getRecentProgress($username, $limit = 3) {
    $conn = getDbConnection();
    
    $stmt = $conn->prepare("SELECT * FROM progress WHERE username = ? ORDER BY date DESC LIMIT ?");
    $stmt->bind_param("si", $username, $limit);
    
    $stmt->execute();
    $result = $stmt->get_result();
    $progress = $result->fetch_all(MYSQLI_ASSOC);
    
    $stmt->close();
    $conn->close();
    
    return $progress;
}
?> 