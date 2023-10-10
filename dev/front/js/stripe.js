document.addEventListener(`DOMContentLoaded`, () => {
    const upgradeTierButtons = document.querySelectorAll(`[data-upgrade-tier]`);

    upgradeTierButtons.forEach(btn => {
        if (Number(btn.getAttribute('data-upgrade-tier')) > 1) {
            let baseUrl = btn.href;
            let url = new URL(baseUrl);
            url.searchParams.append('prefilled_email', btn.getAttribute('data-customer-email'));
            url.searchParams.append('client_reference_id', btn.getAttribute('data-customer-username'));
            btn.href = url.toString();
        }
    });
});
