//Made by Nick DeBlock
//Variables for the form and chat box

const form = document.getElementById('chat-form');
const chatBox = document.getElementById('chat-box');
const helpBtn = document.getElementById("help-btn");

helpBtn.addEventListener("click", async () => {
    const question = "Solve for x: 2x + 3 = 7"; // Example math problem
    const prePrompt = `You are a helpful math tutor.Rules:- Guide the student step-by-step.- Do NOT give the final answer immediately. - Ask the student questions to help them think. Problem:
${question}
`;
    // Send structured tutoring prompt
    const response = await fetch('chat.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: prePrompt })
    });

    const data = await response.json();

    chatBox.innerHTML += `<div class="bot-msg">${data.reply}</div>`;
    chatBox.scrollTop = chatBox.scrollHeight;
});

//  event listener for form submission 
form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const userInput = document.getElementById('user-input').value; //grabs the user input 

    chatBox.innerHTML += `<div class="user-msg">${userInput}</div>`;//appends the message to the chat box
    document.getElementById('user-input').value = '';//resets the users input feild 

    const response = await fetch('chat.php', { //feteches the resonse from the chat.php file
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ message: userInput })
    });

    const data = await response.json();
    chatBox.innerHTML += `<div class="bot-msg">${data.reply}</div>`;//appends the bots resonse in the chat box 
    chatBox.scrollTop = chatBox.scrollHeight;//moves down the chat box 
});