/* eslint-disable no-var */
function sumAll(dName, tInput, tPrice) {
  const inputValue = tInput.value
  const totalPrice = document.getElementById(`priceTotal`)
  const priceOut = document.querySelector(`[data-drug-price="${dName}"]`)
  priceOut.innerHTML = `&dollar;${inputValue * tPrice}`
  var drugArray = []
  const drugInputs = document.querySelectorAll(`input[data-drug-input]`)
  drugInputs.forEach(current => {
    const currentPrice = Number(current.dataset.price)
    const intValue = Number(current.value * currentPrice)
    drugArray.push(intValue)
  })
  const reducer = (accumulator, currentValue) => accumulator + currentValue
  const total = drugArray.reduce(reducer)
  totalPrice.innerHTML = `&dollar;${total}`
}

window.addEventListener(`DOMContentLoaded`, () => {
  // sortable
  const drugInputs = document.querySelectorAll(`input[data-drug-input]`)
  drugInputs.forEach(field => {
    const name = field.dataset.drugInput
    const thisInput = document.getElementById(name)
    // eslint-disable-next-line prefer-destructuring
    const price = field.dataset.price
    field.addEventListener(`change`, () => sumAll(name, thisInput, price))
  })
})
