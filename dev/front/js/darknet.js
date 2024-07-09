const offers = document.querySelectorAll(`.darknet-offer[data-offer-id]`);
if (offers.length > 0) {
    offers.forEach(offer => {
        const offerInputs = offer.querySelectorAll(`input.js-darknet-offer-input`);
        offerInputs.forEach(input => {
            input.addEventListener(`input`, event => {
                let otherInput;
                if (input.classList.contains(`darknet-offer-input`)) {
                    otherInput = offer.querySelector(`input.darknet-offer-range`);
                } else {
                    otherInput = offer.querySelector(`input.darknet-offer-input`);
                }
                otherInput.value = event.target.value;

                const offerButtons = offer.querySelectorAll(`[data-offer-button]`);
                offerButtons.forEach(btn => {
                    if (btn.dataset.offerButton === `buy`) {
                        btn.value = `$${new Intl.NumberFormat().format(
                            Math.round(
                                event.target.value * offer.dataset.offerPrice * (1 + Number(offer.dataset.vendorCharge))
                            )
                        )}`;
                    } else {
                        btn.value = `$${new Intl.NumberFormat().format(event.target.value * offer.dataset.offerPrice)}`;
                    }
                });
            });
        });

        const setMaxBtn = offer.querySelector(`button.js-darknet-set-max`);
        if (setMaxBtn) {
            setMaxBtn.addEventListener(`click`, () => {
                const input = offer.querySelector(`input.js-darknet-offer-input`);
                input.value = input.max;
                const otherInput = offer.querySelector(`input.darknet-offer-range`);
                otherInput.value = input.max;

                input.dispatchEvent(new Event(`input`));
            });
        }
    });
}
