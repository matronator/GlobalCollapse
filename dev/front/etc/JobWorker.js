function checkJob() {
  const request = new XMLHttpRequest()
  request.open("GET", `../../../api/job`, true)
  request.onload = function() {
    if (this.status >= 200 && this.status < 400) {
      // Success!
      const jsonData = JSON.parse(this.response)
      if (jsonData.mission === false) {
        if (jsonData.new === true) {
          postMessage(["done"])
        }
      } else if (jsonData.mission === true) {
        setTimeout(checkJob, 15000)
        postMessage(["update", jsonData.minutes, jsonData.seconds])
      }
    }
  }

  request.onerror = function() {
    // There was a connection error of some sort
  }

  request.send()
}

checkJob()
