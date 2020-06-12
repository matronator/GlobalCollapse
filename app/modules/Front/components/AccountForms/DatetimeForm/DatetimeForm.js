const timezoneSelect = document.getElementById(`timezones`)

if (timezoneSelect) {
  document.addEventListener(`DOMContentLoaded`, () => {
    timezoneSelect.querySelectorAll(`option`).forEach(opt => {
      if (opt.selected) {
        opt.classList.add(`selected`)
      }
    })
  })

  timezoneSelect.addEventListener(`change`, e => {
    timezoneSelect.querySelectorAll(`option`).forEach(opt => {
      if (!opt.selected) {
        opt.classList.remove(`selected`)
      } else {
        opt.classList.add(`selected`)
      }
    })
  })
}
