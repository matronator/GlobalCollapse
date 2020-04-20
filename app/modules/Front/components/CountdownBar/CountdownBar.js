/* eslint-disable no-var */
window.addEventListener(`DOMContentLoaded`, () => {
  // countdown
  const minuteSpan = document.getElementById(`cbMins`)
  const secondSpan = document.getElementById(`cbSecs`)
  const progressBar = document.querySelector(`.countdown-bar`)
  const progressBarFill = progressBar.querySelector(`.countdown-bar-fill`)

  var minutes = Number(minuteSpan.innerHTML)
  var seconds = Number(secondSpan.innerHTML)
  let totalSeconds = seconds + minutes * 60

  setInterval(tick, 1000)

  function tick() {
    if (seconds > 0) {
      seconds -= 1
    } else {
      seconds = 59
      minutes -= 1
    }
    if (minutes <= 0 && seconds <= 0) {
      setTimeout(reloadWindow, 2000)
    }
    totalSeconds -= 1
    const progressMax = Number(progressBar.dataset.barMax)
    const newBarFill = (totalSeconds / progressMax) * 100

    progressBar.dataset.barValue = totalSeconds
    progressBar.dataset.barFill = newBarFill
    progressBarFill.style.width = `${ newBarFill}%`
    secondSpan.innerHTML = seconds > 9 ? seconds : `0${ seconds}`
    minuteSpan.innerHTML = minutes > 9 ? minutes : `0${ minutes}`
  }
  function reloadWindow() {
    window.location.reload(false)
  }
})
