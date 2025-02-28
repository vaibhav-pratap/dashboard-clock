document.addEventListener("DOMContentLoaded", function () {
    function updateClock() {
        let clockElement = document.getElementById("dashboard-clock");
        if (clockElement) {
            clockElement.textContent = new Date().toLocaleTimeString();
        } else {
            console.error("Dashboard Clock element not found.");
        }
    }
    setInterval(updateClock, 1000);
    updateClock();
});
