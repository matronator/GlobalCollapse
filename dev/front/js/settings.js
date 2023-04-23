import "../../../app/modules/Front/components/AccountForms/DatetimeForm/DatetimeForm"
import Push from "push.js"

document.addEventListener("DOMContentLoaded", () => {
    const notifButton = document.getElementById("requestNotificationPermission");

    if (notifButton) {
        notifButton.addEventListener("click", () => {
            Push.Permission.request(function() {
                notifButton.textContent = 'Enabled';
                notifButton.classList.add('uk-button-success');
            });
        });
    }
});
