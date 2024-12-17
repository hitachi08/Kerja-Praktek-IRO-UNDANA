$(document).ready(function () {
  $('[data-toggle="tooltip"]').tooltip({
    delay: { show: 100, hide: 100 },
  });
});

function filterTable() {
  const startDate = document.getElementById("start_date").value;
  const endDate = document.getElementById("end_date").value;
  const status = document.getElementById("status").value.toLowerCase();
  const institution = document
    .getElementById("institution")
    .value.toLowerCase();

  const table = $("#vrfRecordsTable").DataTable();

  table.rows().every(function () {
    const rowData = this.data();
    let showRow = true;

    const visitDate = new Date(rowData[1]);
    if (startDate && visitDate < new Date(startDate)) {
      showRow = false;
    }

    if (endDate && visitDate > new Date(endDate)) {
      showRow = false;
    }

    const rowStatus = rowData[18].toLowerCase().trim();
    if (status && !rowStatus.includes(status)) {
      showRow = false;
    }

    const institutionName = rowData[6].toLowerCase().trim();
    if (institution && !institutionName.includes(institution)) {
      showRow = false;
    }

    if (showRow) {
      $(this.node()).show();
    } else {
      $(this.node()).hide();
    }
  });

  table.draw();
}
