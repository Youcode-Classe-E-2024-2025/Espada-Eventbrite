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
});