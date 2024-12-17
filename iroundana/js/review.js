document.addEventListener("DOMContentLoaded", function () {
  const body = document.body;

  body.addEventListener("click", function (event) {
    const card = event.target.closest(".card");
    if (card && card.hasAttribute("data-id")) {
      const reviewId = card.getAttribute("data-id");

      fetch(`get_review_details.php?id_review=${reviewId}`) // Pastikan parameter sesuai dengan URL
        .then((response) => response.json())
        .then((data) => {
          if (data.error) {
            alert(data.error);
            return;
          }

          const formattedDate = new Date(data.tanggal_review)
            .toISOString()
            .split("T")[0];

          // Isi modal dengan data review
          document.getElementById("modalUserName").textContent = data.user_name;
          document.getElementById("modalUserEmail").textContent =
            data.user_email;

          // Menampilkan rating emotikon
          let emoticonRating = "";
          switch (data.rating) {
            case "😡":
              emoticonRating = "😡 (Angry)";
              break;
            case "😠":
              emoticonRating = "😠 (Slightly Angry)";
              break;
            case "😐":
              emoticonRating = "😐 (Neutral)";
              break;
            case "🙂":
              emoticonRating = "🙂 (Happy)";
              break;
            case "😄":
              emoticonRating = "😄 (Very Happy)";
              break;
            default:
              emoticonRating = "Unknown Rating";
          }
          document.getElementById("modalRating").textContent = emoticonRating;

          // Menambahkan kategori review
          document.getElementById("modalCategory").textContent = data.category;

          document.getElementById("modalDate").textContent = formattedDate;
          document.getElementById("modalReviewText").textContent = data.review;

          // Menampilkan modal
          const reviewModal = new bootstrap.Modal(
            document.getElementById("reviewModal")
          );
          reviewModal.show();
        })
        .catch((error) =>
          console.error("Error fetching review details:", error)
        );
    }
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const filterForm = document.getElementById("filterForm");
  const filters = filterForm.querySelectorAll("select, input");
  const clearButton = document.getElementById("clearFilters");

  const fetchFilteredReviews = () => {
    const formData = new FormData(filterForm);
    const queryString = new URLSearchParams(formData).toString();

    fetch("filter_reviews.php?" + queryString)
      .then((response) => response.text())
      .then((data) => {
        document.querySelector(".row .d-flex.overflow-auto").innerHTML = data;
      })
      .catch((error) => console.error("Error:", error));
  };

  filters.forEach((filter) => {
    if (filter.type === "text") {
      filter.addEventListener("input", fetchFilteredReviews);
    } else {
      filter.addEventListener("change", fetchFilteredReviews);
    }
  });

  if (clearButton) {
    clearButton.addEventListener("click", () => {
      filters.forEach((filter) => {
        if (filter.type === "select-one" || filter.type === "text") {
          filter.value = "";
        } else if (filter.type === "date") {
          filter.value = null;
        }
      });
      fetchFilteredReviews();
    });
  }
});
