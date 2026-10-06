<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin_login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QA Bot Admin Panel</title>

    <link rel="stylesheet" href="admin.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="sidebar">

    <div class="logo">
        <img src="Jambot.jpg" alt="QA Bot">
        <h2>QA Bot</h2>
        <p>Admin Panel</p>
    </div>

    <ul class="menu">
        <li class="active" data-page="dashboard">
            📊 Dashboard
        </li>
        <li>
            <a href="logout.php" style="color:#4a3b2a; text-decoration:none; display:block; padding:18px 25px;">Log Out</a>
        </li>
    </ul>

</div>

<div class="main">

    <header>

        <h1>QA Bot Dashboard</h1>

        <button id="refreshBtn">
            Refresh
        </button>

    </header>

    <!-- Statistics -->

    <section class="stats">

        <div class="card">

            <h3>Total Knowledge</h3>

            <h1 id="knowledgeCount">
                0
            </h1>

        </div>

        <div class="card">

            <h3>Unanswered</h3>

            <h1 id="unansweredCount">
                0
            </h1>

        </div>

        <div class="card">

            <h3>Total Answers</h3>

            <h1 id="answerCount">
                0
            </h1>

        </div>

    </section>

    <!-- Unanswered Questions -->

    <section class="panel">

        <div class="panel-header">

            <h2>Unanswered Questions</h2>

            <input
                type="text"
                id="searchQuestion"
                placeholder="Search..."
            >

        </div>

        <table>

            <thead>

            <tr>

                <th>ID</th>

                <th>Question</th>

                <th>Asked</th>

                <th>Action</th>

            </tr>

            </thead>

            <tbody id="questionTable">

            <!-- Loaded by JavaScript -->

            </tbody>

        </table>

    </section>

    <!-- Add FAQ -->

    <section class="panel">

        <div class="panel-header">

            <h2>Add FAQ</h2>

        </div>

        <form id="faqForm" class="faq-form">

            <input type="hidden" id="faqId">

            <label>Question</label>
            <input type="text" id="faqQuestion" placeholder="Enter FAQ question" required>

            <label>Answer</label>
            <textarea id="faqAnswer" placeholder="Enter FAQ answer" required></textarea>

            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <button type="submit" id="faqSubmitBtn">Add FAQ</button>
                <button type="button" id="cancelFaqEditBtn" style="display:none;">Cancel</button>
            </div>

        </form>

    </section>

    <!-- FAQ List -->

    <section class="panel">

        <div class="panel-header">

            <h2>FAQs</h2>

            <input
                type="text"
                id="searchFaq"
                placeholder="Search FAQs..."
            >

        </div>

        <table>

            <thead>

            <tr>

                <th>ID</th>

                <th>Question</th>

                <th>Answer</th>

                <th>Action</th>

            </tr>

            </thead>

            <tbody id="faqTable">

            <!-- Loaded by JavaScript -->

            </tbody>

        </table>

    </section>

    <!-- Knowledge Base -->

    <section class="panel">

        <div class="panel-header">

            <h2>Knowledge Base</h2>

            <input
                type="text"
                id="searchKnowledge"
                placeholder="Search..."
            >

        </div>

        <table>

            <thead>

            <tr>

                <th>ID</th>

                <th>Question</th>

                <th>Answer</th>

                <th>Action</th>

            </tr>

            </thead>

            <tbody id="knowledgeTable">

            <!-- Loaded by JS -->

            </tbody>

        </table>

    </section>

</div>

<!-- Answer Modal -->

<div class="modal" id="answerModal">

    <div class="modal-content">

        <span class="close">&times;</span>

        <h2 id="modalTitle">Add Answer</h2>

        <form id="answerForm">

            <input
                type="hidden"
                id="questionID"
            >

            <input
                type="hidden"
                id="questionSource"
                value="unanswered"
            >

            <label>Question</label>

            <textarea
                id="questionText"
                readonly
            ></textarea>

            <label>Answer</label>

            <textarea
                id="answerText"
                placeholder="Type the answer..."
                required
            ></textarea>

            <button type="submit">

                Save Answer

            </button>

        </form>

    </div>

</div>

<script src="admin.js"></script>

</body>
</html>
