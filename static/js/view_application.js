function timeAgo(dateString) {
    const now = new Date();
    const appliedDate = new Date(dateString);
    const seconds = Math.floor((now.getTime() - appliedDate.getTime()) / 1000); 
    const intervals = [
        { label: 'year', seconds: 31536000 },
        { label: 'month', seconds: 2592000 },
        { label: 'day', seconds: 86400 },
        { label: 'hour', seconds: 3600 },
        { label: 'minute', seconds: 60 },
        { label: 'second', seconds: 1 }
    ];

    for (const interval of intervals) {
        const count = Math.floor(seconds / interval.seconds);
        if (count > 0) {
            return `${count} ${interval.label}${count !== 1 ? 's' : ''} ago`;
        }
    }
    return 'just now';
}

function updateTimeAgo() {
    const dateElements = document.querySelectorAll('.date');
    dateElements.forEach(element => {
        const appliedDate = element.getAttribute('data-applied-date');
        element.textContent = timeAgo(appliedDate);
    });
}

function confirmAction(action, url) {
    if (confirm(`Are you sure you want to ${action} this application?`)) {
        window.location.href = url;
    }
}

function markResumeAsViewed(button, applicationId) {
    button.textContent = "Resume viewed";
    button.style.background = "#6c757d";
    localStorage.setItem(`resumeViewed_${applicationId}`, true);
}

function restoreResumeViewedState() {
    const buttons = document.querySelectorAll('.resume-viewed');
    buttons.forEach(button => {
        const applicationId = button.getAttribute('data-application-id');
        if (localStorage.getItem(`resumeViewed_${applicationId}`)) {
            button.textContent = "Resume viewed";
            button.style.background = "#6c757d";
        }
    });
}
