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

if (
  jobs.length &&
  duration &&
  energy &&
  money &&
  xp &&
  description &&
  progressBar &&
  progressBarValSpan &&
  progressBarXp &&
  progressBarValSpanXp
) {
  const progressBarFill = progressBar.querySelector(`.progress-bar-fill`)
  const progressValue = Number(progressBar.dataset.barValue)
  const progressMin = Number(progressBar.dataset.barMin)
  const progressMax = Number(progressBar.dataset.barMax)

  const progressBarFillXp = progressBarXp.querySelector(`.progress-bar-fill`)
  const progressValueXp = Number(progressBarXp.dataset.barValue)
  const progressMinXp = Number(progressBarXp.dataset.barMin)
  const progressMaxXp = Number(progressBarXp.dataset.barMax)

  const getBarFill = (value, min, max) => {
    if (max <= min) {
      return 0
    }

    return Math.min(
      100,
      Math.max(0, Math.round(((value - min) / (max - min)) * 100))
    )
  }

  if (progressBarFill && progressBarFillXp) {
    jobs.forEach(job => {
      job.addEventListener(`click`, () => {
        duration.innerHTML = job.dataset.jobDuration
        energy.innerHTML = job.dataset.jobEnergy
        money.innerHTML = job.dataset.jobMoney
        xp.innerHTML = job.dataset.jobXp
        description.innerHTML = job.dataset.jobDescription
        job.querySelector(
          `[data-job-input="${job.dataset.jobName}"]`
        ).checked = true

        document.querySelectorAll(`li[data-job-name]`).forEach(liJob => {
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
        const newBarFill = getBarFill(newBarValue, progressMin, progressMax)
        progressBar.dataset.barFill = newBarFill
        progressBarFill.style.width = `${newBarFill}%`

        // progress bar xp
        const newBarValueXp = progressValueXp + Number(job.dataset.jobXp)
        progressBarXp.dataset.barValue = newBarValueXp
        progressBarValSpanXp.innerHTML = newBarValueXp
        const newBarFillXp = getBarFill(
          newBarValueXp,
          progressMinXp,
          progressMaxXp
        )
        progressBarXp.dataset.barFill = newBarFillXp
        progressBarFillXp.style.width = `${newBarFillXp}%`
      })
    })

    const selectInitialJob = () => jobs.item(0).click()

    if (document.readyState === `loading`) {
      document.addEventListener(`DOMContentLoaded`, selectInitialJob, {
        once: true
      })
    } else {
      selectInitialJob()
    }
  }
}
