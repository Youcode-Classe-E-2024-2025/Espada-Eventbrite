document.addEventListener('DOMContentLoaded', () => {

    const csrfToken = document.querySelector('input[name="csrf_token"]').value;

    const sortBtn = document.getElementById('sortBtn');
    const sortPopup = document.getElementById('sortPopup');

    sortBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        sortPopup.classList.toggle('hidden');
    });

    const sortButtons = document.querySelectorAll('[data-sort]');
    sortButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const sortBy = button.dataset.sort;
            const formData = new FormData();
            formData.append('sort', sortBy);

            const searchParams = new URLSearchParams(formData);
            fetch(`/admin/events/sort?${searchParams.toString()}`)
                .then(response => response.json())
                .then(data => {
                    updateEventsList(data.events);
                });
        });
    });

    document.addEventListener('click', (e) => {
        if (!sortBtn.contains(e.target) && !sortPopup.contains(e.target)) {
            sortPopup.classList.add('hidden');
        }
    });

    function updateEventsList(events) {
        if (!Array.isArray(events)) return;

        const eventsContainer = document.querySelector('#events-grid');
        eventsContainer.innerHTML = '';

        events.forEach(event => {
            const formattedDate = event.date ? new Date(event.date.replace(' ', 'T')).toLocaleDateString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            }) : '';

            const eventCard = `
                <div class="bg-white p-6 rounded-xl shadow">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="bg-blue-100 p-3 rounded-xl">
                                <i class="fa-solid ${event.icon} text-blue-500 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold">${event.title}</h3>
                                <p class="text-gray-500">${event.category}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-20">
                            <div class="flex space-x-8">
                                <div class="text-right">
                                    <p class="font-medium">Date</p>
                                    <p class="text-gray-500">${formattedDate}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-medium">Tickets Sold</p>
                                    <p class="text-gray-500">${event.vip_tickets_sold + event.standard_tickets_sold + event.gratuit_tickets_sold}/${event.vip_tickets_number + event.standard_tickets_number + event.gratuit_tickets_number}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-medium">Revenue</p>
                                    <p class="text-green-500">$${(event.vip_tickets_sold * event.vip_price) + (event.standard_tickets_sold * event.standard_price)}</p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <form action="/admin/events/delete" method="POST" class="inline">
                                    <input type="hidden" name="csrf_token" value="${csrfToken}">
                                    <input type="hidden" name="event_id" value="${event.event_id}">
                                    <button class="p-2 text-red-500 hover:bg-red-50 rounded-lg" title="Delete Event">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                                ${event.validation == 0 ? `
                                    <form action="/admin/events/status" method="POST" class="inline">
                                        <input type="hidden" name="csrf_token" value="${csrfToken}">
                                        <input type="hidden" name="event_id" value="${event.event_id}">
                                        <input type="hidden" name="status" value="0">
                                        <button type="submit" class="p-2 text-gray-500 hover:text-green-500 rounded-lg" title="Approve Event">
                                            <i class="fa-solid fa-check-circle"></i>
                                        </button>
                                    </form>
                                ` : `
                                    <form action="/admin/events/status" method="POST" class="inline">
                                        <input type="hidden" name="csrf_token" value="${csrfToken}">
                                        <input type="hidden" name="event_id" value="${event.event_id}">
                                        <input type="hidden" name="status" value="1">
                                        <button type="submit" class="p-2 text-gray-500 hover:text-red-500 rounded-lg" title="Disapprove Event">
                                            <i class="fa-solid fa-xmark-circle"></i>
                                        </button>
                                    </form>
                                `}
                                ${event.archived == 0 ? `
                                    <form action="/admin/events/status" method="POST" class="inline">
                                        <input type="hidden" name="csrf_token" value="${csrfToken}">
                                        <input type="hidden" name="event_id" value="${event.event_id}">
                                        <input type="hidden" name="status" value="2">
                                        <button type="submit" class="p-2 text-gray-500 hover:text-yellow-500 rounded-lg" title="Archive Event">
                                            <i class="fa-solid fa-box-archive"></i>
                                        </button>
                                    </form>
                                ` : `
                                    <form action="/admin/events/status" method="POST" class="inline">
                                        <input type="hidden" name="csrf_token" value="${csrfToken}">
                                        <input type="hidden" name="event_id" value="${event.event_id}">
                                        <input type="hidden" name="status" value="3">
                                        <button type="submit" class="p-2 text-gray-500 hover:text-green-500 rounded-lg" title="Unarchive Event">
                                            <i class="fa-solid fa-box-open"></i>
                                        </button>
                                    </form>
                                `}
                            </div>
                        </div>
                    </div>
                </div>
            `;
            eventsContainer.insertAdjacentHTML('beforeend', eventCard);
        });
    }
});



