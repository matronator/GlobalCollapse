document.addEventListener(`DOMContentLoaded`, () => {

    const thumbNav = document.querySelector(`.js-thumbnav`)
    const dropElement = document.querySelector(`.js-upload`)

    if (thumbNav && dropElement) {
        const input = dropElement.querySelector(`input`)
        input.addEventListener(`change`, () => {
            // display thumbnails
            const { files } = input
            for (let i = 0; i < files.length; i ++) {
                const reader = new FileReader()
                const item = document.createElement(`li`)
                const thumbnail = document.createElement(`img`)
                thumbnail.width = 100
                item.appendChild(thumbnail)
                thumbNav.appendChild(item)
                reader.onload = e => thumbnail.src = e.target.result
                reader.readAsDataURL(files[i])
            }
        })
    }
})

