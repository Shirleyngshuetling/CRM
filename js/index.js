const container = document.querySelector(".container"); // Main container element
const signUpBtn = document.querySelector(".signUp-btn"); // Sign up button (large screen)
const loginBtn = document.querySelector(".login-btn"); // Login button (large screen)
const smSignUpBtn = document.querySelector(".signUpClick"); // Sign up button (small screen)
const smLoginBtn = document.querySelector(".loginClick"); // Login button (small screen)
const errorMessages = document.querySelectorAll(".error-message"); // All error message elements
const successMessages = document.querySelectorAll(".success-message"); // All success message elements

// Debug log to check error message elements
console.log("Error messages:", document.querySelectorAll(".error-message"));

// ---------- Form Toggle Functionality ----------
signUpBtn?.addEventListener("click", () => {
container.classList.add("active");
});

loginBtn?.addEventListener("click", () => {
container.classList.remove("active");
});

smSignUpBtn?.addEventListener("click", () => {
container.classList.add("active");
});

smLoginBtn?.addEventListener("click", () => {
container.classList.remove("active");
});



// ---------- FORM VALIDATION AND SUBMISSION ----------
// Initialize when DOM is fully loaded
document.addEventListener("DOMContentLoaded", function() {

    const loginForm = document.getElementById("loginForm");
    const signupForm = document.getElementById("signupForm");

    // Login form validation
    if (loginForm) {
        loginForm.addEventListener("submit", function(event) {
            const email = document.getElementById("signinEmail").value;
            const password = document.getElementById("signinPassword").value;

            // Check for empty fields
            if (!email || !password) {
                event.preventDefault(); // Stop form submission
                alert("Please fill in all fields.");
            }
        });
    }

    // Signup form validation
    if (signupForm) {
        signupForm.addEventListener("submit", function(event) {
            const username = document.getElementById("username").value;
            const email = document.getElementById("signupEmail").value;
            const password = document.getElementById("signupPassword").value;
            const role = document.getElementById("role").value;

            // Check for empty fields
            if (!username || !email || !password || !role) {
                event.preventDefault(); // Stop form submission
                alert("Please fill in all fields.");
            }
            // Additional username/password validation
            validate(username, password, event);
        });
    }
    
    // Check if we need to show specific form due to errors
    const appElement1 = document.getElementById("loginApp");
    const appElement2 = document.getElementById("signupApp");
    const showSignUpUponSignupErr = appElement2.getAttribute("data-show-signup") === 'true';
    const showLoginUponLoginErr = appElement1.getAttribute("data-show-login") === 'true';

    // Show signup form if there was a signup error
    if (showSignUpUponSignupErr) {
        container.classList.add("active");
    }
    // Show login form if there was a login error
    if (showLoginUponLoginErr) {
        container.classList.remove("active");
    }
});

// ---------- MESSAGE CLEARING -----------
// Clear error/success messages when switching forms
if (loginBtn) {
    loginBtn.addEventListener("click", clearMessages);
}

if (signUpBtn) {
    signUpBtn.addEventListener("click", clearMessages);
}

// Function to remove messages
function clearMessages() {
    errorMessages.forEach(msg => msg.remove());
    successMessages.forEach(msg => msg.remove());
}

// Logs out the user by redirecting to logout endpoint
function logout() {

    window.location.href = `${BASE_URL}/php/logout.php`;
}

function validateUsername(username) {
    if (username.length < 5)
        return "Usernames must be at least 5 characters.\n";
    else if (/[^a-zA-Z0-9\- ]/.test(username))  // Notice the space inside the brackets
        return "Only a-z, A-Z, 0-9, '-', and spaces are allowed in Usernames.\n";
    return "";
}

function validatePassword(password) {
    if (password === "") 
        return "No Password was entered.\n";
    else if (password.length < 8)
        return "Passwords must be at least 8 characters.\n";
    else if (!/[a-z]/.test(password) || !/[A-Z]/.test(password) || !/[0-9]/.test(password))
        return "Passwords require one each of a-z, A-Z and 0-9.\n";
    return "";
}

function validate(username, password, event) {
    fail = validateUsername(username);
    fail += validatePassword(password);
    if (fail == "") return true;
    else { event.preventDefault(); alert(fail); return false; }
}

// ---------- PASSWORD VISIBILITY TOGGLE ----------
function togglePasswordVisibility() {
    const passwordSignUpField = document.getElementById("signupPassword");
    const passwordSignInField = document.getElementById("signinPassword");
    const toggleIconSignIn = document.querySelector(".toggle-password-signin");
    const toggleIconSignUp = document.querySelector(".toggle-password-signup");

    // Toggle sign-in password field
    if (passwordSignInField){
        if (passwordSignInField.type === "password") {
            passwordSignInField.type = "text";
            toggleIconSignIn.classList.remove("bx-show");
            toggleIconSignIn.classList.add("bx-hide");
        } else {
            passwordSignInField.type = "password";
            toggleIconSignIn.classList.remove("bx-hide");
            toggleIconSignIn.classList.add("bx-show");
        }
    }

    // Toggle sign-up password field
    if (passwordSignUpField){
        if (passwordSignUpField.type === "password") {
            passwordSignUpField.type = "text";
            toggleIconSignUp.classList.remove("bx-show");
            toggleIconSignUp.classList.add("bx-hide");
        } else {
            passwordSignUpField.type = "password";
            toggleIconSignUp.classList.remove("bx-hide");
            toggleIconSignUp.classList.add("bx-show");
        }
    }
    
}