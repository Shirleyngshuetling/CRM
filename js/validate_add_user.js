document.addEventListener("DOMContentLoaded", function() {
    const userForm = document.getElementById("signupForm"); // Get the signup form by its ID
    
    if (userForm) {
        // Attach a submit event listener to the form
        userForm.addEventListener("submit", function(event) {
            // Fetch form field values
            const username = document.getElementById("username").value;
            const email = document.getElementById("signupEmail").value;
            const password = document.getElementById("signupPassword").value;
            const role = document.getElementById("role").value;
            
            // Basic required field check
            if (!username || !email || !password || !role) {
                event.preventDefault(); // Prevent form submission
                alert("Please fill in all fields."); // Alert the user
                return; 
            }
            
            // Username and password validation
            if (!validate(username, password, event)) {
                event.preventDefault(); // Prevent form submission if validation fails
            }
        });
    }
    
    function validateUsername(username) {
        if (username.length < 5)
            return "Usernames must be at least 5 characters.\n";
        else if (/[^a-zA-Z0-9\- ]/.test(username))  // Allow only letters, numbers, hyphens, and spaces
            return "Only a-z, A-Z, 0-9, '-', and spaces are allowed in Usernames.\n";
        return ""; //No errors
    }
    
    function validatePassword(password) {
        if (password === "") 
            return "No Password was entered.\n";
        else if (password.length < 8)
            return "Passwords must be at least 8 characters.\n";
        else if (!/[a-z]/.test(password) || !/[A-Z]/.test(password) || !/[0-9]/.test(password))
            return "Passwords require one each of a-z, A-Z and 0-9.\n";
        return ""; //No errors
    }
    
    // Combined validation function
    function validate(username, password, event) {
        fail = validateUsername(username);
        fail += validatePassword(password);
        if (fail == "") return true; // No validation errors
        else { event.preventDefault(); alert(fail); return false; }
    }

    
});

function togglePasswordVisibility() {
    const passwordSignUpField = document.getElementById("signupPassword");
    const toggleIcon = document.querySelector(".toggle-password");

    if (passwordSignUpField) {
        if (passwordSignUpField.type === "password") {
            // Change input type to text to reveal password
            passwordSignUpField.type = "text";
            toggleIcon.classList.remove("bx-show");
            toggleIcon.classList.add("bx-hide");
        } else {
            // Change input type back to password to hide it
            passwordSignUpField.type = "password";
            toggleIcon.classList.remove("bx-hide");
            toggleIcon.classList.add("bx-show");
        }
    }
    
}