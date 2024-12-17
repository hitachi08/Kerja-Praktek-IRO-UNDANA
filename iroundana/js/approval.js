// Tampilkan modal ketika status 'Reschedule' dipilih
document.querySelectorAll(".status-select").forEach((select) => {
  select.addEventListener("change", function () {
    const id_vrf = this.dataset.id;
    if (this.value === "Reschedule") {
      document.getElementById("rescheduleVrfId").value = id_vrf;
      $("#rescheduleModal").modal("show");
    }
  });
});

function updateStatus(idVrf) {
  const statusSelect = document.getElementById(`status_${idVrf}`);
  const selectedStatus = statusSelect.value;
  const updateButton = document.getElementById(`btn_${idVrf}`);

  // Kirim data ke server melalui AJAX
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "update_vrf_status.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        const response = JSON.parse(xhr.responseText);

        if (response.success) {
          showToast("successToast");

          // Jika status Approved atau Pending, nonaktifkan dropdown dan tombol
          if (
            selectedStatus === "Approved" ||
            selectedStatus === "Reschedule"
          ) {
            statusSelect.disabled = true;
            updateButton.disabled = true;
          }
        } else {
          showToast("errorToast");
        }
      }
    }
  };

  // Kirim data id dan status
  xhr.send(`id_vrf=${idVrf}&status=${selectedStatus}`);
}

function showToast(toastId) {
  const toastElement = document.getElementById(toastId);
  const toast = new bootstrap.Toast(toastElement);
  toast.show();
}

function filterTable() {
  const startDate = document.getElementById("start_date").value;
  const endDate = document.getElementById("end_date").value;
  const status = document.getElementById("status").value.toLowerCase();
  const institution = document
    .getElementById("institution")
    .value.toLowerCase();

  const table = document.getElementById("vrfApprovalTable");
  const rows = table.getElementsByTagName("tr");

  for (let i = 1; i < rows.length; i++) {
    const row = rows[i];
    const tdStatus = row.getAttribute("data-status")
      ? row.getAttribute("data-status").toLowerCase()
      : "";
    const tdInstitution = row.getAttribute("data-institusi")
      ? row.getAttribute("data-institusi").toLowerCase()
      : "";
    const tdDate = row.getAttribute("data-tanggal")
      ? row.getAttribute("data-tanggal")
      : "";

    const isDateMatch =
      (!startDate || tdDate >= startDate) && (!endDate || tdDate <= endDate);
    const isStatusMatch = !status || tdStatus.includes(status);
    const isInstitutionMatch =
      !institution || tdInstitution.includes(institution);

    if (isDateMatch && isStatusMatch && isInstitutionMatch) {
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  }
}

function viewDetails(button) {
  const vrfId = button.getAttribute("data-id");

  fetch(`get_vrf_details.php?id=${vrfId}`)
    .then((response) => response.json())
    .then((data) => {
      if (data.error) {
        alert(data.error);
        return;
      }

      document.getElementById("modalVrfId").textContent = data.id_vrf;
      document.getElementById("modalVisitDate").textContent =
        data.tgl_kunjungan;
      document.getElementById("modalVisitTime").textContent =
        data.waktu_kunjungan;
      document.getElementById("modalDuration").textContent =
        data.durasi_kunjungan;
      document.getElementById("modalApplicantName").textContent =
        data.nama_pemohon;
      document.getElementById("modalApplicantPosition").textContent =
        data.posisi_pemohon;
      document.getElementById("modalInstitution").textContent =
        data.institusi_pemohon;
      document.getElementById("modalWebsite").textContent =
        data.website_pemohon;
      document.getElementById("modalEmail").textContent = data.email_pemohon;
      document.getElementById("modalPhone").textContent = data.telepon_pemohon;
      document.getElementById("modalFax").textContent = data.faks_pemohon;
      document.getElementById("modalDescription").textContent =
        data.deskripsi_institusi;
      document.getElementById("modalVisitPurpose").textContent =
        data.tujuan_kunjungan;
      document.getElementById("modalPeopleMet").textContent =
        data.orang_ditemui || "No Data";
      document.getElementById("modalDiscussionTopics").textContent =
        data.bidang_pembahasan || "No Data";
      document.getElementById("modalUndanaContacts").innerHTML =
        data.kontak_undana || "No Undana Contact";
      document.getElementById("modalDelegations").innerHTML =
        data.delegasi || "No Delegations";
      document.getElementById("modalInterpreter").textContent =
        data.interpreter || "No Interpreter";
      document.getElementById("modalStatus").textContent = data.status;

      const detailsModal = new bootstrap.Modal(
        document.getElementById("detailsModal")
      );
      detailsModal.show();
    })
    .catch((error) => console.error("Error fetching VRF details:", error));
}
