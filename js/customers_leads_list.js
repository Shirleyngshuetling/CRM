// Global variable to track whether we're displaying customers (1) or leads (2)
let customerLeadType;

// Wait for the DOM to be fully loaded before executing our code
document.addEventListener("DOMContentLoaded", () => {
    console.log("DOM fully loaded!");

    // Determine if we're on the customers page or leads page
    const isCustomersPage = window.location.pathname.includes("customer_list.php");
    customerLeadType = isCustomersPage ? 1 : 2; // 1 for customers, 2 for leads

    // Get references to DOM elements
    const displayOptionsBtn = document.querySelector(".displayOptionsBtn");
    const showOptionsBox = document.querySelector(".showOptionsBox");
    const closeButton = document.querySelector("#close-button");
    const toastBox = document.querySelector(".toast-box");
    const menu = document.querySelector(".menu-icon");
    const hamburgerIcon = document.querySelector(".menu-icon img");
    const navigation = document.querySelector(".navigation");
    const main = document.querySelector(".main");
    const searchInput = document.getElementById("search");

    // Log error if search input isn't found
    if (!searchInput) console.error("Search input not found!");

    // Initial load of customers/leads based on page type
    loadCustomers(customerLeadType);

     
    // ------------------- MOBILE MENU TOGGLE FUNCTIONALITY -------------------
    
    menu?.addEventListener("click", () => {
        // Toggle active class on navigation and main content
        navigation.classList.toggle("active");
        main.classList.toggle("active");
        
        // Switch between hamburger and close icon
        hamburgerIcon.src = navigation.classList.contains("active")
            ? "./assets/menu-unfold-line.png" // Open state icon
            : "./assets/menu-fold-line.png"; // Closed state icon
    });

    
    // ------------------- PROFILE MENU FUNCTIONALITY (MOBILE ONLY) -------------------
    
    const profile = document.getElementById('profile');
    const profileMenu = document.getElementById('profileMenu');
    
    /**
     * Checks screen size and enables/disables profile menu functionality accordingly
     */
    function checkScreenSize() {
        if (window.innerWidth <= 600) { // Mobile view
            // Enable menu functionality
            profile.style.pointerEvents = 'auto';
            
            // Add event listeners for mobile interaction
            profile?.addEventListener('click', handleProfileClick);
            document.addEventListener('click', handleDocumentClick);
            profileMenu?.addEventListener('click', handleMenuClick);
        } else { // Desktop view
            // Disable menu functionality
            profile.style.pointerEvents = 'none';
            
            // Remove event listeners to prevent unwanted behavior
            profile?.removeEventListener('click', handleProfileClick);
            document.removeEventListener('click', handleDocumentClick);
            profileMenu?.removeEventListener('click', handleMenuClick);
            
            // Ensure menu is closed
            profileMenu.classList.remove('active');
        }
    }
    
    /**
     * Handles profile icon click - toggles menu visibility
     */
    function handleProfileClick(e) {
        e.stopPropagation(); // Prevent event from bubbling up
        profileMenu.classList.toggle('active'); // Toggle menu visibility
    }
    
    /**
     * Closes profile menu when clicking anywhere else on document
     */
    function handleDocumentClick() {
        profileMenu.classList.remove('active');
    }
    
    /**
     * Prevents menu from closing when clicking inside it
     */
    function handleMenuClick(e) {
        e.stopPropagation(); // Prevent event from bubbling up
    }
    
    // Initial check for screen size
    checkScreenSize();
    
    // Re-check screen size when window is resized
    window.addEventListener('resize', checkScreenSize);

    
    // ------------------- COLUMN DISPLAY OPTIONS FUNCTIONALITY -------------------
    
    displayOptionsBtn?.addEventListener("click", () => {
        // Toggle visibility of column options box
        showOptionsBox.style.display = showOptionsBox.style.display === "block" ? "none" : "block";

        // Save current column visibility state
        saveColumnVisibility();
        // Apply the saved visibility settings
        restoreColumnVisibility();
    });

    
    // ------------------- TOAST NOTIFICATION FUNCTIONALITY -------------------
    
    closeButton?.addEventListener("click", () => {
        toastBox.style.display = "none"; // Hide toast notification
    });

    
    // ------------------- SEARCH FUNCTIONALITY -------------------
    
    searchInput?.addEventListener("input", () => {
        // Save current column visibility before search
        saveColumnVisibility();
        
        // Get search term and fetch matching results
        const searchValue = searchInput.value.trim();
        fetchCustomerLeads(searchValue, customerLeadType);
    });

    // Enable click-based sorting on table headers
    document.querySelectorAll("th.sortable").forEach(header => {
        header.addEventListener("click", () => {
            // Get the key to sort by from the header's data attribute
            const sortKey = header.getAttribute("data-sort");

            // Check if the clicked column is already the current sorted column
            if (currentSort.key === sortKey) {
                // Cycle through sort directions: ascending -> descending -> none
                currentSort.direction = currentSort.direction === "asc" ? "desc" :
                                        currentSort.direction === "desc" ? null : "asc";

                // If direction becomes 'null', clear the sort key (no sorting)
                if (!currentSort.direction) {
                    currentSort.key = null;
                }
            } else {
                // New column clicked, start sorting ascending
                currentSort.key = sortKey;
                currentSort.direction = "asc";
            }

            // Re-filter and re-render the user list based on updated sort state
            filterAndRenderUsers(searchInput.value.trim());

            // Restore column visibility (in case rendering affected column states)
            restoreColumnVisibility();
        });
    });
});

// ------------------- Variables -------------------
let allCustomers = [];
let columnVisibility = {};
let currentSort = { key: null, direction: null };

const statusColors = {
    new: "#38bdf8",
    "in progress":"rgb(244, 226, 38)",
    contacted:"rgb(6, 205, 62)",
    closed:"rgb(255, 145, 108)"
};

// ------------------- Fetch -------------------
function loadCustomers(customerType) {
    const formData = new FormData();
    formData.append("customer_lead_type", customerType);

    fetch("php/fetch_customers_leads.php", { method: "POST", body: formData })
        .then(res => res.json())
        .then(data => {
            allCustomers = data;
            filterAndRenderUsers("");
        })
        .catch(err => console.error("Fetch error:", err));
}

function fetchCustomerLeads(searchValue, customerLeadType) {
    const formData = new FormData();
    formData.append("nameSearch", searchValue);
    formData.append("customerLeadType", customerLeadType);

    fetch("php/search_customers_leads.php", { method: "POST", body: formData })
        .then(res => res.text())
        .then(text => {
            try {
                const data = JSON.parse(text);
                allCustomers = data;
                filterAndRenderUsers(searchValue);
            } catch (err) {
                console.error("JSON Parsing Error:", err);
            }
        })
        .catch(err => console.error("Fetch error:", err));
}

// ------------------- Filter + Render -------------------
function filterAndRenderUsers(searchValue = "") {
    // Filter all customers based on search value matching any field
    const filtered = allCustomers.filter(c =>
        c.customer_lead_id.toString().includes(searchValue.toLowerCase()) || // Match ID
        c.name.toLowerCase().includes(searchValue.toLowerCase()) ||         // Match name
        (c.email && c.email.toLowerCase().includes(searchValue.toLowerCase())) || // Match email if exists
        c.company.toLowerCase().includes(searchValue.toLowerCase()) ||      // Match company
        (c.phone_num && c.phone_num.toLowerCase().includes(searchValue.toLowerCase())) || // Match phone if exists
        (c.notes && c.notes.toLowerCase().includes(searchValue.toLowerCase())) || // Match notes if exists
        (c.status_type && c.status_type.toLowerCase().includes(searchValue.toLowerCase())) // Match status if exists
    );

    // Apply sorting if sort criteria is set
    if (currentSort.key && currentSort.direction) {
        filtered.sort((a, b) => {
            let valueA = a[currentSort.key];
            let valueB = b[currentSort.key];

            // Convert to uppercase for case-insensitive string comparison
            if (typeof valueA === 'string') valueA = valueA.toUpperCase();
            if (typeof valueB === 'string') valueB = valueB.toUpperCase();

            // Compare values based on sort direction
            if (valueA < valueB) return currentSort.direction === 'asc' ? -1 : 1;
            if (valueA > valueB) return currentSort.direction === 'asc' ? 1 : -1;
            return 0; // Values are equal
        });
    }

    // Update the table with filtered and sorted results
    updateCustomerTable(filtered);
    // Ensure sort icons reflect current sort state
    updateSortIcons();
}

function updateCustomerTable(customers) {
    // Get reference to table body
    const tbody = document.querySelector(".customer-table tbody");
    if (!tbody) return console.error("Table body not found!");

    // Restore any hidden columns from user preferences
    restoreColumnVisibility();

    // Check if we're on the leads page (for status column display)
    const isLeadsPage = window.location.pathname.includes("lead_list.php");
    
    // Clear table or show "no records" message
    tbody.innerHTML = customers.length ? "" : "<tr><td colspan='8'>No matching records found</td></tr>";

    // Process each customer/lead
    customers.forEach(customer => {
        // Get color for status indicator
        const color = statusColors[customer.status_type?.toLowerCase()] || "gray";
        const row = document.createElement("tr");
        
        // Mark row as clickable
        row.classList.add("clickable-row");
        
        // Create table row HTML
        row.innerHTML = `
            <td>${customer.customer_lead_id}</td>
            <td>${customer.name}</td>
            <td class="company-column">${customer.company}</td>
            <td class="email-column">${customer.email || 'Null'}</td>
            <td class="phone-column">${customer.phone_num || 'Null'}</td>
            <td class="notes-column">${customer.notes || 'Null'}</td>
            ${isLeadsPage ? `
                <td class="status-column">
                    <span class="status-circle" style="background: ${color};"></span>
                    ${customer.status_type || 'Pending'}
                </td>` : ""}
            <td>
                <button type="button" class="view-button">View</button>
            </td>
        `;

        // Add click handler for entire row
        row.addEventListener("click", (e) => {
            // Don't trigger if clicking the view button (handled separately)
            if (!e.target.classList.contains('view-button')) {
                openCustomerDetails(customer);
            }
        });
        
        // Add row to table
        tbody.appendChild(row);
        
        // Add click handler specifically for view button
        const viewButton = row.querySelector(".view-button");
        viewButton.addEventListener("click", (e) => {
            e.stopPropagation(); // Prevent row click from firing
            openCustomerDetails(customer);
        });
    });

    // Restore column visibility again in case table was recreated
    restoreColumnVisibility();
}
// ------------------- Column Visibility -------------------
function saveColumnVisibility() {
    const columnClasses = ["company-column", "email-column", "phone-column", "notes-column", "status-column"];
    columnClasses.forEach(cls => {
        const cell = document.querySelector(`.${cls}`);
        if (cell) {
            // Store whether it's visible or not
            columnVisibility[cls] = window.getComputedStyle(cell).display !== "none";
        }
    });
}

function restoreColumnVisibility() {
    Object.entries(columnVisibility).forEach(([cls, visible]) => {
        document.querySelectorAll(`.${cls}`).forEach(cell => {
            cell.style.display = visible ? "table-cell" : "none";
        });
    });
}

// ------------------- Sort Icon UI -------------------
function updateSortIcons() {
    const icons = document.querySelectorAll("th.sortable i");
    icons.forEach(icon => icon.className = "fa fa-sort");

    if (!currentSort.key || !currentSort.direction) return;

    const icon = document.getElementById(`icon-${currentSort.key}`);
    if (icon) {
        icon.className = currentSort.direction === "asc"
            ? "fa fa-sort-asc"
            : "fa fa-sort-desc";
    }
}

// ------------------- Details -------------------
// Fetch interactions and reminders for a specific customer
function fetchInteractionsAndReminders(customerId) {
    const formData = new FormData();
    formData.append("customer_lead_id", customerId);

    fetch("php/fetch_interactions_reminders.php", { method: "POST", body: formData })
        .then(res => res.json())
        .then(({ interactions, reminders }) => {
            // Render the fetched interactions and reminders into their respective lists
            renderList("interaction-list", interactions, "No interactions yet.");
            renderList("reminder-list", reminders, "No reminders found.");
        })
        .catch(err => console.error("Fetch details error:", err));
}

// Open customer details modal and populate information
function openCustomerDetails(customer) {
    const modal = document.getElementById("customer-details");
    if (!modal) {
        console.error("Customer details modal not found!");
        return;
    }
    
    // Update header with customer info
    document.getElementById("details-name").textContent = customer.name;
    document.getElementById("details-company").textContent = customer.company || 'N/A';
    
    // Fill in all customer details
    const detailsContainer = document.getElementById("customer-info");
    detailsContainer.innerHTML = `
    <div class="details-grid">
        <div class="detail-item">
            <span class="detail-label">ID:</span>
            <span class="detail-value">${customer.customer_lead_id}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Email:</span>
            <span class="detail-value email-value" 
                data-email="${customer.email || 'N/A'}" 
                title="${customer.email || 'No email'}">
                ${customer.email || 'N/A'}
            </span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Phone:</span>
            <span class="detail-value">${customer.phone_num || 'N/A'}</span>
        </div>
        
        ${customerLeadType === 1 ? '' : `
        <div class="detail-item">
            <span class="detail-label">Status:</span>
            <span class="detail-value status-badge" style="background-color: ${statusColors[customer.status_type?.toLowerCase()] || 'gray'}">
                ${customer.status_type || 'Pending'}
            </span>
        </div>
        `}
    </div>
    
    <div class="details-notes">
        <h4>Notes</h4>
        <p>${customer.notes || 'No notes available.'}</p>
    </div>
`;

    
    // Update status progress indicator
    if (customerLeadType == 2){
        renderStatusProgress(customer.status_type?.toLowerCase());
    }
    
    // Fetch and display interactions and reminders
    fetchInteractionsAndReminders(customer.customer_lead_id);
    
    // Show modal with animation
    modal.classList.remove("hidden");
    setTimeout(() => modal.classList.add("active"), 10);
    
    // Setup close button
    document.getElementById("close-details").onclick = () => {
        modal.classList.remove("active");
        setTimeout(() => modal.classList.add("hidden"), 300);
    };
    
    // Set up the edit button to redirect to the update form
    const editButton = document.getElementById("edit-customer");
    if (editButton) {
        editButton.onclick = () => {
            const form = document.createElement("form");
            form.method = "POST";
            form.action = "update_customer_lead.php";

            const hiddenInput = document.createElement("input");
            hiddenInput.type = "hidden";
            hiddenInput.name = "customer_lead_id";
            hiddenInput.value = customer.customer_lead_id;

            form.appendChild(hiddenInput);
            document.body.appendChild(form);
            form.submit();
        };
    }
    
    // Setup quick action buttons (email, call, add reminder, log interaction)
    setupQuickActions(customer);
}

// Set up quick action buttons for emailing, calling, adding reminders, or logging interactions
function setupQuickActions(customer) {
    const actionsContainer = document.getElementById("quick-actions");
    if (!actionsContainer) return;
    
    // Populate the quick actions container with buttons
    actionsContainer.innerHTML = `
        <button class="action-btn email-btn" title="Send Email">
            <i class="fa fa-envelope"></i>
        </button>
        <button class="action-btn call-btn" title="Call Customer">
            <i class="fa fa-phone"></i>
        </button>
        <button class="action-btn reminder-btn" title="Add Reminder">
            <i class="fa fa-bell"></i>
        </button>
        <button class="action-btn interaction-btn" title="Log Interaction">
            <i class="fa-solid fa-clipboard"></i>
        </button>
    `;
    
    // Email button
    actionsContainer.querySelector(".email-btn").addEventListener("click", () => {
        if (customer.email) {
            window.location.href = `mailto:${customer.email}`;
        } else {
            showToastDetail("No email address available");
        }
    });
    
    // Call button
    actionsContainer.querySelector(".call-btn").addEventListener("click", () => {
        if (customer.phone_num) {
            window.location.href = `tel:${customer.phone_num}`;
        } else {
            showToastDetail("No phone number available");
        }
    });
    
    // Add reminder button - shows a form
    actionsContainer.querySelector(".reminder-btn").addEventListener("click", () => {
        showReminderForm(customer.customer_lead_id);
    });
    
    // Log interaction button - shows a form
    actionsContainer.querySelector(".interaction-btn").addEventListener("click", () => {
        showInteractionForm(customer.customer_lead_id);
    });
}

// rendering of status progress bar
function renderStatusProgress(currentStatus) {
    const stages = ["new", "in progress", "contacted", "closed"];
    const currentIndex = stages.indexOf(currentStatus);
    const container = document.getElementById("status-progress");
    
    if (!container) return; // If container not found, exit early
    
    container.innerHTML = ''; // Clear existing progress content
    
    // Create progress bar container
    const progressBar = document.createElement("div");
    progressBar.className = "progress-track";
    
    // Add stages
    stages.forEach((status, index) => {
        const stageEl = document.createElement("div");
        // Mark stages as completed if their index <= current status index
        stageEl.className = `progress-stage ${index <= currentIndex ? 'completed' : ''}`;
        
        const dot = document.createElement("div");
        dot.className = "progress-dot";
        
        const label = document.createElement("span");
        label.className = "progress-label";
        label.textContent = status.charAt(0).toUpperCase() + status.slice(1);
        
        stageEl.appendChild(dot);
        stageEl.appendChild(label);
        progressBar.appendChild(stageEl);
        
        // Connect stages with lines
        if (index < stages.length - 1) {
            const line = document.createElement("div");
            line.className = `progress-line ${index < currentIndex ? 'completed' : ''}`;
            progressBar.appendChild(line);
        }
    });
    
    container.appendChild(progressBar);
    
    // Trigger animations after modal is fully visible
    setTimeout(() => {
        const lines = container.querySelectorAll('.progress-line');
        const dots = container.querySelectorAll('.progress-dot');
        
        lines.forEach((line, index) => {
            if (index < currentIndex) {
                // Animate line growth
                line.style.transition = 'width 0.6s ease ' + (index * 0.3) + 's';
                line.style.width = '100%';
            }
        });
        
        dots.forEach((dot, index) => {
            if (index <= currentIndex) {
                // Animate dot fill
                dot.style.transition = 'all 0.4s ease ' + (index * 0.4) + 's';
                dot.style.transform = 'scale(1.2)';
                setTimeout(() => {
                    dot.style.transform = 'scale(1)';
                }, 400 + (index * 300));
            }
        });
    }, 10); // Small delay to ensure modal is visible
}

// Renders a list (interactions or reminders) into a given element
function renderList(elementId, items, emptyMsg) {
    const container = document.getElementById(elementId);
    if (!container) return; // Exit early if container not found

    container.innerHTML = ''; // Clear existing list

    // If no items to show, display an empty message
    if (!items?.length) {
        container.innerHTML = `<div class="empty-list">${emptyMsg}</div>`;
        return;
    }

    // Sort by date (newest first)
    items.sort((a, b) => {
        const dateA = new Date(a.formatted_date || a[0]);
        const dateB = new Date(b.formatted_date || b[0]);
        return dateB - dateA;
    });

    // Render each item
    items.forEach(item => {
        const itemEl = document.createElement("div");
        itemEl.className = "list-item";

        // Create Date element
        const dateEl = document.createElement("div");
        dateEl.className = "list-item-date";
        
        const itemDate = new Date(item.formatted_date || item[0]);
        if (isNaN(itemDate.getTime())) {
            console.error(`Invalid date:`, item);
            return;
        }
        
        dateEl.textContent = itemDate.toLocaleDateString(undefined, {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });

        // Content element
        const contentEl = document.createElement("div");
        contentEl.className = "list-item-content";
        
        // Type element
        const typeEl = document.createElement("div");
        typeEl.className = "interaction-type";
        typeEl.textContent = item.type || item[2] || 'Unknown Type';

        // Details element
        const detailsEl = document.createElement("div");
        detailsEl.className = "interaction-details";
        detailsEl.textContent = item.description || item[1] || '';

        // If user is associated with the item, show it
        if (item.user_name || item[3]) {
            const userEl = document.createElement("div");
            userEl.className = "interaction-user";
            userEl.textContent = ` ${item.user_name || item[3]}`;
            contentEl.appendChild(userEl);
        }

        // Append elements
        contentEl.prepend(typeEl);
        contentEl.appendChild(detailsEl);
        itemEl.appendChild(dateEl);
        itemEl.appendChild(contentEl);
        container.appendChild(itemEl);
    });
}

// Show toast message for notifications
function showToastDetail(message, isSuccess = true) {
    const toastBox = document.querySelector(".toast-box-detail") || createToastElementDetail();
    const messageEl = toastBox.querySelector(".toast-message-detail");
    const icon = toastBox.querySelector(".toast-icon-detail");
    
    // Set message and icon
    messageEl.textContent = message;
    icon.className = isSuccess ? "fa-solid fa-circle-check toast-icon-detail" : "fa-solid fa-circle-exclamation toast-icon-detail";
    
    // Show toast
    toastBox.style.display = "flex";
    
    // Auto-hide after 3 seconds
    setTimeout(() => {
        toastBox.style.display = "none";
    }, 3000);
}

function createToastElementDetail() {
    const toast = document.createElement("div");
    toast.className = "toast-box-detail";
    
    toast.innerHTML = `
        <div class="toast-left-detail">
            <i class="fa-solid fa-circle-check toast-icon-detail"></i>
            <span class="toast-message-detail" style="font-family: 'Poppins', sans-serif; font-weight:700;"></span>
        </div>
        <i class="fa-solid fa-xmark" id="close-button-detail" style="cursor:pointer;"></i>
    `;
    
    document.body.appendChild(toast);
    
    // Close button functionality
    const closeButton = toast.querySelector("#close-button-detail");
    closeButton.addEventListener("click", () => {
        toast.style.display = "none";
    });
    
    return toast;
}

// Show reminder form inside customer details modal
function showReminderForm(customerId) {
    // Create form container
    const formContainer = document.createElement("div");
    formContainer.className = "modal-form";
    
    // Build the form HTML
    formContainer.innerHTML = `
        <h3>Add Reminder</h3>
        <form id="reminder-form">
            <div class="form-group">
                <label for="reminderDate">Reminder Date</label>
                <input type="date" id="reminderDate" name="reminderDate" min="${new Date().toISOString().split('T')[0]}" required>
            </div>

            <div class="form-group">
                <label for="interactionType">Interaction Type</label>
                <select id="interactionType" name="interactionType" required>
                    ${interactionTypes.map(type => `
                        <option value="${type.interaction_type_id}">
                            ${type.interaction_type_name}
                        </option>`).join('')}
                </select>
            </div>

            <div class="form-group">
                <label for="reminderNotes">Notes</label>
                <textarea id="reminderNotes" name="reminderNotes" placeholder="reminder notes..."></textarea>
            </div>

            <div class="form-actions">
                <button type="button" id="cancel-reminder">Cancel</button>
                <button type="submit" id="save-reminder">Save Reminder</button>
            </div>
        </form>
    `;
    
    // Add form to modal
    const detailsModal = document.getElementById("customer-details");
    detailsModal.appendChild(formContainer);
    
    // Set default date to tomorrow
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById("reminderDate").valueAsDate = tomorrow;
    
    // Handle cancel button click
    document.getElementById("cancel-reminder").addEventListener("click", () => {
        formContainer.remove();
    });
    
    // Handle form submission
    document.getElementById("reminder-form").addEventListener("submit", (e) => {
        e.preventDefault();// Prevent page reload
        
        // Add console logging for debugging
        console.log("Form submitted");

        // Gather form data
        const reminderDate = document.getElementById("reminderDate").value;
        const reminderNotes = document.getElementById("reminderNotes").value;
        const interactionType = document.getElementById("interactionType").value;
        
        // Create form data object for POST request
        const formData = new FormData();
        formData.append("customer_lead_id", customerId);
        formData.append("reminderDate", reminderDate);
        formData.append("reminderNotes", reminderNotes);
        formData.append("interactionType", interactionType);
        
        // Send reminder data to server
        fetch("php/add_reminders.php", {
            method: "POST",
            headers: {
                'X-Requested-With': 'XMLHttpRequest' // Add this to indicate AJAX request
            },
            body: formData
        })
        .then(res => {
            if (!res.ok) {
                throw new Error(`Server returned ${res.status}`);
            }
            return res.json();
        })
        .then(data => {
            if (data.success) {
                showToastDetail("Reminder added successfully");
                fetchInteractionsAndReminders(customerId); // Refresh the interaction/reminder list
                formContainer.remove(); // Close form
            } else {
                showToastDetail(data.error || "Error adding reminder");
            }
        })
        .catch(error => {
            console.error("Request failed:", error);
            showToastDetail("Error adding reminder");
        });
    });
}

// Show interaction form in modal
function showInteractionForm(customerId) {
    // Create form container
    const formContainer = document.createElement("div");
    formContainer.className = "modal-form";
    
    // Build the form HTML
    formContainer.innerHTML = `
        <h3>Log Interaction</h3>
        <form id="interaction-form">
            <div class="form-group">
                <label for="interactionType">Type</label>
                <select id="interactionType" name="interactionType" required>
                    ${interactionTypes.map(type => `
                        <option value="${type.interaction_type_id}">
                            ${type.interaction_type_name}
                        </option>`).join('')}
                </select>
            </div>
            
            <div class="form-group">
                <label for="interactionHistoryDate">Date</label>
                <input type="date" id="interactionHistoryDate" name="interactionHistoryDate" max="${new Date().toISOString().split('T')[0]}" required>
            </div>
            
            <div class="form-group">
                <label for="interactionDetails">Details</label>
                <textarea id="interactionDetails" name="interactionDetails" rows="4" placeholder="interaction details..." required></textarea>
            </div>
            
            <div class="form-actions">
                <button type="button" id="cancel-interaction">Cancel</button>
                <button type="submit">Save Interaction</button>
            </div>
        </form>
    `;
    
    // Add to modal
    const detailsModal = document.getElementById("customer-details");
    detailsModal.appendChild(formContainer);

    // Set the max date to today
    const today = new Date();
    const maxDate = today.toISOString().split('T')[0];
    document.getElementById("interactionHistoryDate").setAttribute("max", maxDate);
    
    // Set default date to today
    document.getElementById("interactionHistoryDate").valueAsDate = new Date();

    // Handle cancel
    document.getElementById("cancel-interaction").addEventListener("click", () => {
        formContainer.remove();
    });
    
    // Handle form submission
    document.getElementById("interaction-form").addEventListener("submit", (e) => {
        e.preventDefault();
        
        const interactionType = document.getElementById("interactionType").value;
        const interactionHistoryDate = document.getElementById("interactionHistoryDate").value;
        const interactionDetails = document.getElementById("interactionDetails").value;
        
        // Create form data object for POST request
        const formData = new FormData();
        formData.append("customer_lead_id", customerId);
        formData.append("interactionType", interactionType);
        formData.append("interactionHistoryDate", interactionHistoryDate);
        formData.append("interactionDetails", interactionDetails);
        
        // Send interaction data to server
        fetch("php/add_interactions.php", {
            method: "POST",
            headers: {
                'X-Requested-With': 'XMLHttpRequest' // Add this to indicate AJAX request
            },
            body: formData
        })
        .then(res => {
            if (!res.ok) {
                throw new Error(`Server returned ${res.status}`);
            }
            return res.json();
        })
        .then(data => {
            if (data.success) {
                showToastDetail("Interaction logged successfully");
                fetchInteractionsAndReminders(customerId); // Refresh the interaction/reminder list
                formContainer.remove(); //Close form
            } else {
                showToastDetail(data.error || "Error logging interaction");
            }
        })
        .catch(error => {
            console.error("Request failed:", error);
            showToastDetail("Error logging interaction");
        });
    });
}