# Fix Session Logout Issue

## Steps to Complete
- [x] Modify login.php to set session_name based on user role before session_start()
- [x] Modify maindashboard.php to set session_name('Admin_session') before require 'auth_check.php'
- [x] Modify personnel_dashboard.php to set session_name('Personnel_session') before require 'auth_check.php'
- [ ] Test the fix by logging in both admin and personnel accounts in the same browser and logging out one to ensure the other remains logged in
