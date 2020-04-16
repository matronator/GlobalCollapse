window.addEventListener(`DOMContentLoaded`, () => {
  const jobs = document.querySelectorAll(`[data-job]`)
  const duration = document.getElementById(`cost-duration`)
  const energy = document.getElementById(`cost-energy`)
  const money = document.getElementById(`reward-money`)
  const xp = document.getElementById(`reward-xp`)
  const description = document.getElementById(`jobDescription`)

  if (jobs) {
    jobs.forEach(job => {
      job.addEventListener(`change`, () => {
        duration.innerHTML = job.dataset.jobDuration
        energy.innerHTML = job.dataset.jobEnergy
        money.innerHTML = job.dataset.jobMoney
        xp.innerHTML = job.dataset.jobXp
        description.innerHTML = job.dataset.jobDescription
      })
    })
  }
})
