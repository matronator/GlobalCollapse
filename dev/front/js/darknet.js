/* eslint-disable no-var */
function sumAll(dName, tInput, tPrice) {
  const inputValue = tInput.value
  const totalPrice = document.getElementById(`priceTotal`)
  const priceOut = document.querySelector(`[data-drug-price="${dName}"]`)
  priceOut.innerHTML = `&dollar;${new Intl.NumberFormat().format(
    inputValue * tPrice
  )}`
  var drugArray = []
  const drugInputs = document.querySelectorAll(`input[data-drug-input]`)
  drugInputs.forEach(current => {
    const currentPrice = Number(current.dataset.price)
    const intValue = Number(current.value * currentPrice)
    drugArray.push(intValue)
  })
  const reducer = (accumulator, currentValue) => accumulator + currentValue
  const total = drugArray.reduce(reducer)
  totalPrice.innerHTML = `&dollar;${new Intl.NumberFormat().format(total)}`
}

function calculatePrice(offer, price, quantity) {
  return price * quantity
}

const offers = document.querySelectorAll(`.darknet-offer[data-offer-id]`)
if (offers.length > 0) {
  offers.forEach(offer => {
    const offerInput = offer.querySelector(`input.darknet-offer-input`)
    if (offerInput) {
      const offerButtons = offer.querySelectorAll(`[data-offer-button]`)
      offerInput.addEventListener(`change`, el => {
        offerButtons.forEach(btn => {
          if (btn.dataset.offerButton === `buy`) {
            btn.value = `$${new Intl.NumberFormat().format(
              Math.round(
                el.target.value *
                  offer.dataset.offerPrice *
                  (1 + Number(offer.dataset.vendorCharge))
              )
            )}`
          } else {
            btn.value = `$${new Intl.NumberFormat().format(
              el.target.value * offer.dataset.offerPrice
            )}`
          }
        })
      })
    }
  })
}

// sortable
// const drugInputs = document.querySelectorAll(`input[data-drug-input]`)
// drugInputs.forEach(field => {
//   const name = field.dataset.drugInput
//   const thisInput = document.getElementById(name)
//   // eslint-disable-next-line prefer-destructuring
//   const price = field.dataset.price
//   field.addEventListener(`change`, () => sumAll(name, thisInput, price))
// })
