// Check job
import {
  updateTime,
  jobDone
} from "../../../../app/modules/Front/components/CountdownBar/CountdownBar"

let myworker

function startWorker() {
  if (typeof Worker !== "undefined") {
    if (typeof myworker === "undefined") {
      myworker = new Worker(
        `${window.location.origin}/dist/front/etc/JobWorker.js`
      )
    }
    myworker.onmessage = function(event) {
      if (event.data[0] === "update") {
        updateTime(event.data[1], event.data[2])
      } else {
        stopWorker()
        jobDone()
      }
    }
  } else {
    console.log(`Sorry, your browser does not support Web Workers...`)
  }
}

function stopWorker() {
  myworker.terminate()
  myworker = undefined
}

startWorker()
