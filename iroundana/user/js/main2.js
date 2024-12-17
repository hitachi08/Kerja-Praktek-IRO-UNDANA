$(document).ready(function () {
  $("#form-vrf").on("submit", function (e) {
    e.preventDefault(); // Mencegah form submit default

    // Ambil data form
    var formData = $(this).serialize(); // serialize() untuk mengumpulkan data form

    $.ajax({
      url: "send_user_new.php", // URL untuk mengirim data
      type: "POST", // Menggunakan metode POST
      data: formData, // Kirimkan data form
      success: function (response) {
        // Jika sukses, tampilkan pesan sukses
        alert("Data berhasil dikirim: " + response.message);
        $("#form-vrf")[0].reset(); // Reset form setelah sukses
      },
      error: function (xhr, status, error) {
        // Jika ada error, tampilkan pesan error
        alert("Terjadi kesalahan saat mengirim data: " + error);
      },
    });
  });
});

$(function () {
  $("#vrf-form").steps({
    headerTag: "h2",
    bodyTag: "section",
    transitionEffect: "fade",
    enableAllSteps: true,
    autoFocus: true,
    transitionEffectSpeed: 500,
    titleTemplate: '<span class="title">#title#</span>',
    labels: {
      previous: "Previous",
      next: "Next",
      finish: "Finish",
      current: "",
    },
    // Inisialisasi intlTelInput hanya sekali saat form dimuat
    onInit: function () {
      const input = document.querySelector("#phone");
      window.iti = intlTelInput(input, {
        initialCountry: "id", // Sesuaikan dengan negara default
      });
    },
    // Jangan inisialisasi ulang pada perubahan langkah
    onStepChanged: function (event, currentIndex, priorIndex) {
      if (currentIndex === 0 && !window.iti) {
        const input = document.querySelector("#phone");
        window.iti = intlTelInput(input, {
          initialCountry: "id",
        });
      }
    },
  });
});

$(function () {
  var siteSticky = function () {
    $(".js-sticky-header").sticky({ topSpacing: 0 });
  };
  siteSticky();

  var siteMenuClone = function () {
    $(".js-clone-nav").each(function () {
      var $this = $(this);
      $this
        .clone()
        .attr("class", "site-nav-wrap")
        .appendTo(".site-mobile-menu-body");
    });

    setTimeout(function () {
      var counter = 0;
      $(".site-mobile-menu .has-children").each(function () {
        var $this = $(this);

        $this.prepend('<span class="arrow-collapse collapsed">');

        $this.find(".arrow-collapse").attr({
          "data-toggle": "collapse",
          "data-target": "#collapseItem" + counter,
        });

        $this.find("> ul").attr({
          class: "collapse",
          id: "collapseItem" + counter,
        });

        counter++;
      });
    }, 1000);

    $("body").on("click", ".arrow-collapse", function (e) {
      var $this = $(this);
      if ($this.closest("li").find(".collapse").hasClass("show")) {
        $this.removeClass("active");
      } else {
        $this.addClass("active");
      }
      e.preventDefault();
    });

    $(window).resize(function () {
      var $this = $(this),
        w = $this.width();

      if (w > 768) {
        if ($("body").hasClass("offcanvas-menu")) {
          $("body").removeClass("offcanvas-menu");
        }
      }
    });

    $("body").on("click", ".js-menu-toggle", function (e) {
      var $this = $(this);
      e.preventDefault();

      if ($("body").hasClass("offcanvas-menu")) {
        $("body").removeClass("offcanvas-menu");
        $this.removeClass("active");
      } else {
        $("body").addClass("offcanvas-menu");
        $this.addClass("active");
      }
    });

    // click outisde offcanvas
    $(document).mouseup(function (e) {
      var container = $(".site-mobile-menu");
      if (!container.is(e.target) && container.has(e.target).length === 0) {
        if ($("body").hasClass("offcanvas-menu")) {
          $("body").removeClass("offcanvas-menu");
        }
      }
    });
  };
  siteMenuClone();
});

document.getElementById("btn-submit").addEventListener("click", function (e) {
  e.preventDefault(); // Mencegah form dikirim secara default

  let form = document.getElementById("form-vrf");
  let inputs = form.querySelectorAll("input[required], textarea[required]");
  let isValid = true;

  // Reset styling dan notifikasi
  inputs.forEach((input) => {
    input.style.borderColor = "";
  });

  // Periksa setiap input
  inputs.forEach((input) => {
    if (input.value.trim() === "") {
      isValid = false;
      input.style.borderColor = "red"; // Highlight input yang kosong

      // Hilangkan border merah setelah 5 detik
      setTimeout(() => {
        input.style.borderColor = "";
      }, 5000);
    }
  });

  if (isValid) {
    // Ambil data form
    let formData = new FormData(form);

    // Kirim form menggunakan Fetch API
    fetch("send_user_new.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          var successModal = new bootstrap.Modal(
            document.getElementById("successModal")
          );
          successModal.show();
        } else if (data.status === "error") {
          // Jika terjadi error (termasuk jadwal bentrok), tampilkan notifikasi
          showNotification(data.message);
        }
      })
      .catch((error) => {
        // Tampilkan pesan error jika fetch gagal
        showNotification("Please fill in the input field correctly.");
        console.error("Error:", error);
      });
  } else {
    // Jika form tidak valid, tampilkan notifikasi kesalahan
    showNotification("Please fill in all required fields.");
  }
});

// Fungsi untuk menampilkan notifikasi kesalahan
function showNotification(message) {
  let notification = document.getElementById("form-notification");
  if (!notification) {
    // Jika elemen notifikasi belum ada, buat elemen baru
    notification = document.createElement("div");
    notification.id = "form-notification";
    notification.style.position = "fixed";
    notification.style.top = "10px";
    notification.style.right = "10px";
    notification.style.backgroundColor = "#f8d7da";
    notification.style.color = "#721c24";
    notification.style.padding = "10px 20px";
    notification.style.border = "1px solid #f5c6cb";
    notification.style.borderRadius = "5px";
    notification.style.zIndex = "1000";
    document.body.appendChild(notification);
  }

  notification.textContent = message;
  notification.style.display = "block";

  // Sembunyikan notifikasi setelah 5 detik
  setTimeout(() => {
    notification.style.display = "none";
  }, 5000);
}

$(document).ready(function () {
  $("#form-vrf").submit(function (event) {
    event.preventDefault(); // Prevent default form submission
    console.log("Form submission intercepted!");

    var formData = $(this).serialize(); // Serialize form data
    $("#btn-submit").prop("disabled", true); // Disable submit button temporarily

    $.ajax({
      url: "send_user_new.php", // Update to your PHP handler
      type: "POST",
      data: formData,
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          $("#responseMessage").html(
            `<div class="alert alert-success">${response.message}</div>`
          );
          $("#form-vrf")[0].reset(); // Reset form if successful
        } else {
          $("#responseMessage").html(
            `<div class="alert alert-danger">${response.message}</div>`
          );
        }
      },
      error: function (xhr, status, error) {
        $("#responseMessage").html(
          `<div class="alert alert-danger">An error occurred: ${error}</div>`
        );
      },
      complete: function () {
        $("#btn-submit").prop("disabled", false); // Re-enable submit button
      },
    });
  });
});
