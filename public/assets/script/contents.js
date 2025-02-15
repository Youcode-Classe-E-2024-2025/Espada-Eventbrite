document.addEventListener('DOMContentLoaded', () => {
  // Category Modal Elements
  const addCategoryBtn = document.getElementById('add-category-btn');
  const categoryModal = document.getElementById('category-modal');
  const closeCategoryModal = document.getElementById('close-category-modal');
  const cancelCategory = document.getElementById('cancel-category');
  const categoryForm = document.getElementById('category-form');

  // Tag Modal Elements
  const addTagBtn = document.getElementById('add-tag-btn');
  const tagModal = document.getElementById('tag-modal');
  const closeTagModal = document.getElementById('close-tag-modal');
  const cancelTag = document.getElementById('cancel-tag');
  const tagForm = document.getElementById('tag-form');

  // Category Modal Event Listeners
  function toggleCategoryModal() {
    categoryModal.classList.toggle('hidden');
  }

  addCategoryBtn.addEventListener('click', toggleCategoryModal);
  [closeCategoryModal, cancelCategory].forEach(el => {
    el.addEventListener('click', toggleCategoryModal);
  });

  categoryForm.addEventListener('submit', (e) => {
    // e.preventDefault();
    // Just hide the modal
    toggleCategoryModal();
  });

  // Tag Modal Event Listeners
  function toggleTagModal() {
    tagModal.classList.toggle('hidden');
  }

  addTagBtn.addEventListener('click', toggleTagModal);
  [closeTagModal, cancelTag].forEach(el => {
    el.addEventListener('click', toggleTagModal);
  });

  tagForm.addEventListener('submit', (e) => {
    // e.preventDefault();
    // Just hide the modal
    toggleTagModal();
  });

  document.querySelectorAll('.edit-category-btn').forEach(button => {
    button.addEventListener('click', () => {
      const modal = document.getElementById('edit-category-modal');
      const id = button.getAttribute('data-id');
      const name = button.getAttribute('data-name');
      const icon = button.getAttribute('data-icon');

      document.getElementById('edit-category-id').value = id;
      document.getElementById('edit-category-name').value = name;
      document.getElementById('edit-category-icon').value = icon;

      modal.classList.remove('hidden');
    });
  });

  document.getElementById('close-edit-modal').addEventListener('click', () => {
    const modal = document.getElementById('edit-category-modal');
    modal.classList.add('hidden');
  });



  const input = document.querySelector('#tag-input');
  const tagify = new Tagify(input, {
    delimiters: ",",
    dropdown: {
      enabled: 0
    }
  });

  document.getElementById('tag-form').addEventListener('submit', function (e) {
    e.preventDefault();
    const tags = tagify.value.map(tag => tag.value);
    console.log('Sending tags:', tags);

    fetch('/tag/create', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('input[name="csrf_token"]').value
      },
      body: JSON.stringify({
        tags: tags,
        csrf_token: document.querySelector('input[name="csrf_token"]').value
      })
    })
      .then(response => response.json())
      .then(data => {
        console.log('Response:', data);
        if (data.success) {
          tagify.removeAllTags();
          toggleTagModal();
          window.location.reload();
        }
      })
      .catch(error => console.log('Error:', error));
  });

});