import UIkit from "./uikit/uikit";
import Icons from "./uikit/uikit-icons";
import NetteForms from "../../../vendor/nette/forms/src/assets/netteForms.min";
import Choices from "choices.js";
import { notificationFailure, notificationSuccess, choicesOptions } from "./imports/settings";
import { toggle } from "./imports/helpers";
import "./../../../app/modules/Front/components/Assault/PlayerPopover.js";
import { timesToRelative } from "./imports/dates";
import { Axette } from "axette";
import { registerEventHandlers } from "../../../app/modules/Front/components/Buildings/BuildingCard/BuildingCard.js";
import { scrollToActive } from "./imports/sidebar.js";

const axette = new Axette();

axette.onBeforeAjax(() => {
    deInitUIKit();
});

axette.onAfterAjax(() => {
    registerEventHandlers();
    registerFillView();
    timesToRelative();
    NetteForms.initOnLoad();
    reInitUIKit();
});

// UIKit
UIkit.use(Icons);

// nette forms
NetteForms.initOnLoad();

// relative times
timesToRelative();

window.addEventListener(`DOMContentLoaded`, () => {
    scrollToActive();

    // Check if standalone
    if (!window.matchMedia(`(display-mode: standalone)`).matches) {
        const alreadyShown = localStorage.getItem(`pwaPromptShown`);
        let showAgain = true;
        if (alreadyShown) {
            const currentTime = new Date().getTime();
            const duration = 7 * 24 * 60 * 60 * 1000; // 7 days 24 h 60 min 60 sec 1000 milisec = 1 week
            if (currentTime - alreadyShown <= duration) {
                showAgain = false;
            }
        }
        if (showAgain) {
            const pwaPrompt = document.getElementById(`pwaPrompt`);
            pwaPrompt.classList.add(`shown`);
            localStorage.setItem(`pwaPromptShown`, new Date().getTime());
            pwaPrompt.addEventListener(`click`, () => {
                pwaPrompt.classList.remove(`shown`);
                setTimeout(() => {
                    pwaPrompt.remove();
                }, 500);
            });
        }
    }
    // sortable
    UIkit.util.on(
        ".js-sortable",
        "moved",
        ({
            target: {
                children,
                dataset: { callback }
            }
        }) => {
            const idList = [...children].map(el => el.id);
            const req = new XMLHttpRequest();
            req.open("GET", `${callback}&idList=${idList}`);
            req.addEventListener("load", () => {
                if (req.readyState === 4 && req.status === 200) {
                    return UIkit.notification(notificationSuccess);
                }
                return UIkit.notification(notificationFailure);
            });
            req.addEventListener("error", () => UIkit.notification(notificationFailure));
            req.send();
        }
    );

    // multiselect
    const multies = document.querySelectorAll(`.js-select`);
    multies.forEach(multi => new Choices(multi, choicesOptions(multi)));

    // toggle logic
    const togglers = document.querySelectorAll(`[data-toggler]`);
    [...togglers].forEach(toggler => toggler.addEventListener(`change`, () => toggle(togglers)));
    toggle(togglers);

    // Fill view
    registerFillView();
    registerEventHandlers();
});

function registerFillView() {
    const fillViewEls = document.querySelectorAll(`[data-fill-view]`);
    const footer = document.getElementById("main-footer");

    fillViewEls.forEach(fillViewEl => {
        if (innerWidth < 640) {
            fillViewEl.style.height = `100%`;
        } else {
            fillViewEl.style.height = `calc(100% - calc(${footer.getBoundingClientRect().height}px * 1.5))`;
        }
    });
}

function deInitUIKit() {
    const viewportIds = [];
    const viewportEls = document.querySelectorAll(`[uk-height-viewport]`);
    viewportEls.forEach(el => {
        const viewportHeight = el.getAttribute(`uk-height-viewport`);
        const viewportStyle = el.getAttribute(`style`);
        const id = el.getAttribute(`id`);

        localStorage.setItem(`viewport-${id}`, viewportHeight);
        localStorage.setItem(`style-${id}`, viewportStyle);
        viewportIds.push(id);

        el.removeAttribute(`uk-height-viewport`);
        el.removeAttribute(`style`);
    });

    localStorage.setItem(`viewportIds`, JSON.stringify(viewportIds));
}

function reInitUIKit() {
    const viewportIds = JSON.parse(localStorage.getItem(`viewportIds`));
    if (!viewportIds) {
        localStorage.removeItem(`viewportIds`);
        return;
    }

    viewportIds.forEach(id => {
        const el = document.getElementById(id);
        const viewportHeight = localStorage.getItem(`viewport-${id}`);
        const viewportStyle = localStorage.getItem(`style-${id}`);
        el.setAttribute(`style`, viewportStyle);
        el.setAttribute(`uk-height-viewport`, viewportHeight);
        localStorage.removeItem(`viewport-${id}`);
        localStorage.removeItem(`style-${id}`);
    });

    localStorage.removeItem(`viewportIds`);
}
