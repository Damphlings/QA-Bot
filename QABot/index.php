<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QA Bot</title>

    <!-- CSS -->
    <link rel="stylesheet" href="style.css">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="container">

    <!-- Phone -->
    <div class="phone">

        <!-- Header -->
        <div class="header">
            <div class="site-title">
                <h1>QA Bot</h1>
                <p>Your friendly IT Department assistant</p>
            </div>
            <div class="bot-profile">
                <div class="bot-icon">
                     <img src="Jambot.jpg" alt="QA Bot">
                </div>
                <div class="bot-details">
                    <h2>IT DEPARTMENT</h2>
                    <span>Online</span>
                </div>
            </div>
        </div>

        <button id="faqPullTab" class="faq-pull-tab" type="button">FAQs</button>

        <div class="faq-section collapsed" id="faqSection">
            <div class="faq-header">
                <h3>Frequently asked questions</h3>
                <button id="toggleFaqBtn" class="faq-toggle">Close</button>
            </div>
            <div class="faq-list" id="faqList">
                <div class="faq-card">Loading frequently asked questions...</div>
            </div>
        </div>

        <!-- Chat -->
        <div class="chat-area" id="chatArea">

            <!-- Initial Bot Message -->
            <div class="message bot">
                <div class="bubble">
                    Hello👋 How may we help you?
                </div>
            </div>

        </div>

        <!-- Input -->
        <div class="input-area">

            <input
                type="text"
                id="userInput"
                placeholder="Choose Others to type a custom question..."
                autocomplete="off"
                disabled
            >

            <button id="sendBtn">
                Send
            </button>

        </div>

    </div>

</div>

<script src="script.js"></script>

</body>
</html>