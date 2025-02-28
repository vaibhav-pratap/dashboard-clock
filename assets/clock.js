document.addEventListener("DOMContentLoaded", function () {
    function updateClock() {
        let clockElement = document.getElementById("dashboard-clock");
        if (clockElement) {
            let timezone = dashboardClockSettings.timezone || "UTC";
            let now = new Date().toLocaleTimeString("en-US", { timeZone: timezone });
            clockElement.textContent = now;
        } else {
            console.error("Dashboard Clock element not found.");
        }
    }
    setInterval(updateClock, 1000);
    updateClock();
});
