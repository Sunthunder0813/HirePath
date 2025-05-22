document.addEventListener('DOMContentLoaded', () => {
            const tabCurrent = document.getElementById('tab_current');
            const tabOtp = document.getElementById('tab_otp');
            const formCurrent = document.getElementById('form_current');
            const formOtp = document.getElementById('form_otp');
            const sendOtpButton = document.getElementById('sendOtpButton');
            let countdownInterval;

            tabCurrent.addEventListener('click', () => {
                tabCurrent.classList.add('active');
                tabOtp.classList.remove('active');
                formCurrent.classList.add('active');
                formOtp.classList.remove('active');
            });

            tabOtp.addEventListener('click', () => {
                tabOtp.classList.add('active');
                tabCurrent.classList.remove('active');
                formOtp.classList.add('active');
                formCurrent.classList.remove('active');
            });

            sendOtpButton.addEventListener('click', () => {
                if (!sendOtpButton.disabled) {
                    sendOtpButton.disabled = true;
                    sendOtpButton.textContent = 'Sending...';

                    const formData = new FormData();
                    formData.append('send_otp', true);

                    fetch('', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(() => {
                        const endTime = Date.now() + 120000;
                        localStorage.setItem('otpEndTime', endTime);
                        startCountdown(sendOtpButton, endTime);
                        alert('OTP has been sent to your email.');
                    })
                    .catch(() => {
                        sendOtpButton.disabled = false;
                        sendOtpButton.textContent = 'Send OTP';
                        alert('Failed to send OTP. Please try again.');
                    });
                }
            });

            const savedEndTime = localStorage.getItem('otpEndTime');
            if (savedEndTime) {
                const remainingTime = Math.max(0, savedEndTime - Date.now());
                if (remainingTime > 0) {
                    startCountdown(sendOtpButton, savedEndTime);
                } else {
                    localStorage.removeItem('otpEndTime');
                }
            }

            function startCountdown(button, endTime) {
                countdownInterval = setInterval(() => {
                    const remainingTime = Math.max(0, endTime - Date.now());
                    if (remainingTime <= 0) {
                        clearInterval(countdownInterval);
                        button.disabled = false;
                        button.textContent = 'Send OTP';
                        localStorage.removeItem('otpEndTime');
                    } else {
                        const minutes = Math.floor(remainingTime / 60000);
                        const seconds = Math.floor((remainingTime % 60000) / 1000);
                        button.textContent = `Resend OTP (${minutes}:${seconds < 10 ? '0' : ''}${seconds})`;
                    }
                }, 1000);
            }
            
            const profileDropdown = document.querySelector('.profile_dropdown');
            if (profileDropdown) {
                profileDropdown.addEventListener('click', (e) => {
                    e.preventDefault();
                    profileDropdown.classList.toggle('active');
                });

                document.addEventListener('click', (e) => {
                    if (!profileDropdown.contains(e.target)) {
                        profileDropdown.classList.remove('active');
                    }
                });

                const dropdownMenu = document.querySelector('.profile_dropdown .dropdown_menu');
                if (dropdownMenu) {
                    dropdownMenu.addEventListener('click', (e) => {
                        e.stopPropagation();
                    });
                }
            }
        });