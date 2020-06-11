const jobs = document.querySelectorAll(`[data-job-name]`)
const duration = document.getElementById(`cost-duration`)
const energy = document.getElementById(`cost-energy`)
const money = document.getElementById(`reward-money`)
const xp = document.getElementById(`reward-xp`)
const description = document.getElementById(`jobDescription`)
const progressBar = document.getElementById(`bar-energyBar`)
const progressBarValSpan = document.getElementById(`barTextValue-energyBar`)
const progressBarXp = document.getElementById(`bar-jobXp`)
const progressBarValSpanXp = document.getElementById(`barTextValue-jobXp`)

if (jobs && duration && description && progressBar && progressBarXp && money) {
  const progressBarFill = progressBar.querySelector(
    `#bar-energyBar .progress-bar-fill`
  )
  const progressValue = Number(progressBar.dataset.barValue)
  const progressMin = Number(progressBar.dataset.barMin)
  const progressMax = Number(progressBar.dataset.barMax)

  const progressBarFillXp = progressBarXp.querySelector(
    `#bar-jobXp .progress-bar-fill`
  )
  const progressValueXp = Number(progressBarXp.dataset.barValue)
  const progressMinXp = Number(progressBarXp.dataset.barMin)
  const progressMaxXp = Number(progressBarXp.dataset.barMax)

  jobs.forEach(job => {
    job.addEventListener(`click`, () => {
      duration.innerHTML = job.dataset.jobDuration
      energy.innerHTML = job.dataset.jobEnergy
      money.innerHTML = job.dataset.jobMoney
      xp.innerHTML = job.dataset.jobXp
      description.innerHTML = job.dataset.jobDescription
      document.querySelectorAll(`li[data-job-name]`).forEach(liJob => {
        job.querySelector(
          `[data-job-input="${job.dataset.jobName}"]`
        ).checked = true
        if (liJob.dataset.jobName === job.dataset.jobName) {
          liJob.classList.add(`selected`)
        } else {
          liJob.classList.remove(`selected`)
        }
      })
      // progress bar energy
      const newBarValue = progressValue - Number(job.dataset.jobEnergy)
      progressBar.dataset.barValue = newBarValue
      progressBarValSpan.innerHTML = newBarValue
      const newBarFill = Math.round(
        ((newBarValue - progressMin) / (progressMax - progressMin)) * 100
      )
      progressBar.dataset.barFill = newBarFill
      progressBarFill.style.width = `${newBarFill}%`

      // progress bar xp
      const newBarValueXp = progressValueXp + Number(job.dataset.jobXp)
      progressBarXp.dataset.barValue = newBarValueXp
      progressBarValSpanXp.innerHTML = newBarValueXp
      const newBarFillXp = Math.round(
        ((newBarValueXp - progressMinXp) / (progressMaxXp - progressMinXp)) *
          100
      )
      progressBarXp.dataset.barFill = newBarFillXp
      progressBarFillXp.style.width = `${newBarFillXp}%`
    })
  })
}

document.addEventListener(`DOMContentLoaded`, e => {
  if (jobs.item(0)) {
    jobs.item(0).click()
  }
})
