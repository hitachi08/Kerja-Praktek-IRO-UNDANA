window.onload = function () {
  const errorMessage = document.querySelector(".error-message");
  if (errorMessage) {
    setTimeout(() => {
      setTimeout(() => errorMessage.remove(), 500);
    }, 5000);
  }
};

const passwordField = document.getElementById("password");
const showPasswordCheckbox = document.getElementById("show-password");

showPasswordCheckbox.addEventListener("change", function () {
  if (this.checked) {
    passwordField.type = "text";
  } else {
    passwordField.type = "password";
  }
});

function updateTime() {
  const days = [
    "Sunday",
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday",
  ];
  const months = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
  ];

  const now = new Date();

  const dayName = days[now.getDay()];
  const date = `${now.getDate()} ${
    months[now.getMonth()]
  } ${now.getFullYear()}`;

  const hours = now.getHours().toString().padStart(2, "0");
  const minutes = now.getMinutes().toString().padStart(2, "0");
  const seconds = now.getSeconds().toString().padStart(2, "0");
  const time = `- ${hours}:${minutes}:${seconds} -`;

  document.getElementById("day-name").textContent = dayName;
  document.getElementById("date").textContent = date;
  document.getElementById("time").textContent = time;
}

updateTime();
setInterval(updateTime, 1000);
