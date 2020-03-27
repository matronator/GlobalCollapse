/* eslint-disable prefer-template */
window.addEventListener(`DOMContentLoaded`, () => {
  // Disable sidebar links
  const sidebarLinks = document.querySelectorAll(`.sidebar-main a`)
  sidebarLinks.forEach(link => {
    if (link.getAttribute(`href`) !== `#`) {
      link.setAttribute(`href`, `#`)
    } else if (link.classList.contains(`uk-nav-header`)) {
      link.parentNode.classList.add(`uk-open`)
    }
  })

  // Stats
  let extraStats = 4
  const realStats = {
    strength: 3,
    stamina: 7,
    speed: 2
  }
  const baseStats = {
    strength: 3,
    stamina: 7,
    speed: 2
  }
  const baseStatsMax = baseStats.strength + baseStats.stamina + baseStats.speed
  const pointsLeft = document.getElementById(`pointsLeft`)

  // Next section
  let currentSection = 1
  const nextButton = document.getElementById(`nextStep`)
  nextButton.addEventListener(`click`, () => {
    if (currentSection <= 2) {
      const steps = document.querySelectorAll(`[data-step-id]`)
      const sectionActiveID = parseInt(
        document
          .querySelector(`[data-step-id].is-current`)
          .getAttribute(`data-step-id`)
      )
      const sectionNewID = sectionActiveID + 1
      currentSection += 1
      if (currentSection === 2) {
        nextButton.disabled = true
      }
      steps.forEach(step => {
        if (sectionNewID === parseInt(step.dataset.stepId)) {
          step.classList.add(`is-current`)
          step.classList.remove(`is-hidden`)
        } else {
          step.classList.remove(`is-current`)
          step.classList.add(`is-hidden`)
        }
      })
    } else if (currentSection === 3) {
      checkForm()
    }
  })
  // add stats
  function addStats(btn) {
    if (extraStats > 0) {
      extraStats -= 1
      pointsLeft.innerHTML = extraStats
      const stat = btn.dataset.addStat
      const totalStat = document.querySelector(
        '[data-total-stat="' + stat + '"]'
      )
      const extraStat = document.querySelector(
        '[data-extra-stat="' + stat + '"]'
      )
      const currentStat = parseInt(totalStat.innerHTML)
      const added = currentStat + 1
      realStats[stat] = added
      const diff = added - baseStats[stat]
      totalStat.innerHTML = added
      extraStat.innerHTML = diff
    }
  }
  // remove stats
  function removeStats(btn) {
    const stat = btn.dataset.removeStat
    const extraStat = document.querySelector('[data-extra-stat="' + stat + '"]')
    if (extraStats < 4 && parseInt(extraStat.innerHTML) > 0) {
      const totalStat = document.querySelector(
        '[data-total-stat="' + stat + '"]'
      )
      extraStats += 1
      pointsLeft.innerHTML = extraStats
      const currentStat = parseInt(totalStat.innerHTML)
      const added = currentStat - 1
      realStats[stat] = added
      const diff = added - baseStats[stat]
      totalStat.innerHTML = added
      extraStat.innerHTML = diff
    }
  }
  const addButtons = document.querySelectorAll(`.addpoint`)
  addButtons.forEach(btn => {
    btn.addEventListener(`click`, () => {
      addStats(btn)
      if (extraStats === 0) {
        nextButton.disabled = false
      } else {
        nextButton.disabled = true
      }
    })
  })
  const removeButtons = document.querySelectorAll(`.removepoint`)
  removeButtons.forEach(btn => {
    btn.addEventListener(`click`, () => {
      removeStats(btn)
      if (extraStats === 0) {
        nextButton.disabled = false
      } else {
        nextButton.disabled = true
      }
    })
  })

  // validate
  function checkForm() {
    let valid = false
    if (
      realStats.strength >= 3 &&
      realStats.stamina >= 7 &&
      realStats.speed >= 2
    ) {
      if (
        realStats.strength < 8 &&
        realStats.stamina < 12 &&
        realStats.speed < 7
      ) {
        const realStatsTotal =
          realStats.strength + realStats.stamina + realStats.speed
        const totalDiff = realStatsTotal - 4
        if (totalDiff === baseStatsMax) {
          valid = true
          submitForm()
        }
      }
    }
    if (valid === false) {
      alert(`There's something fishy with your stats... Try again.`)
      location.reload(true)
    }
  }

  // submit
  function submitForm() {
    const hiddenPower = document.querySelector('[data-stat-hidden="strength"]')
    const hiddenStamina = document.querySelector('[data-stat-hidden="stamina"]')
    const hiddenSpeed = document.querySelector('[data-stat-hidden="speed"]')
    hiddenPower.value = realStats.strength
    hiddenStamina.value = realStats.stamina
    hiddenSpeed.value = realStats.speed
    const form = document.getElementById(`introForm`)
    form.submit()
  }
})
