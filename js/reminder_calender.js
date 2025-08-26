document.addEventListener('DOMContentLoaded', function() {
    console.log("js loaded");

    // Elements for the hamburger menu
    const menu = document.querySelector(".menu-icon");
    const hamburgerIcon = document.querySelector(".menu-icon img");
    const navigation = document.querySelector(".navigation");
    const main = document.querySelector(".main");

    // Toggle sidebar navigation when hamburger menu is clicked
    if (menu && hamburgerIcon && navigation && main) {
        menu.addEventListener("click", () => {
            navigation.classList.toggle('active');
            main.classList.toggle('active');
            hamburgerIcon.src = navigation.classList.contains('active')
                ? "./assets/menu-unfold-line.png" // Change icon to unfolded
                : "./assets/menu-fold-line.png"; // Change icon to folded
        });
    }

    // Profile menu elements
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
    
    // Handle profile picture click (toggle menu)
    function handleProfileClick(e) {
        e.stopPropagation(); // Prevent click event from bubbling
        profileMenu.classList.toggle('active');
    }
    
    // Handle clicks outside of profile menu (close menu)
    function handleDocumentClick() {
        profileMenu.classList.remove('active');
    }
    
    // Handle clicks inside profile menu (do not close it)
    function handleMenuClick(e) {
        e.stopPropagation(); // Stop event from reaching document
    }
    
    // Initial check
    checkScreenSize();
    
    // Re-check when window is resized
    window.addEventListener('resize', checkScreenSize);

    // Initialize FullCalendar
    var calendarEl = document.getElementById('calendar');
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next',  // Navigation buttons
            center: 'title',    // Centered title
            right: 'today'      // "Today" button
        },
        eventDisplay: 'block',// Display events as blocks
        // Fetch events from server
        events: function(fetchInfo, successCallback, failureCallback) {
            fetch('php/fetch_reminders_calender.php', { 
                method: "POST",
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(events => {
                successCallback(events); // Pass events to calendar
            })
            .catch(error => {
                console.error('Error:', error);
                failureCallback(error);
            });
        },
        // Log each event when it is rendered
        eventDidMount: function(info) {
            console.log('Event rendered:', info.event);
        },
        // Add click handler for events
        eventClick: function(info) {
            info.jsEvent.preventDefault(); // Prevent default behavior
            
            const event = info.event;
            const extProps = event.extendedProps;
            
            // Format date nicely
            const formattedDate = event.start ? new Date(event.start).toLocaleString() : "N/A";
            
            // Update modal content
            document.getElementById("modalTitle").textContent = event.title || "Reminder Details";
            document.getElementById("modalDate").textContent = extProps.reminder_date;
            document.getElementById("modalCustomerType").textContent = extProps.customer_type_name || "N/A";
            document.getElementById("modalName").textContent = extProps.name || "N/A";
            document.getElementById("modalCompany").textContent = extProps.company || "N/A";
            document.getElementById("modalNotes").textContent = extProps.notes || "No notes available";
            document.getElementById("modalUserName").textContent = extProps.user_name || "N/A";
            
            // Show modal
            document.getElementById("reminderModal").style.display = "block";
            document.getElementById("modalOverlay").style.display = "block";
        
        }
    });
    
    calendar.render();
});

// Close modal when clicking "Close" or overlay
document.getElementById('closeModal').addEventListener('click', () => {
    document.getElementById('reminderModal').style.display = 'none';
    document.getElementById('modalOverlay').style.display = 'none';
});

// To open modal:
function openReminderModal() {
    document.getElementById('reminderModal').style.display = 'block';
    document.getElementById('modalOverlay').style.display = 'block';
}

function loadReminders(calendar) {
    console.log("Load reminders function ran");
    fetch('php/fetch_reminders_calender.php', { 
        method: "POST",
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        console.log("Reminders fetched");
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(events => {
        console.log('Received events:', events); // Add this line
        calendar.removeAllEvents();      // Clear existing events
        calendar.addEventSource(events); // Load new events
    })
    .catch(error => {
        console.error('Error fetching reminders:', error);
        alert('There was an error fetching reminders!');
    });
}