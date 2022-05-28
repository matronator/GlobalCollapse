import UIkit from "uikit"
import Icons from "uikit/dist/js/uikit-icons"
import NetteForms from "../../../vendor/nette/forms/src/assets/netteForms.js"
import Choices from "choices.js"
import {
  notificationFailure,
  notificationSuccess,
  choicesOptions
} from "./imports/settings"
import { toggle } from "./imports/helpers"
import "./../../../app/modules/Front/components/Assault/PlayerPopover.js"
import { timesToRelative } from "./imports/dates"

// UIKit
UIkit.use(Icons)

// nette forms
NetteForms.initOnLoad()

// relative times
timesToRelative()

window.addEventListener(`DOMContentLoaded`, () => {
  // Check if standalone
  if (!window.matchMedia(`(display-mode: standalone)`).matches) {
    const alreadyShown = localStorage.getItem(`pwaPromptShown`)
    let showAgain = true
    if (alreadyShown) {
      const currentTime = new Date().getTime()
      const duration = 7 * 24 * 60 * 60 * 1000 // 7 days 24 h 60 min 60 sec 1000 milisec = 1 week
      if (currentTime - alreadyShown <= duration) {
        showAgain = false
      }
    }
    if (showAgain) {
      const pwaPrompt = document.getElementById(`pwaPrompt`)
      pwaPrompt.classList.add(`shown`)
      localStorage.setItem(`pwaPromptShown`, new Date().getTime())
      pwaPrompt.addEventListener(`click`, () => {
        pwaPrompt.classList.remove(`shown`)
        setTimeout(() => {
          pwaPrompt.remove()
        }, 500)
      })
    }
  }
  // sortable
  UIkit.util.on(
    ".js-sortable",
    "moved",
    ({
      target: {
        children,
        dataset: { callback }
      }
    }) => {
      const idList = [...children].map(el => el.id)
      const req = new XMLHttpRequest()
      req.open("GET", `${callback}&idList=${idList}`)
      req.addEventListener("load", () => {
        if (req.readyState === 4 && req.status === 200) {
          return UIkit.notification(notificationSuccess)
        }
        return UIkit.notification(notificationFailure)
      })
      req.addEventListener("error", () =>
        UIkit.notification(notificationFailure)
      )
      req.send()
    }
  )

  // multiselect
  const multies = document.querySelectorAll(`.js-select`)
  multies.forEach(multi => new Choices(multi, choicesOptions(multi)))

  // toggle logic
  const togglers = document.querySelectorAll(`[data-toggler]`)
  ;[...togglers].forEach(toggler =>
    toggler.addEventListener(`change`, () => toggle(togglers))
  )
  toggle(togglers)

  // Counter
  // function covidCounter() {
  //   const infected = document.getElementById("covidInfected")
  //   const dead = document.getElementById("covidDead")
  //   const multiplier = { infected: 1500, dead: 15000 }
  //   const urls = [
  //     "https://coronavirus-19-api.herokuapp.com/all",
  //     "https://corona.lmao.ninja/all"
  //   ]
  //   const take = 0
  //   const requestUrl = urls[take]

  //   const request = new XMLHttpRequest()
  //   request.open("GET", requestUrl, true)

  //   request.onload = function() {
  //     if (this.status >= 200 && this.status < 400) {
  //       // Success!
  //       const data = JSON.parse(this.response)
  //       infected.innerHTML = new Intl.NumberFormat().format(
  //         data.cases * multiplier.infected
  //       )
  //       dead.innerHTML = new Intl.NumberFormat().format(
  //         data.deaths * multiplier.dead
  //       )
  //     }
  //   }

  //   request.onerror = function() {
  //     // There was a connection error of some sort
  //   }

  //   request.send()
  // }
  // covidCounter()
})
