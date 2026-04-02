<?php
//Author Caleb McHaney
//practice.php is the main file for handling practice sessions on the student view allowing the students to do practice exercises
// Flow:
//   1. Student arrives from the lessons page with ?lesson_id=X in the URL.
//   2. Questions for that lesson (matching the student's grade from session) are
//      loaded and stored in $_SESSION so progress survives page reloads.
//   3. One question is shown at a time.
//   4. Wrong answer → "Do you need help?" prompt.
//      Yes → AI chatbot panel; student can retry the same question as many times as needed.
//      No  → Move to the next question immediately.
//   5. After the last question → results summary (score + per-question review).

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include "../db_connect.php";

//student must be logged in with a grade
if (!isset($_SESSION['grade_id']) || !isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$grade_id = intval($_SESSION['grade_id']);

//AI chatbot endpoint
if (isset($_POST['action']) && $_POST['action'] === 'ai_help') {
    header('Content-Type: application/json');

    $question_text  = trim($_POST['question_text']   ?? '');
    $options        = $_POST['options']              ?? [];
    $correct_option = strtoupper(trim($_POST['correct_option'] ?? ''));
    $user_message   = trim($_POST['user_message']    ?? '');
    $history        = json_decode($_POST['conversation'] ?? '[]', true);
    if (!is_array($history)) $history = [];

    // Tutor system prompt — guide the student, never reveal the answer outright
    $system_prompt = <<<PROMPT
You are a friendly, encouraging tutor helping a student who answered a multiple-choice question incorrectly.
Your job is to guide them to understand the concept well enough to work out the correct answer on their own.
Do NOT simply state the correct answer. Instead ask guiding questions, give hints, and explain relevant concepts.
Keep responses concise and age-appropriate (elementary / middle school level).

The question is:
"{$question_text}"

The answer choices are:
A: {$options['a']}
B: {$options['b']}
C: {$options['c']}
D: {$options['d']}

The correct answer is option {$correct_option}. Do not reveal this letter or the answer text directly.
Guide the student through reasoning so they can discover it themselves.
PROMPT;

    // Append the new user message to history before sending
    $history[] = ['role' => 'user', 'content' => $user_message];

    $payload = json_encode([
        'model'      => 'claude-sonnet-4-20250514',
        'max_tokens' => 512,
        'system'     => $system_prompt,
        'messages'   => $history,
    ]);

    $ch = curl_init('https://api.anthropic.com/v1/messages');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'x-api-key: ' . ($_ENV['ANTHROPIC_API_KEY'] ?? getenv('ANTHROPIC_API_KEY')),
            'anthropic-version: 2023-06-01',
        ],
    ]);

    $response  = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200) {
        echo json_encode(['reply' => 'Sorry, I ran into a problem. Please try again.']);
        exit;
    }

    $data  = json_decode($response, true);
    $reply = $data['content'][0]['text'] ?? 'Sorry, I could not generate a response.';

    echo json_encode(['reply' => $reply]);
    exit;
}

//Determine lesson
$lesson_id = intval($_GET['lesson_id'] ?? $_SESSION['practice_lesson_id'] ?? 0);

if (!$lesson_id) {
    header("Location: lessons.php");
    exit;
}

//Load (or resume) a quiz session 
$reset = isset($_GET['reset']);

if ($reset || !isset($_SESSION['practice']) || $_SESSION['practice']['lesson_id'] !== $lesson_id) {

    $stmt = $conn->prepare(
        "SELECT question_id, question_text, option_a, option_b, option_c, option_d, correct_option
         FROM Questions
         WHERE lesson_id = ? AND grade_id = ?
         ORDER BY question_id ASC"
    );
    $stmt->bind_param("ii", $lesson_id, $grade_id);
    $stmt->execute();
    $result    = $stmt->get_result();
    $questions = [];
    while ($row = $result->fetch_assoc()) $questions[] = $row;

    if (empty($questions)) {
        $_SESSION['practice_error'] = "No practice questions are available for this lesson yet.";
        header("Location: lessons.php");
        exit;
    }

    $lstmt = $conn->prepare("SELECT lesson_title FROM Lesson WHERE lesson_id = ?");
    $lstmt->bind_param("i", $lesson_id);
    $lstmt->execute();
    $lesson_row   = $lstmt->get_result()->fetch_assoc();
    $lesson_title = $lesson_row['lesson_title'] ?? 'Practice';

    $_SESSION['practice'] = [
        'lesson_id'    => $lesson_id,
        'lesson_title' => $lesson_title,
        'questions'    => $questions,
        'current'      => 0,
        'answers'      => [],   // [question_id => ['chosen','correct','is_correct','used_ai']]
        'done'         => false,
    ];
    $_SESSION['practice_lesson_id'] = $lesson_id;
}

$quiz = &$_SESSION['practice'];

//POST: student submitted an answer 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_answer'])) {
    $chosen     = strtoupper(trim($_POST['chosen_option'] ?? ''));
    $idx        = intval($_POST['question_index'] ?? $quiz['current']);
    $q          = $quiz['questions'][$idx];
    $is_correct = ($chosen === $q['correct_option']);
    $used_ai    = isset($_POST['used_ai']) && $_POST['used_ai'] === '1';

    // Record the first attempt 
    if (!isset($quiz['answers'][$q['question_id']])) {
        $quiz['answers'][$q['question_id']] = [
            'chosen'     => $chosen,
            'correct'    => $q['correct_option'],
            'is_correct' => $is_correct,
            'used_ai'    => $used_ai,
        ];
    } elseif ($used_ai) {
        $quiz['answers'][$q['question_id']]['used_ai']    = true;
        $quiz['answers'][$q['question_id']]['is_correct'] = $is_correct;
        $quiz['answers'][$q['question_id']]['chosen']     = $chosen;
    }

    if ($is_correct) {
        $next = $idx + 1;
        $quiz['done']    = ($next >= count($quiz['questions']));
        $quiz['current'] = $quiz['done'] ? $idx : $next;
        header("Location: practice.php?lesson_id={$lesson_id}");
        exit;
    } else {
        //Wrong answer 
        header("Location: practice.php?lesson_id={$lesson_id}&wrong=1&idx={$idx}");
        exit;
    }
}

//POST: student declined help
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['skip_to_next'])) {
    $idx = intval($_POST['question_index'] ?? $quiz['current']);
    $q   = $quiz['questions'][$idx];

    // Ensure a wrong answer is on record even when help is declined
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

//View state
$show_wrong_prompt = isset($_GET['wrong']) && isset($_GET['idx']);
$wrong_idx         = intval($_GET['idx'] ?? $quiz['current']);
$current_idx       = $quiz['current'];
$total             = count($quiz['questions']);
$current_q         = $quiz['questions'][$current_idx] ?? null;

//Compute score for results screen
$score = 0;
if ($quiz['done']) {
    foreach ($quiz['answers'] as $a) {
        if ($a['is_correct']) $score++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('../includes/header.php'); ?>
    <title>Practice – <?= htmlspecialchars($quiz['lesson_title']) ?></title>
</head>
<body>
    <?php include('../includes/nav.php'); ?>

    <main>

        <?php if ($quiz['done']): ?>
        <!--RESULTS SCREEN-->
        <h2>Quiz Complete!</h2>
        <p>You scored <strong><?= $score ?> / <?= $total ?></strong>
           (<?= round(($score / max($total, 1)) * 100) ?>%)</p>

        <h3>Question Review</h3>

        <?php foreach ($quiz['questions'] as $i => $q):
            $ans        = $quiz['answers'][$q['question_id']] ?? null;
            $is_correct = $ans && $ans['is_correct'];
            $chosen     = $ans['chosen']  ?? '—';
            $correct    = $ans['correct'] ?? $q['correct_option'];
            $used_ai    = $ans['used_ai'] ?? false;
            $options    = ['A' => $q['option_a'], 'B' => $q['option_b'],
                           'C' => $q['option_c'], 'D' => $q['option_d']];
        ?>
            <div>
                <p>
                    <strong>Q<?= $i + 1 ?>:</strong> <?= htmlspecialchars($q['question_text']) ?>
                    <?php if ($used_ai): ?>(Used AI Help)<?php endif; ?>
                    — <?= $is_correct ? 'Correct' : 'Incorrect' ?>
                </p>
                <ul>
                    <?php foreach ($options as $letter => $text): ?>
                        <li>
                            <?= $letter ?>: <?= htmlspecialchars($text) ?>
                            <?php if ($letter === $correct): ?> ✓ Correct answer<?php endif; ?>
                            <?php if ($letter === $chosen && !$is_correct): ?> ✗ Your answer<?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <hr>
        <?php endforeach; ?>

        <a href="practice.php?lesson_id=<?= $lesson_id ?>&reset=1">
            <button type="button">Retry This Lesson</button>
        </a>
        &nbsp;
        <a href="lessons.php">
            <button type="button">Back to Lessons</button>
        </a>


        <?php elseif ($show_wrong_prompt): ?>
        <!--WRONG ANSWER — help prompt + chatbot-->
        <?php $wq = $quiz['questions'][$wrong_idx]; ?>

        <h2>Not quite!</h2>
        <p>That answer wasn't correct. Would you like some help working through it?</p>

        <button type="button" id="btn-get-help">Yes, help me understand</button>

        &nbsp;

        <form method="POST" action="practice.php?lesson_id=<?= $lesson_id ?>" style="display:inline;">
            <input type="hidden" name="question_index" value="<?= $wrong_idx ?>">
            <input type="hidden" name="last_chosen"
                   value="<?= htmlspecialchars($quiz['answers'][$wq['question_id']]['chosen'] ?? '') ?>">
            <button type="submit" name="skip_to_next">No thanks, move on</button>
        </form>

        <!--Chatbot panel — hidden until student clicks "Yes"-->
        <div id="chatbot-panel" style="display:none;">
            <h3>Tutor Chat</h3>
            <p><em><?= htmlspecialchars($wq['question_text']) ?></em></p>

            <div id="chat-messages"></div>

            <textarea id="chat-input" rows="2" cols="50"
                placeholder="Ask a question or describe what you're confused about..."></textarea>
            <br>
            <button type="button" id="btn-send-chat">Send</button>

            <br><br>

            <!--Retry form — shown inside the chatbot panel-->
            <h4>Try the question again:</h4>
            <form method="POST" action="practice.php?lesson_id=<?= $lesson_id ?>">
                <input type="hidden" name="question_index" value="<?= $wrong_idx ?>">
                <input type="hidden" name="used_ai" value="1">

                <p><strong><?= htmlspecialchars($wq['question_text']) ?></strong></p>

                <?php foreach (['a'=>'A','b'=>'B','c'=>'C','d'=>'D'] as $key => $letter): ?>
                    <label>
                        <input type="radio" name="chosen_option" value="<?= $letter ?>" required>
                        <?= $letter ?>: <?= htmlspecialchars($wq["option_{$key}"]) ?>
                    </label><br>
                <?php endforeach; ?>

                <br>
                <button type="submit" name="submit_answer">Submit Answer</button>
            </form>
        </div>

        <!--Hidden data attributes used by the JS chatbot-->
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
        <!--NORMAL QUESTION VIEW-->
        <h2><?= htmlspecialchars($quiz['lesson_title']) ?> — Practice</h2>
        <p>Question <?= $current_idx + 1 ?> of <?= $total ?></p>

        <?php if ($current_q): ?>
        <form method="POST" action="practice.php?lesson_id=<?= $lesson_id ?>">
            <input type="hidden" name="question_index" value="<?= $current_idx ?>">
            <input type="hidden" name="used_ai" value="0">

            <p><strong><?= htmlspecialchars($current_q['question_text']) ?></strong></p>

            <?php foreach (['a'=>'A','b'=>'B','c'=>'C','d'=>'D'] as $key => $letter): ?>
                <label>
                    <input type="radio" name="chosen_option" value="<?= $letter ?>" required>
                    <?= $letter ?>: <?= htmlspecialchars($current_q["option_{$key}"]) ?>
                </label><br>
            <?php endforeach; ?>

            <br>
            <button type="submit" name="submit_answer">Submit Answer</button>
        </form>
        <?php endif; ?>

        <?php endif; ?>

    </main>

    <!--JS — AI chatbot-->
    <script>
    (function () {
        const panel      = document.getElementById('chatbot-panel');
        const btnHelp    = document.getElementById('btn-get-help');
        const btnSend    = document.getElementById('btn-send-chat');
        const chatInput  = document.getElementById('chat-input');
        const chatMsgs   = document.getElementById('chat-messages');
        const qData      = document.getElementById('js-question-data');

        if (!panel || !btnHelp) return; // not on the wrong-answer screen

        let history = []; // full conversation sent to the server each turn

        // Show chatbot and open with a tutor greeting
        btnHelp.addEventListener('click', function () {
            panel.style.display = 'block';
            btnHelp.disabled    = true;
            sendToAI("Hello! I got this question wrong and need some help understanding it.");
        });

        btnSend.addEventListener('click', sendMessage);
        chatInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
        });

        function sendMessage() {
            const text = chatInput.value.trim();
            if (!text) return;
            appendMessage('You', text);
            chatInput.value = '';
            sendToAI(text);
        }

        function sendToAI(userMessage) {
            history.push({ role: 'user', content: userMessage });

            btnSend.disabled   = true;
            chatInput.disabled = true;

            const typingNode = appendMessage('Tutor', '...');

            const fd = new FormData();
            fd.append('action',         'ai_help');
            fd.append('question_text',  qData.dataset.text);
            fd.append('options[a]',     qData.dataset.a);
            fd.append('options[b]',     qData.dataset.b);
            fd.append('options[c]',     qData.dataset.c);
            fd.append('options[d]',     qData.dataset.d);
            fd.append('correct_option', qData.dataset.correct);
            fd.append('user_message',   userMessage);
            // Send history without the message we just appended (server appends it itself)
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

        // Appends a chat bubble and returns the text <span> so we can update "..."
        function appendMessage(sender, text) {
            const div  = document.createElement('div');
            const span = document.createElement('span');
            div.innerHTML = '<strong>' + sender + ':</strong> ';
            span.textContent = text;
            div.appendChild(span);
            chatMsgs.appendChild(div);
            chatMsgs.scrollTop = chatMsgs.scrollHeight;
            return span;
        }
    })();
    </script>

</body>
</html>