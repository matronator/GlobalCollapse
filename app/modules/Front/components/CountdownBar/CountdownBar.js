import Push from "push.js"
import Timer from "easytimer.js"

const countdown = new Timer()

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

  countdown.start()
  countdown.addEventListener(`secondsUpdated`, e => {
    tick()
  })

  function tick() {
    if (seconds > 0) {
      seconds -= 1
    } else {
      seconds = 59
      minutes -= 1
    }
    totalSeconds -= 1
    const progressMax = Number(progressBar.dataset.barMax)
    const newBarFill = (totalSeconds / progressMax) * 100

    progressBar.dataset.barValue = totalSeconds
    progressBar.dataset.barFill = newBarFill
    progressBarFill.style.width = `${ newBarFill}%`
    secondSpan.innerHTML = seconds > 9 ? seconds : `0${ seconds}`
    minuteSpan.innerHTML = minutes > 9 ? minutes : `0${ minutes}`
    if (minutes <= 0 && seconds <= 0) {
      Push.create("Job finished!")
      setTimeout(reloadWindow, 2000)
    }
  }
  function reloadWindow() {
    window.location.reload(false)
  }
})
