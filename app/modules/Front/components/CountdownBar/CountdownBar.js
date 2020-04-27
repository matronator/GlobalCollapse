import Push from "push.js"

const minuteSpan = document.getElementById(`cbMins`)
const secondSpan = document.getElementById(`cbSecs`)
const progressBar = document.querySelector(`.countdown-bar`)
const progressBarFill = progressBar.querySelector(`.countdown-bar-fill`)
/* eslint-disable no-var */
window.addEventListener(`DOMContentLoaded`, () => {
  // countdown
  window.setInterval(tick, 1000)

  function tick() {
    var minutes = Number(minuteSpan.innerHTML)
    var seconds = Number(secondSpan.innerHTML)
    if (seconds > 0) {
      seconds -= 1
    } else {
      seconds = 59
      minutes -= 1
    }
    updateTime(minutes, seconds)
    if (minutes <= 0 && seconds <= 0) {
      jobDone()
    }
  }
})

export function jobDone() {
  Push.create("Job finished!")
  setTimeout(window.location.reload(), 1000)
}

export function updateTime(m, s) {
  const totalSeconds = Number(m * 60 + s)
  const progressMax = Number(progressBar.dataset.barMax)
  const newBarFill = (totalSeconds / progressMax) * 100

  progressBar.dataset.barValue = totalSeconds
  progressBar.dataset.barFill = newBarFill
  progressBarFill.style.width = `${ newBarFill}%`
  minuteSpan.innerHTML = m > 9 ? m : `0${ m}`
  secondSpan.innerHTML = s > 9 ? s : `0${ s}`
}
