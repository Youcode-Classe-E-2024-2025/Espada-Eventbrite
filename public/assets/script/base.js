const notifBtn = document.getElementById('notif_btn');
const mainContainer = document.getElementById('main-container');
const notifpopup = document.getElementById('notifications-popup');
notifBtn.addEventListener('click',(event) =>{
    event.preventDefault();
    notifpopup.classList.toggle('hidden');
})