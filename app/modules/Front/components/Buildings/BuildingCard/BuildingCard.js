import axette from "axette"

axette.init()

axette.onAjax(registerEventHandlers)

registerEventHandlers()
function registerEventHandlers() {
    const ajaxButtons = document.querySelectorAll(`a.uk-button.ajax`)
    ajaxButtons.forEach(el => {
        el.addEventListener(`click`, e => {
            el.classList.add(`uk-disabled`);
            el.innerHTML = `<div uk-spinner></div>`
        })
    })

    const wrappers = document.querySelectorAll(`.building-wrapper`)
    wrappers?.forEach(wrapper => {

        const infoToggle = wrapper.querySelector(`[data-toggle-info]`)
        if (infoToggle) {
            infoToggle.addEventListener(`click`, e => {
                const infoBlock = document.querySelector(`[data-building-info="${infoToggle.dataset.toggleInfo}"]`)
                infoBlock.classList.toggle(`block-hidden`)
                wrapper.classList.toggle(`building-expanded`)
                infoToggle.textContent = infoBlock.classList.contains(`block-hidden`) ? infoToggle.dataset.showMore : infoToggle.dataset.showLess
                window.dispatchEvent(new Event(`resize`))
            })
        }

    })

    const buildButton = document.querySelector(`[data-build-button]`)
    if (buildButton) {
        buildButton.addEventListener(`click`, e => {
            const list = buildButton.parentElement.querySelector(`[data-build-list]`)
            list.classList.toggle(`block-hidden`)
            const listAll = document.querySelectorAll(`[data-build-list]`)
            listAll.forEach(item => {
                if (item.dataset.buildList !== buildButton.dataset.buildButton) {
                    item.classList.add(`block-hidden`)
                }
            })
        })
    }
}
