import interact from "interactjs";
import { Axette } from "axette";
import tippy, { followCursor } from 'tippy.js';

function initTippy() {
    tippy('[data-item-id]', {
        content: (reference) => {
            const item = reference.nextElementSibling;
            return item.innerHTML;
        },
        animation: 'perspective-extreme',
        animateFill: true,
        followCursor: true,
        allowHTML: true,
        interactive: false,
        maxWidth: 400,
        placement: 'bottom',
        theme: 'light',
        trigger: 'mouseenter',
        popperOptions: {
            modifiers: [
                {
                    name: 'flip',
                    options: {
                        fallbackPlacements: ['top', 'right', 'left'],
                    },
                },
                {
                    name: 'preventOverflow',
                    options: {
                        altAxis: true,
                        tether: false,
                    },
                }
            ],
        },
        plugins: [followCursor],
        appendTo: () => document.body,
    });
}


const axette = new Axette();

axette.onAfterAjax(() => {
    initTippy();
});

document.addEventListener("DOMContentLoaded", () => {
    interact('.inventory-item, .equipped-item').draggable({
        inertia: false,
        autoScroll: true,
        modifiers: [
            interact.modifiers.restrictRect({
                endOnly: true
            })
        ],
        listeners: {
            move: dragMoveListener,
        },
        onstart: dragStartedListener,
        onend: dragEndedListener,
    });
    
    interact('.body-head').dropzone({
        accept: '.inventory-item[data-item-subtype="helmet"]:not(.has-headgear)',
        overlap: 0.75,
        ondropactivate: handleDropActive,
        ondropdeactivate: handleDropDeactive,
        ondragenter: handleDragEnter,
        ondragleave: handleDragLeave,
        ondrop: handleEquip,
    });
    
    interact('.inventory-slot:not([data-slot-filled])').dropzone({
        accept: '.inventory-item, .equipped-item',
        overlap: 0.75,
        ondropactivate: handleDropActive,
        ondropdeactivate: handleDropDeactive,
        ondragenter: handleDragEnter,
        ondragleave: handleDragLeave,
        ondrop: handleMoveItem,
    });
    
    initTippy();
    // UIkit.util.on('.item-dropdown', 'beforeshow', function(e) {
    //     const slot = e.target.getAttribute('data-item-slot');
    //     const item = document.querySelector(`.inventory-item[data-item-slot="${slot}"]`);
    //     if (item.classList.contains('stay-closed')) {
    //         e.preventDefault();
    //     }
    // });
});

function handleDropActive(event) {
    event.target.classList.add('drop-active');
}

function handleDropDeactive(event) {
    event.target.classList.remove('drop-active');
    event.target.classList.remove('drop-target');
}

function handleDragEnter(event) {
    event.target.classList.add('drop-target');
}

function handleDragLeave(event) {
    event.target.classList.remove('drop-target')
}

function handleEquip(event) {
    const itemEl = event.relatedTarget;
    const itemId = itemEl.getAttribute('data-item-id');
    const itemSlot = itemEl.getAttribute('data-item-slot');

    const bodySlot = event.target.getAttribute('data-body-slot');

    let url = document.querySelector('[data-equip-endpoint]').getAttribute('data-equip-endpoint');
    url = `${url}&itemId=${Number(itemId)}&bodySlot=${bodySlot}&slot=${Number(itemSlot)}`;

    axette.sendRequest(url);
}

function handleMoveItem(event) {
    const itemEl = event.relatedTarget;
    let oldSlot = itemEl.getAttribute('data-item-slot');
    const newSlot = event.target.getAttribute('data-inventory-slot');
    
    let url = document.querySelector('[data-move-endpoint]').getAttribute('data-move-endpoint');
    url = `${url}&startSlot=${Number(oldSlot)}&endSlot=${Number(newSlot)}`;

    if (itemEl.classList.contains('equipped-item')) {
        if (!oldSlot) {
            oldSlot = itemEl.parentElement.getAttribute('data-body-slot');
        }
        url = document.querySelector('[data-unequip-endpoint]').getAttribute('data-unequip-endpoint');
        url = `${url}&bodySlot=${oldSlot}&slot=${Number(newSlot)}`;
    }

    axette.sendRequest(url);
}

function dragMoveListener(event) {
    var target = event.target
    var x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx
    var y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy
  
    target.style.transform = 'translate(' + x + 'px, ' + y + 'px)'
  
    target.setAttribute('data-x', x)
    target.setAttribute('data-y', y)

    const items = document.querySelectorAll('.item-dropdown');
    items.forEach(item => {
        item.classList.remove('uk-open');
    });
}

function dragStartedListener(event) {
    const items = document.querySelectorAll('.inventory-item');
    items.forEach(item => {
        item.classList.add('stay-closed');
    });
    event.target.classList.add('dragging');
    document.querySelectorAll('.item-dropdown').forEach(item => {
        item.classList.remove('uk-open');
    });
}

function dragEndedListener(event) {
    const items = document.querySelectorAll('.inventory-item');
    items.forEach(item => {
        item.classList.remove('stay-closed');
    });
    event.target.classList.remove('dragging');

    event.target.style.transform = 'translate(0px, 0px)';
    event.target.setAttribute('data-x', event.dx);
    event.target.setAttribute('data-y', event.dy);

    const dropdowns = document.querySelectorAll('.inventory-dropdown');
    dropdowns.forEach(item => {
        item.classList.remove('uk-open');
    });
}

function createLinkAndClick(url) {
    const link = document.createElement('a');
    link.setAttribute('href', url);
    link.classList.add('ajax');
    document.body.appendChild(link);
    link.click();
    console.log(link);
    link.remove();
    console.log(link);
}
