-- Create database
CREATE DATABASE IF NOT EXISTS fitness_tracker;
USE fitness_tracker;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    age INT,
    gender ENUM('male', 'female', 'other'),
    height DECIMAL(5,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create goals table
CREATE TABLE IF NOT EXISTS goals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    goal_type ENUM('weight_loss', 'muscle_gain', 'endurance', 'flexibility', 'strength') NOT NULL,
    target_value DECIMAL(5,2),
    start_date DATE NOT NULL,
    target_date DATE,
    status ENUM('active', 'completed', 'abandoned') DEFAULT 'active',
    notes TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create exercise_library table
CREATE TABLE IF NOT EXISTS exercise_library (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    muscle_group ENUM('chest', 'back', 'legs', 'shoulders', 'arms', 'core', 'full_body') NOT NULL,
    difficulty ENUM('beginner', 'intermediate', 'advanced') NOT NULL,
    equipment_needed VARCHAR(255),
    video_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create nutrition_log table
CREATE TABLE IF NOT EXISTS nutrition_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    date DATE NOT NULL,
    meal_type ENUM('breakfast', 'lunch', 'dinner', 'snack') NOT NULL,
    food_name VARCHAR(100) NOT NULL,
    calories INT NOT NULL,
    protein DECIMAL(5,2),
    carbs DECIMAL(5,2),
    fats DECIMAL(5,2),
    notes TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create workout_plans table
CREATE TABLE IF NOT EXISTS workout_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    difficulty ENUM('beginner', 'intermediate', 'advanced') NOT NULL,
    duration_weeks INT,
    is_public BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create workout_plan_exercises table
CREATE TABLE IF NOT EXISTS workout_plan_exercises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plan_id INT NOT NULL,
    exercise_id INT NOT NULL,
    sets INT NOT NULL,
    reps INT NOT NULL,
    rest_time INT,
    notes TEXT,
    FOREIGN KEY (plan_id) REFERENCES workout_plans(id),
    FOREIGN KEY (exercise_id) REFERENCES exercise_library(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create workouts table
CREATE TABLE IF NOT EXISTS workouts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    exercise_name VARCHAR(100) NOT NULL,
    sets INT NOT NULL,
    reps INT NOT NULL,
    weight INT NOT NULL,
    date DATETIME NOT NULL,
    INDEX idx_username (username)
);

-- Create progress table
CREATE TABLE IF NOT EXISTS progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    weight DECIMAL(5,2) NOT NULL,
    body_fat DECIMAL(4,2) NOT NULL,
    date DATETIME NOT NULL,
    INDEX idx_username (username)
);

-- Insert some sample exercises
INSERT INTO exercise_library (name, description, muscle_group, difficulty, equipment_needed) VALUES
('Push-ups', 'A classic bodyweight exercise that targets the chest, shoulders, and triceps.', 'chest', 'beginner', 'none'),
('Squats', 'A fundamental lower body exercise that works the quadriceps, hamstrings, and glutes.', 'legs', 'beginner', 'none'),
('Pull-ups', 'An upper body exercise that primarily targets the back and biceps.', 'back', 'intermediate', 'pull-up bar'),
('Plank', 'A core exercise that strengthens the abdominal muscles and improves stability.', 'core', 'beginner', 'none'),
('Deadlift', 'A compound exercise that works multiple muscle groups including the back, legs, and core.', 'full_body', 'advanced', 'barbell'); 