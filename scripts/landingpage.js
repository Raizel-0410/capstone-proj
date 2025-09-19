window.addEventListener("scroll", function () {
  const nav = document.querySelector(".breadcrumb");
  if (window.scrollY > 50) {
    nav.classList.add("scrolled");
  } else {
    nav.classList.remove("scrolled");
  }
});


let count = 0;
const counter = document.getElementById("counter");
const digits = counter.querySelectorAll("span");

function updateCounter(number) {
  const str = number.toString().padStart(digits.length, "0");
  digits.forEach((digit, i) => {
    digit.textContent = str[i];
  });
}

setInterval(() => {
  count++;
  updateCounter(count);
}, 5000);

updateCounter(count);
