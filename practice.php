<?php
//Author Caleb McHaney
//practice.php is the main file for handling practice sessions on the student view allowing the students to do practice exercises

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include "db_connect.php";

$role = $_SESSION['role'] ?? '';
if (!isset($_SESSION['user_id']) || ($role !== 'student' && $role !== 'teacher' && $role !== 'admin')) {
    header("Location: login.php");
    exit;
}

//get the lesson id from the URL or from the session if it was set previously
$lesson_id = intval($_GET['lesson_id'] ?? $_SESSION['practice_lesson_id'] ?? 0);

//if no lesson id is found send the student back to the lesson page
if (!$lesson_id) {
    header("Location: lesson.php");
    exit;
}

//look up the grade that this lesson belongs to, also verify the lesson belongs to the student's school
$grade_id = 0;
$school_id = intval($_SESSION['school_id'] ?? 0);

$stmt = $conn->prepare("SELECT grade_id FROM Lesson WHERE lesson_id = ? AND school_id = ?");
$stmt->bind_param("ii", $lesson_id, $school_id);
$stmt->execute();
$lesson = $stmt->get_result()->fetch_assoc();

$grade_id = intval($lesson['grade_id'] ?? 0);

//handles the chatbot request sent from the browser via fetch
if (isset($_POST['action']) && $_POST['action'] === 'ai_help') {
    header('Content-Type: application/json');

    $question_text  = trim($_POST['question_text']   ?? '');
    $options        = $_POST['options']              ?? [];
    $correct_option = strtoupper(trim($_POST['correct_option'] ?? ''));
    $user_message   = trim($_POST['user_message']    ?? '');
    $history        = json_decode($_POST['conversation'] ?? '[]', true);
    if (!is_array($history)) $history = [];

    // Build a system prompt with the full question context so the AI can guide the student
    $opts_text     = "A: {$options['a']}\nB: {$options['b']}\nC: {$options['c']}\nD: {$options['d']}";
    $system_prompt = "You are a helpful math tutor for elementary school students. "
        . "The student answered the following question incorrectly:\n\n"
        . "Question: {$question_text}\n{$opts_text}\n"
        . "Correct answer: {$correct_option}\n\n"
        . "Guide the student step-by-step without revealing the correct answer directly. "
        . "Ask guiding questions to help them reason through the problem. "
        . "Keep explanations simple, patient, and encouraging. Use visuals for assistance and ask for interests or hobbies that can be incorporated into examples to make it more engaging.";

    // Assemble the messages array: system prompt + prior conversation + latest student message
    $messages = [['role' => 'system', 'content' => $system_prompt]];
    foreach ($history as $turn) {
        if (isset($turn['role'], $turn['content'])) {
            $messages[] = ['role' => $turn['role'], 'content' => $turn['content']];
        }
    }
    $messages[] = ['role' => 'user', 'content' => $user_message];

    // Call the OpenAI API
    $apiKey = getenv('OPENAI_API_KEY');
    $payload = ['model' => 'gpt-3.5-turbo', 'messages' => $messages];

    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: Bearer $apiKey",
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $raw      = curl_exec($ch);

    $response = json_decode($raw, true);
    $reply    = $response['choices'][0]['message']['content'] ?? 'Sorry, I could not process that.';
    echo json_encode(['reply' => $reply]);
    exit;
}

//start a new quiz session or resume the existing one for this lesson
$reset = isset($_GET['reset']);

if ($reset || !isset($_SESSION['practice']) || $_SESSION['practice']['lesson_id'] !== $lesson_id) {

    //fetch all questions for this lesson from the database
    $stmt = $conn->prepare(
        "SELECT question_id, question_text, option_a, option_b, option_c, option_d, correct_option
         FROM Questions
         WHERE lesson_id = ?
         ORDER BY question_id ASC"
    );
    $stmt->bind_param("i", $lesson_id);
    $stmt->execute();
    $result    = $stmt->get_result();
    $questions = [];
    while ($row = $result->fetch_assoc()) $questions[] = $row;

    //if no questions exist for this lesson send the student back
    if (empty($questions)) {
        $_SESSION['practice_error'] = "No practice questions are available for this lesson yet.";
        header("Location: lesson.php");
        exit;
    }

    //get the lesson title to display on the page
    $lstmt = $conn->prepare("SELECT lesson_title FROM Lesson WHERE lesson_id = ?");
    $lstmt->bind_param("i", $lesson_id);
    $lstmt->execute();
    $lesson_row   = $lstmt->get_result()->fetch_assoc();
    $lesson_title = $lesson_row['lesson_title'] ?? 'Practice';

    //store the quiz state in the session so it persists across page loads
    $_SESSION['practice'] = [
        'lesson_id'    => $lesson_id,
        'lesson_title' => $lesson_title,
        'questions'    => $questions,
        'current'      => 0,
        'answers'      => [],
        'done'         => false,
        'skipped'      => [],
    ];
    $_SESSION['practice_lesson_id'] = $lesson_id;
}

//reference the session quiz data for easy access throughout the page
$quiz = &$_SESSION['practice'];

//handles when the student submits an answer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_answer'])) {
    $chosen     = strtoupper(trim($_POST['chosen_option'] ?? ''));
    $idx        = intval($_POST['question_index'] ?? $quiz['current']);
    $q          = $quiz['questions'][$idx];
    $is_correct = ($chosen === $q['correct_option']);
    $used_ai    = isset($_POST['used_ai']) && $_POST['used_ai'] === '1';

    //record the students answer for this question
    if (!isset($quiz['answers'][$q['question_id']])) {
        $quiz['answers'][$q['question_id']] = [
            'chosen'     => $chosen,
            'correct'    => $q['correct_option'],
            'is_correct' => $is_correct,
            'used_ai'    => $used_ai,
        ];
    } elseif ($used_ai) {
        //update the answer if the student retried after using AI help
        $quiz['answers'][$q['question_id']]['used_ai']    = true;
        $quiz['answers'][$q['question_id']]['is_correct'] = $is_correct;
        $quiz['answers'][$q['question_id']]['chosen']     = $chosen;
    }

    if ($is_correct) {
        //advance to the next question or mark the quiz as done
        $next = $idx + 1;
        $quiz['done']    = ($next >= count($quiz['questions']));
        $quiz['current'] = $quiz['done'] ? $idx : $next;
        header("Location: practice.php?lesson_id={$lesson_id}");
        exit;
    } else {
        //send the student to the wrong answer screen
        header("Location: practice.php?lesson_id={$lesson_id}&wrong=1&idx={$idx}");
        exit;
    }
}

//handles when the student skips AI help and moves to the next question
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['skip_to_next'])) {
    $idx = intval($_POST['question_index'] ?? $quiz['current']);
    $q   = $quiz['questions'][$idx];

    //record a wrong answer if nothing was recorded yet
    if (!isset($quiz['answers'][$q['question_id']])) {
        $quiz['answers'][$q['question_id']] = [
            'chosen'     => strtoupper(trim($_POST['last_chosen'] ?? '')),
            'correct'    => $q['correct_option'],
            'is_correct' => false,
            'used_ai'    => false,
        ];
    }

    $next = $idx + 1;
    $quiz['done']    = ($next >= count($quiz['questions']));
    $quiz['current'] = $quiz['done'] ? $idx : $next;
    header("Location: practice.php?lesson_id={$lesson_id}");
    exit;
}

//handles when the student skips a question to come back to at the end
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['skip_question'])) {
    $idx = intval($_POST['question_index'] ?? $quiz['current']);
    $q   = $quiz['questions'][$idx];
    $qid = $q['question_id'];

    if (!in_array($qid, $quiz['skipped'] ?? [])) {
        $quiz['skipped'][] = $qid;
        array_splice($quiz['questions'], $idx, 1);
        $quiz['questions'][] = $q;
        if ($quiz['current'] >= count($quiz['questions'])) {
            $quiz['current'] = count($quiz['questions']) - 1;
        }
    }

    header("Location: practice.php?lesson_id={$lesson_id}");
    exit;
}

//handles when the student chooses to submit the quiz with skipped questions unanswered
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_anyway'])) {
    foreach ($quiz['questions'] as $q) {
        if (!isset($quiz['answers'][$q['question_id']])) {
            $quiz['answers'][$q['question_id']] = [
                'chosen'     => '',
                'correct'    => $q['correct_option'],
                'is_correct' => false,
                'used_ai'    => false,
                'skipped'    => true,
            ];
        }
    }
    $quiz['done'] = true;
    header("Location: practice.php?lesson_id={$lesson_id}");
    exit;
}

//determine which screen to show based on the current state
$show_wrong_prompt = isset($_GET['wrong']) && isset($_GET['idx']);
$wrong_idx         = intval($_GET['idx'] ?? $quiz['current']);
$current_idx       = $quiz['current'];
$total             = count($quiz['questions']);
$current_q         = $quiz['questions'][$current_idx] ?? null;

//calculate the score and skipped count once the quiz is finished
$score         = 0;
$skipped_count = 0;
if ($quiz['done']) {
    foreach ($quiz['answers'] as $a) {
        if ($a['is_correct'])      $score++;
        if (!empty($a['skipped'])) $skipped_count++;
    }
}

//save the attempt to the database the first time the results screen is shown
if ($quiz['done'] && empty($quiz['saved'])) {
    $user_id = intval($_SESSION['user_id']);

    $save_stmt = $conn->prepare(
        "INSERT INTO QuizAttempts (user_id, lesson_id, score, total_questions)
         VALUES (?, ?, ?, ?)"
    );
    $save_stmt->bind_param("iiii", $user_id, $lesson_id, $score, $total);
    $save_stmt->execute();
    $attempt_id = $conn->insert_id;

    foreach ($quiz['questions'] as $q) {
        $ans = $quiz['answers'][$q['question_id']] ?? null;
        if (!$ans) continue;

        $chosen       = $ans['chosen']     ?? '';
        $correct      = $ans['correct']    ?? $q['correct_option'];
        $is_correct   = $ans['is_correct'] ? 1 : 0;
        $used_ai      = $ans['used_ai']    ? 1 : 0;
        $skipped_flag = !empty($ans['skipped']) ? 1 : 0;

        $ans_stmt = $conn->prepare(
            "INSERT INTO QuizAttemptAnswers
                (attempt_id, question_id, chosen_option, correct_option, is_correct, used_ai, skipped)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $ans_stmt->bind_param("iissiii", $attempt_id, $q['question_id'], $chosen, $correct, $is_correct, $used_ai, $skipped_flag);
        $ans_stmt->execute();
    }

    $quiz['saved'] = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('includes/header.php'); ?>
    <title>Practice – <?= htmlspecialchars($quiz['lesson_title']) ?></title>
    <link rel="stylesheet" href="practice.css"/>
</head>
<body>
    <?php include('includes/nav.php'); ?>

    <main>

        <?php if ($quiz['done']): ?>
        <!-- ── RESULTS SCREEN ── -->

        <div class="results-hero">
            <h2>Quiz Complete!</h2>
            <div class="score-big"><?= $score ?> / <?= $total ?></div>
            <div class="score-pct"><?= round(($score / max($total, 1)) * 100) ?>% correct</div>
            <?php if ($skipped_count > 0): ?>
            <div class="score-skipped"><?= $skipped_count ?> question<?= $skipped_count > 1 ? 's' : '' ?> skipped</div>
            <?php endif; ?>
        </div>

        <h3 style="margin-bottom:1rem; color:var(--text-primary);">Question Review</h3>

        <div class="review-list">
        <?php foreach ($quiz['questions'] as $i => $q):
            $ans        = $quiz['answers'][$q['question_id']] ?? null;
            $is_correct = $ans && $ans['is_correct'];
            $was_skipped = $ans && !empty($ans['skipped']);
            $chosen     = $ans['chosen']  ?? '—';
            $correct    = $ans['correct'] ?? $q['correct_option'];
            $used_ai    = $ans['used_ai'] ?? false;
            $options    = ['A' => $q['option_a'], 'B' => $q['option_b'],
                           'C' => $q['option_c'], 'D' => $q['option_d']];
            $item_class = $is_correct ? 'correct' : ($was_skipped ? 'skipped' : 'incorrect');
        ?>
            <div class="review-item <?= $item_class ?>">
                <div class="review-item-header">
                    <span class="review-q-text">Q<?= $i + 1 ?>: <?= htmlspecialchars($q['question_text']) ?></span>
                    <span>
                        <?php if ($was_skipped): ?>
                            <span class="review-badge badge-skipped">Skipped</span>
                        <?php else: ?>
                        <span class="review-badge <?= $is_correct ? 'badge-correct' : 'badge-incorrect' ?>">
                            <?= $is_correct ? '✓ Correct' : '✗ Incorrect' ?>
                        </span>
                        <?php endif; ?>
                        <?php if ($used_ai): ?>
                            <span class="review-badge badge-ai">AI Help</span>
                        <?php endif; ?>
                    </span>
                </div>
                <ul class="review-options">
                    <?php foreach ($options as $letter => $text): ?>
                        <?php
                            $cls = '';
                            if ($letter === $correct) $cls = 'opt-correct';
                            elseif ($letter === $chosen && !$is_correct) $cls = 'opt-wrong-chosen';
                        ?>
                        <li class="<?= $cls ?>">
                            <strong><?= $letter ?>:</strong> <?= htmlspecialchars($text) ?>
                            <?php if ($letter === $correct): ?> — Correct answer<?php endif; ?>
                            <?php if ($letter === $chosen && !$is_correct): ?> — Your answer<?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
        </div>

        <div class="btn-row">
            <a href="practice.php?lesson_id=<?= $lesson_id ?>&reset=1" class="btn-primary">Retry This Lesson</a>
            <a href="lesson.php?lesson_id=<?= $lesson_id ?>" class="btn-secondary">Back to Lesson</a>
        </div>


        <?php elseif ($show_wrong_prompt): ?>
        <!-- ── WRONG ANSWER SCREEN ── -->
        <?php $wq = $quiz['questions'][$wrong_idx]; ?>

        <div class="wrong-card">
            <h2>Not quite!</h2>
            <p>That answer wasn't correct. Would you like some help working through it?</p>
            <div class="btn-row">
                <button type="button" id="btn-get-help" class="btn-primary">Yes, help me understand</button>
                <form method="POST" action="practice.php?lesson_id=<?= $lesson_id ?>" style="display:contents;">
                    <input type="hidden" name="question_index" value="<?= $wrong_idx ?>">
                    <input type="hidden" name="last_chosen"
                           value="<?= htmlspecialchars($quiz['answers'][$wq['question_id']]['chosen'] ?? '') ?>">
                    <button type="submit" name="skip_to_next" class="btn-secondary">No thanks, move on</button>
                </form>
            </div>
        </div>

        <!-- Chatbot panel — hidden until student clicks "Yes" -->
        <div id="chatbot-panel" style="display:none;">
            <div class="chatbot-card">
                <h3>Tutor Chat</h3>
                <div class="chatbot-context"><?= htmlspecialchars($wq['question_text']) ?></div>

                <div id="chat-messages"></div>

                <div class="chat-input-row">
                    <textarea id="chat-input" rows="2"
                        placeholder="Ask a question or describe what you're confused about..."></textarea>
                    <button type="button" id="btn-send-chat" class="btn-primary">Send</button>
                </div>

                <hr class="chatbot-divider">

                <!-- Retry form — shown inside the chatbot panel -->
                <h4 style="margin-bottom:1rem; color:var(--text-primary);">Try the question again:</h4>
                <form method="POST" action="practice.php?lesson_id=<?= $lesson_id ?>">
                    <input type="hidden" name="question_index" value="<?= $wrong_idx ?>">
                    <input type="hidden" name="used_ai" value="1">

                    <p class="question-text"><?= htmlspecialchars($wq['question_text']) ?></p>

                    <ul class="options-list">
                    <?php foreach (['a'=>'A','b'=>'B','c'=>'C','d'=>'D'] as $key => $letter): ?>
                        <li>
                            <label class="option-label">
                                <input type="radio" name="chosen_option" value="<?= $letter ?>" required>
                                <span class="option-letter"><?= $letter ?></span>
                                <?= htmlspecialchars($wq["option_{$key}"]) ?>
                            </label>
                        </li>
                    <?php endforeach; ?>
                    </ul>

                    <div class="btn-row">
                        <button type="submit" name="submit_answer" class="btn-primary">Submit Answer</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Hidden span that stores question data for the JavaScript chatbot to read -->
        <span id="js-question-data"
              data-text="<?= htmlspecialchars($wq['question_text']) ?>"
              data-a="<?= htmlspecialchars($wq['option_a']) ?>"
              data-b="<?= htmlspecialchars($wq['option_b']) ?>"
              data-c="<?= htmlspecialchars($wq['option_c']) ?>"
              data-d="<?= htmlspecialchars($wq['option_d']) ?>"
              data-correct="<?= htmlspecialchars($wq['correct_option']) ?>"
              style="display:none;">
        </span>


        <?php else: ?>
        <!-- ── NORMAL QUESTION VIEW ── -->

        <div class="practice-header">
            <h2><?= htmlspecialchars($quiz['lesson_title']) ?> — Practice</h2>
            <p>Question <?= $current_idx + 1 ?> of <?= $total ?></p>
        </div>

        <div class="progress-wrap">
            <div class="progress-label">
                <span>Progress</span>
                <span><?= $current_idx ?> / <?= $total ?> completed</span>
            </div>
            <div class="progress-bar">
                <div class="progress-bar-fill"
                     style="width:<?= round(($current_idx / max($total, 1)) * 100) ?>%"></div>
            </div>
        </div>

        <?php if ($current_q):
            $already_skipped = in_array($current_q['question_id'], $quiz['skipped'] ?? []);
        ?>

        <?php if ($already_skipped): ?>
        <div class="skipped-banner">
            You skipped this question earlier — answer it now or submit the quiz without it.
        </div>
        <?php endif; ?>

        <div class="question-card">
            <form method="POST" action="practice.php?lesson_id=<?= $lesson_id ?>">
                <input type="hidden" name="question_index" value="<?= $current_idx ?>">
                <input type="hidden" name="used_ai" value="0">

                <p class="question-text"><?= htmlspecialchars($current_q['question_text']) ?></p>

                <ul class="options-list">
                <?php foreach (['a'=>'A','b'=>'B','c'=>'C','d'=>'D'] as $key => $letter): ?>
                    <li>
                        <label class="option-label">
                            <input type="radio" name="chosen_option" value="<?= $letter ?>" required>
                            <span class="option-letter"><?= $letter ?></span>
                            <?= htmlspecialchars($current_q["option_{$key}"]) ?>
                        </label>
                    </li>
                <?php endforeach; ?>
                </ul>

                <div class="btn-row">
                    <button type="submit" name="submit_answer" class="btn-primary">Submit Answer</button>
                </div>
            </form>
        </div>

        <?php if (!$already_skipped): ?>
        <div class="skip-row">
            <form method="POST" action="practice.php?lesson_id=<?= $lesson_id ?>">
                <input type="hidden" name="question_index" value="<?= $current_idx ?>">
                <button type="submit" name="skip_question" class="btn-skip">Skip this question</button>
            </form>
        </div>
        <?php endif; ?>

        <?php if ($already_skipped): ?>
        <div class="submit-anyway-row">
            <form method="POST" action="practice.php?lesson_id=<?= $lesson_id ?>">
                <button type="submit" name="submit_anyway" class="btn-warning">Submit Quiz Without Remaining Questions</button>
            </form>
        </div>
        <?php endif; ?>

        <?php endif; ?>

        <?php endif; ?>

    </main>

    <footer>
        <p>&copy; 2025 BrightStart Math Tutoring. All rights reserved.</p>
    </footer>

    <!-- JavaScript that handles the chatbot chat panel -->
    <script>
    (function () {
        const panel      = document.getElementById('chatbot-panel');
        const btnHelp    = document.getElementById('btn-get-help');
        const btnSend    = document.getElementById('btn-send-chat');
        const chatInput  = document.getElementById('chat-input');
        const chatMsgs   = document.getElementById('chat-messages');
        const qData      = document.getElementById('js-question-data');

        //only run this code on the wrong answer screen
        if (!panel || !btnHelp) return;

        //keeps track of the full conversation to send to the server each time
        let history = [];

        //show the chatbot panel when the student asks for help
        btnHelp.addEventListener('click', function () {
            panel.style.display = 'block';
            btnHelp.disabled    = true;
            panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
            sendToAI("Hello! I got this question wrong and need some help understanding it.");
        });

        btnSend.addEventListener('click', sendMessage);
        chatInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
        });

        function sendMessage() {
            const text = chatInput.value.trim();
            if (!text) return;
            appendMessage('You', text, 'user');
            chatInput.value = '';
            sendToAI(text);
        }

        //sends the students message to the PHP chatbot endpoint and displays the reply
        function sendToAI(userMessage) {
            history.push({ role: 'user', content: userMessage });

            btnSend.disabled   = true;
            chatInput.disabled = true;

            const typingNode = appendMessage('Tutor', '...', 'tutor');

            const fd = new FormData();
            fd.append('action',         'ai_help');
            fd.append('question_text',  qData.dataset.text);
            fd.append('options[a]',     qData.dataset.a);
            fd.append('options[b]',     qData.dataset.b);
            fd.append('options[c]',     qData.dataset.c);
            fd.append('options[d]',     qData.dataset.d);
            fd.append('correct_option', qData.dataset.correct);
            fd.append('user_message',   userMessage);
            fd.append('conversation',   JSON.stringify(history.slice(0, -1)));

            fetch('practice.php?lesson_id=<?= $lesson_id ?>', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(data => {
                    const reply = data.reply || 'Sorry, something went wrong.';
                    typingNode.textContent = reply;
                    history.push({ role: 'assistant', content: reply });
                })
                .catch(() => {
                    typingNode.textContent = 'Sorry, I could not connect. Please try again.';
                })
                .finally(() => {
                    btnSend.disabled   = false;
                    chatInput.disabled = false;
                    chatInput.focus();
                });
        }

        //creates a chat bubble and returns the text node so it can be updated
        function appendMessage(sender, text, role) {
            const div = document.createElement('div');
            div.className = 'chat-bubble ' + (role === 'user' ? 'user' : 'tutor');
            div.textContent = text;
            chatMsgs.appendChild(div);
            chatMsgs.scrollTop = chatMsgs.scrollHeight;
            return div;
        }
    })();
    </script>

</body>
<!-- lines 1-494 written by Caleb McHaney, AI chat bot created by Nick Deblock -->
</html>
