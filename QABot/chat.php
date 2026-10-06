<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';

    if ($action === 'faq') {
        header('Content-Type: application/json; charset=UTF-8');

        $result = $conn->query(
            "SELECT id, question, answer FROM faqs ORDER BY created_at DESC LIMIT 6"
        );

        $questions = [];

        while ($row = $result->fetch_assoc()) {
            $questions[] = $row;
        }

        echo json_encode($questions);
        exit();
    }

    if ($action === 'check') {
        header('Content-Type: application/json; charset=UTF-8');

        $question = trim($_GET['question'] ?? '');
        $normalized = strtolower($question);
        $normalized = preg_replace('/[^a-z0-9\s]/', '', $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        $normalized = trim($normalized);

        $stmt = $conn->prepare(
            "SELECT answer FROM knowledge_base WHERE question = ? ORDER BY id DESC LIMIT 1"
        );
        $stmt->bind_param("s", $normalized);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo json_encode(["status" => "answered", "answer" => $row['answer']]);
        } else {
            echo json_encode(["status" => "pending"]);
        }

        exit();
    }

    http_response_code(400);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['error' => 'Invalid request.']);
    exit();
}

header('Content-Type: text/plain; charset=UTF-8');

if (!isset($_POST['message'])) {
    exit("Invalid request.");
}

$message = trim($_POST['message']);

if ($message == "") {
    exit("Please enter a message.");
}

/*
|--------------------------------------------------------------------------
| Normalize Question
|--------------------------------------------------------------------------
*/
$normalized = strtolower($message);
$normalized = preg_replace('/[^a-z0-9\s]/', '', $normalized);
$normalized = preg_replace('/\s+/', ' ', $normalized);
$normalized = trim($normalized);

/*
|--------------------------------------------------------------------------
| Search Knowledge Base
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
SELECT answer
FROM knowledge_base
WHERE question = ?
ORDER BY RAND()
LIMIT 1
");

$stmt->bind_param("s", $normalized);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $row = $result->fetch_assoc();

    echo $row['answer'];

    exit();

}

/*
|--------------------------------------------------------------------------
| Save Unanswered Question
|--------------------------------------------------------------------------
*/

$check = $conn->prepare("
SELECT id, asked_count
FROM unanswered_questions
WHERE question = ?
");

$check->bind_param("s", $normalized);
$check->execute();

$existing = $check->get_result();

if ($existing->num_rows > 0) {

    $row = $existing->fetch_assoc();

    $update = $conn->prepare("
    UPDATE unanswered_questions
    SET asked_count = asked_count + 1
    WHERE id = ?
    ");

    $update->bind_param("i", $row['id']);
    $update->execute();

} else {

    $insert = $conn->prepare("
    INSERT INTO unanswered_questions(question)
    VALUES(?)
    ");

    $insert->bind_param("s", $normalized);
    $insert->execute();

}

echo "Please wait for the admin to respond.";
?>