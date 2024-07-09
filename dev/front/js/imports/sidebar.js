export function scrollToActive() {
    const sideMenu = document.querySelector(`.uk-nav-default`);
    const active = sideMenu.querySelector(`li.uk-active`);

    if (active) {
        active.scrollIntoView({ behavior: "instant" });
    }
}
