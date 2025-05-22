document.addEventListener('DOMContentLoaded', function () {
            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('confirm_password');
            const passwordChecklist = document.getElementById('passwordChecklist');

            const togglePassword = document.getElementById('togglePassword');
            const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
            const passwordToggleImage = document.getElementById('passwordToggleImage');
            const confirmPasswordToggleImage = document.getElementById('confirmPasswordToggleImage');

            function setupToggle(toggleElement, field, imageElement) {
                toggleElement.addEventListener('click', () => {
                    if (field.type === 'password') {
                        field.type = 'text';
                        imageElement.src = '../../static/img/icon/visible.png';
                    } else {
                        field.type = 'password';
                        imageElement.src = '../../static/img/icon/hidden.png';
                    }
                });
            }

            function toggleVisibility(field, toggleElement) {
                if (field.value.length > 0) {
                    toggleElement.style.display = 'flex';
                } else {
                    toggleElement.style.display = 'none';
                }
            }

            passwordField.addEventListener('input', () => {
                toggleVisibility(passwordField, togglePassword);
            });

            confirmPasswordField.addEventListener('input', () => {
                toggleVisibility(confirmPasswordField, toggleConfirmPassword);
            });

            toggleVisibility(passwordField, togglePassword);
            toggleVisibility(confirmPasswordField, toggleConfirmPassword);

            function validatePassword() {
                const password = passwordField.value;

                const lengthValid = password.length >= 8;
                const uppercaseValid = /[A-Z]/.test(password);
                const lowercaseValid = /[a-z]/.test(password);
                const specialValid = /\W/.test(password);
                const numberValid = /\d/.test(password);

                updateChecklistItem('password_length', lengthValid);
                updateChecklistItem('password_uppercase', uppercaseValid);
                updateChecklistItem('password_lowercase', lowercaseValid);
                updateChecklistItem('password_special', specialValid);
                updateChecklistItem('password_number', numberValid);
            }

            function updateChecklistItem(id, isValid) {
                const listItem = document.getElementById(id);
                if (isValid) {
                    listItem.classList.add('valid');
                } else {
                    listItem.classList.remove('valid');
                }
            }

            function checkPasswordMatch() {
                if (confirmPasswordField.value.length === 0) {
                    confirmPasswordField.classList.remove('error', 'valid');
                } else if (passwordField.value !== confirmPasswordField.value) {
                    confirmPasswordField.classList.add('error');
                    confirmPasswordField.classList.remove('valid');
                } else {
                    confirmPasswordField.classList.add('valid');
                    confirmPasswordField.classList.remove('error');
                }
            }

            passwordField.addEventListener('focus', () => {
                passwordChecklist.classList.add('visible');
            });

            passwordField.addEventListener('blur', () => {
                if (passwordField.value.length === 0) {
                    passwordChecklist.classList.remove('visible');
                }
            });
            passwordField.addEventListener('focus', () => {
                passwordChecklist.style.display = 'block';
                passwordChecklist.classList.add('visible');
            });

            passwordField.addEventListener('blur', () => {
                setTimeout(() => { 
                    if (!newPasswordField.matches(':focus') && newPasswordField.value.length === 0) {
                        passwordChecklist.style.display = 'none'; 
                        passwordChecklist.classList.remove('visible');
                    }
                }, 200);
            });

            passwordField.addEventListener('input', () => {
                passwordChecklist.style.display = 'block';
                passwordChecklist.classList.add('visible');
                validatePassword(); 
            });

            passwordField.addEventListener('input', validatePassword);
            confirmPasswordField.addEventListener('input', checkPasswordMatch);
            setupToggle(togglePassword, passwordField, passwordToggleImage);
            setupToggle(toggleConfirmPassword, confirmPasswordField, confirmPasswordToggleImage);
        });