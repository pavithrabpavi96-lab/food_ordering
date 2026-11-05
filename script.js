document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".food-card button").forEach(function (button) {
        button.addEventListener("click", function () {
            button.textContent = "Adding...";
        });
    });
});