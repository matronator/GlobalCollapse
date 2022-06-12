const offers = document.querySelectorAll(`.darknet-offer[data-offer-id]`)
if (offers.length > 0) {
  offers.forEach(offer => {
    const offerInput = offer.querySelector(`input.darknet-offer-input`)
    if (offerInput) {
      const offerButtons = offer.querySelectorAll(`[data-offer-button]`)
      offerInput.addEventListener(`input`, el => {
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
