import "./bootstrap";
import "../css/app.css";
import "./countdown"; // مزادات عدّ تنازلي

// Global variables
let currentConversationId = null;
let lastMessageId = null;
let pollingInterval = null;

// Messaging functionality
document.addEventListener("DOMContentLoaded", function () {
    console.log(
        "Brwazi App loaded - Axios ready:",
        typeof window.axios !== "undefined"
    );

    // Ensure header renders only once across complex Blade compositions
    const headerInstances = document.querySelectorAll("#main-header");
    if (headerInstances.length > 1) {
        headerInstances.forEach((el, index) => {
            if (index > 0) {
                el.remove();
            }
        });
        console.warn(
            `Removed duplicated header instances (kept 1 of ${headerInstances.length})`
        );
    }

    const header = document.querySelector("#main-header");
    const mobileMenuBtn = document.getElementById("mobile-menu-btn");
    const mobileMenu = document.getElementById("mobile-menu");
    const mobileMenuClose = document.getElementById("mobile-menu-close");
    const mobileSearchBtn = document.getElementById("mobile-search-btn");
    const mobileSearch = document.getElementById("mobile-search");

    const openMobileMenu = () => {
        if (!mobileMenu) return;
        mobileMenu.classList.add("active");
        document.body.style.overflow = "hidden";
    };

    const closeMobileMenu = () => {
        if (!mobileMenu) return;
        mobileMenu.classList.remove("active");
        document.body.style.overflow = "auto";
    };

    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener("click", () => {
            openMobileMenu();
        });
    }

    if (mobileMenuClose && mobileMenu) {
        mobileMenuClose.addEventListener("click", () => {
            closeMobileMenu();
        });
    }

    if (mobileMenu) {
        mobileMenu.addEventListener("click", (event) => {
            if (event.target === mobileMenu) {
                closeMobileMenu();
            }
        });
    }

    if (mobileSearchBtn && mobileSearch) {
        mobileSearchBtn.addEventListener("click", () => {
            mobileSearch.classList.toggle("hidden");
            if (!mobileSearch.classList.contains("hidden")) {
                mobileSearch.querySelector("input")?.focus();
            }
        });
    }

    if (header) {
        let lastScrolledState = null;
        const updateHeaderState = () => {
            const scrolled = window.scrollY > 50;
            if (scrolled !== lastScrolledState) {
                header.classList.toggle("is-scrolled", scrolled);
                lastScrolledState = scrolled;
            }
        };

        let ticking = false;
        window.addEventListener(
            "scroll",
            () => {
                if (!ticking) {
                    window.requestAnimationFrame(() => {
                        updateHeaderState();
                        ticking = false;
                    });
                    ticking = true;
                }
            },
            { passive: true }
        );

        updateHeaderState();
    }

    // Get current conversation ID from URL or data attribute
    const conversationElement = document.querySelector(
        "[data-conversation-id]"
    );
    if (conversationElement) {
        currentConversationId = conversationElement.dataset.conversationId;
        const messages = document.querySelectorAll(
            ".message-item[data-message-id]"
        );
        if (messages.length > 0) {
            lastMessageId = messages[messages.length - 1].dataset.messageId;
        }

        console.log(
            "Live messaging enabled for conversation:",
            currentConversationId
        );
        startLiveMessaging();
    }

    // Auto-scroll messages to bottom
    const messagesContainer = document.querySelector(".messages-container");
    if (messagesContainer) {
        scrollToBottom(messagesContainer);
    }

    // Real-time message sending
    const messageForm = document.querySelector("#messageForm");
    if (messageForm) {
        messageForm.addEventListener("submit", async function (e) {
            e.preventDefault();
            await sendMessage(this);
        });
    }

    // Search users functionality with live search
    const userSearch = document.querySelector("#userSearch");
    if (userSearch) {
        let searchTimeout;
        userSearch.addEventListener("input", function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performUserSearch(this.value);
            }, 300); // Debounce 300ms
        });
    }
});

// Live messaging functions
function startLiveMessaging() {
    if (!currentConversationId) return;

    // Poll for new messages every 3 seconds
    pollingInterval = setInterval(checkForNewMessages, 3000);

    // Also check when tab becomes visible
    document.addEventListener("visibilitychange", function () {
        if (!document.hidden && currentConversationId) {
            checkForNewMessages();
        }
    });
}

async function checkForNewMessages() {
    if (!currentConversationId) return;

    try {
        const response = await axios.get(
            `/user/conversation/${currentConversationId}/new-messages`,
            {
                params: {
                    after_id: lastMessageId,
                },
            }
        );

        if (response.data.messages && response.data.messages.length > 0) {
            appendNewMessages(response.data.messages);
            // Update last message ID
            const newMessages = response.data.messages;
            lastMessageId = newMessages[newMessages.length - 1].id;
        }
    } catch (error) {
        console.log("Error checking for new messages:", error);
    }
}

function appendNewMessages(messages) {
    const messagesContainer = document.querySelector(".messages-container");
    if (!messagesContainer) return;

    const currentUserId = document.querySelector("[data-current-user-id]")
        ?.dataset.currentUserId;

    messages.forEach((message) => {
        const messageHtml = createMessageElement(message, currentUserId);
        messagesContainer.insertAdjacentHTML("beforeend", messageHtml);
    });

    // Auto scroll to bottom
    scrollToBottom(messagesContainer);

    // Mark messages as read
    markMessagesAsRead();
}

function createMessageElement(message, currentUserId) {
    const isOwn = message.sender_id == currentUserId;
    const alignClass = isOwn ? "justify-end" : "justify-start";
    const bgClass = isOwn ? "bg-blue-500 text-white" : "bg-gray-100";

    let content = "";
    if (message.type === "image" && message.image_path) {
        content = `<img src="/storage/${message.image_path}" alt="صورة" class="max-w-xs rounded">`;
    } else if (message.type === "artwork_request") {
        content = `
            <div class="p-3 border border-orange-200 rounded bg-orange-50">
                <i class="fas fa-palette text-orange-500"></i>
                <span class="text-orange-700">طلب لوحة فنية</span>
                <p class="text-sm mt-1">${message.content}</p>
            </div>`;
    } else {
        content = `<p>${message.content}</p>`;
    }

    return `
        <div class="flex ${alignClass} mb-4 message-item" data-message-id="${
        message.id
    }">
            <div class="max-w-xs lg:max-w-md ${bgClass} rounded-lg px-4 py-2">
                ${content}
                <p class="text-xs mt-1 opacity-70">
                    ${new Date(message.created_at).toLocaleTimeString("ar-SA")}
                </p>
            </div>
        </div>`;
}

async function sendMessage(form) {
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const messageInput = form.querySelector(
        'input[name="content"], textarea[name="content"]'
    );

    // Disable submit button
    submitButton.disabled = true;
    submitButton.textContent = "جاري الإرسال...";

    try {
        const response = await axios.post(form.action, formData);

        if (response.data.success) {
            // Clear the form
            if (messageInput) messageInput.value = "";

            // Add message immediately to UI
            if (response.data.message) {
                const currentUserId = document.querySelector(
                    "[data-current-user-id]"
                )?.dataset.currentUserId;
                appendNewMessages([response.data.message]);
                lastMessageId = response.data.message.id;
            }
        }
    } catch (error) {
        console.error("Error sending message:", error);
        alert("خطأ في إرسال الرسالة");
    } finally {
        // Re-enable submit button
        submitButton.disabled = false;
        submitButton.textContent = "إرسال";
    }
}

async function performUserSearch(query) {
    if (!query || query.length < 2) {
        document.querySelector("#searchResults")?.remove();
        return;
    }

    try {
        const response = await axios.get("/user/search-users", {
            params: { query: query },
        });

        displaySearchResults(response.data.users || []);
    } catch (error) {
        console.error("Search error:", error);
    }
}

function displaySearchResults(users) {
    const existingResults = document.querySelector("#searchResults");
    existingResults?.remove();

    if (users.length === 0) return;

    const searchInput = document.querySelector("#userSearch");
    const resultsHtml = `
        <div id="searchResults" class="absolute top-full left-0 right-0 bg-white border rounded-lg shadow-lg max-h-60 overflow-y-auto z-10">
            ${users
                .map(
                    (user) => `
                <div class="p-3 hover:bg-gray-50 cursor-pointer border-b user-result" data-user-id="${
                    user.id
                }">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <img src="${
                            user.profile_picture || "/imgs/default-avatar.png"
                        }"
                             alt="${user.name}" class="w-8 h-8 rounded-full">
                        <div>
                            <p class="font-medium">${user.name}</p>
                            <p class="text-sm text-gray-500">@${
                                user.username || user.email
                            }</p>
                        </div>
                    </div>
                </div>
            `
                )
                .join("")}
        </div>`;

    searchInput.parentNode.classList.add("relative");
    searchInput.insertAdjacentHTML("afterend", resultsHtml);

    // Add click handlers for results
    document.querySelectorAll(".user-result").forEach((result) => {
        result.addEventListener("click", function () {
            const userId = this.dataset.userId;
            startNewConversation(userId);
        });
    });
}

async function startNewConversation(userId) {
    try {
        const response = await axios.post("/user/conversation/start", {
            user_id: userId,
        });

        if (response.data.success && response.data.conversation_id) {
            window.location.href = `/user/conversation/${response.data.conversation_id}`;
        }
    } catch (error) {
        console.error("Error starting conversation:", error);
    }
}

function scrollToBottom(container) {
    container.scrollTop = container.scrollHeight;
}

function markMessagesAsRead() {
    if (!currentConversationId) return;

    axios
        .post(`/user/conversation/${currentConversationId}/mark-read`)
        .catch((error) =>
            console.log("Error marking messages as read:", error)
        );
}

// Cleanup on page unload
window.addEventListener("beforeunload", function () {
    if (pollingInterval) {
        clearInterval(pollingInterval);
    }
});
