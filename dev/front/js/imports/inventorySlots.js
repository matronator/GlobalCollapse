export function headgearChangerAttach() {
    const headgearEls = document.querySelectorAll(`.inventory-item[data-item-subtype="headgear"]:not(.has-helmet)`);
    headgearEls.forEach(el => {
        el.addEventListener("mouseover", addHeadgearIndicator);
        el.addEventListener("mouseout", removeHeadgearIndicator);
    });
}

export function headgearChangerDetach() {
    const headgearEls = document.querySelectorAll(`.inventory-item[data-item-subtype="headgear"]:not(.has-helmet)`);
    headgearEls.forEach(el => {
        el.removeEventListener("mouseover", addHeadgearIndicator);
        el.removeEventListener("mouseout", removeHeadgearIndicator);
    });
}

export function twoHandedMeleeChangerAttach() {
    const twoHandedMeleeEls = document.querySelectorAll(`.inventory-item[data-item-subtype="two-handed-melee"]:not(.has-shield):not(.has-two-handed-ranged)`);
    twoHandedMeleeEls.forEach(el => {
        el.addEventListener("mouseover", addTwoHandedMeleeIndicator);
        el.addEventListener("mouseout", removeTwoHandedMeleeIndicator);
    });
}

export function twoHandedMeleeChangerDetach() {
    const twoHandedMeleeEls = document.querySelectorAll(`.inventory-item[data-item-subtype="two-handed-melee"]:not(.has-shield):not(.has-two-handed-ranged)`);
    twoHandedMeleeEls.forEach(el => {
        el.removeEventListener("mouseover", addTwoHandedMeleeIndicator);
        el.removeEventListener("mouseout", removeTwoHandedMeleeIndicator);
    });
}

export function twoHandedRangedChangerAttach() {
    const twoHandedRangedEls = document.querySelectorAll(`.inventory-item[data-item-subtype="two-handed-ranged"]:not(.has-shield):not(.has-two-handed-melee)`);
    twoHandedRangedEls.forEach(el => {
        el.addEventListener("mouseover", addTwoHandedRangedIndicator);
        el.addEventListener("mouseout", removeTwoHandedRangedIndicator);
    });
}

export function twoHandedRangedChangerDetach() {
    const twoHandedRangedEls = document.querySelectorAll(`.inventory-item[data-item-subtype="two-handed-ranged"]:not(.has-shield):not(.has-two-handed-melee)`);
    twoHandedRangedEls.forEach(el => {
        el.removeEventListener("mouseover", addTwoHandedRangedIndicator);
        el.removeEventListener("mouseout", removeTwoHandedRangedIndicator);
    });
}

function addHeadgearIndicator() {
    toggleIndicator(document.querySelector(`.player-body-slot[data-body-slot="head"]`), "headgear-slot-indicator");
    toggleIndicator(document.querySelector(`.player-body-slot[data-body-slot="face"]`), "headgear-slot-indicator");
}

function removeHeadgearIndicator() {
    toggleIndicator(document.querySelector(`.player-body-slot[data-body-slot="head"]`), "headgear-slot-indicator", false);
    toggleIndicator(document.querySelector(`.player-body-slot[data-body-slot="face"]`), "headgear-slot-indicator", false);
}

function addTwoHandedMeleeIndicator() {
    toggleIndicator(document.querySelector(`.player-body-slot[data-body-slot="melee"]`), "two-handed-melee-slot-indicator");
    toggleIndicator(document.querySelector(`.player-body-slot[data-body-slot="shield"]`), "two-handed-melee-slot-indicator");
}

function addTwoHandedRangedIndicator() {
    toggleIndicator(document.querySelector(`.player-body-slot[data-body-slot="ranged"]`), "two-handed-ranged-slot-indicator");
    toggleIndicator(document.querySelector(`.player-body-slot[data-body-slot="shield"]`), "two-handed-ranged-slot-indicator");
}

function removeTwoHandedMeleeIndicator() {
    toggleIndicator(document.querySelector(`.player-body-slot[data-body-slot="melee"]`), "two-handed-melee-slot-indicator", false);
    toggleIndicator(document.querySelector(`.player-body-slot[data-body-slot="shield"]`), "two-handed-melee-slot-indicator", false);
}

function removeTwoHandedRangedIndicator() {
    toggleIndicator(document.querySelector(`.player-body-slot[data-body-slot="ranged"]`), "two-handed-ranged-slot-indicator", false);
    toggleIndicator(document.querySelector(`.player-body-slot[data-body-slot="shield"]`), "two-handed-ranged-slot-indicator", false);
}

function toggleIndicator(el, indicatorClass, add = true) {
    if (add) {
        el.classList.add(indicatorClass);
    } else {
        el.classList.remove(indicatorClass);
    }
}

export function attachChangers() {
    headgearChangerAttach();
    twoHandedMeleeChangerAttach();
    twoHandedRangedChangerAttach();
}

export function detachChangers() {
    headgearChangerDetach();
    twoHandedMeleeChangerDetach();
    twoHandedRangedChangerDetach();
}
