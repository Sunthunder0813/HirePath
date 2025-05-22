document.addEventListener('DOMContentLoaded', () => {
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

    function showTab(tab) {
        document.getElementById('tabOverview').style.display = (tab === 'overview') ? '' : 'none';
        document.getElementById('tabJobs').style.display = (tab === 'jobs') ? '' : 'none';
        document.getElementById('tabOverviewBtn').classList.toggle('active', tab === 'overview');
        document.getElementById('tabJobsBtn').classList.toggle('active', tab === 'jobs');
    }