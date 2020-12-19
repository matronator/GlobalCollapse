import Push from "push.js"

/* eslint-disable no-param-reassign */
const minuteSpan = document.getElementById(`cbMins`)
const secondSpan = document.getElementById(`cbSecs`)
const progressBar = document.querySelector(`.countdown-bar`)
const progressBarFill = progressBar.querySelector(`.countdown-bar-fill`)
const progressMax = Number(progressBar.dataset.barMax)
let timer

tick(minuteSpan.dataset.timerMinutes, secondSpan.dataset.timerSeconds)

function tick(m, s) {
  if (s > 0) {
    s -= 1
  } else {
    s = 59
    m -= 1
  }
  minuteSpan.dataset.timerMinutes = m
  secondSpan.dataset.timerSeconds = s
  updateTime()
  timer = setTimeout(() => {
    tick(m, s)
  }, 1000)
}

function updateTime() {
  const mins = Number(minuteSpan.dataset.timerMinutes)
  const secs = Number(secondSpan.dataset.timerSeconds)
  const totalSeconds = mins * 60 + secs
  const newBarFill = (totalSeconds / progressMax) * 100

  progressBar.dataset.barValue = totalSeconds
  progressBar.dataset.barFill = newBarFill
  progressBarFill.style.width = `${newBarFill}%`
  minuteSpan.innerHTML = mins > 9 ? mins : `0${mins}`
  secondSpan.innerHTML = secs > 9 ? secs : `0${secs}`
  if (mins <= 0 && secs <= 0) {
    clearTimeout(timer)
    Push.create("Job finished!", {
      body: "How's it hangin'?",
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
