<?php
// chatbot.php

session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userMessage = strtolower(trim($_POST['message'] ?? ''));

    if (!$userMessage) {
        echo json_encode(['success' => false, 'error' => 'Empty message']);
        exit;
    }

    // Define your Q&A pairs here (expand as you want)
    $faq = [
        'what is ai' => 'AI (Artificial Intelligence) is the simulation of human intelligence in machines that are programmed to think and learn.',
        'python' => 'Python is a popular programming language known for its readability and versatility in AI, web development, and more.',
        'java' => 'Java is a high-level programming language used for building applications across platforms.',
        'javascript' => 'JavaScript is the scripting language of the web, used to make web pages interactive.',
        'c++' => 'C++ is a powerful programming language often used in system/software development and competitive programming.',
        'react' => 'React is a JavaScript library for building user interfaces, especially single-page applications.',
        'competitive programming' => 'Competitive programming involves solving algorithmic problems within time constraints, often for contests.',
        'web development' => 'Web development is the work involved in developing websites and web applications.',
        'help' => 'You can ask me about AI, Python, Java, JavaScript, C++, React, competitive programming, and web development.',
    ];

    // Try to find a keyword match in user message
    $reply = "Sorry, I don't have an answer for that. Try asking about AI, Python, Java, JavaScript, C++, React, or web development.";

    foreach ($faq as $question => $answer) {
        if (strpos($userMessage, $question) !== false) {
            $reply = $answer;
            break;
        }
    }

    echo json_encode(['success' => true, 'reply' => $reply]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Free FAQ Chatbot - FutureBot</title>
<style>
  body { font-family: Arial, sans-serif; margin: 20px; background: #f0f2f5; }
  #chat-box { max-width: 700px; margin: auto; background: #fff; border-radius: 8px; padding: 20px; height: 400px; overflow-y: auto; box-shadow: 0 2px 6px rgba(0,0,0,0.15); }
  .message { margin: 10px 0; }
  .user { text-align: right; color: #007bff; }
  .bot { text-align: left; color: #28a745; white-space: pre-wrap; }
  form { max-width: 700px; margin: 20px auto; display: flex; }
  input[type=text] { flex: 1; padding: 10px; font-size: 16px; border-radius: 5px 0 0 5px; border: 1px solid #ccc; }
  button { padding: 10px 20px; font-size: 16px; background: #007bff; border: none; color: white; border-radius: 0 5px 5px 0; cursor: pointer; }
  button:hover { background: #0056b3; }
</style>
</head>
<body>

<div id="chat-box"></div>

<form id="chat-form">
  <input type="text" id="message" placeholder="Ask me about AI, Python, Java, React, etc..." autocomplete="off" required />
  <button type="submit">Send</button>
</form>

<script>
  const chatBox = document.getElementById('chat-box');
  const form = document.getElementById('chat-form');
  const input = document.getElementById('message');

  function addMessage(sender, text) {
    const div = document.createElement('div');
    div.classList.add('message', sender);
    div.textContent = text;
    chatBox.appendChild(div);
    chatBox.scrollTop = chatBox.scrollHeight;
  }

  form.addEventListener('submit', async e => {
    e.preventDefault();
    const msg = input.value.trim();
    if (!msg) return;
    addMessage('user', msg);
    input.value = '';

    try {
      const response = await fetch('', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({message: msg})
      });
      const data = await response.json();
      if (data.success) {
        addMessage('bot', data.reply);
      } else {
        addMessage('bot', '🤖 FutureBot: ' + (data.error || 'Something went wrong.'));
      }
    } catch (err) {
      addMessage('bot', '🤖 FutureBot: Network error.');
    }
  });
</script>

</body>
</html>
