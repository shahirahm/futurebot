<?php
// chatbot_api.php
header('Content-Type: application/json');
session_start();

// Make sure user is logged in (optional)
if (!isset($_SESSION['email'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$OPENAI_API_KEY = 'sk-xxxxxx';  // Replace with your actual OpenAI API key

// Get the user message from POST data
$input = json_decode(file_get_contents('php://input'), true);
$user_message = $input['message'] ?? '';

if (!$user_message) {
    echo json_encode(['error' => 'No message provided']);
    exit;
}

// Prepare data for OpenAI Chat Completion API
$data = [
    "model" => "gpt-4o-mini",
    "messages" => [
        ["role" => "user", "content" => $user_message]
    ],
    "temperature" => 0.7,
    "max_tokens" => 200
];

// Initialize cURL
$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $OPENAI_API_KEY"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// Execute request
$response = curl_exec($ch);

if ($response === false) {
    echo json_encode(['error' => 'Curl error: ' . curl_error($ch)]);
    curl_close($ch);
    exit;
}

curl_close($ch);

// Parse response and send only AI message back
$response_data = json_decode($response, true);
$ai_message = $response_data['choices'][0]['message']['content'] ?? 'Sorry, no response from AI.';

echo json_encode(['reply' => trim($ai_message)]);
