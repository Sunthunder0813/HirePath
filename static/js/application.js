
        document.addEventListener('DOMContentLoaded', () => {
            const statusFilter = document.getElementById('status_filter');
            const cardGrid = document.querySelector('.card_grid');

            statusFilter.addEventListener('change', () => {
                const selectedStatus = statusFilter.value;
                const xhr = new XMLHttpRequest();
                xhr.open('GET', `application.php?status=${encodeURIComponent(selectedStatus)}`, true);
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(xhr.responseText, 'text/html');
                        const newCardGrid = doc.querySelector('.card_grid');
                        cardGrid.innerHTML = newCardGrid.innerHTML;
                    }
                };
                xhr.send();
            });

            
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