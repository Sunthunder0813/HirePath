document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.getElementById('togglePassword');
            const passwordField = document.getElementById('password');
            const toggleImage = document.getElementById('passwordToggleImage');

            passwordField.addEventListener('focus', function () {
                if (passwordField.value.length > 0) {
                    togglePassword.classList.add('show');
                }
            });

            passwordField.addEventListener('input', function () {
                if (passwordField.value.length > 0) {
                    togglePassword.classList.add('show');
                } else {
                    togglePassword.classList.remove('show');
                }
            });

            passwordField.addEventListener('blur', function () {
                if (passwordField.value.length === 0) {
                    togglePassword.classList.remove('show');
                }
            });

            togglePassword.addEventListener("click", function () {
                if (passwordField.type === "password") {
                    passwordField.type = "text";
                    toggleImage.src = "../../static/img/icon/visible.png"; 
                } else {
                    passwordField.type = "password";
                    toggleImage.src = "../../static/img/icon/hidden.png"; 
                }
            });
        });