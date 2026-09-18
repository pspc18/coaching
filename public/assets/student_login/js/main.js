// page loader .......................................
document.addEventListener("DOMContentLoaded", function() {
    const loader = document.getElementById("pageLoader");
    if (loader) {
      setTimeout(() => {
        loader.classList.add("hidden");
      }, 500); // 0.5 second delay for smooth fade out
    }
  });





  // header dropdown ..................................
document.addEventListener("DOMContentLoaded", () => {

    const openBtn = document.getElementById("openSwitchSheet");
    const bottomSheet = document.getElementById("switchBottomSheet");
    const sheet = bottomSheet.querySelector(".switch-sheet-content");
    const cancelBtn = document.getElementById("cancelSwitch");

    let startY = 0, currentY = 0, dragging = false;

    /* =============================
        OPEN SHEET
    ============================== */
    openBtn.addEventListener("click", (e) => {
        e.stopPropagation();

        bottomSheet.style.display = "flex"; // Show first

        setTimeout(() => {
            bottomSheet.classList.add("active"); // Slide in
            sheet.style.transform = "translateY(0)";
        }, 10);
    });


    /* =============================
        CLOSE SHEET
    ============================== */
    const closeSheet = () => {
        sheet.style.transition = "transform .25s ease";
        sheet.style.transform = "translateY(100%)";

        setTimeout(() => {
            bottomSheet.classList.remove("active");
            sheet.style.transition = "none";
            bottomSheet.style.display = "none"; // Fully hide
        }, 250);
    };

    cancelBtn.addEventListener("click", closeSheet);

    bottomSheet.addEventListener("click", (e) => {
        if (e.target === bottomSheet) closeSheet();
    });


    /* =============================
        DRAG TO CLOSE
    ============================== */
    const startDrag = (y) => {
        startY = y;
        dragging = true;
        sheet.style.transition = "none";
    };

    const moveDrag = (y) => {
        if (!dragging) return;
        currentY = y;
        const diff = currentY - startY;
        if (diff > 0) sheet.style.transform = `translateY(${diff}px)`;
    };

    const endDrag = () => {
        if (!dragging) return;
        dragging = false;

        const diff = currentY - startY;
        sheet.style.transition = "transform .25s ease";

        if (diff > 100) closeSheet();
        else sheet.style.transform = "translateY(0)";
    };

    sheet.addEventListener("touchstart", (e) => startDrag(e.touches[0].clientY), { passive: true });
    sheet.addEventListener("touchmove", (e) => {
        moveDrag(e.touches[0].clientY);
        if (dragging) e.preventDefault();
    }, { passive: false });
    sheet.addEventListener("touchend", endDrag);


    /* =============================
        SELECT OPTION
    ============================== */
    document.querySelectorAll(".switch-option-list li").forEach(li => {
        li.addEventListener("click", () => {
            const name = li.textContent.trim();

            // Set text on button (if needed)
            if (openBtn.firstChild)
                openBtn.firstChild.textContent = name + " ";

            closeSheet();
        });
    });

});





// sidebar .......................................
document.addEventListener("DOMContentLoaded", () => {
  const toggleBtn = document.getElementById("menuToggle");
  const closeBtn = document.getElementById("closeSidebar");
  const sidebar = document.getElementById("sidebar");
  let overlay = document.querySelector(".sidebar-overlay");
  if (!overlay) {
    overlay = document.createElement("div");
    overlay.classList.add("sidebar-overlay");
    document.body.appendChild(overlay);
  }
  function toggleSidebar(open) {
    if (!sidebar) return;
    if (open) {
      sidebar.classList.add("active");
      overlay.classList.add("show");
      sidebar.style.transform = "translateX(0)";
      document.body.style.overflow = "hidden";
    } else {
      sidebar.classList.remove("active");
      overlay.classList.remove("show");
      sidebar.style.transform = "translateX(-100%)";
      document.body.style.overflow = "";
    }
  }
  if (toggleBtn) toggleBtn.addEventListener("click", e => { e.stopPropagation(); toggleSidebar(true); });
  if (closeBtn) closeBtn.addEventListener("click", e => { e.stopPropagation(); toggleSidebar(false); });
  overlay.addEventListener("click", () => toggleSidebar(false));
  let touchStartX = 0;
  let touchEndX = 0;

  document.addEventListener("touchstart", e => {
    touchStartX = e.changedTouches[0].screenX;
  });

  document.addEventListener("touchend", e => {
    touchEndX = e.changedTouches[0].screenX;
    handleGesture();
  });

  function handleGesture() {
    const swipeDistance = touchEndX - touchStartX;

    if (swipeDistance > 60 && !sidebar.classList.contains("active")) {
    //   toggleSidebar(true);
    }

    if (swipeDistance < -60 && sidebar.classList.contains("active")) {
      toggleSidebar(false);
    }
  }
  const currentURL = window.location.pathname.split("/").pop();
  document.querySelectorAll(".app-sidebar a").forEach(link => {
    const href = link.getAttribute("href");
    if (href === currentURL || (href === "dashboard" && currentURL === "")) {
      link.classList.add("active");
    }
  });

  console.log("ðŸ’« Sidebar animation + swipe gestures loaded!");
});





// theme changer...........................................
document.addEventListener("DOMContentLoaded", function() {
    const root = document.documentElement;

    // Grab both buttons
    const sidebarToggle = document.getElementById("sidebarThemeToggle");
    // Initialize theme
    const savedTheme = localStorage.getItem("theme") || "dark";
    root.setAttribute("data-theme", savedTheme);
    updateButton(sidebarToggle, savedTheme);


    // Add event listeners if buttons exist
    if(sidebarToggle) sidebarToggle.addEventListener("click", toggleTheme);


    function toggleTheme() {
        const currentTheme = root.getAttribute("data-theme") || "light";
        const nextTheme = currentTheme === "light" ? "dark" : "light";
        root.setAttribute("data-theme", nextTheme);
        localStorage.setItem("theme", nextTheme);

        // Update both buttons
        updateButton(sidebarToggle, nextTheme);
    }

    function updateButton(button, theme) {
        if(!button) return;
        const icon = button.querySelector("i");
        const text = button.querySelector("span b") || button.querySelector("span");
        if(theme === "dark") {
            if(icon) icon.classList.replace("bi-moon", "bi-sun");
            if(text) text.textContent = "Light Mode";
        } else {
            if(icon) icon.classList.replace("bi-sun", "bi-moon");
            if(text) text.textContent = "Dark Mode";
        }
    }
});

// // student profile edit ................
// document.addEventListener('DOMContentLoaded', () => {
//   const cameraIcon   = document.getElementById('cameraIcon');
//   const bottomSheet  = document.getElementById('photoBottomSheet');
//   const sheet        = document.getElementById('photoSheetContent');
//   const cancelBtn    = document.getElementById('cancelSheet');
//   const photoInput   = document.getElementById('photoInput');
//   const profileImage = document.getElementById('profileImage');

//   let startY = 0;
//   let currentY = 0;
//   let dragging = false;
//   cameraIcon.addEventListener('click', e => {
//     e.stopPropagation();
//     bottomSheet.classList.add('active');
//     sheet.style.transform = 'translateY(0)';
//   });
//   function closeSheet() {
//     sheet.style.transition = 'transform .25s ease';
//     sheet.style.transform  = 'translateY(100%)';
//     setTimeout(() => {
//       bottomSheet.classList.remove('active');
//       sheet.style.transition = 'none';
//       sheet.style.transform  = 'translateY(0)';
//     }, 250);
//   }

//   cancelBtn.addEventListener('click', closeSheet);
//   bottomSheet.addEventListener('click', e => {
//     if (e.target === bottomSheet) closeSheet();
//   });

//   const startDrag = y => {
//     startY = y;
//     dragging = true;
//     sheet.style.transition = 'none';
//   };
//   const moveDrag = y => {
//     if (!dragging) return;
//     currentY = y;
//     const diff = currentY - startY;
//     if (diff > 0) {
//       sheet.style.transform = `translateY(${diff}px)`;
//     }
//   };
//   const endDrag = () => {
//     if (!dragging) return;
//     dragging = false;
//     const diff = currentY - startY;
//     sheet.style.transition = 'transform .25s ease';
//     if (diff > 100) {
//       closeSheet();
//     } else {
//       sheet.style.transform = 'translateY(0)';
//     }
//   };

//   sheet.addEventListener('touchstart', e => {
//     startDrag(e.touches[0].clientY);
//   }, {passive:true});

//   sheet.addEventListener('touchmove', e => {
//     moveDrag(e.touches[0].clientY);
//     if (dragging) e.preventDefault(); 
//   }, {passive:false});

//   sheet.addEventListener('touchend', endDrag);
//   sheet.addEventListener('mousedown', e => startDrag(e.clientY));
//   window.addEventListener('mousemove', e => moveDrag(e.clientY));
//   window.addEventListener('mouseup', endDrag);
//   document.getElementById('viewPhoto').onclick = () => {
//     window.open(profileImage.src, '_blank');
//     closeSheet();
//   };
//   document.getElementById('uploadPhoto').onclick = () => {
//     closeSheet();
//     photoInput.click();
//   };
//   document.getElementById('deletePhoto').onclick = () => {
//     profileImage.src = "{{ asset('assets/img/user_icon.png') }}";
//     closeSheet();
//   };

//   photoInput.addEventListener('change', e => {
//     const f = e.target.files[0];
//     if (!f) return;
//     const reader = new FileReader();
//     reader.onload = ev => profileImage.src = ev.target.result;
//     reader.readAsDataURL(f);
//   });
// });

 
// // student information ...............................
// document.addEventListener("DOMContentLoaded", function () {
//   const editBtn = document.getElementById("editProfileBtn");
//   const saveBtn = document.getElementById("saveProfileBtn");

//   editBtn.addEventListener("click", function () {
//     const fields = document.querySelectorAll(".info-col p");

//     fields.forEach(p => {
//       const value = p.textContent.trim();
//       const input = document.createElement("input");
//       input.type = "text";
//       input.value = value;
//       input.classList.add("form-control", "form-control-sm", "editable-field");
//       p.replaceWith(input);
//     });

//     editBtn.classList.add("d-none");
//     saveBtn.classList.remove("d-none");
//   });

//   saveBtn.addEventListener("click", function () {
//     const inputs = document.querySelectorAll(".editable-field");

//     inputs.forEach(input => {
//       const newP = document.createElement("p");
//       newP.textContent = input.value;
//       input.replaceWith(newP);
//     });

//     saveBtn.classList.add("d-none");
//     editBtn.classList.remove("d-none");
//   });
// });

 document.getElementById("hardRefreshBtn").addEventListener("click", function () {

    fetch("/hard-refresh", { cache: "no-store" })
        .then(res => res.json())
        .then(data => {
            if (data.status === "success") {

                // Force browser to ignore cache by adding timestamp
                const url = window.location.origin + window.location.pathname + '?v=' + new Date().getTime();

                // Replace page to avoid back button issues
                window.location.replace(url);
            }
        })
        .catch(err => {
            console.error("Hard refresh failed:", err);
            alert("Hard refresh failed. Try manually clearing browser cache.");
        });
});

document.getElementById("hardRefreshBtn").addEventListener("click", function () {
    const img = this.querySelector("img");
    
    img.classList.add("refresh-rotate");

    // animation complete ke baad class remove — taaki next click par fir chale
    setTimeout(() => {
        img.classList.remove("refresh-rotate");
    }, 700);
});
