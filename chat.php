<?php
//Full page was written Nick DeBlock
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$userMessage = $input['message'] ?? '';

if(!$userMessage){
    echo json_encode(['reply' => 'Please enter a message.']);
    exit;
}

$apiKey = getenv('OPENAI_API_KEY');

$data = [
    "model" => "gpt-3.5-turbo",
    "messages" => [
        ["role" => "user", "content" => $userMessage]
    ]
];

$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    "Authorization: Bearer $apiKey"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$result = curl_exec($ch);
curl_close($ch);

$response = json_decode($result, true);
$botReply = $response['choices'][0]['message']['content'] ?? 'Sorry, I could not process that.';

echo json_encode(['reply' => $botReply]);
