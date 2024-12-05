<?php

$servername = "localhost";
$username = "root"; 
$password = "1313"; 
$dbname = "quiz_app"; 
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) 
{
    die("Connection failed: " . $conn->connect_error);
}
echo "Connection successful";


$quiz_title = "PHP Quiz";
$questions = [
    [
        "question" => "What's my name?",
        "options" => ["Ace", "Eys", "Iys", "Ezu"],
        "answer" => 0
    ],
    [
        "question" => "San ka nakatira?",
        "options" => ["Bahay", "Home", "Puerto Princesa City", "Tabi-tabi"],
        "answer" => 2
    ],
    [
        "question" => "Ano gawa mo?",
        "options" => ["Humihinga", "BAKIT", "Ayun", "Minsan"],
        "answer" => 0
    ]
];

$score = 0;
$username = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);

    
    $checkUser = $conn->query("SELECT * FROM users WHERE username = '$username'");
    if ($checkUser->num_rows > 0) {
        echo "<h2>You have already taken this quiz!</h2>";
        echo "<a href='index.php'>Go Back</a>";
        exit;
    }

    
    foreach ($questions as $index => $question) {
        if (isset($_POST['question' . $index]) && $_POST['question' . $index] == $question['answer']) {
            $score++;
        }
    }

    
    $stmt = $conn->prepare("INSERT INTO users (username, score) VALUES (?, ?)");
    $stmt->bind_param("si", $username, $score);
    $stmt->execute();
    $stmt->close();

    echo "<h2>Your Score: $score / " . count($questions) . "</h2>";
    echo "<a href='index.php'>Try Again?</a>";
    exit;
}


$leaderboard = $conn->query("SELECT username, score FROM users ORDER BY score DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $quiz_title; ?></title>
</head>
<body>
    <h1><?php echo $quiz_title; ?></h1>
    <form action="" method="post">
        <label for="username">Enter your name:</label>
        <input type="text" name="username" required><br><br>

        <?php foreach ($questions as $index => $question) { ?>
            <fieldset>
                <legend><?php echo $question['question']; ?></legend>
                <?php foreach ($question['options'] as $optionIndex => $option) { ?>
                    <label>
                        <input type="radio" name="question<?php echo $index; ?>" value="<?php echo $optionIndex; ?>">
                        <?php echo $option; ?>
                    </label><br>
                <?php } ?>
            </fieldset>
        <?php } ?>
        <button type="submit">Submit</button>
    </form>

    <h2>Leaderboard</h2>
    <ul>
        <?php while ($row = $leaderboard->fetch_assoc()) { ?>
            <li><?php echo $row['username'] . ": " . $row['score']; ?></li>
        <?php } ?>
    </ul>
</body>
</html>