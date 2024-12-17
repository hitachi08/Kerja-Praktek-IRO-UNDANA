function sortTable(columnIndex, header) {
  var table,
    rows,
    switching,
    i,
    x,
    y,
    shouldSwitch,
    direction,
    switchcount = 0;
  table = document.getElementById("vrfTable");
  switching = true;
  direction = "asc";

  let headers = table.getElementsByTagName("th");
  for (let h of headers) {
    h.querySelector(".sort-icon").innerHTML = "&#x25B4;";
  }

  header.querySelector(".sort-icon").innerHTML = "&#x25B4;";

  while (switching) {
    switching = false;
    rows = table.rows;

    for (i = 1; i < rows.length - 1; i++) {
      shouldSwitch = false;
      x = rows[i].getElementsByTagName("TD")[columnIndex];
      y = rows[i + 1].getElementsByTagName("TD")[columnIndex];

      if (direction === "asc") {
        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
          shouldSwitch = true;
          break;
        }
      } else if (direction === "desc") {
        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
          shouldSwitch = true;
          break;
        }
      }
    }
    if (shouldSwitch) {
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      switchcount++;
    } else {
      if (switchcount === 0 && direction === "asc") {
        direction = "desc";
        switching = true;
      }
    }
  }

  if (direction === "asc") {
    header.querySelector(".sort-icon").innerHTML = "&#x25B4;";
  } else {
    header.querySelector(".sort-icon").innerHTML = "&#x25BE;";
  }
}
