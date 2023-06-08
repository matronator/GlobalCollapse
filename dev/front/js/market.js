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
    const inventoryItems = document.querySelectorAll('.inventory-item > img');
    inventoryItems.forEach((item) => {
        interact(item).off('tap', onInventoryItemClick);
        interact(item).on('tap', onInventoryItemClick);
    });

    const buyButtons = document.querySelectorAll('[data-buy-item]');
    buyButtons.forEach((button) => {
        button.removeEventListener('click', onBuyItemClick);
        button.addEventListener('click', onBuyItemClick);
    });
}

function onBuyItemClick(e) {
    const item = e.currentTarget;
    const itemId = item.getAttribute('data-market-buy');

    let url = document.querySelector('[data-buy-endpoint]').getAttribute('data-buy-endpoint');
    url = `${url}&itemId=${Number(itemId)}`;

    axette.sendRequest(url);
}

function onInventoryItemClick(e) {
    const item = e.target.parentElement;
    const slot = document.getElementById('sellItemSlot');
    if (item.classList.contains('sell-active')) {
        makeSellInactive(slot, item);
    } else {
        const activeItemsForSell = document.querySelectorAll('.inventory-item.sell-active');
        activeItemsForSell.forEach(el => {
            el.classList.remove('sell-active');
        });

        makeSellActive(slot, item);
    }
}

document.addEventListener("DOMContentLoaded", () => {
    registerClickListeners();
    interact('.inventory-item').draggable({
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

    interact('.sell-item-slot').dropzone({
        accept: '.inventory-item:not(.sell-active)',
        overlap: 0.75,
        ondropactivate: handleDropActive,
        ondropdeactivate: handleDropDeactive,
        ondragenter: handleDragEnter,
        ondragleave: handleDragLeave,
        ondrop: handleDropItemToSell,
    });

    interact('.inventory-slot:not([data-slot-filled])').dropzone({
        accept: '.inventory-item',
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
    event.target.classList.remove('drop-target');
}

function handleDropItemToSell(event) {
    const sellSlotEl = event.target;
    const itemEl = event.relatedTarget;

    makeSellActive(sellSlotEl, itemEl);
}

function makeSellActive(slotEl, itemEl) {
    const slotItemEl = slotEl.querySelector('.market-sell-item');
    const itemId = itemEl.getAttribute('data-item-id');
    const itemSlot = itemEl.getAttribute('data-item-slot');
    const sellButton = document.querySelector('button[data-sell-item]');
    const sellPriceEl = document.getElementById('itemSellPrice');

    slotItemEl.removeAttribute('data-is-empty');
    slotItemEl.setAttribute('data-is-filled', true);
    slotItemEl.setAttribute('data-original-id', itemId);
    slotItemEl.setAttribute('data-original-slot', itemSlot);
    slotItemEl.innerHTML = itemEl.innerHTML;
    sellButton.innerHTML = `<span uk-icon="money4"></span> ${sellButton.parentElement.getAttribute('data-sell-locale')}`;
    sellButton.classList.add('uk-button-danger');
    sellButton.classList.remove('uk-button-default');
    sellButton.classList.remove('uk-disabled');
    sellButton.addEventListener('click', onSellItemClick);

    sellPriceEl.innerHTML = `<span class="uk-text-muted uk-text-light">You will get</span> ${itemEl.getAttribute('data-item-cost')} <span class="uk-text-muted uk-text-light">for this item.</span>`;

    slotItemEl.addEventListener('click', onDroppedItemClick);

    itemEl.classList.add('sell-active');
}

function makeSellInactive(slotEl, itemEl) {
    const slotItemEl = slotEl.querySelector('.market-sell-item');
    const sellButton = document.querySelector('button[data-sell-item]');
    const sellPriceEl = document.getElementById('itemSellPrice');

    slotItemEl.removeAttribute('data-is-filled');
    slotItemEl.setAttribute('data-is-empty', true);
    slotItemEl.setAttribute('data-original-id', '');
    slotItemEl.setAttribute('data-original-slot', '');
    slotItemEl.innerHTML = '';
    sellButton.innerHTML = `<span uk-icon="push"></span> ${sellButton.parentElement.getAttribute('data-drop-locale')}`;
    sellButton.classList.add('uk-disabled');
    sellButton.classList.add('uk-button-default');
    sellButton.classList.remove('uk-button-danger');

    sellPriceEl.innerHTML = '';

    itemEl.classList.remove('sell-active');

    slotItemEl.removeEventListener('click', onDroppedItemClick);
}

function onSellItemClick(e) {
    const itemEl = document.querySelector('.market-sell-item[data-is-filled]');
    const itemSlot = itemEl.getAttribute('data-original-slot');

    let url = document.querySelector('[data-sell-endpoint]').getAttribute('data-sell-endpoint');
    console.log(url);
    url = `${url}&slotId=${Number(itemSlot)}`;

    axette.sendRequest(url);
}

function onDroppedItemClick(e) {
    const item = document.querySelector(`.inventory-item[data-item-id="${e.currentTarget.getAttribute('data-original-id')}"]`);
    const slot = document.getElementById('sellItemSlot');
    makeSellInactive(slot, item);
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
