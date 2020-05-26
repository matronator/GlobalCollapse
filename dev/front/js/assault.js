const totalRounds = document.querySelector(`[data-assault-rounds]`).dataset
  .assaultRounds

const timeouts = []
const rounds = document.querySelectorAll(`[data-assault-round]`)
const skipBtn = document.getElementById(`assaultSkip`)

document.addEventListener(`DOMContentLoaded`, () => {
  if (rounds && totalRounds > 0) {
    playRound(1)
  }

  skipBtn.addEventListener(`click`, () => {
    for (let i = 0; i < timeouts.length; i++) {
      clearTimeout(timeouts[i])
    }
    const currentRound = document.querySelector(
      `[data-assault-round="${totalRounds - 1}"]`
    )
    skipBtn.classList.add(`uk-hidden`)
    currentRound.classList.remove(`uk-hidden`)
    document.getElementById(`assaultResult`).classList.remove(`uk-hidden`)
    document.getElementById(`assaultDone`).classList.remove(`uk-hidden`)
    const vHpBar = document.getElementById(`bar-victimHp`)
    const vHpBarSpan = document.getElementById(`barTextValue-victimHp`)
    const vHpBarFill = vHpBar.querySelector(`#bar-victimHp .progress-bar-fill`)

    const aHpBar = document.getElementById(`bar-attackerHp`)
    const aHpBarSpan = document.getElementById(`barTextValue-attackerHp`)
    const aHpBarFill = aHpBar.querySelector(
      `#bar-attackerHp .progress-bar-fill`
    )
    const { victimHp } = currentRound.querySelector(
      `.attacker-round[data-attacker-round="${totalRounds - 1}"]`
    ).dataset
    const { attackerHp } = currentRound.querySelector(
      `.victim-round[data-victim-round="${totalRounds - 1}"]`
    ).dataset
    vHpBar.dataset.barValue = victimHp
    vHpBarSpan.innerHTML = victimHp
    let newBarFill = Math.round(
      ((victimHp - 0) / (Number(vHpBar.dataset.barMax) - 0)) * 100
    )
    if (newBarFill < 0) {
      newBarFill = 0
    }
    vHpBar.dataset.barFill = newBarFill
    vHpBarFill.style.width = `${newBarFill}%`

    aHpBar.dataset.barValue = attackerHp
    aHpBarSpan.innerHTML = attackerHp
    let newBarFillA = Math.round(
      ((attackerHp - 0) / (Number(aHpBar.dataset.barMax) - 0)) * 100
    )
    if (newBarFillA < 0) {
      newBarFillA = 0
    }
    aHpBar.dataset.barFill = newBarFillA
    aHpBarFill.style.width = `${newBarFillA}%`
  })
})

function playRound(current) {
  const vHpBar = document.getElementById(`bar-victimHp`)
  const vHpBarSpan = document.getElementById(`barTextValue-victimHp`)
  const vHpBarFill = vHpBar.querySelector(`#bar-victimHp .progress-bar-fill`)

  const aHpBar = document.getElementById(`bar-attackerHp`)
  const aHpBarSpan = document.getElementById(`barTextValue-attackerHp`)
  const aHpBarFill = aHpBar.querySelector(`#bar-attackerHp .progress-bar-fill`)

  if (current < totalRounds) {
    const currentRound = document.querySelector(
      `[data-assault-round="${current}"]`
    )
    const { attackerDmg, victimHp } = currentRound.querySelector(
      `.attacker-round[data-attacker-round="${current}"]`
    ).dataset

    timeouts.push(
      setTimeout(() => {
        attackerHit(
          currentRound,
          current,
          vHpBar,
          vHpBarSpan,
          vHpBarFill,
          victimHp,
          aHpBar,
          aHpBarSpan,
          aHpBarFill
        )
      }, 2000)
    )
  }
}

function attackerHit(
  currentRound,
  current,
  vHpBar,
  vHpBarSpan,
  vHpBarFill,
  victimHp,
  aHpBar,
  aHpBarSpan,
  aHpBarFill
) {
  vHpBar.dataset.barValue = victimHp
  vHpBarSpan.innerHTML = victimHp
  let newBarFill = Math.round(
    ((victimHp - 0) / (Number(vHpBar.dataset.barMax) - 0)) * 100
  )
  if (newBarFill < 0) {
    newBarFill = 0
  }
  vHpBar.dataset.barFill = newBarFill
  vHpBarFill.style.width = `${newBarFill}%`

  rounds.forEach(round => {
    round.classList.add(`uk-hidden`)
  })
  currentRound.classList.remove(`uk-hidden`)

  if (victimHp > 0) {
    const { victimDmg, attackerHp } = currentRound.querySelector(
      `.victim-round[data-victim-round="${current}"]`
    ).dataset

    timeouts.push(
      setTimeout(() => {
        victimHit(aHpBar, aHpBarSpan, aHpBarFill, attackerHp, current)
      }, 2000)
    )
  } else {
    document.getElementById(`assaultResult`).classList.remove(`uk-hidden`)
    document.getElementById(`assaultDone`).classList.remove(`uk-hidden`)
    skipBtn.classList.add(`uk-hidden`)
  }
}

function victimHit(aHpBar, aHpBarSpan, aHpBarFill, attackerHp, current) {
  aHpBar.dataset.barValue = attackerHp
  aHpBarSpan.innerHTML = attackerHp
  let newBarFillA = Math.round(
    ((attackerHp - 0) / (Number(aHpBar.dataset.barMax) - 0)) * 100
  )
  if (newBarFillA < 0) {
    newBarFillA = 0
  }
  aHpBar.dataset.barFill = newBarFillA
  aHpBarFill.style.width = `${newBarFillA}%`

  if (attackerHp > 0) {
    playRound(current + 1)
  } else {
    document.getElementById(`assaultResult`).classList.remove(`uk-hidden`)
    document.getElementById(`assaultDone`).classList.remove(`uk-hidden`)
    skipBtn.classList.add(`uk-hidden`)
  }
}
