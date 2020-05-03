const jobs = document.querySelectorAll(`[data-job-name]`)
const duration = document.getElementById(`cost-duration`)
const energy = document.getElementById(`cost-energy`)
const money = document.getElementById(`reward-money`)
const xp = document.getElementById(`reward-xp`)
const description = document.getElementById(`jobDescription`)
const progressBar = document.getElementById(`bar-jobEnergy`)
const progressBarValSpan = document.getElementById(`barTextValue`)

if (jobs && duration && description && progressBar && money) {
  const progressBarFill = progressBar.querySelector(`.progress-bar-fill`)
  const progressValue = Number(progressBar.dataset.barValue)
  const progressMin = Number(progressBar.dataset.barMin)
  const progressMax = Number(progressBar.dataset.barMax)

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
      // progress bar
      const newBarValue = progressValue - Number(job.dataset.jobEnergy)
      progressBar.dataset.barValue = newBarValue
      progressBarValSpan.innerHTML = newBarValue
      const newBarFill = Math.round(
        ((newBarValue - progressMin) / (progressMax - progressMin)) * 100
      )
      progressBar.dataset.barFill = newBarFill
      progressBarFill.style.width = `${newBarFill}%`
    })
  })
}
