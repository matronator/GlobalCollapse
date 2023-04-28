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
    registerClickListeners();
});

function registerClickListeners() {
    const equippedItems = document.querySelectorAll('.equipped-item > img');
    equippedItems.forEach((item) => {
        interact(item).off('tap', onEquippedItemClick);
        interact(item).on('tap', onEquippedItemClick);
        // item.removeEventListener('click', onEquippedItemClick);
        // item.addEventListener('click', onEquippedItemClick);
    });

    const inventoryItems = document.querySelectorAll('.inventory-item > img');
    inventoryItems.forEach((item) => {
        interact(item).off('tap', onInventoryItemClick);
        interact(item).on('tap', onInventoryItemClick);
        // item.removeEventListener('click', onInventoryItemClick);
        // item.addEventListener('click', onInventoryItemClick);
    });
}

function onEquippedItemClick(e) {
    const item = e.target.parentElement;
    const oldSlot = item.parentElement.getAttribute('data-body-slot');
    const emptySlotEl = document.querySelector('.inventory-slot[data-slot-empty]');
    const emptySlot = emptySlotEl.getAttribute('data-inventory-slot');

    let url = document.querySelector('[data-unequip-endpoint]').getAttribute('data-unequip-endpoint');
    url = `${url}&bodySlot=${oldSlot}&slot=${Number(emptySlot)}`;

    axette.sendRequest(url);
}

function onInventoryItemClick(e) {
    const item = e.target.parentElement;
    console.log(item);
    const oldSlot = item.getAttribute('data-item-slot');
    const itemId = item.getAttribute('data-item-id');
    const subtype = item.getAttribute('data-item-subtype');
    let emptySlotEl;
    if (subtype === 'headgear') {
        const headEl = document.querySelector(`.player-body-slot[data-body-slot="head"]`);
        const maskEl = document.querySelector(`.player-body-slot[data-body-slot="face"]`);
        if (headEl.hasAttribute('data-slot-filled') || maskEl.hasAttribute('data-slot-filled')) {
            return;
        }
        emptySlotEl = document.querySelector(`.player-body-slot[data-slot-empty][data-body-slot="face"]`);
    } else {
        emptySlotEl = document.querySelector(`.player-body-slot[data-body-slot="${getBodyPartForGear(subtype)}"]`);
    }
    if (!emptySlotEl) return;

    let url = document.querySelector('[data-equip-endpoint]').getAttribute('data-equip-endpoint');
    url = `${url}&itemId=${Number(itemId)}&bodySlot=${emptySlotEl.getAttribute('data-body-slot')}&slot=${Number(oldSlot)}`;

    axette.sendRequest(url);
}

document.addEventListener("DOMContentLoaded", () => {
    registerClickListeners();
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

    interact('.body-face').dropzone({
        accept: '.inventory-item[data-item-subtype="mask"]:not(.has-headgear), .inventory-item[data-item-subtype="headgear"]:not(.has-headgear)',
        overlap: 0.75,
        ondropactivate: handleDropActive,
        ondropdeactivate: handleDropDeactive,
        ondragenter: handleDragEnter,
        ondragleave: handleDragLeave,
        ondrop: handleEquip,
    });

    interact('.body-shoulders').dropzone({
        accept: '.inventory-item[data-item-subtype="shoulders"]',
        overlap: 0.75,
        ondropactivate: handleDropActive,
        ondropdeactivate: handleDropDeactive,
        ondragenter: handleDragEnter,
        ondragleave: handleDragLeave,
        ondrop: handleEquip,
    });

    interact('.body-body').dropzone({
        accept: '.inventory-item[data-item-subtype="body"]',
        overlap: 0.75,
        ondropactivate: handleDropActive,
        ondropdeactivate: handleDropDeactive,
        ondragenter: handleDragEnter,
        ondragleave: handleDragLeave,
        ondrop: handleEquip,
    });

    interact('.body-melee').dropzone({
        accept: '.inventory-item[data-item-subtype="melee"]',
        overlap: 0.75,
        ondropactivate: handleDropActive,
        ondropdeactivate: handleDropDeactive,
        ondragenter: handleDragEnter,
        ondragleave: handleDragLeave,
        ondrop: handleEquip,
    });

    interact('.body-ranged').dropzone({
        accept: '.inventory-item[data-item-subtype="ranged"]',
        overlap: 0.75,
        ondropactivate: handleDropActive,
        ondropdeactivate: handleDropDeactive,
        ondragenter: handleDragEnter,
        ondragleave: handleDragLeave,
        ondrop: handleEquip,
    });

    interact('.body-legs').dropzone({
        accept: '.inventory-item[data-item-subtype="legs"]',
        overlap: 0.75,
        ondropactivate: handleDropActive,
        ondropdeactivate: handleDropDeactive,
        ondragenter: handleDragEnter,
        ondragleave: handleDragLeave,
        ondrop: handleEquip,
    });

    interact('.body-feet').dropzone({
        accept: '.inventory-item[data-item-subtype="feet"]',
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

const bodyPartMap = {
    'head': 'helmet',
    'face': 'mask',
    'shoulders': 'shoulders',
    'body': 'chest',
    'melee': 'melee',
    'ranged': 'ranged',
    'legs': 'legs',
    'feet': 'feet',
};

function matchBodyPartToGear(gear, bodyPart) {
    return bodyPartMap[bodyPart] === gear;
}

function getBodyPartForGear(gear) {
    return Object.keys(bodyPartMap).find(key => bodyPartMap[key] === gear);
}