const notifBtn = document.getElementById('notif_btn');
const notifPopup = document.getElementById('notifications-popup');
const locationBtn = document.getElementById('location-btn');
const locationModal = document.getElementById('location-modal');
const closeLocationModal = document.getElementById('close-location-modal');
const cancelLocation = document.getElementById('cancel-location');
const confirmLocation = document.getElementById('confirm-location');
const locationSearch = document.getElementById('location-search');
const locationList = document.getElementById('location-list');

  // Event Listeners
  locationBtn.addEventListener('click', () => {
    locationModal.classList.remove('hidden');
    populateLocations();
  });

  [closeLocationModal, cancelLocation].forEach(el => {
    el.addEventListener('click', () => {
      locationModal.classList.add('hidden');
    });
  });

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