window.addEventListener(`DOMContentLoaded`, () => {
  const sidebarLinks = document.querySelectorAll(`.sidebar-main a`)
  sidebarLinks.forEach(link => {
    if (link.getAttribute(`href`) !== `#`) {
      link.setAttribute(`href`, `#`)
    } else if (link.classList.contains(`uk-nav-header`)) {
      link.parentNode.classList.add(`uk-open`)
    }
  })

  let currentSection = 1
  const nextButton = document.getElementById(`nextStep`)

  nextButton.addEventListener(`click`, () => {
    if (currentSection < 3) {
      const steps = document.querySelectorAll(`[data-step-id]`)
      const sectionActiveID = parseInt(
        document
          .querySelector(`[data-step-id].is-current`)
          .getAttribute(`data-step-id`)
      )
      const sectionNewID = sectionActiveID + 1
      currentSection += 1
      steps.forEach(step => {
        if (sectionNewID === parseInt(step.dataset.stepId)) {
          step.classList.add(`is-current`)
          step.classList.remove(`is-hidden`)
        } else {
          step.classList.remove(`is-current`)
          step.classList.add(`is-hidden`)
        }
      })
    }
  })
})
