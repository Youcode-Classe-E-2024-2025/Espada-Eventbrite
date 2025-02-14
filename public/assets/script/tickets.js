

document.getElementById('eventSelectOptions').addEventListener('change', function() {
    // Get the selected event ID
    const eventId = this.value;
    eventTicket
    fetchEventData(eventId);
})

function fetchEventData(eventId) {
    const url = `/Organiser/eventTicket/${eventId}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('eventsDisplay');
            tbody.innerHTML = ''; // Clear existing content

            // Assuming `data` is an array of event booking details
            data.forEach(booking => {
                const row = document.createElement('tr');
                row.classList.add('border-b');
                row.innerHTML = `
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                                <i class="fa-solid fa-music text-indigo-600"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">${booking.event_title}</div>
                                <div class="text-sm text-gray-500">${booking.event_date}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            ${booking.user_avatar ? `<img src="${booking.user_avatar}" class="h-8 w-8 rounded-full"/>` : `<div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center text-gray-500 text-sm">N/A</div>`}
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">${booking.user_name}</div>
                                <div class="text-sm text-gray-500">${booking.user_email}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">${booking.booking_date}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">${booking.booking_type}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Confirmed</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">$${booking.booking_price}</td>
                `;
                tbody.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Error fetching data:', error);
        });
}
