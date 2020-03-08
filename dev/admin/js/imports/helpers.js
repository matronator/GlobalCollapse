export function toggle(togglers) {
  Object.entries(
    [...togglers].reduce((togglerGroups, toggler) => {
      const id = toggler.dataset.toggler || `reset`
      const group = togglerGroups[id]
      if (group) {
        group.push(toggler)
      } else {
        togglerGroups[id] = [toggler]
      }
      return togglerGroups
    }, {})
  ).forEach(([key, togglers]) => {
    const target = document.querySelector(`[data-toggle="${key}"]`)
    if (togglers.some(toggler => toggler.checked)) {
      target
        ? target.classList.remove(`uk-hidden`)
        : [...document.querySelectorAll(`[data-toggle]`)].forEach(toggle =>
            toggle.classList.remove(`uk-hidden`)
          )
    } else {
      target && target.classList.add(`uk-hidden`)
    }
  })
}
