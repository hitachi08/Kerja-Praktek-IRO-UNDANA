function showNotification(message, type) {
  const notification = document.getElementById("notification");
  notification.textContent = message;

  if (type === "success") {
    notification.style.backgroundColor = "#3963e0";
  } else if (type === "error") {
    notification.style.backgroundColor = "#f44336";
  }

  notification.style.display = "block";
  notification.style.color = "#fff";
  notification.style.padding = "10px";
  notification.style.position = "fixed";
  notification.style.top = "10px";
  notification.style.right = "10px";
  notification.style.borderRadius = "5px";

  setTimeout(() => {
    notification.style.display = "none";
  }, 5000);
}

const emailInput = document.getElementById("email");
const emailErrorMessage = document.getElementById("email-error-message");

emailInput.addEventListener("blur", function () {
  const emailValue = emailInput.value;

  const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
  if (!emailRegex.test(emailValue)) {
    emailErrorMessage.style.display = "block";
    emailErrorMessage.textContent = "Please enter a valid email address.";

    setTimeout(function () {
      emailErrorMessage.style.display = "none";
    }, 5000);
  } else {
    emailErrorMessage.style.display = "none";
  }
});

const passwordField = document.getElementById("password");
const passwordValidationMessage = document.getElementById(
  "password-validation-message"
);

function validatePasswordLength() {
  if (passwordField.value.length < 8) {
    passwordValidationMessage.style.display = "block";
    if (passwordField.value.length === 0) {
      passwordValidationMessage.style.display = "none";
    }
  } else {
    passwordValidationMessage.style.display = "none";
  }
}

passwordField.addEventListener("input", validatePasswordLength);

const form = document.getElementById("registerForm");
form.addEventListener("submit", function (event) {
  if (passwordField.value.length < 8) {
    event.preventDefault();
    passwordValidationMessage.style.display = "block";
  }
});

const showPasswordCheckbox = document.getElementById("show-password");
const confirmPasswordField = document.getElementById("confirm_password");

showPasswordCheckbox.addEventListener("change", function () {
  if (this.checked) {
    passwordField.type = "text";
    confirmPasswordField.type = "text";
  } else {
    passwordField.type = "password";
    confirmPasswordField.type = "password";
  }
});

const password = document.getElementById("password");
const confirmPassword = document.getElementById("confirm_password");
const errorMessage = document.getElementById("error-message");

form.addEventListener("submit", function (event) {
  if (password.value !== confirmPassword.value) {
    errorMessage.style.display = "block";
    event.preventDefault();

    setTimeout(function () {
      errorMessage.style.display = "none";
    }, 5000);
  } else {
    errorMessage.style.display = "none";
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

const modal = document.getElementById("successModal");
const closeBtn = document.getElementsByClassName("close-btn")[0];

function showModal() {
  modal.style.display = "block";
}

closeBtn.addEventListener("click", function () {
  modal.style.display = "none";
});

window.addEventListener("click", function (event) {
  if (event.target === modal) {
    modal.style.display = "none";
  }
});

form.addEventListener("submit", function (event) {
  if (password.value !== confirmPassword.value) {
    errorMessage.style.display = "block";
    event.preventDefault();
  } else {
    errorMessage.style.display = "none";
    showModal();
    event.preventDefault();
  }
});
