import ajaxette from "ajaxette"

ajaxette.init()

ajaxette.onAjaxHook(registerEventHandlers)

registerEventHandlers()
function registerEventHandlers() {
  const infoToggles = document.querySelectorAll(`[data-toggle-info]`)
  if (infoToggles) {
    infoToggles.forEach(toggle => {
      toggle.addEventListener(`click`, e => {
        const infoBlock = document.querySelector(
          `[data-building-info="${toggle.dataset.toggleInfo}"]`
        )
        infoBlock.classList.toggle(`block-hidden`)
        window.dispatchEvent(new Event(`resize`))
      })
    })
  }

  const buildButtons = document.querySelectorAll(`[data-build-button]`)
  if (buildButtons) {
    buildButtons.forEach(btn => {
      btn.addEventListener(`click`, e => {
        const list = btn.parentElement.querySelector(`[data-build-list]`)
        list.classList.toggle(`block-hidden`)
        const listAll = document.querySelectorAll(`[data-build-list]`)
        listAll.forEach(item => {
          if (item.dataset.buildList !== btn.dataset.buildButton) {
            item.classList.add(`block-hidden`)
          }
        })
      })
    })
  }
}
