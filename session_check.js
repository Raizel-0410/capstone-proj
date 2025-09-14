const evtSource = new EventSource("session_watch.php");

evtSource.addEventListener("logout", function(e) {
  alert("⚠️ Your session was terminated because you logged in elsewhere.");
  window.location.href = "loginpage.php";
});
