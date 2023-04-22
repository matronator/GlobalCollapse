import interact from "interactjs";
import UIkit from "uikit";
import axette from "axette"

axette.init();

document.addEventListener("DOMContentLoaded", () => {
    interact('.inventory-item').draggable({
        inertia: false,
        autoScroll: true,
        modifiers: [
            interact.modifiers.restrictRect({
                // restriction: 'parent',
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
        accept: '.inventory-item[data-item-subtype="helmet"]',
        overlap: 0.75,
        ondropactivate: handleDropActive,
        ondropdeactivate: handleDropDeactive,
        ondragenter: handleDragEnter,
        ondragleave: handleDragLeave,
        ondrop: handleDrop,
    });

    UIkit.util.on('.item-dropdown', 'beforeshow', function(e) {
        const slot = e.target.getAttribute('data-item-slot');
        const item = document.querySelector(`.inventory-item[data-item-slot="${slot}"]`);
        if (item.classList.contains('stay-closed')) {
            e.preventDefault();
        }
    });
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

function handleDrop(event) {
    console.log('drop');
    const itemEl = event.relatedTarget;
    const itemId = itemEl.getAttribute('data-item-id');
    const itemSlot = itemEl.getAttribute('data-item-slot');

    const bodySlot = event.target.getAttribute('data-body-slot');

    let url = document.querySelector('[data-equip-endpoint]').getAttribute('data-equip-endpoint');
    url = `${url}&itemId=${itemId}&bodySlot=${bodySlot}&slot=${itemSlot}`;

    console.log(url);

    const link = document.createElement('a');
    link.setAttribute('href', url);
    link.classList.add('ajax');
    document.body.appendChild(link);
    link.click();
    console.log(link);
    link.remove();
    console.log(link);
}

function dragMoveListener(event) {
    var target = event.target
    var x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx
    var y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy
  
    target.style.transform = 'translate(' + x + 'px, ' + y + 'px)'
  
    target.setAttribute('data-x', x)
    target.setAttribute('data-y', y)

    const items = document.querySelectorAll('.inventory-dropdown');
    items.forEach(item => {
        item.classList.remove('uk-open');
    });
}

let startPos;

function dragStartedListener(event) {
    const items = document.querySelectorAll('.inventory-item');
    items.forEach(item => {
        item.classList.add('stay-closed');
    });
    event.target.classList.add('dragging');
    startPos = event.target.getBoundingClientRect();
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
