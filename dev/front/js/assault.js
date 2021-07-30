import { Howl, Howler } from "howler"
/**
 * @typedef sfx
 * @property {Howl[]} hit
 * @property {Howl[]} miss
 * @property {string} baseURL
 */

/**
 * @type {sfx}
 */
const sfx = {
  hit: [],
  miss: [],
  baseURL: `http://${location.host}/dist/front/etc/audio/assaults/`
}

for (let i = 1; i <= 3; i++) {
  sfx.hit[i] = new Howl({
    src: [`${sfx.baseURL}hit-${i}.webm`, `${sfx.baseURL}hit-${i}.mp3`]
  })
  if (i < 3) {
    sfx.miss[i] = new Howl({
      src: [`${sfx.baseURL}miss-${i}.webm`, `${sfx.baseURL}miss-${i}.mp3`]
    })
  }
}

const totalRounds = document.querySelector(`[data-assault-rounds]`).dataset
  .assaultRounds

let thisRound = 1
const timeouts = []
const rounds = document.querySelectorAll(`[data-assault-round]`)
const skipBtn = document.getElementById(`assaultSkip`)
const attackerAvatar = document.querySelector(
  `.assault-avatar.assault-attacker`
)
const victimAvatar = document.querySelector(`.assault-avatar.assault-victim`)
let canRun = true

// Attacker animation
const attackerDmgSpan = document.getElementById(`attackerDmg`)
attackerDmgSpan.addEventListener("animationend", () => {
  attackerDmgSpan.classList.remove("dmg-hit-attacker")
  const currentRound = document.querySelector(
    `[data-assault-round="${thisRound}"]`
  )
  const { victimHp } = currentRound.querySelector(
    `.attacker-round[data-attacker-round="${thisRound}"]`
  ).dataset
  const aHpBar = document.getElementById(`bar-attackerHp`)
  const aHpBarSpan = document.getElementById(`barTextValue-attackerHp`)
  const aHpBarFill = aHpBar.querySelector(`#bar-attackerHp .progress-bar-fill`)
  halfTime(victimHp, thisRound, aHpBar, aHpBarSpan, aHpBarFill, currentRound)
})

// Victim animation
const victimDmgSpan = document.getElementById(`victimDmg`)
victimDmgSpan.addEventListener("animationend", () => {
  victimDmgSpan.classList.remove("dmg-hit-victim")
  const currentRound = document.querySelector(
    `[data-assault-round="${thisRound}"]`
  )
  const { attackerHp } = currentRound.querySelector(
    `.victim-round[data-victim-round="${thisRound}"]`
  ).dataset
  if (attackerHp <= 0) {
    attackerAvatar.classList.add(`assault-dead`)
  }
  newRound(attackerHp, thisRound)
})

document.addEventListener(`DOMContentLoaded`, () => {
  if (rounds && totalRounds > 0) {
    playRound(thisRound)
  }

  skipBtn.addEventListener(`click`, () => {
    canRun = false
    for (let i = 0; i < timeouts.length; i++) {
      clearTimeout(timeouts[i])
    }
    const currentRound = document.querySelector(
      `[data-assault-round="${Number(totalRounds - 1)}"]`
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
    if (newBarFill <= 0) {
      newBarFill = 0
      attackersTurn()
      victimAvatar.classList.add(`assault-dead`)
    }
    vHpBar.dataset.barFill = newBarFill
    vHpBarFill.style.width = `${newBarFill}%`

    aHpBar.dataset.barValue = attackerHp
    aHpBarSpan.innerHTML = attackerHp
    let newBarFillA = Math.round(
      ((attackerHp - 0) / (Number(aHpBar.dataset.barMax) - 0)) * 100
    )
    if (newBarFillA <= 0) {
      newBarFillA = 0
      victimsTurn()
      attackerAvatar.classList.add(`assault-dead`)
    }
    aHpBar.dataset.barFill = newBarFillA
    aHpBarFill.style.width = `${newBarFillA}%`
  })
})

function playRound(current) {
  if (canRun) {
    timeouts.push(
      setTimeout(() => {
        attackersTurn()
      }, 750)
    )
    const vHpBar = document.getElementById(`bar-victimHp`)
    const vHpBarSpan = document.getElementById(`barTextValue-victimHp`)
    const vHpBarFill = vHpBar.querySelector(`#bar-victimHp .progress-bar-fill`)

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
            vHpBar,
            vHpBarSpan,
            vHpBarFill,
            victimHp,
            attackerDmg
          )
        }, 1250)
      )
    }
  }
}

function attackerHit(
  currentRound,
  vHpBar,
  vHpBarSpan,
  vHpBarFill,
  victimHp,
  attackerDmg
) {
  if (canRun) {
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

    attackerDmgSpan.innerHTML = `- ${attackerDmg}`
    attackerDmgSpan.classList.remove(`hidden`)
    attackerDmgSpan.classList.add(`dmg-hit-attacker`)
    victimDmgSpan.classList.add(`hidden`)

    if (attackerDmg > 0) {
      sfx.hit[Math.ceil(Math.random() * 3)].play()
    } else {
      sfx.miss[Math.ceil(Math.random() * 2)].play()
    }
  }
}

function victimHit(aHpBar, aHpBarSpan, aHpBarFill, attackerHp, victimDmg) {
  if (canRun) {
    attackerAvatar.classList.remove(`assault-player-current`)
    victimAvatar.classList.add(`assault-player-current`)
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

    victimDmgSpan.innerHTML = `- ${victimDmg}`
    victimDmgSpan.classList.remove(`hidden`)
    victimDmgSpan.classList.add(`dmg-hit-victim`)
    attackerDmgSpan.classList.add(`hidden`)

    if (victimDmg > 0) {
      sfx.hit[Math.ceil(Math.random() * 3)].play()
    } else {
      sfx.miss[Math.ceil(Math.random() * 2)].play()
    }
  }
}

function newRound(attackerHp, current) {
  if (attackerHp > 0) {
    thisRound = current + 1
    playRound(thisRound)
  } else {
    document.getElementById(`assaultResult`).classList.remove(`uk-hidden`)
    document.getElementById(`assaultDone`).classList.remove(`uk-hidden`)
    skipBtn.classList.add(`uk-hidden`)
  }
}
function halfTime(
  victimHp,
  current,
  aHpBar,
  aHpBarSpan,
  aHpBarFill,
  currentRound
) {
  if (canRun) {
    if (victimHp > 0) {
      timeouts.push(
        setTimeout(() => {
          victimsTurn()
        }, 750)
      )
      const { victimDmg, attackerHp } = currentRound.querySelector(
        `.victim-round[data-victim-round="${current}"]`
      ).dataset
      timeouts.push(
        setTimeout(() => {
          victimHit(
            aHpBar,
            aHpBarSpan,
            aHpBarFill,
            attackerHp,
            victimDmg,
            current
          )
        }, 1250)
      )
    } else {
      document.getElementById(`assaultResult`).classList.remove(`uk-hidden`)
      document.getElementById(`assaultDone`).classList.remove(`uk-hidden`)
      skipBtn.classList.add(`uk-hidden`)
      victimAvatar.classList.add(`assault-dead`)
    }
  }
}

function attackersTurn() {
  attackerAvatar.classList.add(`assault-player-current`)
  victimAvatar.classList.remove(`assault-player-current`)
}
function victimsTurn() {
  attackerAvatar.classList.remove(`assault-player-current`)
  victimAvatar.classList.add(`assault-player-current`)
}
