document.addEventListener("DOMContentLoaded", function() {
    console.log("reminder.js loaded");
    fetchCustomers(); // Call your function here
});

function fetchCustomers() {
    fetch("php/reminder_popup.php", { method: "POST" })
        .then(response => response.json()) // Convert response to JSON
        .then(customers => {
            console.log(`Fetched ${customers.length} customers`);
            openPopup(customers);
        }) // Pass data to function
        .catch(error => console.error('Error fetching customers:', error));
}

let customerIndex = 0;
let customerList = [];

function openPopup(customers) {
    console.log("openPopup ran");
    const popupDetails = document.getElementById("popup-details");
    popupDetails.innerHTML = ""; // Clear previous content

    if (!customers.length) {
        popupDetails.innerHTML = "<tr><td colspan='7'>No customers found</td></tr>";
        document.getElementById("customer-popup").classList.add("show");
        return;
    }
    
    customerList = customers; // Store the customer array
    customerIndex = 0; // Start at the first customer

    displayCustomer();
}

// Function to display the current customer in the popup
function displayCustomer() {
    if (customerIndex >= customerList.length) {
        closePopup(); // Close when all customers are shown
        return;
    }

    const customer = customerList[customerIndex];
    const popupDetails = document.getElementById("popup-details");

    let content = `<div>
        <p><strong>Name:</strong> ${customer.name}</p>
        <p><strong>Email:</strong> ${customer.email}</p>
        <p><strong>Reminder Date:</strong> ${customer.reminder_date}</p>
        <p><strong>Interaction Type:</strong> ${customer.interaction_type_name}</p>
        <p><strong>Notes:</strong> ${customer.notes || 'N/A'}</p>
    </div>`;
    popupDetails.innerHTML = content;

    // document.getElementById("customer-popup").style.display = "flex";
    document.getElementById("customer-popup").classList.add("show");
}

// Function to handle the close button and move to the next customer
function closePopup() {
    customerIndex++; // Move to the next customer

    if (customerIndex < customerList.length) {
        displayCustomer(); // Show the next customer
    } else {
        // document.getElementById("customer-popup").style.display = "none"; // Hide popup if done
        document.getElementById("customer-popup").classList.remove("show");
    }
}