document.addEventListener("DOMContentLoaded", () => {
    console.log("Page loaded!");
    fetchRemindersFromServer(); 
    setupLiveSearch();
    setupDisplayOptionsToggle();
    setupSidebarToggle();
    setupSortableColumns(); // Added sortable columns setup

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
    // Profile click handler
    function handleProfileClick(e) {
        e.stopPropagation();
        profileMenu.classList.toggle('active');
    }
    
    // Outside click closes menu
    function handleDocumentClick() {
        profileMenu.classList.remove('active');
    }

    // Stop menu click from closing itself
    function handleMenuClick(e) {
        e.stopPropagation();
    }
    
    // Initial check
    checkScreenSize();
    
    // Re-check when window is resized
    window.addEventListener('resize', checkScreenSize);
});

// Global variables
let allReminders = [];
let currentSort = { key: null, direction: "asc" };
let columnVisibility = {};

// Color mapping for interaction types
const interactionTypeColors = {
    'Phone Call': '#96d3ff',       
    'Email': '#dbdc72',     
    'Meeting': '#f4b298',    
    'Video Conference': '#eb89ac',      
    'Social Media': '#b498f4',  
    'Default': '#6b7280'     // White (fallback)
};

// Style mapping for interaction types
const interactionTypeStyles = {
    'Phone Call':    { text: '#0077b6', border: '#0077b6' },
    'Email':         { text: '#888800', border: '#888800' },
    'Meeting':       { text: '#b34700', border: '#b34700' },
    'Video Conference': { text: '#a00050', border: '#a00050' },
    'Social Media':  { text: '#4a00b3', border: '#4a00b3' },
    'Default':       { text: '#374151', border: '#374151' } // Tailwind slate-700
};

/**
 * Fetches all reminders from the server.
 */
function fetchRemindersFromServer() {
    console.log("🚀 Fetching reminders...");
    fetch("php/fetch_reminders.php", { method: "POST" })
        .then(res => res.json())
        .then(data => {
            console.log("API Response:", data);
            allReminders = data;
            renderReminders(data);
        })
        .catch(error => console.error("Fetch error:", error));
}

/**
 * Live search setup.
 */
function setupLiveSearch() {
    const searchInput = document.getElementById("search");
    if (!searchInput) return console.error("Search input not found!");

    searchInput.addEventListener("input", () => {
        saveColumnVisibility(); // Before filtering
        const value = searchInput.value.trim();
        searchReminders(value);
    });
}

/**
 * Sends a search query to the server.
 */
function searchReminders(searchValue) {
    const formData = new FormData();
    formData.append("reminderSearch", searchValue);

    fetch("php/search_reminders.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.text())
        .then(text => {
            try {
                const data = JSON.parse(text);
                console.log("Search API Response:", data);
                allReminders = data;
                filterAndRenderReminders(searchValue);
            } catch (err) {
                console.error("JSON Parsing Error:", err);
            }
        })
        .catch(error => console.error("Search Fetch Error:", error));
}

/**
 * Filters and sorts reminders, then renders them.
 */
function filterAndRenderReminders(searchValue = "") {
    const searchLower = searchValue.toLowerCase();

    const filtered = allReminders.filter(reminder =>
        reminder.reminder_date.toLowerCase().includes(searchLower)||
        reminder.interaction_type_name.toLowerCase().includes(searchLower) ||
        (reminder.customer_type_name && reminder.customer_type_name.toLowerCase().includes(searchLower)) ||
        (reminder.name && reminder.name.toLowerCase().includes(searchLower)) ||
        (reminder.company && reminder.company.toLowerCase().includes(searchLower)) ||
        (reminder.user_name && reminder.user_name.toLowerCase().includes(searchLower))||
        (reminder.notes && reminder.notes.toLowerCase().includes(searchLower))
    );

    // Sort if needed
    if (currentSort.key && currentSort.direction) {
        filtered.sort((a, b) => {
            let valA = a[currentSort.key] ?? '';
            let valB = b[currentSort.key] ?? '';

            // Handle date sorting differently
            if (currentSort.key === 'reminder_date') {
                valA = new Date(valA).getTime();
                valB = new Date(valB).getTime();
            } else {
                // Normal string sorting
                if (typeof valA === 'string') valA = valA.toUpperCase();
                if (typeof valB === 'string') valB = valB.toUpperCase();
            }
            
            return currentSort.direction === 'asc' 
                ? (valA < valB ? -1 : 1)
                : (valA > valB ? -1 : 1);
        });
    }

    renderReminders(filtered);
}

/**
 * Renders reminders into the table.
 */
function renderReminders(reminders) {
    const tbody = document.querySelector(".reminder-table tbody");
    

    if (!tbody) return console.error("tbody not found!");

    // Apply visibility settings BEFORE rebuilding the table
    restoreColumnVisibility();

    tbody.innerHTML = reminders.length
        ? reminders.map(reminder => {
            const interactionType = reminder.interaction_type_name || 'Default';
            const bgColor = interactionTypeColors[interactionType] || interactionTypeColors['Default'];
            const borderColor = interactionTypeStyles[interactionType]?.border || interactionTypeStyles['Default'].border;
        
            return `
                <tr>
                    <td>${reminder.reminder_date}</td>
                    <td class="interaction-type-cell">
                        <span class="interaction-label" style="
                            background-color: ${bgColor};
                            border: 2px solid ${borderColor};
                            color: ${"white"};
                        ">
                            ${interactionType}
                        </span>
                    </td>
                    <td>${reminder.customer_type_name}</td>
                    <td>${reminder.name}</td>
                    <td class="company-column">${reminder.company}</td>
                    <td>${reminder.user_name}</td>
                    <td class="notes-column">${reminder.notes || 'Null'}</td>
                </tr>
            `;
        }).join("")
        : `<tr><td colspan="8">No matching records found</td></tr>`;

    restoreColumnVisibility();
}

/**
 * Saves current visibility state of specific columns.
 */
function saveColumnVisibility() {
    ["company-column", "email-column", "phone-column", "notes-column", "status-column"].forEach(col => {
        const cell = document.querySelector(`.${col}`);
        columnVisibility[col] = cell?.style.display !== "none";
    });
}

/**
 * Restores column visibility after rendering.
 */
function restoreColumnVisibility() {
    Object.entries(columnVisibility).forEach(([col, visible]) => {
        document.querySelectorAll(`.${col}`).forEach(cell => {
            cell.style.display = visible ? "table-cell" : "none";
        });
    });
}

/**
 * Applies sorting based on dropdowns.
 */
function sortReminder() {
    currentSort.field = document.getElementById("sortProperty").value;
    currentSort.direction = document.getElementById("sortDirection").value;
    filterAndRenderReminders(document.getElementById("search").value.trim());
}

/**
 * Toggles visibility of display options box.
 */
function setupDisplayOptionsToggle() {
    const displayBtn = document.querySelector(".displayOptionsBtn");
    const optionsBox = document.querySelector(".showOptionsBox");

    if (displayBtn && optionsBox) {
        displayBtn.addEventListener("click", () => {
            optionsBox.style.display = optionsBox.style.display === "block" ? "none" : "block";
        
            // Save visibility immediately after toggling
            saveColumnVisibility();
            // Apply the new visibility
            restoreColumnVisibility();
        });
    }
}

/**
 * Sidebar toggle logic.
 */
function setupSidebarToggle() {
    const menuIcon = document.querySelector('.menu-icon');
    const hamburgerImg = menuIcon?.querySelector("img");
    const navigation = document.querySelector('.navigation');
    const main = document.querySelector('.main');

    if (!menuIcon || !hamburgerImg || !navigation || !main) return;

    menuIcon.addEventListener("click", () => {
        navigation.classList.toggle("active");
        main.classList.toggle("active");

        hamburgerImg.src = navigation.classList.contains("active")
            ? "./assets/menu-unfold-line.png"
            : "./assets/menu-fold-line.png";
    });
}

/**
 * Sets up sortable table headers.
 */
function setupSortableColumns() {
    const sortableHeaders = document.querySelectorAll("th.sortable");

    sortableHeaders.forEach(header => {
        header.addEventListener("click", () => {
            const field = header.dataset.sort;
            toggleSort(field);
            restoreColumnVisibility(); // Explicitly restore
            filterAndRenderReminders(document.getElementById("search").value.trim());
            restoreColumnVisibility(); // Explicitly restore
            updateSortIcons(field);
        });
    });
}

/**
 * Toggles the sorting state (asc, desc, none).
 */
function toggleSort(field) {
    if (currentSort.key === field) {
        currentSort.direction = currentSort.direction === "asc" ? "desc" 
                              : currentSort.direction === "desc" ? null 
                              : "asc";
        
        if (!currentSort.direction) {
            currentSort.key = null;
        }
    } else {
        currentSort.key = field;
        currentSort.direction = "asc";
    }
}

/**
 * Updates sort icons on table headers.
 */
function updateSortIcons(activeField) {
    const icons = document.querySelectorAll("th.sortable i");
    icons.forEach(icon => icon.className = "fa fa-sort");

    if (!currentSort.key || !currentSort.direction) return;

    const currentIcon = document.getElementById(`icon-${currentSort.key}`);
    if (currentIcon) {
        if (currentSort.direction === "asc") {
            currentIcon.className = "fa fa-sort-asc";
        } else if (currentSort.direction === "desc") {
            currentIcon.className = "fa fa-sort-desc";
        }
    }
}
