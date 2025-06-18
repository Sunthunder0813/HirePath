function timeSince(date) {
    if (!date) return "Unknown date";

    let safeDate = date.replace(' ', 'T');
    const parsedDate = new Date(safeDate);

    if (isNaN(parsedDate.getTime())) {
        safeDate = safeDate.replace(/-/g, '/');
        const fallbackDate = new Date(safeDate);
        if (isNaN(fallbackDate.getTime())) {
            console.error("Invalid date format:", date);
            return "Invalid date";
        }
        return timeSince(fallbackDate.toISOString());
    }

    const now = new Date();
    const seconds = Math.floor((now - parsedDate) / 1000);

    if (seconds < 0) {
        return "just now";
    }

    let interval = Math.floor(seconds / 31536000);
    if (interval >= 1) return interval + " year" + (interval === 1 ? "" : "s") + " ago";

    interval = Math.floor(seconds / 2592000);
    if (interval >= 1) return interval + " month" + (interval === 1 ? "" : "s") + " ago";

    interval = Math.floor(seconds / 86400);
    if (interval >= 1) return interval + " day" + (interval === 1 ? "" : "s") + " ago";

    interval = Math.floor(seconds / 3600);
    if (interval >= 1) return interval + " hour" + (interval === 1 ? "" : "s") + " ago";

    interval = Math.floor(seconds / 60);
    if (interval >= 1) return interval + " minute" + (interval === 1 ? "" : "s") + " ago";

    return seconds + " second" + (seconds === 1 ? "" : "s") + " ago";
}

window.onload = function() {
    document.querySelector('.loading').style.display = 'block';

    document.querySelectorAll('.job').forEach(function(jobElement) {
        const createdAt = jobElement.getAttribute('data_created_at'); 
        const jobPostedElement = jobElement.querySelector('.job_posted'); 
        jobPostedElement.innerText = timeSince(createdAt); 
    });

    document.querySelector('.loading').style.display = 'none';
};

function showDetails(jobId, employerId, element) {
    document.querySelectorAll('.job').forEach(job => {
        job.classList.remove('active');
    });

    element.classList.add('active');

    document.querySelector('.loading').style.display = 'block';

    setTimeout(() => {
        const title = element.getAttribute('data_title');
        const category = element.getAttribute('data_category');
        const salary = element.getAttribute('data_salary');
        const location = element.getAttribute('data_location');
        const createdAt = element.getAttribute('data_created_at');
        const company_name = element.getAttribute('data_company_name');
        const skills = element.getAttribute('data_skills');
        const education = element.getAttribute('data_education');
        const description = element.getAttribute('data_description');

        if (document.getElementById('job_title')) document.getElementById('job_title').innerText = title;
        if (document.getElementById('job_category')) document.getElementById('job_category').innerText = category;
        if (document.getElementById('job_salary')) document.getElementById('job_salary').innerText = salary;
        if (document.getElementById('job_location')) document.getElementById('job_location').innerText = location;
        if (document.getElementById('job_description')) document.getElementById('job_description').innerHTML = description;
        if (document.getElementById('job_company')) document.getElementById('job_company').innerText = company_name;
        if (document.getElementById('job_skills')) document.getElementById('job_skills').innerText = skills;
        if (document.getElementById('job_education')) document.getElementById('job_education').innerText = education;

        if (document.getElementById('job_title_container')) {
            document.getElementById('job_title_container').setAttribute('data_job_id_now', jobId);
        }

        const companyLink = document.getElementById('job_company');
        if (companyLink) {
            companyLink.setAttribute('onclick', `checkLoginAndViewCompany(${employerId})`);
        }

        if (document.getElementById('job_posted')) {
            document.getElementById('job_posted').innerText = timeSince(createdAt);
        }
        if (document.querySelector('.job_details')) {
            document.querySelector('.job_details').style.display = 'block';
        }

        document.querySelector('.loading').style.display = 'none';
    }, 100); 
}

function toggleMenu() {
    const navLinks = document.querySelector('.nav_links');
    navLinks.classList.toggle('show');
    if (navLinks.classList.contains('show')) {
        navLinks.style.zIndex = '1100'; 
    } else {
        navLinks.style.zIndex = '';
    }
}

const regions = {
    "NCR": [
        "Alabang", "Las Piñas", "Makati", "Malabon", "Manila", 
        "Mandaluyong", "Marikina", "Muntinlupa", "Navotas", 
        "Parañaque", "Pasig", "Pasay", "Quezon City", "San Juan", 
        "Taguig", "Valenzuela"
    ],
    "CAR": [
        "Apayao", "Baguio City", "Bontoc", "Bauang", "Itogon", 
        "Kalinga", "La Trinidad", "Mountain Province", "Tabuk", 
        "Tuba"
    ],
    "Region I": [
        "Alaminos City", "Baguio City", "Dagupan City", "Laoag City", 
        "Lingayen", "San Fernando City", "Urdaneta City", "Vigan City", 
        "Pangasinan", "Ilocos Norte", "Ilocos Sur", "La Union"
    ],
    "Region II": [
        "Aparri", "Cauayan City", "Ilagan City", "Isabela", 
        "Quirino", "Santiago City", "Tuguegarao City", "Nueva Vizcaya"
    ],
    "Region III": [
        "Angeles City", "Balanga City", "Bulacan", "Capas", 
        "Cavite", "Mabalacat", "Nueva Ecija", "Olongapo City", 
        "Pampanga", "San Fernando City", "Tarlac City"
    ],
    "Region IV-A": [
        "Antipolo City", "Batangas City", "Biñan", "Calamba City", 
        "Cavite City", "Dasmariñas", "Imus", "Laguna", 
        "Lucena City", "San Pablo City", "Santa Rosa", "Talisay"
    ],
    "Region IV-B": [
        "Boac", "Calapan City", "Mamburao", "Odiongan", 
        "Puerto Princesa City", "Roxas", "Romblon", "San Jose"
    ],
    "Region V": [
        "Albay", "Camarines Norte", "Camarines Sur", "Iriga City", 
        "Legazpi City", "Ligao City", "Masbate City", "Naga City", 
        "Sorsogon City", "Tabaco City"
    ],
    "Region VI": [
        "Bacolod City", "Binalbagan", "Iloilo City", "Kabankalan City", 
        "La Carlota", "Passi City", "Roxas City", "San Carlos City", 
        "Talisay City", "Negros Occidental", "Aklan", "Antique", 
        "Capiz", "Guimaras"
    ],
    "Region VII": [
        "Bohol", "Cebu City", "Dumaguete City", "Lapu-Lapu City", 
        "Mandaue City", "Toledo City", "Talisay City", "Carcar City", 
        "Siquijor"
    ],
    "Region VIII": [
        "Borongan City", "Butuan City", "Calbayog City", "Ormoc City", 
        "Samar", "Tacloban City", "Southern Leyte", "Leyte", 
        "Northern Samar"
    ],
    "Region IX": [
        "Dipolog City", "Dapitan City", "Pagadian City", "Zamboanga City", 
        "Zamboanga del Norte", "Zamboanga del Sur", "Zamboanga Sibugay"
    ],
    "Region X": [
        "Bukidnon", "Cagayan de Oro City", "El Salvador City", 
        "Gingoog City", "Iligan City", "Malaybalay City", 
        "Misamis Oriental", "Ozamiz City", "Camiguin"
    ],
    "Region XI": [
        "Compostela Valley", "Davao City", "Digos City", "Panabo City", 
        "Samal City", "Tagum City", "Davao del Norte", 
        "Davao del Sur", "Davao Occidental"
    ],
    "Region XII": [
        "General Santos City", "Kidapawan City", "Koronadal City", 
        "Sultan Kudarat", "Tacurong City", "South Cotabato", 
        "Cotabato City", "North Cotabato"
    ],
    "Region XIII": [
        "Agusan del Norte", "Agusan del Sur", "Butuan City", 
        "Bislig City", "Cabadbaran City", "Surigao City", 
        "Tandag City", "Samar", "Leyte"
    ],
    "BARMM": [
        "Maguindanao", "Sulu", "Tawi-Tawi"
    ]
};


function populateCities() {
    const regionSelect = document.getElementById('region_select');
    const citySelect = document.getElementById('city_select');
    const selectedRegion = regionSelect.value;
    
    citySelect.innerHTML = ''; 

    if (selectedRegion && regions[selectedRegion]) {
        regions[selectedRegion].forEach(city => {
            const option = document.createElement('option');
            option.value = city;
            option.textContent = city;
            citySelect.appendChild(option);
        });
        citySelect.style.display = 'block';
    } else {
        citySelect.style.display = 'none';
    }
}

function openFilterModal() {
    document.getElementById('filter_modal').style.display = 'block';
}

function closeFilterModal() {
    document.getElementById('filter_modal').style.display = 'none';
}

function removeFilters() {
    window.location.href = 'index.php';
}

document.addEventListener('DOMContentLoaded', function() {
    const filterModal = document.getElementById('filter_modal');
    if (filterModal) {
        filterModal.addEventListener('mousedown', function(e) {
            if (e.target === filterModal) {
                closeFilterModal();
            }
        });
        document.addEventListener('keydown', function(e) {
            if (filterModal.style.display === 'block' && e.key === 'Escape') {
                closeFilterModal();
            }
        });
    }
});

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

    const locationFilter = "<?php echo $locationFilter; ?>";
    const citySelect = document.getElementById('city_select');
    const regionSelect = document.getElementById('region_select');

    if (locationFilter) {
        
        if (citySelect && regionSelect) {
            const cityOption = Array.from(citySelect.options).find(option => option.value === locationFilter);
            if (cityOption) {
                citySelect.value = locationFilter;
                citySelect.style.display = 'block';
            } else {
                regionSelect.value = locationFilter;
            }
        }
    }
});

function applyFilters() {
    const search = document.getElementById('search_input').value;
    const category = document.getElementById('filter_category').value;
    const salary = document.getElementById('filter_salary').value;
    const location = document.getElementById('city_select').value || document.getElementById('region_select').value;

    let queryParams = [];
    if (search) queryParams.push(`search=${encodeURIComponent(search)}`);
    if (category) queryParams.push(`category=${encodeURIComponent(category)}`);
    if (salary) queryParams.push(`salary=${encodeURIComponent(salary)}`);
    if (location) queryParams.push(`location=${encodeURIComponent(location)}`);

    const queryString = queryParams.length > 0 ? `?${queryParams.join('&')}` : '';
    window.location.href = `index.php${queryString}`;
}

document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('search_input');
    if (searchInput) {
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                applyFilters();
            }
        });
    }
});
