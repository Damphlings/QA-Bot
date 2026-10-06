const sendBtn = document.getElementById("sendBtn");
const userInput = document.getElementById("userInput");
const chatArea = document.getElementById("chatArea");
const faqList = document.getElementById("faqList");
const toggleFaqBtn = document.getElementById("toggleFaqBtn");
const faqPullTab = document.getElementById("faqPullTab");
const phone = document.querySelector(".phone");

sendBtn.addEventListener("click", sendMessage);

userInput.addEventListener("keypress", function(e){
    if(e.key === "Enter"){
        sendMessage();
    }
});

function setChatEnabled(enabled) {
    userInput.disabled = !enabled;
    sendBtn.disabled = !enabled;
    userInput.placeholder = enabled ? "Type your question..." : "Choose Others to type a custom question...";
}

setChatEnabled(false);
loadFAQ();

if (toggleFaqBtn) {
    toggleFaqBtn.addEventListener("click", toggleFaqSection);
}

if (faqPullTab) {
    faqPullTab.addEventListener("click", toggleFaqSection);
}

let typingIndicator = null;

function sendMessage(){

    if (userInput.disabled) return;

    const message = userInput.value.trim();

    if(message === "") return;

    appendMessage(message,"user");

    userInput.value="";

    fetchChatResponse(message);

}

function showTypingIndicator() {
    hideTypingIndicator();

    const msg = document.createElement("div");
    msg.className = "message bot typing";

    const bubble = document.createElement("div");
    bubble.className = "bubble typing-bubble";
    bubble.innerHTML = '<span></span><span></span><span></span>';

    msg.appendChild(bubble);
    chatArea.appendChild(msg);
    chatArea.scrollTop = chatArea.scrollHeight;

    typingIndicator = msg;
}

function hideTypingIndicator() {
    if (typingIndicator) {
        typingIndicator.remove();
        typingIndicator = null;
    }
}

const TYPING_ANIMATION_DELAY = 700;

function fetchChatResponse(message) {
    showTypingIndicator();

    fetch("chat.php",{
        method:"POST",
        headers:{
            "Content-Type":"application/x-www-form-urlencoded"
        },
        body:"message="+encodeURIComponent(message)
    })
    .then(response=>response.text())
    .then(data=>{
        setTimeout(() => {
            hideTypingIndicator();
            appendMessage(data,"bot");

            if (data.trim() === "Please wait for the admin to respond.") {
                pollForAdminAnswer(message);
            }
        }, TYPING_ANIMATION_DELAY);
    })
    .catch(()=>{
        setTimeout(() => {
            hideTypingIndicator();
            appendMessage("Something went wrong.","bot");
        }, TYPING_ANIMATION_DELAY);
    });
}

function askFaq(question, answer) {
    appendMessage(question,"user");
    closeFaqSection();

    if (typeof answer === 'string' && answer.trim() !== '') {
        showTypingIndicator();
        setTimeout(() => {
            hideTypingIndicator();
            appendMessage(answer,"bot");
        }, TYPING_ANIMATION_DELAY);
        return;
    }

    fetchChatResponse(question);
}

function normalizeQuestion(text) {
    return text
        .toLowerCase()
        .replace(/[^a-z0-9\s]/g, '')
        .replace(/\s+/g, ' ')
        .trim();
}

function pollForAdminAnswer(message) {
    const normalized = normalizeQuestion(message);
    const pollInterval = 3000;
    let attempts = 0;
    const maxAttempts = 50;

    const intervalId = setInterval(() => {
        fetch(`chat.php?action=check&question=${encodeURIComponent(normalized)}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'answered') {
                    showTypingIndicator();
                    setTimeout(() => {
                        hideTypingIndicator();
                        appendMessage(data.answer, 'bot');
                    }, 800);
                    clearInterval(intervalId);
                }
            })
            .catch(() => {
                // ignore polling errors
            });

        attempts += 1;
        if (attempts >= maxAttempts) {
            clearInterval(intervalId);
        }
    }, pollInterval);
}

function enableChatInput() {
    if (!userInput.disabled) return;
    setChatEnabled(true);
    appendMessage("Sure — type your question now.","bot");
    userInput.focus();
}

function loadFAQ() {
    fetch("chat.php?action=faq")
    .then(response => {
        if (!response.ok) throw new Error(`HTTP ${response.status} ${response.statusText}`);
        return response.json();
    })
    .then(data => {
        renderFAQ(data);
    })
    .catch(error => {
        console.error("Failed to load FAQs:", error);
        faqList.innerHTML = '<div class="faq-card">Unable to load frequently asked questions.</div>';
    });
}

function renderFAQ(items) {
    faqList.innerHTML = "";

    if (!Array.isArray(items) || items.length === 0) {
        faqList.innerHTML = '<div class="faq-card">No frequently asked questions available.</div>';
        const chatButton = document.createElement("button");
        chatButton.type = "button";
        chatButton.className = "faq-card other";
        chatButton.textContent = "Chat";
        chatButton.addEventListener("click", enableChatInput);
        faqList.appendChild(chatButton);
        return;
    }

    items.forEach(item => {
        const button = document.createElement("button");
        button.type = "button";
        button.className = "faq-card";
        button.textContent = item.question;
        button.addEventListener("click", () => askFaq(item.question, item.answer));
        faqList.appendChild(button);
    });

    const chatButton = document.createElement("button");
    chatButton.type = "button";
    chatButton.className = "faq-card other";
    chatButton.textContent = "Chat";
    chatButton.addEventListener("click", enableChatInput);
    faqList.appendChild(chatButton);
}

function toggleFaqSection() {
    const faqSection = document.getElementById("faqSection");

    if (!faqSection) return;

    const expanded = !faqSection.classList.contains("expanded");
    faqSection.classList.toggle("expanded", expanded);
    faqSection.classList.toggle("collapsed", !expanded);
    toggleFaqBtn.textContent = expanded ? "Close" : "Browse FAQs";
}

function closeFaqSection() {
    const faqSection = document.getElementById("faqSection");

    if (!faqSection) return;

    faqSection.classList.add("collapsed");
    faqSection.classList.remove("expanded");
    if (toggleFaqBtn) {
        toggleFaqBtn.textContent = "Browse FAQs";
    }
}

function appendMessage(text,type){

    const msg=document.createElement("div");
    msg.className="message "+type;

    const bubble=document.createElement("div");
    bubble.className="bubble";
    bubble.innerHTML=text;

    msg.appendChild(bubble);

    chatArea.appendChild(msg);

    chatArea.scrollTop=chatArea.scrollHeight;

}