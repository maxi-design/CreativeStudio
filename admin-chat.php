<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel de Chat - Administrador</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
  <div class="max-w-2xl mx-auto mt-10 bg-white shadow-md rounded-xl overflow-hidden">
    <div class="bg-gradient-to-r from-indigo-500 to-pink-500 text-white p-4">
      <h2 class="text-lg font-semibold">Panel de Chat - Administrador</h2>
    </div>

    <div id="chat-messages" class="p-4 h-96 overflow-y-auto space-y-2 text-sm">
      <!-- Mensajes se cargan aquí -->
    </div>

    <div class="border-t p-4 flex items-center space-x-2">
      <input id="chat-input" type="text" placeholder="Escribe una respuesta..." class="flex-1 px-3 py-2 border rounded-full focus:outline-none">
      <button id="chat-send" class="bg-indigo-600 text-white px-4 py-2 rounded-full hover:bg-indigo-700">Enviar</button>
    </div>
  </div>

  <script>
    const chatMessages = document.getElementById('chat-messages');
    const chatInput = document.getElementById('chat-input');
    const chatSend = document.getElementById('chat-send');

    function loadMessages() {
      fetch('get-messages.php')
        .then(res => res.json())
        .then(data => {
          chatMessages.innerHTML = '';
          data.forEach(msg => {
            const div = document.createElement('div');
            div.className = (msg.sender === 'admin')
              ? 'bg-indigo-500 text-white rounded-xl px-4 py-2 w-max ml-auto max-w-[85%]'
              : 'bg-gray-200 rounded-xl px-4 py-2 w-max max-w-[85%]';
            div.textContent = msg.content;
            chatMessages.appendChild(div);
          });
          chatMessages.scrollTop = chatMessages.scrollHeight;
        });
    }

    function sendMessage() {
      const text = chatInput.value.trim();
      if (!text) return;

      fetch('send-message.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ sender: 'admin', content: text })
      }).then(() => {
        chatInput.value = '';
        loadMessages();
      });
    }

    chatSend.addEventListener('click', sendMessage);
    chatInput.addEventListener('keydown', e => {
      if (e.key === 'Enter') sendMessage();
    });

    loadMessages();
    setInterval(loadMessages, 5000);
  </script>
</body>
</html>
