const notifBtn = document.getElementById('notif_btn');
const notifPopup = document.getElementById('notifications-popup');

if (notifBtn && notifPopup) {
    // Toggle notification popup
    notifBtn.addEventListener('click', (event) => {
        event.stopPropagation();
        notifPopup.classList.toggle('hidden');
    });

    // Close notification popup when clicking outside
    document.addEventListener('click', (event) => {
        if (!notifPopup.contains(event.target) && !notifBtn.contains(event.target)) {
            notifPopup.classList.add('hidden');
        }
    });

    // Prevent popup from closing when clicking inside it
    notifPopup.addEventListener('click', (event) => {
        event.stopPropagation();
    });
}