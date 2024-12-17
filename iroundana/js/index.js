// Function to refresh modal content
function refreshModalContent(notifications) {
  allNotificationsList.innerHTML = "";

  notifications.forEach((notif) => {
    const isRead = notif.notif === "read";
    const listItem = document.createElement("div");
    listItem.className = `list-group-item d-flex justify-content-between align-items-center ${
      isRead ? "read-notification" : ""
    }`;

    listItem.innerHTML = `
      <div class="d-flex align-items-center">
        <div class="icon-circle bg-primary mr-3">
          <i class="fas fa-user text-white"></i>
        </div>
        <div>
          <strong class="text-gray-900">${notif.nama_pemohon}</strong>
          <div class="small text-gray-900">${notif.institusi_pemohon}</div>
          <div class="small text-muted">${new Date(
            notif.tanggal_submit
          ).toLocaleDateString()}</div>
        </div>
      </div>
      <a href="vrf_approval.php?id=${
        notif.id_vrf
      }" class="btn btn-primary btn-sm" data-id="${notif.id_vrf}">View</a>
    `;

    // Add event listener for the View button to mark notification as read
    const viewButton = listItem.querySelector("a");
    viewButton.addEventListener("click", function (event) {
      event.preventDefault(); // Prevent default anchor behavior
      const notifId = event.target.getAttribute("data-id");
      markAsRead(notifId); // Mark the notification as read

      // Redirect ke VRF_APPROVAL.PHP dengan parameter ID
      window.location.href = `vrf_approval.php?highlight_id=${notifId}`;
    });

    allNotificationsList.appendChild(listItem);
  });
}

// Function to update the notification counter dynamically
function updateNotificationCounter(notifications) {
  const unreadNotifications = notifications.filter(
    (notif) => notif.notif === "unread"
  );
  const alertsCounter = document.getElementById("alertsCounter");
  alertsCounter.textContent = unreadNotifications.length;
  return unreadNotifications.length;
}

// Function to handle clicking of notifications
document.addEventListener("DOMContentLoaded", function () {
  const notificationItems = document.querySelectorAll(".notification-item");
  notificationItems.forEach((item) => {
    item.addEventListener("click", function () {
      // Mark the notification as read by adding a class or style change
      item.classList.add("read-notification");

      // Get the notification ID
      const notifId = item.dataset.id;

      // Send AJAX request to mark the notification as read
      fetch("mark_as_read.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ id: notifId }), // Send the ID of the clicked notification
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.status === "success") {
            refreshNotifications(); // Refresh notifications after marking as read
          }
        })
        .catch((error) => console.error("Error:", error));
    });
  });
});

function markAsRead(id_vrf) {
  const endpoint = "mark_as_read.php";
  fetch(endpoint, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ id: id_vrf }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        console.log(`Notification ${id_vrf} marked as read.`);
        refreshNotifications(); // Refresh notifications in the dropdown
      }
    })
    .catch((error) => {
      console.error("Error marking notification as read:", error);
    });
}

document.addEventListener("DOMContentLoaded", function () {
  const allNotificationsList = document.getElementById("allNotificationsList");
  const alertsCounter = document.getElementById("alertsCounter");

  setInterval(refreshNotifications, 5000);

  // Function to update the notification counter dynamically
  function updateNotificationCounter(notifications) {
    const unreadNotifications = notifications.filter(
      (notif) => notif.notif === "unread"
    );
    alertsCounter.textContent = unreadNotifications.length;
    return unreadNotifications.length;
  }

  // Function to refresh and display notifications in the dropdown and modal
  function refreshNotifications() {
    const endpoint = "get_notifications.php?" + new Date().getTime(); // Avoid cached response
    fetch(endpoint)
      .then((response) => response.json())
      .then((notifications) => {
        // Refresh dropdown
        const dropdownList = document.querySelector(".dropdown-list");
        dropdownList.innerHTML = `<h6 class="dropdown-header">Notification Center</h6>`;

        // Sort notifications: unread first, then read
        notifications.sort((a, b) => {
          if (a.notif === "unread" && b.notif === "read") return -1;
          if (a.notif === "read" && b.notif === "unread") return 1;
          return 0;
        });

        // Display top 3 notifications in the dropdown
        const notificationsToShow = notifications.slice(0, 3);
        notificationsToShow.forEach((notif) => {
          const readClass = notif.notif === "read" ? "read-notification" : "";
          const notificationHTML = `
          <a class="dropdown-item d-flex align-items-center ${readClass}" 
             href="vrf_approval.php?highlight_id=${notif.id_vrf}"
             onclick="markAsRead(${notif.id_vrf})">
            <div class="mr-3">
              <div class="icon-circle bg-primary">
                <i class="fas fa-user text-white"></i>
              </div>
            </div>
            <div>
              <div class="small text-gray-900">${new Date(
                notif.tanggal_submit
              ).toLocaleDateString()}</div>
              <span class="font-weight-bold text-gray-900">${
                notif.nama_pemohon
              }</span>
              <div class="text-gray-900">${notif.institusi_pemohon}</div>
            </div>
          </a>
        `;
          dropdownList.insertAdjacentHTML("beforeend", notificationHTML);
        });

        // Add footer buttons to the dropdown
        dropdownList.insertAdjacentHTML(
          "beforeend",
          `
          <a class="dropdown-item text-center small text-gray-500 show-all-link" 
             href="#" data-toggle="modal" data-target="#allNotificationsModal">Show All Notifications</a>
          <a class="dropdown-item text-center small text-gray-500" 
             href="#" id="markAllAsRead">Mark All as Read</a>
        `
        );

        // Update notification counter
        updateNotificationCounter(notifications);

        // Refresh modal content
        refreshModalContent(notifications);
      })
      .catch((error) =>
        console.error("Error refreshing notifications:", error)
      );
  }

  // Handle "Mark All as Read" click
  document.body.addEventListener("click", function (event) {
    if (
      event.target.id === "markAllAsRead" ||
      event.target.id === "markAllAsReadModal"
    ) {
      fetch("mark_all_as_read.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ action: "mark_all_as_read" }), // Action for marking all notifications
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.status === "success") {
            refreshNotifications(); // Refresh both dropdown and modal
          }
        })
        .catch((error) => console.error("Error marking all as read:", error));
    }
  });

  // Initial load
  refreshNotifications();
});

document
  .getElementById("updateUsernameForm")
  .addEventListener("submit", function (e) {
    e.preventDefault(); // Mencegah pengiriman form secara langsung

    const form = e.target;
    const formData = new FormData(form);

    fetch("update_username.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Update elemen dropdown
          const dropdownItem = document.querySelector(
            ".dropdown-item .text-gray-800"
          );
          if (dropdownItem) {
            dropdownItem.innerText = data.username.toUpperCase();
          }

          const adminUsernameDisplay = document.querySelector(
            ".text-left .text-gray-600"
          );

          if (adminUsernameDisplay) {
            adminUsernameDisplay.innerText = data.username.toUpperCase();
          }

          // Update elemen username di modal
          const currentUsernameInput =
            document.getElementById("currentUsername");
          if (currentUsernameInput) {
            currentUsernameInput.value = data.username;
          }

          // Kosongkan input "New Username" setelah sukses
          const newUsernameInput = document.getElementById("newUsername");
          if (newUsernameInput) {
            newUsernameInput.value = "";
          }

          // Menyembunyikan modal dan memberi notifikasi
          alert("Username updated successfully!");
          $("#settingsModal").modal("hide");
        } else {
          console.error("Error updating username:", data.error);
          alert("Failed to update username: " + data.error);
        }
      })
      .catch((error) => {
        console.error("Fetch Error:", error);
        alert("An error occurred. Please try again.");
      });
  });
