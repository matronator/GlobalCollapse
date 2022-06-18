import ClipboardJS from 'clipboard';

const clipboard = new ClipboardJS(`.crypto-address`, {
    target: function(trigger) {
        return trigger;
    },
});

clipboard.on(`success`, e => {
    const tooltipText = e.trigger.getAttribute(`uk-tooltip`);
    e.trigger.setAttribute(`uk-tooltip`, `&#10003; Copied!`);
    setTimeout(() => {
        e.trigger.setAttribute(`uk-tooltip`, tooltipText);
    }, 4000);
})
