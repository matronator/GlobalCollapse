/* eslint-disable no-var */
window.addEventListener(`DOMContentLoaded`, () => {
  // countdown
  const hourSpan = document.querySelector(`.countdown-hours`)
  const minuteSpan = document.querySelector(`.countdown-minutes`)
  const secondSpan = document.querySelector(`.countdown-seconds`)
  var hours = Number(hourSpan.innerHTML)
  var minutes = Number(minuteSpan.innerHTML)
  var seconds = Number(secondSpan.innerHTML)

  setInterval(tick, 1000)

  function tick() {
    if (seconds > 0) {
      seconds -= 1
    } else {
      seconds = 59
      minutes -= 1
      if (minutes > 0) {
        minutes -= 1
      } else {
        minutes = 59
        hours -= 1
      }
    }
    if (hours <= 0 && minutes <= 0 && seconds <= 0) {
      setTimeout(reloadWindow, 2000)
    }
    hourSpan.innerHTML = hours > 9 ? hours : `0${hours}`
    minuteSpan.innerHTML = minutes > 9 ? minutes : `0${minutes}`
    secondSpan.innerHTML = seconds > 9 ? seconds : `0${seconds}`
  }
  function reloadWindow() {
    window.location.reload(false)
  }
})
