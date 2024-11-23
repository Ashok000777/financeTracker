// loginpage.js

document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent form submission for validation

    var email = document.getElementsByName('email')[0].value;
    var password = document.getElementsByName('password')[0].value;

    // Validate email format
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        alert('Please enter a valid email address.');
        return false;
    }

    // Validate password
    if (password.length === 0) {
        alert('Please enter your password.');
        return false;
    }

    // All validations passed, submit the form
    this.submit();
});
