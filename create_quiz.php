<!-- create_quiz.php -->
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get quiz details from the form
    $quizTitle = $_POST['quiz_title'];
    $questionCount = (int)$_POST['question_count'];

    // Insert quiz details into the database
    $userID = $_SESSION['user_id'];
    $sql = "INSERT INTO quizzes (user_id, quiz_title) VALUES ('$userID', '$quizTitle')";
    if (mysqli_query($conn, $sql)) {
        // Get the quiz ID generated for this quiz
        $quizID = mysqli_insert_id($conn);

        // Loop through each question and its options
        for ($i = 1; $i <= $questionCount; $i++) {
            $question = $_POST["question$i"];
            $option1 = $_POST["question${i}_option1"];
            $option2 = $_POST["question${i}_option2"];
            $option3 = $_POST["question${i}_option3"];
            $option4 = $_POST["question${i}_option4"];
            $correctAnswer = $_POST["question${i}_correct"];

            // Insert question and options into the database
            $sql = "INSERT INTO quiz_questions (quiz_id, question_text, option1, option2, option3, option4, correct_answer) 
                    VALUES ('$quizID', '$question', '$option1', '$option2', '$option3', '$option4', '$correctAnswer')";
            mysqli_query($conn, $sql);
        }

        // Quiz creation successful, you can redirect to the dashboard or show a success message
        header("Location: dashboard.php");
        exit();
    } else {
        // Error handling if quiz creation fails
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>

<!-- HTML form for quiz creation -->
<form method="post">
    <label for="quiz_title">Quiz Title:</label>
    <input type="text" name="quiz_title" required>
    <br>
    <label for="question_count">Number of Questions:</label>
    <input type="number" name="question_count" required>
    <br>
    <!-- Add inputs for each question and options -->
    <?php
    for ($i = 1; $i <= $questionCount; $i++) {
        echo "<label for='question$i'>Question $i:</label>";
        echo "<input type='text' name='question$i' required>";
        echo "<br>";

        echo "<label for='question${i}_option1'>Option 1:</label>";
        echo "<input type='text' name='question${i}_option1' required>";
        echo "<br>";

        echo "<label for='question${i}_option2'>Option 2:</label>";
        echo "<input type='text' name='question${i}_option2' required>";
        echo "<br>";

        echo "<label for='question${i}_option3'>Option 3:</label>";
        echo "<input type='text' name='question${i}_option3' required>";
        echo "<br>";

        echo "<label for='question${i}_option4'>Option 4:</label>";
        echo "<input type='text' name='question${i}_option4' required>";
        echo "<br>";

        echo "<label for='question${i}_correct'>Correct Answer (1-4):</label>";
        echo "<input type='number' name='question${i}_correct' min='1' max='4' required>";
        echo "<br>";
    }
    ?>
    <input type="submit" value="Create Quiz">
</form>
