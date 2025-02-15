document.addEventListener('DOMContentLoaded', () => {
    const filterBtn = document.getElementById('filterBtn');
    const sortBtn = document.getElementById('sortBtn');
    const filterPopup = document.getElementById('filterPopup');
    const sortPopup = document.getElementById('sortPopup');

    filterBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        filterPopup.classList.toggle('hidden');
        sortPopup.classList.add('hidden');
    });

    sortBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        sortPopup.classList.toggle('hidden');
        filterPopup.classList.add('hidden');
    });

    document.addEventListener('click', (e) => {
        if (!filterBtn.contains(e.target) && !filterPopup.contains(e.target)) {
            filterPopup.classList.add('hidden');
        }
        if (!sortBtn.contains(e.target) && !sortPopup.contains(e.target)) {
            sortPopup.classList.add('hidden');
        }
    });
});
