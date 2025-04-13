<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<header>
    <div class="container">
        <h1>Virtual Personal Trainer</h1>
        <nav>
            <a href="dashboard.php" class="<?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a>
            <a href="workout.php" class="<?php echo $current_page === 'workout.php' ? 'active' : ''; ?>">Workouts</a>
            <a href="nutrition.php" class="<?php echo $current_page === 'nutrition.php' ? 'active' : ''; ?>">Nutrition</a>
            <a href="progress.php" class="<?php echo $current_page === 'progress.php' ? 'active' : ''; ?>">Progress</a>
            <a href="goals.php" class="<?php echo $current_page === 'goals.php' ? 'active' : ''; ?>">Goals</a>
            <a href="profile.php" class="<?php echo $current_page === 'profile.php' ? 'active' : ''; ?>">Profile</a>
            <a href="logout.php" class="logout">Logout</a>
        </nav>
    </div>
</header> 