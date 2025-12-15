// =======================
// Loader
// =======================
window.addEventListener("load", function () {
  const loader = document.getElementById("loader");
  if (loader) loader.style.display = "none";
});

// =======================
// SAFETY: Check questions
// =======================
if (typeof questions === "undefined" || !Array.isArray(questions) || questions.length === 0) {
  const qt = document.getElementById("question-text");
  if (qt) qt.innerHTML = "❌ No questions available for this round.";
  const btn = document.getElementById("start-timer-btn");
  if (btn) btn.style.display = "none";
  throw new Error("No questions found");
}

// =======================
// Timer
// =======================
let timerInterval;
let timeLeft = typeof TIME_LIMIT !== "undefined" ? TIME_LIMIT : 10;

function startTimer() {
  const timerDisplay = document.getElementById("time-remaining");
  const timerContainer = document.getElementById("timer");
  const startButton = document.getElementById("start-timer-btn");

  if (!timerDisplay || !timerContainer || !startButton) return;

  timerContainer.classList.remove("hidden");
  startButton.classList.add("hidden");

  timerDisplay.textContent = formatTime(timeLeft);

  timerInterval = setInterval(() => {
    timeLeft--;
    timerDisplay.textContent = formatTime(timeLeft);

    if (timeLeft <= 0) {
      clearInterval(timerInterval);
      timerDisplay.textContent = "Time's Up!";
      lockOptions();
      showNextButton();
    }
  }, 1000);
}

function resetTimer() {
  clearInterval(timerInterval);
  timeLeft = typeof TIME_LIMIT !== "undefined" ? TIME_LIMIT : 10;

  const timerContainer = document.getElementById("timer");
  const startButton = document.getElementById("start-timer-btn");

  if (timerContainer) timerContainer.classList.add("hidden");
  if (startButton) startButton.classList.remove("hidden");
}

function formatTime(seconds) {
  const m = Math.floor(seconds / 60);
  const s = seconds % 60;
  return `${m}:${s.toString().padStart(2, "0")}`;
}

// =======================
// Quiz Logic
// =======================
let currentQuestionIndex = 0;
let score = 0;
let correctAnswers = 0;
let wrongAnswers = 0;

const questionText = document.getElementById("question-text");
const optionsList = document.getElementById("options-list");
const feedbackDiv = document.getElementById("feedback");
const explanationDiv = document.getElementById("explanation");
const nextBtn = document.getElementById("next-btn");
const backBtn = document.getElementById("back-btn");

const optionLabels = ["A", "B", "C", "D"];

loadQuestion();

function loadQuestion() {
  const q = questions[currentQuestionIndex];

  questionText.innerHTML = q.question;
  optionsList.innerHTML = "";
  feedbackDiv.classList.add("hidden");
  explanationDiv.classList.add("hidden");
  nextBtn.classList.add("hidden");
  backBtn.classList.toggle("hidden", currentQuestionIndex === 0);

  updateProgressBar();

  q.options.forEach((opt, idx) => {
    const li = document.createElement("li");
    li.innerHTML = `
      <span class="option-label">${optionLabels[idx]}.</span>
      <span class="option-text">${opt}</span>
    `;
    li.onclick = () => checkAnswer(li, opt, q.correct);
    optionsList.appendChild(li);
  });

  resetTimer();
  startTimer();
}

function checkAnswer(selected, chosen, correct) {
  lockOptions();

  if (chosen === correct) {
    selected.classList.add("correct");
    score += 2;
    correctAnswers++;
  } else {
    selected.classList.add("incorrect");
    wrongAnswers++;

    document.querySelectorAll(".options li").forEach(li => {
      if (li.querySelector(".option-text").textContent === correct) {
        li.classList.add("correct");
      }
    });
  }

  feedbackDiv.classList.remove("hidden");
  explanationDiv.innerHTML =
    `<strong>Solution:</strong><br>${questions[currentQuestionIndex].explanation ?? ""}`;
  explanationDiv.classList.remove("hidden");
  nextBtn.classList.remove("hidden");
}

function lockOptions() {
  document.querySelectorAll(".options li").forEach(li => li.onclick = null);
}

function showNextButton() {
  nextBtn.classList.remove("hidden");
}

function loadPreviousQuestion() {
  if (currentQuestionIndex > 0) {
    currentQuestionIndex--;
    loadQuestion();
  }
}

function loadNextQuestion() {
  resetTimer();
  currentQuestionIndex++;

  if (currentQuestionIndex < questions.length) {
    loadQuestion();
  } else {
    finishRound();
  }
}

// =======================
// End of Round (NO redirect bug)
// =======================
function finishRound() {
  hideTimerUI();
  updateProgressBar(100);
  saveResult();

  questionText.innerHTML = `
    <div class="completion-message">
      🎉 Round Completed 🎉<br><br>
      <strong>Score:</strong> ${score}<br>
      Correct: ${correctAnswers}<br>
      Wrong: ${wrongAnswers}<br><br>

      <a href="../user/dashboard.php">
        <button>Back to Dashboard</button>
      </a>
    </div>
  `;

  optionsList.innerHTML = "";
  feedbackDiv.innerHTML = "";
  explanationDiv.innerHTML = "";
  nextBtn.classList.add("hidden");
  backBtn.classList.add("hidden");

  addConfettiEffect();
}

// =======================
// Save Result
// =======================
function saveResult() {
  fetch("../question/save-result.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      quiz_id: QUIZ_ID,
      score: score,
      correct: correctAnswers,
      wrong: wrongAnswers
    })
  })
  .then(res => res.json())
  .then(data => {
    console.log("RESULT SAVE RESPONSE →", data);
  })
  .catch(err => {
    console.error("SAVE FAILED →", err);
  });
}


// =======================
// UI Helpers
// =======================
function hideTimerUI() {
  const timer = document.getElementById("timer");
  const startBtn = document.getElementById("start-timer-btn");

  if (timer) timer.classList.add("hidden");
  if (startBtn) startBtn.classList.add("hidden");

  clearInterval(timerInterval);
}

function updateProgressBar(force = null) {
  const bar = document.getElementById("progress-bar");
  if (!bar) return;

  const progress = force !== null
    ? force
    : ((currentQuestionIndex + 1) / questions.length) * 100;

  bar.style.width = `${progress}%`;
}

// =======================
// Confetti
// =======================
function addConfettiEffect() {
  const box = document.createElement("div");
  box.id = "confetti";
  document.body.appendChild(box);

  for (let i = 0; i < 80; i++) {
    const c = document.createElement("div");
    c.className = "confetti";
    c.style.left = Math.random() * 100 + "%";
    c.style.backgroundColor = `hsl(${Math.random() * 360},100%,50%)`;
    box.appendChild(c);
  }

  setTimeout(() => box.remove(), 3000);
}
