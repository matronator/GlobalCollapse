import UIkit from "../../front/js/uikit/uikit"
import Icons from "../../front/js/uikit/uikit-icons"
import NetteForms from "../../../vendor/nette/forms/src/assets/netteForms.js"
import ClassicEditor from "@ckeditor/ckeditor5-build-classic"
import Choices from "choices.js"
import flatpickr from "flatpickr"
import { Czech } from "flatpickr/dist/l10n/cs"
import {
  notificationFailure,
  notificationSuccess,
  choicesOptions
} from "./imports/settings"
import { toggle } from "./imports/helpers"
import { Axette } from "axette";

const axette = new Axette('.ajax');
axette.onAfterAjax(() => {
  NetteForms.initOnLoad();
});

// UIKit
UIkit.use(Icons)

// nette forms
NetteForms.initOnLoad()

document.addEventListener(`DOMContentLoaded`, () => {
  // ckfinder
  const fields = document.querySelectorAll(".js-wysiwyg")
  fields.forEach(field => {
    ClassicEditor.create(field).catch(err => console.error(err.stack))
  })

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

  // date picker
  flatpickr(`.js-date`, { locale: Czech })

  // toggle logic
  const togglers = document.querySelectorAll(`[data-toggler]`)
  ;[...togglers].forEach(toggler =>
    toggler.addEventListener(`change`, () => toggle(togglers))
  )
  toggle(togglers)
})
