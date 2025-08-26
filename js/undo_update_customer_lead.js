// Store initial form values for undo functionality
let originalValues = {};

document.addEventListener("DOMContentLoaded", () => {
    let inputs = document.querySelectorAll("#customerForm input, #customerForm select, #customerForm textarea");
    inputs.forEach(input => {
        originalValues[input.name] = input.value; // Store original values
    });
});

// Undo function - restore original values
document.addEventListener("DOMContentLoaded", () => {
    let undoButton = document.getElementById("undoChanges");
    if (undoButton) {
        undoButton.addEventListener("click", () => {
            let inputs = document.querySelectorAll("#customerForm input, #customerForm select, #customerForm textarea");
            inputs.forEach(input => {
                input.value = originalValues[input.name];
            });
        });
    }
});
