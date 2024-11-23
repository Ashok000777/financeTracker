// sign.js

document.getElementById('signup').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent form submission for validation
    
    var fullname = document.getElementsByName('fullname')[0].value;
    var email = document.getElementsByName('email')[0].value;
    var password = document.getElementsByName('password')[0].value;
    var confirmPassword = document.getElementsByName('confirmpassword')[0].value;

    // Validate email format
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        alert('Please enter a valid email address.');
        return false;
    }

    // Check if passwords match
    if (password !== confirmPassword) {
        alert('Passwords do not match. Please re-enter.');
        return false;
    }

    // All validations passed, submit the form
    this.submit();
});
