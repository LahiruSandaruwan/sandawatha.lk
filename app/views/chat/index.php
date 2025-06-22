<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

// Include necessary files
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Chat.php';
require_once __DIR__ . '/../../models/Message.php';

// Initialize models
$userModel = new User($db);
$chatModel = new Chat($db);
$messageModel = new Message($db);

// Get current user's data
$currentUser = $userModel->getById($_SESSION['user_id']);
if (!$currentUser) {
    header('Location: /logout.php');
    exit();
}

// Get chat contacts
$contacts = $chatModel->getContacts($_SESSION['user_id']);

// Get selected contact from URL
$selectedContactId = filter_input(INPUT_GET, 'contact_id', FILTER_VALIDATE_INT);

// Get current language
$currentLang = $_SESSION['language'] ?? 'en';

// Translations
$translations = [
    'en' => [
        'messages' => 'Messages',
        'contacts' => 'Contacts',
        'search' => 'Search contacts...',
        'type_message' => 'Type a message...',
        'send' => 'Send',
        'online' => 'Online',
        'offline' => 'Offline',
        'last_seen' => 'Last seen',
        'no_contacts' => 'No contacts yet',
        'start_chat' => 'Select a contact to start chatting',
        'loading' => 'Loading messages...',
        'today' => 'Today',
        'yesterday' => 'Yesterday',
        'attach' => 'Attach',
        'image_only' => 'Only images are allowed (JPG, PNG)',
        'max_size' => 'Maximum file size: 5MB'
    ],
    // Add Sinhala and Tamil translations here
];

// Get translations for current language
$t = $translations[$currentLang];

// Include header
require_once __DIR__ . '/../shared/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="flex h-[calc(100vh-12rem)]">
            <!-- Contacts Sidebar -->
            <div class="w-80 border-r border-gray-200 flex flex-col">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($t['contacts']) ?></h2>
                    <div class="mt-2">
                        <input type="text" 
                               id="contactSearch" 
                               placeholder="<?= htmlspecialchars($t['search']) ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>
                
                <div class="flex-1 overflow-y-auto" id="contactsList">
                    <?php if (empty($contacts)): ?>
                        <div class="text-center py-8 text-gray-500">
                            <?= htmlspecialchars($t['no_contacts']) ?>
                        </div>
                    <?php else: ?>
                        <?php foreach ($contacts as $contact): ?>
                            <a href="?contact_id=<?= $contact['id'] ?>" 
                               class="contact-item block px-4 py-3 hover:bg-gray-50 transition-colors <?= $selectedContactId === $contact['id'] ? 'bg-gray-100' : '' ?>"
                               data-contact-id="<?= $contact['id'] ?>">
                                <div class="flex items-center space-x-3">
                                    <div class="relative">
                                        <img src="<?= htmlspecialchars($contact['profile_photo'] ?? '/assets/images/default-avatar.jpg') ?>" 
                                             alt="<?= htmlspecialchars($contact['name']) ?>" 
                                             class="w-10 h-10 rounded-full object-cover">
                                        <?php if ($contact['is_online']): ?>
                                            <span class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full bg-green-400 ring-2 ring-white"></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                <?= htmlspecialchars($contact['name']) ?>
                                            </p>
                                            <?php if ($contact['last_message_time']): ?>
                                                <p class="text-xs text-gray-500">
                                                    <?= htmlspecialchars(formatMessageTime($contact['last_message_time'])) ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($contact['last_message']): ?>
                                            <p class="text-sm text-gray-500 truncate">
                                                <?= htmlspecialchars($contact['last_message']) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Chat Window -->
            <div class="flex-1 flex flex-col">
                <?php if ($selectedContactId): ?>
                    <?php 
                    $selectedContact = $userModel->getById($selectedContactId);
                    if ($selectedContact):
                    ?>
                        <!-- Chat Header -->
                        <div class="p-4 border-b border-gray-200 flex items-center space-x-3">
                            <div class="relative">
                                <img src="<?= htmlspecialchars($selectedContact['profile_photo'] ?? '/assets/images/default-avatar.jpg') ?>" 
                                     alt="<?= htmlspecialchars($selectedContact['name']) ?>" 
                                     class="w-10 h-10 rounded-full object-cover">
                                <?php if ($selectedContact['is_online']): ?>
                                    <span class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full bg-green-400 ring-2 ring-white"></span>
                                <?php endif; ?>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    <?= htmlspecialchars($selectedContact['name']) ?>
                                </h3>
                                <p class="text-sm text-gray-500">
                                    <?php if ($selectedContact['is_online']): ?>
                                        <?= htmlspecialchars($t['online']) ?>
                                    <?php else: ?>
                                        <?= htmlspecialchars($t['last_seen']) ?> <?= htmlspecialchars(formatLastSeen($selectedContact['last_seen'])) ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>

                        <!-- Messages Area -->
                        <div class="flex-1 overflow-y-auto p-4" id="messagesArea">
                            <div class="space-y-4">
                                <!-- Messages will be loaded here via AJAX -->
                                <div class="text-center text-gray-500">
                                    <?= htmlspecialchars($t['loading']) ?>
                                </div>
                            </div>
                        </div>

                        <!-- Message Input -->
                        <div class="p-4 border-t border-gray-200">
                            <form id="messageForm" class="flex items-center space-x-4">
                                <input type="hidden" name="contact_id" value="<?= $selectedContactId ?>">
                                
                                <!-- File Upload -->
                                <div class="relative">
                                    <input type="file" 
                                           id="attachment" 
                                           name="attachment" 
                                           accept="image/jpeg,image/png" 
                                           class="hidden">
                                    <button type="button" 
                                            onclick="document.getElementById('attachment').click()" 
                                            class="inline-flex items-center p-2 border border-gray-300 rounded-full text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                        </svg>
                                    </button>
                                </div>
                                
                                <input type="text" 
                                       id="messageInput" 
                                       name="message" 
                                       placeholder="<?= htmlspecialchars($t['type_message']) ?>" 
                                       class="flex-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-full sm:text-sm border-gray-300">
                                
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-full shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <?= htmlspecialchars($t['send']) ?>
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="flex-1 flex items-center justify-center text-gray-500">
                        <?= htmlspecialchars($t['start_chat']) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let lastMessageId = 0;
    let isPolling = false;
    const selectedContactId = <?= $selectedContactId ?? 'null' ?>;
    
    // Format message time
    function formatMessageTime(timestamp) {
        const date = new Date(timestamp * 1000);
        const now = new Date();
        const yesterday = new Date(now);
        yesterday.setDate(yesterday.getDate() - 1);
        
        if (date.toDateString() === now.toDateString()) {
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        } else if (date.toDateString() === yesterday.toDateString()) {
            return '<?= $t['yesterday'] ?>';
        } else {
            return date.toLocaleDateString();
        }
    }
    
    // Load messages
    function loadMessages() {
        if (!selectedContactId || isPolling) return;
        
        isPolling = true;
        $.ajax({
            url: '/api/messages/get.php',
            method: 'GET',
            data: {
                contact_id: selectedContactId,
                after_id: lastMessageId
            },
            success: function(response) {
                if (response.success && response.messages.length > 0) {
                    const messagesArea = $('#messagesArea .space-y-4');
                    
                    response.messages.forEach(message => {
                        const isOwn = message.sender_id === <?= $_SESSION['user_id'] ?>;
                        const messageHtml = `
                            <div class="flex ${isOwn ? 'justify-end' : 'justify-start'}">
                                <div class="max-w-[70%] ${isOwn ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-900'} rounded-lg px-4 py-2 shadow">
                                    ${message.attachment ? `
                                        <img src="${message.attachment}" alt="Attachment" class="max-w-full rounded-lg mb-2">
                                    ` : ''}
                                    <p class="text-sm">${message.message}</p>
                                    <p class="text-xs ${isOwn ? 'text-indigo-200' : 'text-gray-500'} text-right mt-1">
                                        ${formatMessageTime(message.created_at)}
                                    </p>
                                </div>
                            </div>
                        `;
                        
                        messagesArea.append(messageHtml);
                        lastMessageId = Math.max(lastMessageId, message.id);
                    });
                    
                    // Scroll to bottom
                    messagesArea.scrollTop(messagesArea[0].scrollHeight);
                }
                isPolling = false;
            },
            error: function() {
                isPolling = false;
            }
        });
    }
    
    // Start polling
    if (selectedContactId) {
        loadMessages();
        setInterval(loadMessages, 3000); // Poll every 3 seconds
    }
    
    // Handle message form submission
    $('#messageForm').submit(function(e) {
        e.preventDefault();
        
        const form = $(this);
        const messageInput = form.find('#messageInput');
        const message = messageInput.val().trim();
        const attachment = form.find('#attachment')[0].files[0];
        
        if (!message && !attachment) return;
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '/api/messages/send.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    messageInput.val('');
                    form.find('#attachment').val('');
                    loadMessages();
                }
            }
        });
    });
    
    // Handle file selection
    $('#attachment').change(function() {
        const file = this.files[0];
        if (file) {
            // Validate file type
            const validTypes = ['image/jpeg', 'image/png'];
            if (!validTypes.includes(file.type)) {
                alert('<?= $t['image_only'] ?>');
                this.value = '';
                return;
            }
            
            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('<?= $t['max_size'] ?>');
                this.value = '';
                return;
            }
        }
    });
    
    // Handle contact search
    $('#contactSearch').on('input', function() {
        const query = $(this).val().toLowerCase();
        $('.contact-item').each(function() {
            const name = $(this).find('.text-gray-900').text().toLowerCase();
            $(this).toggle(name.includes(query));
        });
    });
});

// Helper function to format last seen time
function formatLastSeen(timestamp) {
    if (!timestamp) return '';
    
    const date = new Date(timestamp * 1000);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000);
    
    if (diff < 60) {
        return 'just now';
    } else if (diff < 3600) {
        const minutes = Math.floor(diff / 60);
        return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
    } else if (diff < 86400) {
        const hours = Math.floor(diff / 3600);
        return `${hours} hour${hours > 1 ? 's' : ''} ago`;
    } else {
        return date.toLocaleDateString();
    }
}
</script>

<?php
function formatMessageTime($timestamp) {
    if (!$timestamp) return '';
    
    $date = new DateTime('@' . $timestamp);
    $now = new DateTime();
    $yesterday = new DateTime('yesterday');
    
    if ($date->format('Y-m-d') === $now->format('Y-m-d')) {
        return $date->format('H:i');
    } elseif ($date->format('Y-m-d') === $yesterday->format('Y-m-d')) {
        return 'Yesterday';
    } else {
        return $date->format('d/m/Y');
    }
}
?>

<?php require_once __DIR__ . '/../shared/footer.php'; ?> 