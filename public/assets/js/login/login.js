// for signup function
// function signup(){
//     document.getElementById("loginForm").action = "signup"
//     document.getElementById('login-or-signup-msg').innerHTML = "Signup with redmine";
//     document.getElementById('login-to-signup-change').innerHTML = "Already have an account?<a  href='' onclick='return login()' id='login-to-signup-changelink'> Login</a>";
//     return false;
// }


// for Login function
// function login(){
//     document.getElementById("loginForm").action = "login"
//     document.getElementById('login-or-signup-msg').innerHTML = "Get Started";
//     document.getElementById('login-to-signup-change').innerHTML = "Don't have an account?<a  href='' onclick='return signup()' id='login-to-signup-changelink'>Sign up</a>";
//     return false;
// }

// document.addEventListener('DOMContentLoaded', (event) => {
//     function setFullHeight() {
//         var screenHeight = window.innerHeight;
//         var screenWidth = window.innerWidth;
//         var body = document.querySelector('body');
//         body.style.height = screenHeight + 'px';
//         body.style.width = screenWidth + 'px';
//     }
    
//     // Set height initially
//     setFullHeight();
// });

// Example starter JavaScript for disabling form submissions if there are invalid fields
(function () {
    'use strict';
    window.addEventListener('load', function () {
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.getElementsByClassName('needs-validation');
        // Loop over them and prevent submission
        var validation = Array.prototype.filter.call(forms, function (form) {
            form.addEventListener('submit', function (event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

// toggle function for password visible and hide
const togglePassword = document.querySelector('#togglePassword');
const password = document.querySelector('#password');
togglePassword.addEventListener('click', () => {
    // Toggle the type attribute using
    // getAttribure() method
    const type = password
        .getAttribute('type') === 'password' ?
        'text' : 'password';
    password.setAttribute('type', type);
    // Toggle the eye and bi-eye icon
    togglePassword.classList.toggle('bi-eye');
});