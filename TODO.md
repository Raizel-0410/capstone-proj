<<<<<<< HEAD
- [x] Edit visitors.php: Update nav-tabs to remove Live Video and add Verify, Facial Verification, Vehicle Verification, ID Verification tabs. Update tab-content with new panes, add Next buttons in appropriate tabs, move Mark Entry and add Reject to ID tab. Add Skip button in Vehicle Verification tab.
- [x] Edit scripts/visitors.js: Add event listeners for Next buttons to switch tabs, update modal show logic to activate Details tab, handle Reject button, remove live video related code. Add event listener for Skip button.
- [x] Test tab navigation and button functionality. (Skipped as per user request)
=======
# Fix Session Logout Issue

## Steps to Complete
- [x] Modify login.php to set session_name based on user role before session_start()
- [x] Modify maindashboard.php to set session_name('Admin_session') before require 'auth_check.php'
- [x] Modify personnel_dashboard.php to set session_name('Personnel_session') before require 'auth_check.php'
- [ ] Test the fix by logging in both admin and personnel accounts in the same browser and logging out one to ensure the other remains logged in
>>>>>>> pangupdate
