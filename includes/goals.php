<?php
require_once 'db.php';

function addGoal($username, $goal) {
    $conn = getDbConnection();
    
    $stmt = $conn->prepare("INSERT INTO goals (username, goal_type, target_value, current_value, deadline, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssdds", 
        $username, 
        $goal['goal_type'], 
        $goal['target_value'], 
        $goal['current_value'], 
        $goal['deadline']
    );
    
    $success = $stmt->execute();
    $stmt->close();
    $conn->close();
    
    return $success;
}

function getUserGoals($username) {
    $conn = getDbConnection();
    
    $stmt = $conn->prepare("SELECT * FROM goals WHERE username = ? ORDER BY created_at DESC");
    $stmt->bind_param("s", $username);
    
    $stmt->execute();
    $result = $stmt->get_result();
    $goals = $result->fetch_all(MYSQLI_ASSOC);
    
    $stmt->close();
    $conn->close();
    
    return $goals;
}

function getRecentGoals($username, $limit = 5) {
    $conn = getDbConnection();
    
    $stmt = $conn->prepare("SELECT * FROM goals WHERE username = ? ORDER BY created_at DESC LIMIT ?");
    $stmt->bind_param("si", $username, $limit);
    
    $stmt->execute();
    $result = $stmt->get_result();
    $goals = $result->fetch_all(MYSQLI_ASSOC);
    
    $stmt->close();
    $conn->close();
    
    return $goals;
}

function updateGoalProgress($username, $goal_id, $current_value) {
    $conn = getDbConnection();
    
    $stmt = $conn->prepare("UPDATE goals SET current_value = ? WHERE id = ? AND username = ?");
    $stmt->bind_param("dis", $current_value, $goal_id, $username);
    
    $success = $stmt->execute();
    $stmt->close();
    $conn->close();
    
    return $success;
}
?> 