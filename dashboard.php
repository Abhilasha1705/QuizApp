<!-- dashboard.php -->
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'db_connection.php';

// Function to get the total number of quizzes taken by a registered user
function getTotalQuizzesTaken($conn, $userID) {
    $sql = "SELECT COUNT(DISTINCT quiz_id) AS total FROM quiz_results WHERE user_id='$userID'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

$userID = $_SESSION['user_id'];

// Retrieve user information from the database
$sql = "SELECT * FROM users WHERE id='$userID'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Retrieve quizzes from the database
$sql = "SELECT * FROM quizzes";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo $user['username']; ?>!</h1>
    <h2>Profile Information</h2>
    <p>Email: <?php echo $user['email']; ?></p>
    <p>Total Quizzes Taken: <?php echo getTotalQuizzesTaken($conn, $userID); ?></p>

    <h2>Available Quizzes</h2>
    <?php
    // Display a list of available quizzes with links to take them
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $quizID = $row['id'];
            $quizTitle = $row['quiz_title'];
            echo "<p><a href='take_quiz.php?quiz_id=$quizID'>$quizTitle</a></p>";
        }
    } else {
        echo "<p>No quizzes available.</p>";
    }
    ?>

    <h2>Quiz Results</h2>
    <?php
    // Retrieve quiz results for the user from the database
    $sql = "SELECT quizzes.quiz_title, quiz_results.score 
            FROM quiz_results 
            JOIN quizzes ON quiz_results.quiz_id = quizzes.id 
            WHERE quiz_results.user_id='$userID'";
    $result = mysqli_query($conn, $sql);

    // Display a list of completed quizzes and their respective scores
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $quizTitle = $row['quiz_title'];
            $score = $row['score'];
            echo "<p>$quizTitle - Score: $score</p>";
        }
    } else {
        echo "<p>No quiz results yet.</p>";
    }
    ?>

    <a href="logout.php">Logout</a>
</body>
</html>
