// ===========================
// AI DELAS-ALAS Admin Panel
// admin.js
// ===========================

const questionTable = document.getElementById("questionTable");
const faqTable = document.getElementById("faqTable");
const knowledgeTable = document.getElementById("knowledgeTable");

const knowledgeCount = document.getElementById("knowledgeCount");
const unansweredCount = document.getElementById("unansweredCount");
const answerCount = document.getElementById("answerCount");

const answerModal = document.getElementById("answerModal");
const closeModal = document.querySelector(".close");

const answerForm = document.getElementById("answerForm");

const questionID = document.getElementById("questionID");
const questionSource = document.getElementById("questionSource");
const modalTitle = document.getElementById("modalTitle");
const questionText = document.getElementById("questionText");
const answerText = document.getElementById("answerText");

const faqForm = document.getElementById("faqForm");
const faqQuestion = document.getElementById("faqQuestion");
const faqAnswer = document.getElementById("faqAnswer");
const faqId = document.getElementById("faqId");
const faqSubmitBtn = document.getElementById("faqSubmitBtn");
const cancelFaqEditBtn = document.getElementById("cancelFaqEditBtn");

const refreshBtn = document.getElementById("refreshBtn");

let editingFaqId = null;

const searchQuestion = document.getElementById("searchQuestion");
const searchFaq = document.getElementById("searchFaq");
const searchKnowledge = document.getElementById("searchKnowledge");

document.addEventListener("DOMContentLoaded", () => {
    if (refreshBtn) {
        refreshBtn.addEventListener("click", () => loadDashboard());
        refreshBtn.onclick = () => loadDashboard();
    }

    loadDashboard();
    setInterval(() => {
        loadStats();
        loadUnanswered();
    }, 3000);
});

//=============================
// Load Everything
//=============================

function loadDashboard(){

    loadStats();
    loadUnanswered();
    loadFaqs();
    loadKnowledge();

}

//=============================
// Statistics
//=============================

function loadStats(){

    fetch("admin.php?action=stats")
    .then(response => {
        if (!response.ok) throw new Error(`HTTP ${response.status} ${response.statusText}`);
        return response.json();
    })
    .then(data => {

        knowledgeCount.innerText = data.knowledge;
        unansweredCount.innerText = data.unanswered;
        answerCount.innerText = data.answers;

    })
    .catch(error => {
        console.error("Failed to load stats:", error);
    });

}

//=============================
// Unanswered Questions
//=============================

function loadUnanswered(){

    fetch("admin.php?action=unanswered")
    .then(response => {
        if (!response.ok) throw new Error(`HTTP ${response.status} ${response.statusText}`);
        return response.json();
    })
    .then(data=>{

        questionTable.innerHTML="";

        data.forEach(question=>{

            questionTable.innerHTML += `
                <tr>

                    <td>${question.id}</td>

                    <td>${question.question}</td>

                    <td>${question.asked_count}</td>

                    <td>

                        <button
                            class="answer-btn"
                            onclick="openModal(${question.id}, '${escapeQuotes(question.question)}', 'unanswered')">

                            Answer

                        </button>

                    </td>

                </tr>
            `;

        });

    })
    .catch(error => {
        console.error("Failed to load unanswered questions:", error);
        questionTable.innerHTML = "<tr><td colspan=4>No unanswered questions could be loaded.</td></tr>";
    });

}

//=============================
// FAQ List
//=============================

function loadFaqs(){

    fetch("admin.php?action=faqs")
    .then(response => {
        if (!response.ok) throw new Error(`HTTP ${response.status} ${response.statusText}`);
        return response.json();
    })
    .then(data=>{

        faqTable.innerHTML="";

        data.forEach(item=>{

            faqTable.innerHTML += `
                <tr>

                    <td>${item.id}</td>

                    <td>${item.question}</td>

                    <td>${item.answer}</td>

                    <td>

                        <button
                            class="icon-btn edit-btn"
                            onclick="editFaq(${item.id}, '${escapeQuotes(item.question)}', '${escapeQuotes(item.answer)}')"
                            title="Edit FAQ">

                            ✏️

                        </button>

                        <button
                            class="icon-btn delete-btn"
                            onclick="deleteFaq(${item.id})"
                            title="Delete FAQ">

                            🗑️

                        </button>

                    </td>

                </tr>
            `;

        });

    })
    .catch(error => {
        console.error("Failed to load FAQs:", error);
        faqTable.innerHTML = "<tr><td colspan=4>No FAQs could be loaded.</td></tr>";
    });

}

//=============================
// Knowledge Base
//=============================

function loadKnowledge(){

    fetch("admin.php?action=knowledge")
    .then(response=>{
        if (!response.ok) throw new Error(`HTTP ${response.status} ${response.statusText}`);
        return response.json();
    })
    .then(data=>{

        knowledgeTable.innerHTML="";

        data.forEach(item=>{

            knowledgeTable.innerHTML += `
            <tr>

                <td>${item.id}</td>

                <td>${item.question}</td>

                <td>${item.answer}</td>

                <td>

                    <button
                        class="icon-btn delete-btn"
                        onclick="deleteAnswer(${item.id})"
                        title="Delete Answer">

                        🗑️

                    </button>
                    <button
                        class="answer-btn"
                        onclick="openModal(${item.id}, '${escapeQuotes(item.question)}', 'knowledge')">

                        Add Answer

                    </button>

                </td>

            </tr>
            `;

        });

    })
    .catch(error => {
        console.error("Failed to load knowledge base:", error);
        knowledgeTable.innerHTML = "<tr><td colspan=4>No knowledge base items could be loaded.</td></tr>";
    });

}

//=============================
// Open Modal
//=============================

function openModal(id, question, source){

    source = source || "unanswered";

    questionID.value = id;

    questionSource.value = source;

    questionText.value = question;

    answerText.value = "";

    modalTitle.innerText = source === "knowledge" ? "Add Answer to Knowledge Item" : "Answer Unanswered Question";

    answerModal.style.display = "flex";

}

//=============================
// Close Modal
//=============================

closeModal.onclick=function(){

    answerModal.style.display="none";

}

window.onclick=function(e){

    if(e.target==answerModal){

        answerModal.style.display="none";

    }

}

//=============================
// Save Answer
//=============================

answerForm.addEventListener("submit",function(e){

    e.preventDefault();

    const formData=new FormData();

    formData.append("action","save");

    formData.append("id",questionID.value);

    formData.append("question",questionText.value);

    formData.append("answer",answerText.value);

    formData.append("source",questionSource.value);

    fetch("admin.php",{

        method:"POST",

        body:formData

    })
    .then(response => {
        if (!response.ok) throw new Error(`HTTP ${response.status} ${response.statusText}`);
        return response.json();
    })
    .then(result=>{

        if (result.status === "success") {
            alert(result.message);
            answerModal.style.display="none";
            loadDashboard();
        } else {
            alert(result.message || "Unable to save answer.");
        }

    })
    .catch(error => {
        console.error("Failed to save answer:", error);
        alert("Unable to save answer. Check the browser console for details.");
    });

});

function editFaq(id, question, answer) {
    faqId.value = id;
    faqQuestion.value = question;
    faqAnswer.value = answer;
    editingFaqId = id;
    faqSubmitBtn.textContent = "Save Changes";
    cancelFaqEditBtn.style.display = "inline-block";
    faqQuestion.focus();
    faqQuestion.scrollIntoView({ behavior: "smooth", block: "center" });
}

function resetFaqForm() {
    faqForm.reset();
    faqId.value = "";
    editingFaqId = null;
    faqSubmitBtn.textContent = "Add FAQ";
    cancelFaqEditBtn.style.display = "none";
}

cancelFaqEditBtn.addEventListener("click", resetFaqForm);

faqForm.addEventListener("submit", function(e) {
    e.preventDefault();

    const question = faqQuestion.value.trim();
    const answer = faqAnswer.value.trim();

    if (question === "" || answer === "") {
        alert("Please enter both a question and an answer.");
        return;
    }

    const formData = new FormData();
    formData.append("action", editingFaqId ? "faq_update" : "faq_add");
    formData.append("question", question);
    formData.append("answer", answer);

    if (editingFaqId) {
        formData.append("id", editingFaqId);
    }

    fetch("admin.php", {
        method: "POST",
        body: formData
    })
    .then(response => {
        if (!response.ok) throw new Error(`HTTP ${response.status} ${response.statusText}`);
        return response.json();
    })
    .then(result => {
        if (result.status === "success") {
            alert(result.message);
            resetFaqForm();
            loadDashboard();
        } else {
            alert(result.message || (editingFaqId ? "Unable to update FAQ." : "Unable to add FAQ."));
        }
    })
    .catch(error => {
        console.error("Failed to save FAQ:", error);
        alert(editingFaqId ? "Unable to update FAQ. Check the browser console for details." : "Unable to add FAQ. Check the browser console for details.");
    });
});

//=============================
// Delete Answer
//=============================

function deleteFaq(id){

    if(!confirm("Delete this FAQ?")){

        return;

    }

    const formData=new FormData();

    formData.append("action","delete_faq");

    formData.append("id",id);

    fetch("admin.php",{

        method:"POST",

        body:formData

    })
    .then(response => {
        if (!response.ok) throw new Error(`HTTP ${response.status} ${response.statusText}`);
        return response.json();
    })
    .then(result=>{

        if (result.status === "success") {
            alert(result.message);
            loadDashboard();
        } else {
            alert(result.message || "Unable to delete FAQ.");
        }

    })
    .catch(error => {
        console.error("Failed to delete FAQ:", error);
        alert("Unable to delete FAQ. Check the browser console for details.");
    });

}

function deleteAnswer(id){

    if(!confirm("Delete this answer?")){

        return;

    }

    const formData=new FormData();

    formData.append("action","delete");

    formData.append("id",id);

    fetch("admin.php",{

        method:"POST",

        body:formData

    })
    .then(response => {
        if (!response.ok) throw new Error(`HTTP ${response.status} ${response.statusText}`);
        return response.json();
    })
    .then(result=>{

        if (result.status === "success") {
            alert(result.message);
            loadDashboard();
        } else {
            alert(result.message || "Unable to delete answer.");
        }

    })
    .catch(error => {
        console.error("Failed to delete answer:", error);
        alert("Unable to delete answer. Check the browser console for details.");
    });

}

//=============================
// Search Unanswered
//=============================

searchQuestion.addEventListener("keyup",function(){

    let filter=this.value.toLowerCase();

    let rows=questionTable.getElementsByTagName("tr");

    for(let i=0;i<rows.length;i++){

        let text=rows[i].innerText.toLowerCase();

        rows[i].style.display=text.includes(filter) ? "" : "none";

    }

});

searchFaq.addEventListener("keyup",function(){

    let filter=this.value.toLowerCase();

    let rows=faqTable.getElementsByTagName("tr");

    for(let i=0;i<rows.length;i++){

        let text=rows[i].innerText.toLowerCase();

        rows[i].style.display=text.includes(filter) ? "" : "none";

    }

});

//=============================
// Search Knowledge
//=============================

searchKnowledge.addEventListener("keyup",function(){

    let filter=this.value.toLowerCase();

    let rows=knowledgeTable.getElementsByTagName("tr");

    for(let i=0;i<rows.length;i++){

        let text=rows[i].innerText.toLowerCase();

        rows[i].style.display=text.includes(filter) ? "" : "none";

    }

});

//=============================
// Escape Quotes
//=============================

function escapeQuotes(text){

    return text
        .replace(/'/g,"&#39;")
        .replace(/"/g,"&quot;");

}