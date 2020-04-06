/* eslint-disable no-var */
window.addEventListener(`DOMContentLoaded`, () => {
  // Stats
  const pointsLeft = document.getElementById(`pointsLeft`)
  const baseExtraStats = pointsLeft.innerHTML
  let extraStats = pointsLeft.innerHTML
  const strengthVal = document.getElementById(`hidden-1`)
  const staminaVal = document.getElementById(`hidden-2`)
  const speedVal = document.getElementById(`hidden-3`)
  const usedSp = document.getElementById(`hidden-4`)
  let usedSpoints = 0
  const extraStatsVal = {
    strength: parseInt(strengthVal.value),
    stamina: parseInt(staminaVal.value),
    speed: parseInt(speedVal.value)
  }
  const realStats = {
    strength: parseInt(document.getElementById(`hidden-1`).value),
    stamina: parseInt(document.getElementById(`hidden-2`).value),
    speed: parseInt(document.getElementById(`hidden-3`).value)
  }
  const baseStats = {
    strength: parseInt(document.getElementById(`hidden-1`).value),
    stamina: parseInt(document.getElementById(`hidden-2`).value),
    speed: parseInt(document.getElementById(`hidden-3`).value)
  }
  const baseStatsMax = baseStats.strength + baseStats.stamina + baseStats.speed

  // add stats
  function addStats(btn) {
    if (extraStats > 0) {
      extraStats -= 1
      pointsLeft.innerHTML = extraStats
      const stat = btn.dataset.addStat
      const totalStat = document.querySelector(`[data-total-stat="${stat}"]`)
      const extraStat = document.querySelector(`[data-extra-stat="${stat}"]`)
      const currentStat = parseInt(totalStat.innerHTML)
      const added = currentStat + 1
      realStats[stat] = added
      totalStat.innerHTML = added
      usedSpoints += 1
      extraStatsVal[stat] += 1
      document.querySelector(`[data-stat-hidden="${stat}"]`).value =
        extraStatsVal[stat]
      extraStat.innerHTML = extraStatsVal[stat]
      usedSp.value = usedSpoints
      usedSp.dataset.extraValue = usedSpoints
    }
  }
  // remove stats
  function removeStats(btn) {
    const stat = btn.dataset.removeStat
    const extraStat = document.querySelector(`[data-extra-stat="${stat}"]`)
    if (extraStats < baseExtraStats && parseInt(extraStat.innerHTML) > 0) {
      const totalStat = document.querySelector(`[data-total-stat="${stat}"]`)
      extraStats += 1
      pointsLeft.innerHTML = extraStats
      const currentStat = parseInt(totalStat.innerHTML)
      const added = currentStat - 1
      realStats[stat] = added
      totalStat.innerHTML = added
      usedSpoints -= 1
      extraStatsVal[stat] -= 1
      document.querySelector(`[data-stat-hidden="${stat}"]`).value =
        extraStatsVal[stat]
      extraStat.innerHTML = extraStatsVal[stat]
      usedSp.value = usedSpoints
      usedSp.dataset.extraValue = usedSpoints
    }
  }
  const addButtons = document.querySelectorAll(`.addpoint`)
  addButtons.forEach(btn => {
    btn.addEventListener(`click`, () => {
      addStats(btn)
    })
  })
  const removeButtons = document.querySelectorAll(`.removepoint`)
  removeButtons.forEach(btn => {
    btn.addEventListener(`click`, () => {
      removeStats(btn)
    })
  })

  const resetBtn = document.getElementById(`btnReset`)
  resetBtn.addEventListener(`click`, () => {
    resetForm()
  })

  function resetForm() {
    window.location.reload(false)
  }

  // validate
  function checkForm() {
    let valid = false
    if (
      realStats.strength >= baseStats.strength &&
      realStats.stamina >= baseStats.stamina &&
      realStats.speed >= baseStats.speed
    ) {
      if (
        realStats.strength <= baseStats.strength + baseExtraStats &&
        realStats.stamina <= baseStats.stamina + baseExtraStats &&
        realStats.speed <= baseStats.speed + baseExtraStats
      ) {
        const realStatsTotal =
          realStats.strength + realStats.stamina + realStats.speed
        const baseStatsTotal =
          baseStats.strength + baseStats.stamina + baseStats.speed
        const statsUsed = baseStatsTotal - extraStats
        if (statsUsed > 0) {
          const totalDiff = realStatsTotal - statsUsed
          if (totalDiff === baseStatsMax) {
            valid = true
            submitForm()
          }
        } else {
          return false
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
    const form = document.getElementById(`skillpointsForm`)
    form.submit()
  }
})
