document.addEventListener("DOMContentLoaded", () => {
  const expectedTbody = document.querySelector("#expectedVehiclesTable tbody");
  const insideTbody = document.querySelector("#insideVehiclesTable tbody");

 function escapeHtml(s) {
  if (!s) return "";
  return String(s)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
=======
=======
=======
=======
=======

>>>>>>> Stashed changes

>>>>>>> Stashed changes

>>>>>>> Stashed changes

>>>>>>> Stashed changes

>>>>>>> Stashed changes


  // ---- Load Expected Vehicles ----
  async function loadExpectedVehicles() {
    try {
      const res = await fetch("fetch_expected_vehicles.php");
      const data = await res.json();
      console.log("Expected vehicles:", data);

      expectedTbody.innerHTML = "";
      if (!Array.isArray(data) || data.length === 0) {
        expectedTbody.innerHTML =
          `<tr><td colspan="6" class="text-center">No expected vehicles</td></tr>`;
        return;
      }

     data.forEach(v => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
    <td>${escapeHtml(v.vehicle_owner)}</td>
    <td>${escapeHtml(v.vehicle_brand)}</td>
    <td>${escapeHtml(v.vehicle_model)}</td>
    <td>${escapeHtml(v.vehicle_color)}</td>
    <td>${escapeHtml(v.plate_number)}</td>
    <td>${escapeHtml(v.status)}</td>
  `;
  expectedTbody.appendChild(tr);
});

    } catch (err) {
      console.error("Error loading expected vehicles:", err);
    }
  }
  window.loadExpectedVehicles = loadExpectedVehicles;

  // ---- Load Inside Vehicles ----
  async function loadInsideVehicles() {
    try {
      const res = await fetch("fetch_inside_vehicles.php");
      const data = await res.json();
      console.log("Inside vehicles:", data);

      insideTbody.innerHTML = "";
      if (!Array.isArray(data) || data.length === 0) {
        insideTbody.innerHTML =
          `<tr><td colspan="8" class="text-center">No vehicles inside</td></tr>`;
        return;
      }

      data.forEach(v => {
  const tr = document.createElement("tr");
  tr.innerHTML = `
    <td>${escapeHtml(v.vehicle_owner)}</td>
    <td>${escapeHtml(v.vehicle_brand)}</td>
    <td>${escapeHtml(v.vehicle_model)}</td>
    <td>${escapeHtml(v.vehicle_color)}</td>
    <td>${escapeHtml(v.plate_number)}</td>
    <td>${escapeHtml(v.entry_time || "")}</td>
    <td>${v.exit_time ? escapeHtml(v.exit_time) : "Still Inside"}</td>
    <td>${escapeHtml(v.status || "Inside")}</td>
    <td>
      ${!v.exit_time
        ? `<button class="btn btn-sm btn-danger exit-btn" data-id="${v.id}">
             Mark Exit
           </button>`
        : ""}
    </td>
  `;
  insideTbody.appendChild(tr);
});

    } catch (err) {
      console.error("Error loading inside vehicles:", err);
    }
  }
  window.loadInsideVehicles = loadInsideVehicles;

  // ---- Handle Exit Button ----
  insideTbody.addEventListener("click", e => {
    const btn = e.target.closest(".exit-btn");
    if (btn) {
      const id = btn.dataset.id;
      if (confirm("Mark this vehicle as exited?")) {
        fetch("mark_exit.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: "vehicle_id=" + encodeURIComponent(id)
        })
          .then(res => res.json())
          .then(result => {
            if (result.success) {
              alert("🚗 Vehicle exit marked!");
              loadInsideVehicles();
            } else {
              alert("Error: " + result.message);
            }
          })
          .catch(err => {
            console.error(err);
            alert("Request failed.");
          });
      }
    }
  });

  // ---- Auto refresh ----
  loadExpectedVehicles();
  loadInsideVehicles();
  setInterval(() => {
    loadExpectedVehicles();
    loadInsideVehicles();
  }, 30000);
});
