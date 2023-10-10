document.addEventListener(`DOMContentLoaded`, () => {
    const upgradeTierButtons = document.querySelectorAll(`[data-price-id]`);

    upgradeTierButtons.forEach(btn => {
        if (Number(btn.getAttribute('data-upgrade-tier')) > 1) {
            btn.addEventListener(`click`, (e) => {
                e.preventDefault();
                Paddle.Checkout.open({
                    settings: {
                        theme: "light",
                    },
                    items: [{
                        priceId: btn.getAttribute('data-price-id'),
                        quantity: 1,
                    }],
                    customer: {
                        email: btn.getAttribute('data-customer-email'),
                    },
                });
            });
        }
    });
});
