/* eslint-disable no-unsafe-negation */
/* eslint-disable no-var */
var notify = function() {
  // Check for notification compatibility.
  if (!"Notification" in window) {
    // If the browser version is unsupported, remain silent.
    return
  }
  // Log current permission level
  console.log(Notification.permission)
  // If the user has not been asked to grant or deny notifications
  // from this domain...
  if (Notification.permission === "default") {
    Notification.requestPermission(() => {
      // ...callback this function once a permission level has been set.
      notify()
    })
  } else if (Notification.permission === "granted") {
    // If the user has granted permission for this domain to send notifications...
    const n = new Notification("New message from Liz", {
      body: 'Liz: "Hi there!"',
      // ...prevent duplicate notifications
      tag: "unique string"
    })
    // Remove the notification from Notification Center when clicked.
    n.onclick = function() {
      this.close()
    }
    // Callback function when the notification is closed.
    n.onclose = function() {
      console.log("Notification closed")
    }
  } else if (Notification.permission === "denied") {
    // If the user does not want notifications to come from this domain...
    // ...remain silent.
    return
  }
}
