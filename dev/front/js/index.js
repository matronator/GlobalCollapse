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

// UIKit
UIkit.use(Icons)

// nette forms
NetteForms.initOnLoad()

window.addEventListener(`DOMContentLoaded`, () => {
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
  const infected = document.getElementById("covidInfected")
  const dead = document.getElementById("covidDead")
  const multiplier = 1000

  const request = new XMLHttpRequest()
  request.open("GET", "https://coronavirus-19-api.herokuapp.com/all", true)

  request.onload = function() {
    if (this.status >= 200 && this.status < 400) {
      // Success!
      const data = JSON.parse(this.response)
      infected.innerHTML = new Intl.NumberFormat().format(
        data.cases * multiplier
      )
      dead.innerHTML = new Intl.NumberFormat().format(data.deaths * multiplier)
    } else {
      // We reached our target server, but it returned an error
    }
  }

  request.onerror = function() {
    // There was a connection error of some sort
  }

  request.send()
})
