document.addEventListener("DOMContentLoaded", function () {
    console.log("DOM fully loaded!");
    
    initializeUI();
    fetchUsers(""); // Load all users initially
});

// Global variables
let allUsers = [];
let currentSort = { field: null, direction: 'asc' };
const currentUserId = document.body.dataset.userId;

// Initialize UI components
function initializeUI() {
    // Toast message close button
    const toastBox = document.querySelector(".toast-box");
    const closeButton = document.querySelector("#close-button");

    if (closeButton && toastBox) {
        closeButton.addEventListener("click", () => {
            toastBox.style.display = "none";
        });
    }

    // Menu (Sidebar) toggle for mobile

    const menu = document.querySelector('.menu-icon');
    const hamburgerIcon = document.querySelector('.menu-icon img');
    const navigation = document.querySelector('.navigation');
    const main = document.querySelector('.main');

    menu.addEventListener('click', function () {
        navigation.classList.toggle('active');
        main.classList.toggle('active');

        hamburgerIcon.src = navigation.classList.contains('active') ?
            "./assets/menu-unfold-line.png" : "./assets/menu-fold-line.png";
    });

    // Profile menu dropdown for mobile
    const profile = document.getElementById('profile');
    const profileMenu = document.getElementById('profileMenu');
    
    // Only enable profile menu functionality on mobile
    function checkScreenSize() {
        if (window.innerWidth <= 600) { // Mobile
            // Enable menu functionality
            profile.style.pointerEvents = 'auto';
            
            profile?.addEventListener('click', handleProfileClick);
            document.addEventListener('click', handleDocumentClick);
            profileMenu?.addEventListener('click', handleMenuClick);
        } else { // Desktop
            // Disable menu functionality
            profile.style.pointerEvents = 'none';
            
            profile?.removeEventListener('click', handleProfileClick);
            document.removeEventListener('click', handleDocumentClick);
            profileMenu?.removeEventListener('click', handleMenuClick);
            
            profileMenu.classList.remove('active');
        }
    }
    
    // Event handlers
    function handleProfileClick(e) {
        e.stopPropagation();
        profileMenu.classList.toggle('active');
    }
    
    function handleDocumentClick() {
        profileMenu.classList.remove('active');
    }
    
    function handleMenuClick(e) {
        e.stopPropagation();
    }
    
    // Initial check
    checkScreenSize();
    
    // Re-check when window is resized
    window.addEventListener('resize', checkScreenSize);

    // Sorting dropdown
    const sortDropdown = document.getElementById("sortCombined");
        if (sortDropdown) {
            sortDropdown.addEventListener("change", sortUser);
        }
    
    // Live search input
    const searchInput = document.getElementById("search");
    if (searchInput) {
        searchInput.addEventListener("input", function () {
            const searchValue = this.value.trim();
            console.log(`Typing detected: "${searchValue}"`);
            filterAndRenderUsers(searchValue);
        });
    }
}

// Fetch users from server (search-aware)
function fetchUsers(searchValue) {
    let formData = new FormData();
    formData.append("userSearch", searchValue);

    fetch("php/search_users.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        try {
            const data = JSON.parse(text);
            allUsers = data;
            filterAndRenderUsers(searchValue);
        } catch (error) {
            console.error("JSON Parsing Error:", error, "\nRaw Response:", text);
        }
    })
    .catch(error => console.error("Fetch error:", error));
}


// Filter + Sort + Render
function filterAndRenderUsers(searchValue = "") {
    const filteredUsers = allUsers.filter(user => {
        return (
            (user.user_id?.toString().includes(searchValue.toLowerCase()) || '') ||
            (user.user_name?.toLowerCase().includes(searchValue.toLowerCase()) || '') ||
            (user.role_type_name?.toLowerCase().includes(searchValue.toLowerCase()) || '') ||
            (user.email?.toLowerCase().includes(searchValue.toLowerCase()) || '') ||
            (user.user_status?.toLowerCase().includes(searchValue.toLowerCase()) || '')
        );
    });
    
    // Sort if sorting is applied
    if (currentSort.field) {
        filteredUsers.sort((a, b) => {
            let valueA = a[currentSort.field];
            let valueB = b[currentSort.field];

            // Safely handle null/undefined
            if (valueA == null) valueA = "";
            if (valueB == null) valueB = "";

            // If numeric (e.g., ID), parse and compare numerically
            if (!isNaN(valueA) && !isNaN(valueB)) {
                valueA = parseFloat(valueA);
                valueB = parseFloat(valueB);
            } else {
                // Normalize to strings for comparison
                valueA = valueA.toString().toLowerCase();
                valueB = valueB.toString().toLowerCase();
            }

            if (valueA < valueB) return currentSort.direction === 'asc' ? -1 : 1;
            if (valueA > valueB) return currentSort.direction === 'asc' ? 1 : -1;
            return 0;
        });
    }

    updateUserCards(filteredUsers);
}


// Handle sort button click
function sortUser() {
    const [field, direction] = document.getElementById("sortCombined").value.split("|");
    currentSort = { field, direction };

    const searchValue = document.getElementById("search").value.trim();
    filterAndRenderUsers(searchValue);
}

// Render user cards
function updateUserCards(users) {
    const cardGrid = document.querySelector(".user-card-grid");
    if (!cardGrid) {
        console.error("User card grid container not found.");
        return;
    }

    cardGrid.innerHTML = "";

    if (!users.length) {
        cardGrid.innerHTML = "<p>No users found</p>";
        return;
    }

    users.forEach(user => {
        const card = document.createElement("div");
        card.classList.add("user-card");

        card.innerHTML = `
            <div class="user-card-content">
                <h3 class="userId">User ID: ${user.user_id}</h3>
                <h3 class="user-name">${user.user_name}</h3>
                <p class="user-email"><b>Email: </b>${user.email}</p>
                <p class="user-role"><b>Role: </b>${user.role_type_name}</p>
                <p class="user-status"><b>Status: </b>${user.user_status}</p>
                <div class="card-actions">
                    <form class="inline-form" action="update_user.php" method="POST">
                        <input type="hidden" name="userId" value="${user.user_id}">
                        <button type="submit" class="update_user_button" title="update user">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                    </form>
                    <button class="update_user_button status-toggle-btn 
                                ${user.user_status === 'active' ? 'deactivate-btn' : 'activate-btn'}"
                            title="${user.user_status === 'active' ? 'Deactivate user' : 'Activate user'}"
                            onclick="handleDeactivate(${user.user_id}, this)"
                            ${user.user_id == currentUserId ? 'disabled' : ''}>
                        <i class="fa-solid ${user.user_status === 'active' ? 'fa-user-xmark' : 'fa-user-check'}"></i>
                        ${user.user_status === 'active' ? 'Deactivate' : 'Activate'}
                    </button>
                </div>
            </div>
        `;

        cardGrid.appendChild(card);
    });
}

// Handle activate/deactivate user confirmation
function handleDeactivate(userId, buttonElement) {
    if (userId.toString() === currentUserId.toString()) {
        alert("You cannot modify your own account status");
        return;
    }

    const card = buttonElement.closest('.user-card-content');
    const statusElement = card.querySelector('.user-status');
    const currentStatus = statusElement.textContent.includes('inactive') ? 'inactive' : 'active';
    const action = currentStatus === 'active' ? 'deactivate' : 'activate';
    const actionColor = currentStatus === 'active' ? '#ff6b6b' : '#4CAF50';

    // Create modal overlay and dialog
    const overlay = document.createElement('div');
    overlay.style.cssText = `
        position: fixed; top: 0; left: 0; width: 100%;
        height: 100%; background-color: rgba(0,0,0,0.5); z-index: 999;
    `;

    const dialog = document.createElement('div');
    dialog.style.cssText = `
        position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
        background: white; padding: 20px; border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2); z-index: 1000;
        text-align: center; max-width: 300px;
    `;
    dialog.innerHTML = `
        <p>Are you sure you want to ${action} this user?</p>
        <div style="display: flex; justify-content: center; gap: 10px; margin-top: 15px;">
            <button id="confirmAction" style="padding: 6px 15px; background-color: ${actionColor}; color: white; border: none; border-radius: 4px;">
                ${action === 'deactivate' ? 'Deactivate' : 'Activate'}
            </button>
            <button id="cancelAction" style="padding: 6px 15px; background-color: #f1f1f1; border: none; border-radius: 4px;">
                Cancel
            </button>
        </div>
    `;

    document.body.appendChild(overlay);
    document.body.appendChild(dialog);

    // Confirm action
    document.getElementById('confirmAction').addEventListener('click', async () => {
        const confirmBtn = document.getElementById('confirmAction');
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing';

        try {
            const response = await fetch('php/update_user_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `userId=${userId}&action=${action}`
            });
            const data = await response.json();
            if (!data.success) throw new Error(data.error || 'Action failed');

            // Update status text
            statusElement.innerHTML = `<b>Status: </b>${action === 'deactivate' ? 'inactive' : 'active'}`;
            statusElement.style.transition = 'opacity 0.2s ease';
            statusElement.style.opacity = '0';
            setTimeout(() => {
                statusElement.style.opacity = '1';
            }, 200);

            // Update button appearance
            buttonElement.innerHTML = `
                <i class="fa-solid ${action === 'deactivate' ? 'fa-user-xmark' : 'fa-user-check'}"></i>
                ${action === 'deactivate' ? 'Activate' : 'Deactivate'}
            `;
            buttonElement.title = action === 'deactivate' ? 'Activate user' : 'Deactivate user';
            buttonElement.classList.toggle('deactivate-btn');
            buttonElement.classList.toggle('activate-btn');
            
        } catch (error) {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        } finally {
            document.body.removeChild(dialog);
            document.body.removeChild(overlay);
        }
    });

    // Cancel action
    document.getElementById('cancelAction').addEventListener('click', () => {
        document.body.removeChild(dialog);
        document.body.removeChild(overlay);
    });
}

// Save selected sort preference in localStorage
document.getElementById("sortCombined").addEventListener("change", () => {
    localStorage.setItem("sortPref", document.getElementById("sortCombined").value);
});

// Load saved sort preference from localStorage
document.addEventListener("DOMContentLoaded", () => {
    const savedSort = localStorage.getItem("sortPref");
    if (savedSort) {
        document.getElementById("sortCombined").value = savedSort;
        const [field, direction] = savedSort.split("|");
        currentSort = { field, direction };
    }
});


