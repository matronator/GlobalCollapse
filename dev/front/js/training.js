import Push from "push.js"
import Timer from "easytimer.js"
import axette from "axette"

axette.init()

axette.onAjax(training)

function training() {
  document.addEventListener(`DOMContentLoaded`, () => {
    // countdown
    const hourSpan = document.querySelector(`.countdown-hours`)
    const minuteSpan = document.querySelector(`.countdown-minutes`)
    const secondSpan = document.querySelector(`.countdown-seconds`)
    var hours = Number(hourSpan.innerHTML)
    var minutes = Number(minuteSpan.innerHTML)
    var seconds = Number(secondSpan.innerHTML)

    const countdown = new Timer()

    countdown.start()
    countdown.addEventListener(`secondsUpdated`, e => {
      tick()
    })

    function tick() {
      if (seconds > 0) {
        seconds -= 1
      } else {
        seconds = 59
        if (minutes > 0) {
          minutes -= 1
        } else {
          minutes = 59
          hours -= 1
        }
      }
      if (hours <= 0 && minutes <= 0 && seconds < 1) {
        Push.create("Training done!")
        setTimeout(reloadWindow, 1000)
      }
      hourSpan.innerHTML = hours > 9 ? hours : `0${hours}`
      minuteSpan.innerHTML = minutes > 9 ? minutes : `0${minutes}`
      secondSpan.innerHTML = seconds > 9 ? seconds : `0${seconds}`
    }
    function reloadWindow() {
      window.location.replace(location.href)
    }
  })
}

training()
