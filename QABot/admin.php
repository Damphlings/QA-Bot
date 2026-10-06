<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    http_response_code(401);
    header("Content-Type: application/json");
    echo json_encode([
        "status" => "error",
        "message" => "Authentication required."
    ]);
    exit;
}

require_once("db.php");

header("Content-Type: application/json");

/*
====================================================
GET REQUESTS
====================================================
*/

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $action = $_GET['action'] ?? '';

    switch ($action) {

        /*
        ----------------------------------------
        Dashboard Statistics
        ----------------------------------------
        */

        case "stats":

            $knowledge = $conn->query("SELECT COUNT(DISTINCT question) AS total FROM knowledge_base")->fetch_assoc()['total'];

            $answers = $conn->query("SELECT COUNT(*) AS total FROM knowledge_base")->fetch_assoc()['total'];

            $unanswered = $conn->query("SELECT COUNT(*) AS total FROM unanswered_questions WHERE status='Pending'")->fetch_assoc()['total'];

            echo json_encode([
                "knowledge" => $knowledge,
                "answers" => $answers,
                "unanswered" => $unanswered
            ]);

        break;

        /*
        ----------------------------------------
        Unanswered Questions
        ----------------------------------------
        */

        case "unanswered":

            $result = $conn->query("
                SELECT *
                FROM unanswered_questions
                WHERE status='Pending'
                ORDER BY asked_count DESC
            ");

            $rows = [];

            while($row = $result->fetch_assoc()){

                $rows[] = $row;

            }

            echo json_encode($rows);

        break;

        /*
        ----------------------------------------
        Knowledge Base
        ----------------------------------------
        */

        case "knowledge":

            $result = $conn->query("
                SELECT *
                FROM knowledge_base
                ORDER BY question ASC
            ");

            $rows = [];

            while($row = $result->fetch_assoc()){

                $rows[] = $row;

            }

            echo json_encode($rows);

        break;

        case "faqs":

            $result = $conn->query("
                SELECT *
                FROM faqs
                ORDER BY question ASC
            ");

            $rows = [];

            while($row = $result->fetch_assoc()){

                $rows[] = $row;

            }

            echo json_encode($rows);

        break;

    }

    exit();

}

/*
====================================================
POST REQUESTS
====================================================
*/

$action = $_POST['action'] ?? '';

switch($action){

    /*
    ====================================
    Save Answer
    ====================================
    */

    case "save":

        $id = intval($_POST['id']);

        $question = trim($_POST['question']);

        $answer = trim($_POST['answer']);

        $source = trim($_POST['source'] ?? 'unanswered');

        if($answer == ""){

            echo json_encode([
                "status"=>"error",
                "message"=>"Answer cannot be empty."
            ]);

            exit();

        }

        /*
        Insert into Knowledge Base
        */

        $stmt = $conn->prepare("
            INSERT INTO knowledge_base(question,answer)
            VALUES(?,?)
        ");

        $stmt->bind_param("ss",$question,$answer);

        $stmt->execute();

        if ($source === 'unanswered') {
            /*
            Mark unanswered as Answered
            */
            $stmt = $conn->prepare("
                UPDATE unanswered_questions
                SET status='Answered'
                WHERE id=?
            ");

            $stmt->bind_param("i",$id);

            $stmt->execute();
        }

        echo json_encode([
            "status"=>"success",
            "message"=>"Answer added successfully."
        ]);

    break;

    case "faq_add":

        $question = trim($_POST['question']);

        $answer = trim($_POST['answer']);

        if($question == "" || $answer == ""){

            echo json_encode([
                "status"=>"error",
                "message"=>"Question and answer are required."
            ]);

            exit();

        }

        $check = $conn->prepare("
            SELECT id
            FROM faqs
            WHERE question = ?
            LIMIT 1
        ");
        $check->bind_param("s", $question);
        $check->execute();
        $existingFaq = $check->get_result();

        if ($existingFaq->num_rows > 0) {
            echo json_encode([
                "status"=>"error",
                "message"=>"A FAQ with that question already exists."
            ]);
            exit();
        }

        $stmt = $conn->prepare("
            INSERT INTO faqs(question,answer)
            VALUES(?,?)
        ");

        $stmt->bind_param("ss",$question,$answer);

        $stmt->execute();

        echo json_encode([
            "status"=>"success",
            "message"=>"FAQ added successfully."
        ]);

    break;

    case "faq_update":

        $id = intval($_POST['id'] ?? 0);
        $question = trim($_POST['question']);
        $answer = trim($_POST['answer']);

        if($id <= 0 || $question == "" || $answer == ""){

            echo json_encode([
                "status"=>"error",
                "message"=>"Question, answer, and FAQ ID are required."
            ]);

            exit();

        }

        $check = $conn->prepare("
            SELECT id
            FROM faqs
            WHERE question = ? AND id != ?
            LIMIT 1
        ");
        $check->bind_param("si", $question, $id);
        $check->execute();
        $existingFaq = $check->get_result();

        if ($existingFaq->num_rows > 0) {
            echo json_encode([
                "status"=>"error",
                "message"=>"A FAQ with that question already exists."
            ]);
            exit();
        }

        $stmt = $conn->prepare("
            UPDATE faqs
            SET question = ?, answer = ?
            WHERE id = ?
        ");

        $stmt->bind_param("ssi", $question, $answer, $id);
        $stmt->execute();

        echo json_encode([
            "status"=>"success",
            "message"=>"FAQ updated successfully."
        ]);

    break;

    /*
    ====================================
    Delete Answer
    ====================================
    */

    case "delete":

        $id = intval($_POST['id']);

        $stmt = $conn->prepare("
            DELETE FROM knowledge_base
            WHERE id=?
        ");

        $stmt->bind_param("i",$id);

        $stmt->execute();

        echo json_encode([
            "status"=>"success",
            "message"=>"Answer deleted successfully."
        ]);

    break;

    case "delete_faq":

        $id = intval($_POST['id']);

        $stmt = $conn->prepare("
            DELETE FROM faqs
            WHERE id=?
        ");

        $stmt->bind_param("i",$id);

        $stmt->execute();

        echo json_encode([
            "status"=>"success",
            "message"=>"FAQ deleted successfully."
        ]);

    break;

    default:

        echo json_encode([
            "status"=>"error",
            "message"=>"Invalid request."
        ]);

}
?>