/* eslint-disable no-var */
window.addEventListener(`DOMContentLoaded`, () => {
  // countdown
  const minuteSpan = document.getElementById(`cbMins`)
  const secondSpan = document.getElementById(`cbSecs`)
  const progressBar = document.querySelector(`.countdown-bar`)
  const progressBarFill = progressBar.querySelector(`.countdown-bar-fill`)

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
      }
    }
    if (minutes <= 0 && seconds <= 0) {
      setTimeout(reloadWindow, 2000)
    }
    const progressMax = Number(progressBar.dataset.barMax)
    const totalSeconds = seconds + minutes * 60
    const newBarFill = ((totalSeconds - 0) / (progressMax - 0)) * 100

    progressBar.dataset.barValue = totalSeconds
    progressBar.dataset.barFill = newBarFill
    progressBarFill.style.width = `${ newBarFill}%`
    minuteSpan.innerHTML = minutes > 9 ? minutes : `0${minutes}`
    secondSpan.innerHTML = seconds > 9 ? seconds : `0${seconds}`
  }
  function reloadWindow() {
    window.location.reload(false)
  }
})
