<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['username'])) {
    die("Please log in first");
}

$username = $_SESSION['username'];
$conn = getDbConnection();

$goals = [
    [
        'goal_type' => 'Weight loss',
        'target_value' => 66.70,
        'current_value' => 0,
        'deadline' => '2025-04-26',
        'notes' => 'bnvcxdz',
        'status' => 'Active',
        'created_at' => '2025-04-13 06:57:06'
    ],
    [
        'goal_type' => 'Muscle gain',
        'target_value' => 56.00,
        'current_value' => 0,
        'deadline' => '2025-04-26',
        'notes' => 'hi',
        'status' => 'Active',
        'created_at' => '2025-04-13 06:48:17'
    ],
    [
        'goal_type' => 'Weight loss',
        'target_value' => 67.00,
        'current_value' => 0,
        'deadline' => '2025-04-25',
        'notes' => 'ghhgfd',
        'status' => 'Active',
        'created_at' => '2025-04-13 06:17:09'
    ]
];

foreach ($goals as $goal) {
    $stmt = $conn->prepare("INSERT INTO goals (username, goal_type, target_value, current_value, deadline, notes, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssddssss", 
        $username,
        $goal['goal_type'],
        $goal['target_value'],
        $goal['current_value'],
        $goal['deadline'],
        $goal['notes'],
        $goal['status'],
        $goal['created_at']
    );
    
    if ($stmt->execute()) {
        echo "Goal '{$goal['goal_type']}' added successfully.<br>";
    } else {
        echo "Error adding goal '{$goal['goal_type']}': " . $stmt->error . "<br>";
    }
    
    $stmt->close();
}

$conn->close();
echo "All goals have been processed. <a href='profile.php'>Return to Profile</a>";
?> 