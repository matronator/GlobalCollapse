import Push from "push.js"

const minuteSpan = document.getElementById(`cbMins`)
const secondSpan = document.getElementById(`cbSecs`)
const progressBar = document.querySelector(`.countdown-bar`)
const progressBarFill = progressBar.querySelector(`.countdown-bar-fill`)

setTimeout(() => {
  tick(minuteSpan.dataset.timerMinutes, secondSpan.dataset.timerSeconds)
}, 1000)

function tick(m, s) {
  let seconds = s
  let minutes = m
  if (seconds > 0) {
    seconds -= 1
  } else {
    seconds = 59
    minutes -= 1
  }
  updateTime(minutes, seconds)
}

function updateTime(m, s) {
  const mins = m
  const secs = s
  minuteSpan.dataset.timerMinutes = Number(mins)
  secondSpan.dataset.timerSeconds = Number(secs)
  const totalSeconds = mins * 60 + secs
  const progressMax = Number(progressBar.dataset.barMax)
  const newBarFill = (totalSeconds / progressMax) * 100

  progressBar.dataset.barValue = totalSeconds
  progressBar.dataset.barFill = newBarFill
  progressBarFill.style.width = `${newBarFill}%`
  minuteSpan.innerHTML = mins > 9 ? mins : `0${mins}`
  secondSpan.innerHTML = secs > 9 ? secs : `0${secs}`
  if (mins <= 0 && secs <= 0) {
    setTimeout(() => {
      jobDone()
    }, 1000)
  }

  setTimeout(() => {
    tick(mins, secs)
  }, 1000)
}

function jobDone() {
  Push.create("Job finished!")
  window.location.reload(true)
}

let myworker

function startWorker() {
  if (typeof Worker !== "undefined") {
    if (typeof myworker === "undefined") {
      myworker = new Worker(
        `${window.location.origin}/dist/front/etc/JobWorker.js`
      )
    }
    myworker.onmessage = function(event) {
      if (event.data[0] === "update") {
        updateTime(event.data[1], event.data[2])
      } else {
        stopWorker()
        jobDone()
      }
    }
  } else {
    console.log(`Sorry, your browser does not support Web Workers...`)
  }
}

function stopWorker() {
  myworker.terminate()
  myworker = undefined
}

startWorker()
