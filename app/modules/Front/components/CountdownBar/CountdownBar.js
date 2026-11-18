import Push from "push.js"

/* eslint-disable no-param-reassign */
const minuteSpan = document.getElementById(`cbMins`)
const secondSpan = document.getElementById(`cbSecs`)
const progressBar = document.querySelector(`.countdown-bar`)
const progressBarFill = progressBar.querySelector(`.countdown-bar-fill`)
const progressMax = Number(progressBar.dataset.barMax)
let timer
let completed = false

tick(minuteSpan.dataset.timerMinutes, secondSpan.dataset.timerSeconds)

function tick(m, s) {
  m = Math.max(0, Number(m) || 0)
  s = Math.max(0, Number(s) || 0)

  if (m <= 0 && s <= 0) {
    minuteSpan.dataset.timerMinutes = 0
    secondSpan.dataset.timerSeconds = 0
    updateTime()
    return
  }

  if (s > 0) {
    s -= 1
  } else {
    s = 59
    m -= 1
  }
  minuteSpan.dataset.timerMinutes = m
  secondSpan.dataset.timerSeconds = s
  updateTime()
  if (m > 0 || s > 0) {
    timer = setTimeout(() => {
      tick(m, s)
    }, 1000)
  }
}

function updateTime() {
  const mins = Number(minuteSpan.dataset.timerMinutes)
  const secs = Number(secondSpan.dataset.timerSeconds)
  const totalSeconds = Math.max(0, mins * 60 + secs)
  const newBarFill =
    progressMax > 0
      ? Math.min(100, Math.max(0, (totalSeconds / progressMax) * 100))
      : 0

  progressBar.dataset.barValue = totalSeconds
  progressBar.dataset.barFill = newBarFill
  progressBarFill.style.width = `${newBarFill}%`
  minuteSpan.innerHTML = mins > 9 ? mins : `0${mins}`
  secondSpan.innerHTML = secs > 9 ? secs : `0${secs}`
  if (totalSeconds <= 0 && !completed) {
    completed = true
    clearTimeout(timer)
    Push.create("Job finished!", {
      body: "You have returned from your mission.",
      icon: `${window.location.origin}/dist/front/images/favicon-32x32.png`,
      timeout: 3000
    })
    setTimeout(() => {
      jobDone()
    }, 3000)
  }
}

function jobDone() {
  window.location.reload()
}

let myworker

function startWorker() {
  if (typeof Worker !== "undefined") {
    if (typeof myworker === "undefined") {
      myworker = new Worker(
        `${window.location.origin}/dist/front/etc/JobWorker.js`,
        {
          type: "module"
        }
      )
    }
    myworker.onmessage = function(event) {
      if (event.data[0] === "update") {
        clearTimeout(timer)
        tick(event.data[1], event.data[2])
      } else {
        jobDone()
        stopWorker()
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
