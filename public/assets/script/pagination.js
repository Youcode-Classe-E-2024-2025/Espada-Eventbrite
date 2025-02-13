document.addEventListener('DOMContentLoaded', function() {
    const eventsContainer = document.getElementById('events-container');
    const paginationControls = document.getElementById('pagination-controls');
    const prevButton = document.getElementById('prev-page');
    const nextButton = document.getElementById('next-page');

    async function loadEvents(page) {
        try {
            const response = await fetch(`/events/list?page=${page}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await response.json();
            
            // Update events
            eventsContainer.innerHTML = data.events.map(event => `
                <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    <div class="relative">
                        <img class="w-full h-48 object-cover" src="${event.visual_content}" alt="tech conference">
                        <span class="absolute top-4 right-4 bg-gray-500 text-white px-3 py-1 rounded-full text-sm">
                            <i class="fa-solid ${event.icon} mr-1"></i>${event.category}
                        </span>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center text-sm text-gray-500 mb-2">
                            <i class="fa-regular fa-calendar mr-2"></i>
                            <span>${new Date(event.date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}</span>
                            <i class="fa-solid fa-location-dot mx-2"></i>
                            <span>${event.lieu}</span>
                        </div>
                        <h4 class="text-xl font-bold mb-2">${event.title}</h4>
                        <p class="text-gray-600 mb-4">${event.description.slice(0, 20)}...</p>
                        <div class="flex items-center justify-between">
                            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Book Now</button>
                        </div>
                    </div>
                </div>
            `).join('');

            // Update pagination buttons
            prevButton.disabled = data.currentPage === 1;
            nextButton.disabled = data.currentPage === data.totalPages;
            prevButton.dataset.page = data.currentPage - 1;
            nextButton.dataset.page = data.currentPage + 1;

            // Update showing text
            const showingText = document.querySelector('.text-gray-600');
            const start = ((data.currentPage - 1) * 2) + 1;
            const end = Math.min(data.currentPage * 2, data.totalEvents);
            showingText.textContent = `Showing ${start}-${end} of ${data.totalEvents} events`;

            // Update URL without reloading
            const url = new URL(window.location);
            url.searchParams.set('page', page);
            window.history.pushState({}, '', url);
        } catch (error) {
            console.error('Error loading events:', error);
        }
    }

    // Add click event listeners to pagination buttons
    paginationControls.addEventListener('click', function(e) {
        const button = e.target.closest('button');
        if (button && !button.disabled) {
            const page = parseInt(button.dataset.page);
            loadEvents(page);
        }
    });
});