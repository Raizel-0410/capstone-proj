document.addEventListener("DOMContentLoaded", () => {

  // Next button event listeners for tab navigation
  const nextToVerifyBtn = document.getElementById("nextToVerify");
  const nextToFacialBtn = document.getElementById("nextToFacial");
  const nextToVehicleBtn = document.getElementById("nextToVehicle");
  const nextToIdBtn = document.getElementById("nextToId");
  const skipVehicleBtn = document.getElementById("skipVehicle");
  const rejectBtn = document.getElementById("rejectBtn");

  function showTab(tabId) {
    const tabTrigger = document.querySelector(`#visitorTab button[data-bs-target="#${tabId}"]`);
    if (tabTrigger) {
      const tab = new bootstrap.Tab(tabTrigger);
      tab.show();
    }
  }

  if (nextToVerifyBtn) {
    nextToVerifyBtn.addEventListener("click", () => {
      showTab("verify");
    });
  }

  if (nextToFacialBtn) {
    nextToFacialBtn.addEventListener("click", () => {
      showTab("facial");
    });
  }

  if (nextToVehicleBtn) {
    nextToVehicleBtn.addEventListener("click", () => {
      showTab("vehicle");
    });
  }

  if (nextToIdBtn) {
    nextToIdBtn.addEventListener("click", () => {
      showTab("id");
    });
  }

  if (skipVehicleBtn) {
    skipVehicleBtn.addEventListener("click", () => {
      showTab("id");
    });
  }

  if (rejectBtn) {
    rejectBtn.addEventListener("click", () => {
      // Close the modal on reject
      const modalEl = document.getElementById("visitorDetailsModal");
      const modalInstance = bootstrap.Modal.getInstance(modalEl);
      if (modalInstance) {
        modalInstance.hide();
      }
      alert("Verification rejected.");
    });
  }

   /* ---- Logout modal ---- */
  const logoutLink = document.getElementById("logout-link");
  if (logoutLink) {
    logoutLink.addEventListener("click", (ev) => {
      ev.preventDefault();
      const modal = document.getElementById("confirmModal");
      const msgEl = document.getElementById("confirmMessage");
      const yes = document.getElementById("confirmYes");
      const no = document.getElementById("confirmNo");

      msgEl.textContent = "Are you sure you want to log out?";
      modal.classList.add("show");

      yes.onclick = () => { window.location.href = logoutLink.href; };
      no.onclick = () => { modal.classList.remove("show"); };
    });
  }

  const expectedVisitorsTbody = document.querySelector("#expectedVisitorsTable tbody");
  const insideVisitorsTbody = document.querySelector("#insideVisitorsTable tbody");
  const exitedVisitorsTbody = document.querySelector("#exitedVisitorsTable tbody");
  const markEntryBtn = document.getElementById("markEntryBtn");
  let currentVisitorId = null;

 function escapeHtml(s) {
    if (!s) return "";
    return String(s)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }


  /* ---- Load Expected Visitors Table ---- */
  async function loadExpectedVisitors() {
    try {
      const res = await fetch("fetch_expected_visitors.php");
      const data = await res.json();

      expectedVisitorsTbody.innerHTML = "";

      if (!Array.isArray(data) || data.length === 0) {
        expectedVisitorsTbody.innerHTML =
          `<tr><td colspan="7" class="text-center">No expected visitors found</td></tr>`;
        return;
      }

      data.forEach(v => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${escapeHtml(v.first_name || "")}</td>
          <td>${escapeHtml(v.last_name || "")}</td>
          <td>${escapeHtml(v.contact_number || "")}</td>
          <td>${escapeHtml(v.date)}</td>
          <td>${escapeHtml(v.status)}</td>
          <td>
            <button class="btn btn-info btn-sm view-btn" data-id="${v.id}">View</button>
            <button class="btn btn-success btn-sm entry-btn" data-id="${v.id}">Mark Entry</button>
          </td>
        `;
        expectedVisitorsTbody.appendChild(tr);
      });
    } catch (err) {
      console.error("Error loading expected visitors:", err);
      expectedVisitorsTbody.innerHTML =
        `<tr><td colspan="7" class="text-center text-danger">Failed to load expected visitors</td></tr>`;
    }
  }

  /* ---- Load Inside Visitors Table ---- */
  async function loadInsideVisitors() {
    try {
      const res = await fetch("fetch_inside_visitors.php");
      const data = await res.json();

      insideVisitorsTbody.innerHTML = "";

      if (!Array.isArray(data) || data.length === 0) {
        insideVisitorsTbody.innerHTML =
          `<tr><td colspan="8" class="text-center">No inside visitors found</td></tr>`;
        return;
      }

      data.forEach(v => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${escapeHtml(v.first_name || "")}</td>
          <td>${escapeHtml(v.last_name || "")}</td>
          <td>${escapeHtml(v.contact_number || "")}</td>
          <td>${escapeHtml(v.key_card_number || "")}</td>
          <td>${escapeHtml(v.time_in || "")}</td>
          <td>${v.time_out ? escapeHtml(v.time_out) : ""}</td>
          <td>${escapeHtml(v.status)}</td>
          <td>
            <button class="btn btn-info btn-sm view-btn" data-id="${v.id}">View</button>
            <button class="btn btn-warning btn-sm edit-btn" data-id="${v.id}">Edit</button>
            <button class="btn btn-danger btn-sm exit-btn" data-id="${v.id}">Mark Exit</button>
          </td>
        `;
        insideVisitorsTbody.appendChild(tr);
      });
    } catch (err) {
      console.error("Error loading inside visitors:", err);
      insideVisitorsTbody.innerHTML =
        `<tr><td colspan="8" class="text-center text-danger">Failed to load inside visitors</td></tr>`;
    }
  }

  /* ---- Load Exited Visitors Table ---- */
  async function loadExitedVisitors() {
    try {
      const res = await fetch("fetch_exited_visitors.php");
      const data = await res.json();

      exitedVisitorsTbody.innerHTML = "";

      if (!Array.isArray(data) || data.length === 0) {
        exitedVisitorsTbody.innerHTML =
          `<tr><td colspan="8" class="text-center">No exited visitors found</td></tr>`;
        return;
      }

      data.forEach(v => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${escapeHtml(v.first_name || "")}</td>
          <td>${escapeHtml(v.last_name || "")}</td>
          <td>${escapeHtml(v.contact_number || "")}</td>
          <td>${escapeHtml(v.key_card_number || "")}</td>
          <td>${escapeHtml(v.time_in || "")}</td>
          <td>${escapeHtml(v.time_out || "")}</td>
          <td>${escapeHtml(v.status)}</td>
          <td>
            <button class="btn btn-info btn-sm view-btn" data-id="${v.id}">View</button>
          </td>
        `;
        exitedVisitorsTbody.appendChild(tr);
      });
    } catch (err) {
      console.error("Error loading exited visitors:", err);
      exitedVisitorsTbody.innerHTML =
        `<tr><td colspan="8" class="text-center text-danger">Failed to load exited visitors</td></tr>`;
    }
  }

  /* ---- Handle Action Buttons ---- */
  expectedVisitorsTbody.addEventListener("click", async (e) => {
    const btn = e.target.closest("button");
    if (!btn) return;

    const visitorId = btn.dataset.id;

    // View Button
    if (btn.classList.contains("view-btn")) {
      try {
        if (!visitorId) {
          alert("Visitor ID is missing.");
          return;
        }
        const res = await fetch(`fetch_visitor_details.php?id=${encodeURIComponent(visitorId)}`);
        const visitor = await res.json();

        if (!visitor.success) {
          alert(visitor.message || "Visitor data not found");
          return;
        }

        document.getElementById("visitorName").textContent = escapeHtml(visitor.data.full_name);
        document.getElementById("visitorContact").textContent = escapeHtml(visitor.data.contact_number);
        document.getElementById("visitorEmail").textContent = escapeHtml(visitor.data.email);
        document.getElementById("visitorAddress").textContent = escapeHtml(visitor.data.address);
        document.getElementById("visitorReason").textContent = escapeHtml(visitor.data.reason);
        document.getElementById("visitorPersonnel").textContent = escapeHtml(visitor.data.personnel_related || '');
        document.getElementById("visitorIDPhoto").src = visitor.data.id_photo_path;
        document.getElementById("visitorSelfie").src = visitor.data.selfie_photo_path;

        // Vehicle Info
        const vehicleInfo = document.getElementById("vehicleInfo");
        if (visitor.data.vehicle_owner) {
          vehicleInfo.style.display = 'block';
          document.getElementById("vehicleOwner").textContent = escapeHtml(visitor.data.vehicle_owner);
          document.getElementById("vehicleBrand").textContent = escapeHtml(visitor.data.vehicle_brand);
          document.getElementById("vehicleModel").textContent = escapeHtml(visitor.data.vehicle_model);
          document.getElementById("vehicleColor").textContent = escapeHtml(visitor.data.vehicle_color);
          document.getElementById("plateNumber").textContent = escapeHtml(visitor.data.plate_number);
          document.getElementById("vehiclePhoto").src = visitor.data.vehicle_photo_path || '';
        } else {
          vehicleInfo.style.display = 'none';
        }

        // Driver Info
        const driverInfo = document.getElementById("driverInfo");
        if (visitor.data.driver_name) {
          driverInfo.style.display = 'block';
          document.getElementById("driverName").textContent = escapeHtml(visitor.data.driver_name);
          document.getElementById("driverIdPhoto").src = visitor.data.driver_id || '';
        } else {
          driverInfo.style.display = 'none';
        }

        currentVisitorId = visitor.data.id;

        // Hide or show verify tabs based on visitor status
        const verifyTabBtn = document.querySelector('#visitorTab button[data-bs-target="#verify"]');
        const facialTabBtn = document.querySelector('#visitorTab button[data-bs-target="#facial"]');
        const vehicleTabBtn = document.querySelector('#visitorTab button[data-bs-target="#vehicle"]');
        const idTabBtn = document.querySelector('#visitorTab button[data-bs-target="#id"]');

        if (visitor.data.status === "Inside" || visitor.data.status === "Exited") {
          if (verifyTabBtn) verifyTabBtn.style.display = 'none';
          if (facialTabBtn) facialTabBtn.style.display = 'none';
          if (vehicleTabBtn) vehicleTabBtn.style.display = 'none';
          if (idTabBtn) idTabBtn.style.display = 'none';

          // Hide Next buttons in verify tabs
          const nextToVerifyBtn = document.getElementById("nextToVerify");
          const nextToFacialBtn = document.getElementById("nextToFacial");
          const nextToVehicleBtn = document.getElementById("nextToVehicle");

          if (nextToVerifyBtn) nextToVerifyBtn.style.display = 'none';
          if (nextToFacialBtn) nextToFacialBtn.style.display = 'none';
          if (nextToVehicleBtn) nextToVehicleBtn.style.display = 'none';

          // Show details tab by default since all verify tabs are hidden
          const detailsTabTriggerEl = document.querySelector('#details-tab');
          if (detailsTabTriggerEl) {
            const tab = bootstrap.Tab.getInstance(detailsTabTriggerEl) || new bootstrap.Tab(detailsTabTriggerEl);
            tab.show();
          }
        } else {
          if (verifyTabBtn) verifyTabBtn.style.display = 'block';
          if (facialTabBtn) facialTabBtn.style.display = 'block';
          if (vehicleTabBtn) vehicleTabBtn.style.display = 'block';
          if (idTabBtn) idTabBtn.style.display = 'block';

          // Show Next buttons in verify tabs
          const nextToVerifyBtn = document.getElementById("nextToVerify");
          const nextToFacialBtn = document.getElementById("nextToFacial");
          const nextToVehicleBtn = document.getElementById("nextToVehicle");

          if (nextToVerifyBtn) nextToVerifyBtn.style.display = 'inline-block';
          if (nextToFacialBtn) nextToFacialBtn.style.display = 'inline-block';
          if (nextToVehicleBtn) nextToVehicleBtn.style.display = 'inline-block';

          // Show details tab by default
          const detailsTabTriggerEl = document.querySelector('#details-tab');
          if (detailsTabTriggerEl) {
            const tab = bootstrap.Tab.getInstance(detailsTabTriggerEl) || new bootstrap.Tab(detailsTabTriggerEl);
            tab.show();
          }
        }

        new bootstrap.Modal(document.getElementById("visitorDetailsModal")).show();


      } catch (err) {
        console.error(err);
        alert("Failed to fetch visitor details.");
      }
    }
  });

  insideVisitorsTbody.addEventListener("click", async (e) => {
    const btn = e.target.closest("button");
    if (!btn) return;

    const visitorId = btn.dataset.id;

    // View Button
    if (btn.classList.contains("view-btn")) {
      // ... same as above
      try {
        if (!visitorId) {
          alert("Visitor ID is missing.");
          return;
        }
        const res = await fetch(`fetch_visitor_details.php?id=${encodeURIComponent(visitorId)}`);
        const visitor = await res.json();

        if (!visitor.success) {
          alert(visitor.message || "Visitor data not found");
          return;
        }

        document.getElementById("visitorName").textContent = escapeHtml(visitor.data.full_name);
        document.getElementById("visitorContact").textContent = escapeHtml(visitor.data.contact_number);
        document.getElementById("visitorEmail").textContent = escapeHtml(visitor.data.email);
        document.getElementById("visitorAddress").textContent = escapeHtml(visitor.data.address);
        document.getElementById("visitorReason").textContent = escapeHtml(visitor.data.reason);
        document.getElementById("visitorPersonnel").textContent = escapeHtml(visitor.data.personnel_related || '');
        document.getElementById("visitorIDPhoto").src = visitor.data.id_photo_path;
        document.getElementById("visitorSelfie").src = visitor.data.selfie_photo_path;

        // Vehicle Info
        const vehicleInfo = document.getElementById("vehicleInfo");
        if (visitor.data.vehicle_owner) {
          vehicleInfo.style.display = 'block';
          document.getElementById("vehicleOwner").textContent = escapeHtml(visitor.data.vehicle_owner);
          document.getElementById("vehicleBrand").textContent = escapeHtml(visitor.data.vehicle_brand);
          document.getElementById("vehicleModel").textContent = escapeHtml(visitor.data.vehicle_model);
          document.getElementById("vehicleColor").textContent = escapeHtml(visitor.data.vehicle_color);
          document.getElementById("plateNumber").textContent = escapeHtml(visitor.data.plate_number);
          document.getElementById("vehiclePhoto").src = visitor.data.vehicle_photo_path || '';
        } else {
          vehicleInfo.style.display = 'none';
        }

        // Driver Info
        const driverInfo = document.getElementById("driverInfo");
        if (visitor.data.driver_name) {
          driverInfo.style.display = 'block';
          document.getElementById("driverName").textContent = escapeHtml(visitor.data.driver_name);
          document.getElementById("driverIdPhoto").src = visitor.data.driver_id || '';
        } else {
          driverInfo.style.display = 'none';
        }

        currentVisitorId = visitor.data.id;

        // Hide or show verify tabs based on visitor status
        const verifyTabBtn = document.querySelector('#visitorTab button[data-bs-target="#verify"]');
        const facialTabBtn = document.querySelector('#visitorTab button[data-bs-target="#facial"]');
        const vehicleTabBtn = document.querySelector('#visitorTab button[data-bs-target="#vehicle"]');
        const idTabBtn = document.querySelector('#visitorTab button[data-bs-target="#id"]');

        if (visitor.data.status === "Inside" || visitor.data.status === "Exited") {
          if (verifyTabBtn) verifyTabBtn.style.display = 'none';
          if (facialTabBtn) facialTabBtn.style.display = 'none';
          if (vehicleTabBtn) vehicleTabBtn.style.display = 'none';
          if (idTabBtn) idTabBtn.style.display = 'none';

          // Hide Next buttons in verify tabs
          const nextToVerifyBtn = document.getElementById("nextToVerify");
          const nextToFacialBtn = document.getElementById("nextToFacial");
          const nextToVehicleBtn = document.getElementById("nextToVehicle");

          if (nextToVerifyBtn) nextToVerifyBtn.style.display = 'none';
          if (nextToFacialBtn) nextToFacialBtn.style.display = 'none';
          if (nextToVehicleBtn) nextToVehicleBtn.style.display = 'none';

          // Show details tab by default since all verify tabs are hidden
          const detailsTabTriggerEl = document.querySelector('#details-tab');
          if (detailsTabTriggerEl) {
            const tab = bootstrap.Tab.getInstance(detailsTabTriggerEl) || new bootstrap.Tab(detailsTabTriggerEl);
            tab.show();
          }
        } else {
          if (verifyTabBtn) verifyTabBtn.style.display = 'block';
          if (facialTabBtn) facialTabBtn.style.display = 'block';
          if (vehicleTabBtn) vehicleTabBtn.style.display = 'block';
          if (idTabBtn) idTabBtn.style.display = 'block';

          // Show Next buttons in verify tabs
          const nextToVerifyBtn = document.getElementById("nextToVerify");
          const nextToFacialBtn = document.getElementById("nextToFacial");
          const nextToVehicleBtn = document.getElementById("nextToVehicle");

          if (nextToVerifyBtn) nextToVerifyBtn.style.display = 'inline-block';
          if (nextToFacialBtn) nextToFacialBtn.style.display = 'inline-block';
          if (nextToVehicleBtn) nextToVehicleBtn.style.display = 'inline-block';

          // Show details tab by default
          const detailsTabTriggerEl = document.querySelector('#details-tab');
          if (detailsTabTriggerEl) {
            const tab = bootstrap.Tab.getInstance(detailsTabTriggerEl) || new bootstrap.Tab(detailsTabTriggerEl);
            tab.show();
          }
        }

        new bootstrap.Modal(document.getElementById("visitorDetailsModal")).show();


      } catch (err) {
        console.error(err);
        alert("Failed to fetch visitor details.");
      }
    }
  });

  // Add event listener for exited visitors table buttons
  exitedVisitorsTbody.addEventListener("click", async (e) => {
    const btn = e.target.closest("button");
    if (!btn) return;

    const visitorId = btn.dataset.id;

    // View Button
    if (btn.classList.contains("view-btn")) {
      try {
        if (!visitorId) {
          alert("Visitor ID is missing.");
          return;
        }
        const res = await fetch(`fetch_visitor_details.php?id=${encodeURIComponent(visitorId)}`);
        const visitor = await res.json();

        if (!visitor.success) {
          alert(visitor.message || "Visitor data not found");
          return;
        }

        document.getElementById("visitorName").textContent = escapeHtml(visitor.data.full_name);
        document.getElementById("visitorContact").textContent = escapeHtml(visitor.data.contact_number);
        document.getElementById("visitorEmail").textContent = escapeHtml(visitor.data.email);
        document.getElementById("visitorAddress").textContent = escapeHtml(visitor.data.address);
        document.getElementById("visitorReason").textContent = escapeHtml(visitor.data.reason);
        document.getElementById("visitorPersonnel").textContent = escapeHtml(visitor.data.personnel_related || '');
        document.getElementById("visitorIDPhoto").src = visitor.data.id_photo_path;
        document.getElementById("visitorSelfie").src = visitor.data.selfie_photo_path;

        // Vehicle Info
        const vehicleInfo = document.getElementById("vehicleInfo");
        if (visitor.data.vehicle_owner) {
          vehicleInfo.style.display = 'block';
          document.getElementById("vehicleOwner").textContent = escapeHtml(visitor.data.vehicle_owner);
          document.getElementById("vehicleBrand").textContent = escapeHtml(visitor.data.vehicle_brand);
          document.getElementById("vehicleModel").textContent = escapeHtml(visitor.data.vehicle_model);
          document.getElementById("vehicleColor").textContent = escapeHtml(visitor.data.vehicle_color);
          document.getElementById("plateNumber").textContent = escapeHtml(visitor.data.plate_number);
          document.getElementById("vehiclePhoto").src = visitor.data.vehicle_photo_path || '';
        } else {
          vehicleInfo.style.display = 'none';
        }

        // Driver Info
        const driverInfo = document.getElementById("driverInfo");
        if (visitor.data.driver_name) {
          driverInfo.style.display = 'block';
          document.getElementById("driverName").textContent = escapeHtml(visitor.data.driver_name);
          document.getElementById("driverIdPhoto").src = visitor.data.driver_id || '';
        } else {
          driverInfo.style.display = 'none';
        }

        currentVisitorId = visitor.data.id;

        // Hide or show verify tabs based on visitor status
        const verifyTabBtn = document.querySelector('#visitorTab button[data-bs-target="#verify"]');
        const facialTabBtn = document.querySelector('#visitorTab button[data-bs-target="#facial"]');
        const vehicleTabBtn = document.querySelector('#visitorTab button[data-bs-target="#vehicle"]');
        const idTabBtn = document.querySelector('#visitorTab button[data-bs-target="#id"]');

        if (visitor.data.status === "Inside" || visitor.data.status === "Exited") {
          if (verifyTabBtn) verifyTabBtn.style.display = 'none';
          if (facialTabBtn) facialTabBtn.style.display = 'none';
          if (vehicleTabBtn) vehicleTabBtn.style.display = 'none';
          if (idTabBtn) idTabBtn.style.display = 'none';

          // Hide Next buttons in verify tabs
          const nextToVerifyBtn = document.getElementById("nextToVerify");
          const nextToFacialBtn = document.getElementById("nextToFacial");
          const nextToVehicleBtn = document.getElementById("nextToVehicle");

          if (nextToVerifyBtn) nextToVerifyBtn.style.display = 'none';
          if (nextToFacialBtn) nextToFacialBtn.style.display = 'none';
          if (nextToVehicleBtn) nextToVehicleBtn.style.display = 'none';

          // Show details tab by default since all verify tabs are hidden
          const detailsTabTriggerEl = document.querySelector('#details-tab');
          if (detailsTabTriggerEl) {
            const tab = bootstrap.Tab.getInstance(detailsTabTriggerEl) || new bootstrap.Tab(detailsTabTriggerEl);
            tab.show();
          }
        } else {
          if (verifyTabBtn) verifyTabBtn.style.display = 'block';
          if (facialTabBtn) facialTabBtn.style.display = 'block';
          if (vehicleTabBtn) vehicleTabBtn.style.display = 'block';
          if (idTabBtn) idTabBtn.style.display = 'block';

          // Show Next buttons in verify tabs
          const nextToVerifyBtn = document.getElementById("nextToVerify");
          const nextToFacialBtn = document.getElementById("nextToFacial");
          const nextToVehicleBtn = document.getElementById("nextToVehicle");

          if (nextToVerifyBtn) nextToVerifyBtn.style.display = 'inline-block';
          if (nextToFacialBtn) nextToFacialBtn.style.display = 'inline-block';
          if (nextToVehicleBtn) nextToVehicleBtn.style.display = 'inline-block';

          // Show details tab by default
          const detailsTabTriggerEl = document.querySelector('#details-tab');
          if (detailsTabTriggerEl) {
            const tab = bootstrap.Tab.getInstance(detailsTabTriggerEl) || new bootstrap.Tab(detailsTabTriggerEl);
            tab.show();
          }
        }

        new bootstrap.Modal(document.getElementById("visitorDetailsModal")).show();


      } catch (err) {
        console.error(err);
        alert("Failed to fetch visitor details.");
      }
    }
  });

  loadExpectedVisitors();
loadInsideVisitors();
loadExitedVisitors();
});
