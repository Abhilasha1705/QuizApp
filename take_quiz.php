<!-- take_quiz.php -->
<?php
require_once 'db_connection.php';

// Get the quiz ID from the URL parameter
if (!empty($_GET['quiz_id'])) {
    $quizID = (int)$_GET['quiz_id'];

    // Retrieve quiz details from the database
    $sql = "SELECT * FROM quizzes WHERE id='$quizID'";
    $result = mysqli_query($conn, $sql);
    $quiz = mysqli_fetch_assoc($result);

    // Retrieve quiz questions and options from the database
    $sql = "SELECT * FROM quiz_questions WHERE quiz_id='$quizID'";
    $result = mysqli_query($conn, $sql);
    $questions = mysqli_fetch_all($result, MYSQLI_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle the user's answers
        $totalQuestions = count($questions);
        $score = 0;

        foreach ($questions as $question) {
            $questionID = $question['id'];
            $selectedAnswer = $_POST["question$questionID"];

            if ($selectedAnswer === $question['correct_answer']) {
                $score++;
            }
        }

        // Save the quiz result for registered users
        if (isset($_SESSION['user_id'])) {
            $userID = $_SESSION['user_id'];
            $sql = "INSERT INTO quiz_results (user_id, quiz_id, score) VALUES ('$userID', '$quizID', '$score')";
            mysqli_query($conn, $sql);
        }

        // Display the quiz result
        echo "<h2>Your Quiz Result</h2>";
        echo "<p>Total Questions: $totalQuestions</p>";
        echo "<p>Correct Answers: $score</p>";

        // Provide a link to return to the dashboard or other pages
        echo '<a href="dashboard.php">Back to Dashboard</a>';
        exit();
    }
} else {
    // If the quiz ID is not provided, redirect to the homepage or display an error message
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Take Quiz</title>
</head>
<body>
    <h1><?php echo $quiz['quiz_title']; ?></h1>
    <form method="post">
        <!-- Display one question at a time with multiple-choice options -->
        <?php
        foreach ($questions as $question) {
            $questionID = $question['id'];
            $questionText = $question['question_text'];
            $option1 = $question['option1'];
            $option2 = $question['option2'];
            $option3 = $question['option3'];
            $option4 = $question['option4'];

            echo "<h3>Question $questionID:</h3>";
            echo "<p>$questionText</p>";

            echo "<label><input type='radio' name='question$questionID' value='1' required> $option1</label><br>";
            echo "<label><input type='radio' name='question$questionID' value='2'> $option2</label><br>";
            echo "<label><input type='radio' name='question$questionID' value='3'> $option3</label><br>";
            echo "<label><input type='radio' name='question$questionID' value='4'> $option4</label><br>";
        }
        ?>
        <input type="submit" value="Submit">
    </form>
</body>
</html>
