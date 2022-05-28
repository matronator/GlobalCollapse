/**
 * Copyright (c) 2021 Matronator
 *
 * This software is released under the MIT License.
 * https://opensource.org/licenses/MIT
 */

import tippy, { createSingleton } from "tippy.js"

document.addEventListener(`DOMContentLoaded`, () => {
    const tippyInstances = tippy(document.querySelectorAll(`[data-popover-trigger]`), {
        hideOnClick: true,
        popperOptions: {
            modifiers: [
                {
                    name: `preventOverflow`,
                    options: {
                        boundary: document.querySelector(`main`),
                    },
                },
            ],
        },
    })
    const singleton = createSingleton(tippyInstances, {
        // appendTo: document.body,
        // maxWidth: 'fit-content',
        delay: [0, 250],
        overrides: ['placement'],
        moveTransition: 'transform 0.2s ease',
        interactive: true,
        offset: [0, 0],
        zIndex: 10,
    })

    tippyInstances.forEach(el => {
        el.setContent(document.querySelector(`[data-player-popover="${el.reference.getAttribute('data-popover-trigger')}"]`))
    })

    // triggers?.forEach(trigger => {
    //     const popover = document.querySelector(`[data-player-popover="${trigger.getAttribute('data-popover-trigger')}"]`)

    //     tippy(trigger, {
    //         content: popover,
    //     })
    //     // const popperInstance = createPopper(trigger, popover, {
    //     //     modifiers: [
    //     //         {
    //     //             name: "offset",
    //     //             options: {
    //     //                 offset: [0, 0],
    //     //             },
    //     //         },
    //     //     ],
    //     // })
    //     // function show() {
    //     //     popover.setAttribute(`data-show`, '')
    //     //     popover.classList.remove(`uk-hidden`)
    //     //     popperInstance.setOptions({
    //     //         modifiers: [{ name: 'eventListeners', enabled: true }],
    //     //     })
    //     //     popperInstance.update()
    //     // }

    //     // function hide() {
    //     //     popover.removeAttribute(`data-show`)
    //     //     popover.classList.add(`uk-hidden`)
    //     //     popperInstance.setOptions({
    //     //         modifiers: [{ name: 'eventListeners', enabled: false }],
    //     //     })
    //     // }

    //     // const showEvents = ['mouseenter', 'focus', 'pointerenter']
    //     // const hideEvents = ['mouseleave', 'blur', 'pointerleave']

    //     // showEvents.forEach(event => {
    //     //     trigger.addEventListener(event, show)
    //     // })

    //     // hideEvents.forEach(event => {
    //     //     trigger.addEventListener(event, hide)
    //     // })
    // })
})
