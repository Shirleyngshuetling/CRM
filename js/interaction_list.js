// Wait for the DOM to be fully loaded before executing setup functions
document.addEventListener("DOMContentLoaded", function () {
    console.log("Page loaded!");
    setupUI();

    const path = window.location.pathname;
    if(path.includes('dashboard_main.php')){
        fetchInteractionHistory();
    }
    else{
        setupLiveSearch();
        fetchInteractionHistory();
    }
    
});

// Global variables
let allInteractions = []; // Holds all fetched interaction records
let currentSort = { key: null, direction: null }; // Tracks current sorting state
let columnVisibility = {}; // Tracks visibility state of table columns

// Color settings for different interaction types
const interactionTypeColors = {
    'Phone Call': '#96d3ff',       
    'Email': '#dbdc72',     
    'Meeting': '#f4b298',    
    'Video Conference': '#eb89ac',      
    'Social Media': '#b498f4',  
    'Default': '#6b7280'     // White (fallback)
};

// Text and border color settings for interaction types
const interactionTypeStyles = {
    'Phone Call':    { text: '#0077b6', border: '#0077b6' },
    'Email':         { text: '#888800', border: '#888800' },
    'Meeting':       { text: '#b34700', border: '#b34700' },
    'Video Conference': { text: '#a00050', border: '#a00050' },
    'Social Media':  { text: '#4a00b3', border: '#4a00b3' },
    'Default':       { text: '#374151', border: '#374151' } // Tailwind slate-700
};


// Sets up basic UI elements and event listeners
function setupUI() {
    const closeButton = document.querySelector("#close-button");
    const toastBox = document.querySelector(".toast-box");
    const displayOptionsBtn = document.querySelector(".displayOptionsBtn");
    const showOptionsBox = document.querySelector(".showOptionsBox");
    const menu = document.querySelector(".menu-icon");
    const hamburgerIcon = document.querySelector(".menu-icon img");
    const navigation = document.querySelector(".navigation");
    const main = document.querySelector(".main");

    // Close toast message
    if (closeButton && toastBox) {
        closeButton.addEventListener("click", () => {
            toastBox.style.display = "none";
        });
    }

    // Toggle display options panel
    if (displayOptionsBtn && showOptionsBox) {
        displayOptionsBtn.addEventListener("click", () => {
            showOptionsBox.style.display = showOptionsBox.style.display === "block" ? "none" : "block";

            // Save visibility immediately after toggling
            saveColumnVisibility();
            // Apply the new visibility
            restoreColumnVisibility();
        });
    }

    // Toggle sidebar navigation menu
    if (menu && hamburgerIcon && navigation && main) {
        menu.addEventListener("click", () => {
            navigation.classList.toggle('active');
            main.classList.toggle('active');
            hamburgerIcon.src = navigation.classList.contains('active')
                ? "./assets/menu-unfold-line.png"
                : "./assets/menu-fold-line.png";
        });
    }

    // Enable column sorting
    setupSortableHeaders();
}


// Setup sorting by clicking table headers
function setupSortableHeaders() {
    document.querySelectorAll("th.sortable").forEach(header => {
        header.addEventListener("click", () => {
            const sortKey = header.getAttribute("data-sort");

            if (currentSort.key === sortKey) {
                if (currentSort.direction === "asc") {
                    currentSort.direction = "desc";
                } else if (currentSort.direction === "desc") {
                    currentSort.direction = null;
                    currentSort.key = null;
                } else {
                    currentSort.direction = "asc";
                }
            } else {
                currentSort.key = sortKey;
                currentSort.direction = "asc";
            }

            // Filter and re-render with new sort order
            const searchValue = document.getElementById("search").value.trim();
            filterAndRenderInteractions(searchValue);
            restoreColumnVisibility(); // Explicitly restore
        });
    });
}




// Update sorting icons
function updateSortIcons() {
    const icons = document.querySelectorAll("th.sortable i");
    icons.forEach(icon => icon.className = "fa fa-sort"); // Reset all icons

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

// Set up live search functionality
function setupLiveSearch() {
    const searchInput = document.getElementById("search");

    if (!searchInput) {
        console.error("Search input not found!");
        return;
    }

    // Trigger search on every keystroke
    searchInput.addEventListener("input", function () {
        saveColumnVisibility();
        const searchValue = this.value.trim();
        console.log(`Typing: "${searchValue}"`);
        fetchSearchResults(searchValue);
    });
}

// Save visibility state of columns
function saveColumnVisibility() {
    const columnClasses = ["date-column", "company-column", "details-column"];
    columnClasses.forEach(className => {
        const cells = document.querySelectorAll(`.${className}`);
        columnVisibility[className] = cells[0]?.style.display !== "none";
    });
}

function restoreColumnVisibility() {
    Object.entries(columnVisibility).forEach(([cls, visible]) => {
        document.querySelectorAll(`.${cls}`).forEach(cell => {
            cell.style.display = visible ? "table-cell" : "none";
        });
    });
}

//Fetch full interaction history (initial load)
function fetchInteractionHistory() {
    console.log("Fetching all interactions...");

    fetch("php/fetch_interactions.php", { method: "POST" })
        .then(response => response.json())
        .then(data => {
            console.log("✅ Interactions fetched:", data);
            allInteractions = data;
            filterAndRenderInteractions();
        })
        .catch(error => console.error("Fetch error:", error));
}

// Fetch search results
function fetchSearchResults(searchValue) {
    let formData = new FormData();
    formData.append("interactionSearch", searchValue);

    fetch("php/search_interactions.php", {
        method: "POST",
        body: formData
    })
        .then(response => response.text())
        .then(text => {
            console.log("🔍 API Response:", text);
            const data = JSON.parse(text);
            allInteractions = data;
            filterAndRenderInteractions(searchValue);
        })
        .catch(error => {
            console.error("Search fetch error:", error);
        });
}

//Apply search, sort, and render
function filterAndRenderInteractions(searchValue = "") {
    const searchLower = searchValue.toLowerCase();

    // Filter interactions by search term
    const filtered = allInteractions.filter(interaction =>
        (interaction.interaction_date && interaction.interaction_date.toLowerCase().includes(searchLower)) ||
        (interaction.interaction_type_name && interaction.interaction_type_name.toLowerCase().includes(searchLower)) ||
        (interaction.customer_type_name && interaction.customer_type_name.toLowerCase().includes(searchLower)) ||
        (interaction.name && interaction.name.toLowerCase().includes(searchLower)) ||
        interaction.user_name.toLowerCase().includes(searchLower) ||
        (interaction.company && interaction.company.toLowerCase().includes(searchLower)) ||
        (interaction.interaction_details && interaction.interaction_details.toLowerCase().includes(searchLower))
    );

    // Apply sorting if necessary
    if (currentSort.key && currentSort.direction) {
        filtered.sort((a, b) => {
            let valA = a[currentSort.key] ?? '';
            let valB = b[currentSort.key] ?? '';

            // Special handling for dates
            if (currentSort.key === 'interaction_date') {
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

    updateInteractionTable(filtered);
    updateSortIcons();
}


// Render the table with filtered interactions
function updateInteractionTable(interactions) {
    const tbody = document.querySelector(".interaction-table tbody");

    if (!tbody) {
        console.error("Table body not found!");
        return;
    }

    // Apply visibility settings BEFORE rebuilding the table
    restoreColumnVisibility();

    tbody.innerHTML = "";

    // If no results found, show a friendly message
    if (!interactions.length) {
        tbody.innerHTML = "<tr><td colspan='8'>No matching records found</td></tr>";
        return;
    }

    // Render each interaction row
    interactions.forEach(interaction => {
        const row = document.createElement("tr");
        const interactionType = interaction.interaction_type_name || 'Default';
        const bgColor = interactionTypeColors[interactionType] || interactionTypeColors['Default'];
        const borderColor = interactionTypeStyles[interactionType]?.border || interactionTypeStyles['Default'].border;

        row.innerHTML = `
            <td class="date-column">${interaction.interaction_date || 'Null'}</td>
            <td class="interaction-type-cell">
                <span class="interaction-label" style="
                    background-color: ${bgColor};
                    border: 2px solid ${borderColor};
                    color: ${"white"};
                ">
                    ${interactionType}
                </span>
            </td>
            <td>${interaction.customer_type_name}</td>
            <td>${interaction.name}</td>
            <td class="company-column">${interaction.company || 'Null'}</td>
            <td>${interaction.user_name}</td>
            <td class="details-column">${interaction.interaction_details || 'Null'}</td>
            <td>
                <form action="update_interaction.php" method="POST">
                    <input type="hidden" name="interaction_history_id" value="${interaction.interaction_history_id}">
                    <button type="submit" class="update_button">Update</button>
                </form>
            </td>
        `;
        tbody.appendChild(row);
    });

    // Reapply visibility after creating rows
    Object.keys(columnVisibility).forEach(className => {
        document.querySelectorAll(`.${className}`).forEach(cell => {
            cell.style.display = columnVisibility[className] ? "table-cell" : "none";
        });
    });

    console.log("Table updated.");
}
