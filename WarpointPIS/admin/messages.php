<?php
session_start();
require '../db.php';
if (!isset($_SESSION['admin'])) exit;

// Получаем последнее сообщение от каждого пользователя
$search = $_GET['search'] ?? '';

$sql = "
  SELECT 
    m1.*, 
    a.first_name, 
    a.last_name,
    a.phone_number,
    COALESCE(unread_counts.unread_count, 0) AS unread_count
  FROM (
    SELECT 
      MAX(id) AS last_msg_id,
      CASE 
        WHEN sender_id = 0 THEN receiver_id 
        ELSE sender_id 
      END AS user_id
    FROM messages
    WHERE sender_id = 0 OR receiver_id = 0
    GROUP BY user_id
  ) latest
  JOIN messages m1 ON m1.id = latest.last_msg_id
  JOIN accounts a ON a.account_id = latest.user_id
  LEFT JOIN (
    SELECT sender_id, COUNT(*) AS unread_count
    FROM messages
    WHERE is_admin_sender = 0 AND is_read = 0
    GROUP BY sender_id
  ) unread_counts ON unread_counts.sender_id = latest.user_id
";

$params = [];

if (!empty($search)) {
    $sql .= " WHERE a.first_name LIKE :search OR a.last_name LIKE :search OR a.phone_number LIKE :search";
    $params['search'] = "%$search%";
}

$sql .= " ORDER BY m1.sent_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$lastMessages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Сообщения</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #1b1b1b;
      color: #fff;
      display: flex;
    }
    .sidebar {
      width: 220px;
      background-color: #1b1b1b;
      color: white;
      height: 100vh;
      padding: 20px;
      position: fixed;
    }
    .sidebar h2 { color: #fdd835; }
    .sidebar ul { list-style: none; padding: 0; margin-top: 30px; }
    .sidebar ul li { margin: 20px 0; }
    .sidebar ul li a {
      color: white; text-decoration: none; font-size: 16px; transition: 0.3s;
    }
    .sidebar ul li a:hover { color: #fdd835; }

    .content {
      flex: 1;
      padding: 30px;
      margin-left: 220px;
    }

    .content h2 {
      color: #1b1b1b; /* или просто #000 — чёрный */
      margin-left: 20px; /* чтобы отступ от края был */
    }

    .chat-card {
      background: #222;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      cursor: pointer;
      margin-left: 20px;
    }

    .chat-info {
      display: flex;
      flex-direction: column;
    }

    .chat-name {
      font-weight: bold;
      color: #fdd835;
      margin-bottom: 5px;
    }

    .chat-message {
      color: #ccc;
      font-size: 14px;
    }

    .chat-time {
      font-size: 12px;
      color: #888;
    }

    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      top: 0; left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.7);
    }

    .modal-content {
      background: #1b1b1b;
      color: white;
      width: 600px;
      max-height: 90vh;
      margin: 50px auto;
      padding: 20px;
      border-radius: 8px;
      overflow-y: auto;
      position: relative;
    }

    .modal-content .close {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 24px;
      cursor: pointer;
    }

    .modal-content h3 {
      margin-top: 0;
    }

    .modal-content label {
      display: block;
      margin-top: 10px;
      margin-bottom: 5px;
    }

    .modal-content select,
    .modal-content textarea {
      width: 100%;
      padding: 8px;
      border: 1px solid #666;
      border-radius: 4px;
      background: #333;
      color: #fff;
    }

    .modal-content textarea {
      height: 80px;
      resize: none;
    }
    
    .send-btn {
      margin-top: 15px;
      background-color: #fdd835;
      color: #000;
      border: none;
      padding: 10px;
      width: 100%;
      font-weight: bold;
      border-radius: 4px;
      cursor: pointer;
    }

    .chat-history {
  max-height: 400px;
  overflow-y: auto;
  margin-bottom: 15px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

/* Стилизация скроллбара для WebKit (Chrome, Edge, Opera) */
.chat-history::-webkit-scrollbar {
  width: 8px;
}

.chat-history::-webkit-scrollbar-track {
  background: transparent;
}

.chat-history::-webkit-scrollbar-thumb {
  background-color: #555;
  border-radius: 10px;
  border: 2px solid transparent;
  background-clip: content-box;
}

.chat-history::-webkit-scrollbar-thumb:hover {
  background-color: #888;
}

/* Стилизация скроллбара для Firefox */
.chat-history {
  scrollbar-width: thin;
  scrollbar-color: #555 transparent;
}

.msg {
  max-width: 70%;
  padding: 10px 15px;
  border-radius: 15px;
  position: relative;
  word-wrap: break-word;
}

.msg.user {
  align-self: flex-start;
  background-color: #333;
  color: #fff;
  border-top-left-radius: 0;
}

.msg.admin {
  align-self: flex-end;
  background-color: #fdd835;
  color: #000;
  border-top-right-radius: 0;
}

.msg small {
  display: block;
  font-size: 11px;
  margin-top: 6px;
  color: rgba(255, 255, 255, 0.6);
}

.msg.admin small {
  color: rgba(0, 0, 0, 0.6);
}

.unread-indicator {
  background-color: #fdd835;
  color: #000;
  font-size: 11px;
  font-weight: bold;
  border-radius: 50%;
  padding: 4px 8px;
  margin-left: 8px;
  min-width: 20px;
  text-align: center;
  display: inline-block;
  line-height: 1;
}

    .send-form textarea {
      width: 100%;
      padding: 10px;
      border-radius: 5px;
      background: #333;
      border: none;
      resize: none;
      margin-top: 10px;
    }

    .send-form button {
      background: #fdd835;
      color: #000;
      border: none;
      padding: 8px 15px;
      margin-top: 10px;
      cursor: pointer;
      font-weight: bold;
      border-radius: 5px;
    }

    .send-message-btn {
      background: #fdd835;
      color: #000;
      padding: 10px 20px;
      border: none;
      font-weight: bold;
      border-radius: 6px;
      margin-bottom: 20px;
      cursor: pointer;
      margin-left: 20px;
    }
  </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="content">
  <h2>Все сообщения от пользователей</h2>
  <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
  <form method="GET" style="display: flex; gap: 10px; align-items: center;">
    <input type="text" name="search" placeholder="Поиск по имени, фамилии или телефону"
           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
           style="padding: 8px; border-radius: 4px; border: 1px solid #ccc; width: 250px;">

    <button type="submit" style="padding: 8px 14px; background: #fdd835; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
       Найти
    </button>

    <a href="messages.php" style="padding: 8px 14px; background: #fdd835; color: black; text-decoration: none; border-radius: 4px; font-weight: bold;">
      Сброс
    </a>
  </form>
</div>
  <button id="openModalBtn" class="send-message-btn">Отправить сообщение</button>
  <div class="message-list">
    <?php foreach ($lastMessages as $msg): ?>
      <?php
        $user_id = $msg['sender_id'] != 0 ? $msg['sender_id'] : $msg['receiver_id'];
        $name = htmlspecialchars($msg['last_name'] . ' ' . $msg['first_name']);
      ?>
      <div class="chat-card" data-user="<?= $user_id ?>" onclick="openChat(<?= $user_id ?>, '<?= $name ?>')">
        <div class="chat-info">
          <div class="chat-name">
            <?= $name ?>
            <?php if (!empty($msg['unread_count']) && $msg['unread_count'] > 0): ?>
              <span class="unread-indicator"><?= $msg['unread_count'] ?></span>
            <?php endif; ?>
          </div>
          <div class="chat-message"><?= htmlspecialchars($msg['message']) ?></div>
        </div>
        <div class="chat-time"><?= date('d.m.Y H:i', strtotime($msg['sent_at'])) ?></div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- Модальное окно отправки -->
<div id="modalSend" class="modal">
  <div class="modal-content">
    <span class="close" onclick="document.getElementById('modalSend').style.display='none'">&times;</span>
      <h3>Отправить сообщение <span id="receiverName" style="color: #fdd835;"></span></h3>
      <form action="send_admin_message.php" method="POST">
        <label>Пользователь:</label>
        <select name="receiver_id" id="receiverSelect" required>
          <option value="">Выберите пользователя</option>
          <?php
            $users = $pdo->query("SELECT account_id, first_name, last_name FROM accounts")->fetchAll();
            foreach ($users as $u):
          ?>
            <option value="<?= $u['account_id'] ?>">
              <?= htmlspecialchars($u['last_name'] . ' ' . $u['first_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <label>Сообщение:</label>
        <textarea name="message" required></textarea>
        <button type="submit" class="send-btn">Отправить</button>
      </form>
  </div>
</div>

<!-- Модальное окно -->
<div id="chatModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="document.getElementById('chatModal').style.display='none'">&times;</span>
    <h3>Переписка с <span id="chatUserName"></span></h3>
    <div id="chatHistory" class="chat-history"></div>

    <form id="chatForm" class="send-form" onsubmit="sendMessage(event)">
      <textarea name="message" placeholder="Сообщение..." required></textarea>
      <input type="hidden" name="receiver_id" id="receiverId">
      <button type="submit">Отправить</button>
    </form>
  </div>
</div>

<script>
document.getElementById('openModalBtn').onclick = () => {
  document.getElementById('modalSend').style.display = 'block';
  document.getElementById('receiverName').innerText = '';
  document.getElementById('receiverIdInput').value = '';
};

function openChat(userId, name = '') {
  document.getElementById('chatUserName').innerText = name;
  document.getElementById('receiverId').value = userId || '';
  document.getElementById('chatModal').style.display = 'block';

  // Удалить индикатор непрочитанных сообщений сразу
  const card = document.querySelector(`.chat-card[data-user='${userId}']`);
  if (card) {
    const badge = card.querySelector('.unread-indicator');
    if (badge) badge.remove();
  }

  if (!userId) {
    document.getElementById('chatHistory').innerHTML = '<em>Новый пользователь</em>';
    return;
  }

  fetch('get_conversation.php?user_id=' + userId)
    .then(res => res.json())
    .then(messages => {
      const chat = messages.map(m => {
        const cls = m.is_admin_sender == 1 ? 'admin' : 'user';
        const date = new Date(m.sent_at);
        const formattedDate = `${String(date.getDate()).padStart(2, '0')}.${String(date.getMonth() + 1).padStart(2, '0')}.${date.getFullYear()} ${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`;

        return `
          <div class="msg ${cls}">
            ${m.message}
            <small>${formattedDate}</small>
          </div>
        `;
      }).join('');
      document.getElementById('chatHistory').innerHTML = chat;
    });
}

function sendMessage(e) {
  e.preventDefault();
  const form = document.getElementById('chatForm');
  const data = new FormData(form);

  fetch('send_admin_message.php', {
    method: 'POST',
    body: data
  }).then(res => res.text()).then(() => {
    openChat(data.get('receiver_id'), document.getElementById('chatUserName').innerText);
    form.reset();
  });
}
</script>
</body>
</html>
