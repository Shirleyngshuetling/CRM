window.addEventListener("load", function() {
    fetch("php/send_email.php", { method: "POST" })
        .then(response => response.text())
        .then(data => console.log("Response from PHP:", data))
        .catch(error => console.error("Error:", error));
});