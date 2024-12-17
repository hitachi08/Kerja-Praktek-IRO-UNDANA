// Get the select element and textarea
const categorySelect = document.getElementById('category');
const commentsTextarea = document.getElementById('comments');

// Define the placeholders based on categories
const placeholders = {
    "visit": "Share your thoughts about the visit",
    "website": "Tell us what you think about the website"
};

// Event listener for when the category is changed
categorySelect.addEventListener('change', function () {
    const selectedCategory = categorySelect.value;

    // Update the placeholder based on the selected category
    if (selectedCategory && placeholders[selectedCategory]) {
        commentsTextarea.placeholder = placeholders[selectedCategory];
    } else {
        commentsTextarea.placeholder = "Write your comments here...";  // Default placeholder
    }
});

document.querySelectorAll('.rating-emote').forEach(function (emote) {
    emote.addEventListener('click', function () {
        document.querySelectorAll('.rating-emote').forEach(function (item) {
            item.classList.remove('selected');
        });

        emote.classList.add('selected');
        document.getElementById('rating').value = emote.getAttribute('data-value');
    });
});

// Handle Star Rating
const stars = document.querySelectorAll('.star-rating .rating-emote');
const ratingInput = document.getElementById('rating');

const ratingColorMap = {
    "😡 Angry": "red",
    "😠 Slightly Angry": "orange",
    "😐 Neutral": "gray",
    "🙂 Happy": "yellow",
    "😄 Very Happy": "green"
};
stars.forEach(star => {
    star.addEventListener('click', function () {

        const rating = this.getAttribute('data-value');
        ratingInput.value = rating;
        updatePreviewRating(rating);
        stars.forEach(s => s.classList.remove('checked'));
        for (let i = 0; i < rating; i++) {
            stars[i].classList.add('checked');
        }
    });
});

function updatePreviewRating(rating) {
    previewRating.textContent = rating || '0';
    const color = ratingColorMap[rating] || 'black';
    previewRating.style.color = color;
}

// Handle Preview Modal
const previewBtn = document.getElementById('previewBtn');
const previewName = document.getElementById('previewName');
const previewEmail = document.getElementById('previewEmail');
const previewCategory = document.getElementById('previewCategory');
const previewRating = document.getElementById('previewRating');
const previewComments = document.getElementById('previewComments');
const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));

previewBtn.addEventListener('click', () => {
    previewName.textContent = document.getElementById('name').value || 'N/A';
    previewEmail.textContent = document.getElementById('email').value || 'N/A';
    previewCategory.textContent = document.getElementById('category').value || 'N/A';
    previewRating.textContent = ratingInput.value || '0';
    previewComments.textContent = document.getElementById('comments').value || 'N/A';
    previewModal.show();
});