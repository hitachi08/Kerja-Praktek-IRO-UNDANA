document.addEventListener("DOMContentLoaded", function () {
  const currentYear = new Date().getFullYear(); // Tahun saat ini

  // Variabel state tahun untuk masing-masing chart
  let selectedYearVRF = currentYear;
  let selectedYearVisits = currentYear;
  let selectedYearUsers = currentYear;

  // Dropdown container
  const dropdownVRF = document.getElementById("dropdown-vrfStatusChart");
  const dropdownVisits = document.getElementById("dropdown-visitsTimeChart");
  const dropdownUsers = document.getElementById("dropdown-barChart");

  // Fungsi untuk menambahkan item tahun ke dropdown
  const addYearOptions = (dropdown, updateFunction) => {
    const nextYear = currentYear + 1;

    // Tahun depan
    const nextYearItem = document.createElement("a");
    nextYearItem.classList.add("dropdown-item");
    nextYearItem.href = "#";
    nextYearItem.dataset.year = nextYear;
    nextYearItem.innerText = nextYear;
    dropdown.appendChild(nextYearItem);

    // Event listener untuk tahun depan
    nextYearItem.addEventListener("click", function (e) {
      e.preventDefault();
      updateFunction(nextYear);
    });

    // 5 Tahun terakhir
    for (let i = 0; i < 5; i++) {
      const year = currentYear - i;
      const yearItem = document.createElement("a");
      yearItem.classList.add("dropdown-item");
      yearItem.href = "#";
      yearItem.dataset.year = year;
      yearItem.innerText = year;
      dropdown.appendChild(yearItem);

      // Event listener untuk masing-masing tahun
      yearItem.addEventListener("click", function (e) {
        e.preventDefault();
        updateFunction(year);
      });
    }
  };

  // Chart initialization
  const pieCtx = document.getElementById("vrfStatusChart");
  const lineCtx = document.getElementById("visitsTimeChart").getContext("2d");
  const barCtx = document.getElementById("barChart").getContext("2d");

  const pieChart = new Chart(pieCtx, {
    type: "pie",
    data: {
      labels: ["Approved", "Pending", "Reschedule"],
      datasets: [
        {
          data: [],
          backgroundColor: ["#1cc88a", "#ffc107", "#36b9cc"],
        },
      ],
    },
  });

  const lineChart = new Chart(lineCtx, {
    type: "line",
    data: {
      labels: [
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "May",
        "Jun",
        "Jul",
        "Aug",
        "Sep",
        "Oct",
        "Nov",
        "Dec",
      ],
      datasets: [
        {
          label: "VRF Records",
          data: [],
          borderColor: "#4e73df",
          fill: false,
        },
      ],
    },
  });

  const barChart = new Chart(barCtx, {
    type: "bar",
    data: {
      labels: [
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "May",
        "Jun",
        "Jul",
        "Aug",
        "Sep",
        "Oct",
        "Nov",
        "Dec",
      ],
      datasets: [
        {
          label: "New Users",
          data: [],
          backgroundColor: "#4e73df",
        },
      ],
    },
  });

  // Fungsi Update Chart
  const updateVRFChart = (year) => {
    selectedYearVRF = year;
    fetch(`fetch_chart_data.php?year=${year}`)
      .then((res) => res.json())
      .then((data) => {
        pieChart.data.datasets[0].data = [
          data.pieChart.approved,
          data.pieChart.pending,
          data.pieChart.reschedule,
        ];
        pieChart.update();
        document.getElementById("selected-vrfStatusChart").textContent = year;
      });
  };

  const updateVisitsChart = (year) => {
    selectedYearVisits = year;
    fetch(`fetch_chart_data.php?year=${year}`)
      .then((res) => res.json())
      .then((data) => {
        lineChart.data.datasets[0].data = data.lineChart;
        lineChart.update();
        document.getElementById("selected-visitsTimeChart").textContent = year;
      });
  };

  const updateUsersChart = (year) => {
    selectedYearUsers = year;
    fetch(`fetch_chart_data.php?year=${year}`)
      .then((res) => res.json())
      .then((data) => {
        barChart.data.datasets[0].data = data.barChart;
        barChart.update();
        document.getElementById("selected-barChart").textContent = year;
      });
  };

  // Tambahkan item tahun ke masing-masing dropdown
  addYearOptions(dropdownVRF, updateVRFChart);
  addYearOptions(dropdownVisits, updateVisitsChart);
  addYearOptions(dropdownUsers, updateUsersChart);

  // Inisialisasi chart pertama kali
  updateVRFChart(selectedYearVRF);
  updateVisitsChart(selectedYearVisits);
  updateUsersChart(selectedYearUsers);
});

//Function For Update Data From Dashboard
function updateDashboardData() {
  // Kirim permintaan AJAX ke server
  fetch("fetch_dashboard_data.php")
    .then((response) => response.json()) // Parse response sebagai JSON
    .then((data) => {
      // Update setiap card dengan data terbaru
      document.querySelector("#totalUser").innerText = data.total_user;
      document.querySelector("#submissionsPending").innerText =
        data.submissions_pending;
      document.querySelector("#totalReviews").innerText = data.total_reviews;
      document.querySelector("#submissionsComplete").innerText =
        data.submissions_complete;
    })
    .catch((error) => console.error("Error fetching dashboard data:", error));
}

// Jalankan pertama kali saat halaman dimuat
updateDashboardData();

// Jalankan setiap 5 detik
setInterval(updateDashboardData, 5000);
