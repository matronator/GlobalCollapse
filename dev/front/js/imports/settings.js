export const notificationSuccess = {
  message: "Změna byla úspěšně provedena",
  status: "success",
  pos: "bottom-center"
}

export const notificationFailure = {
  message: "Něco se pokazilo",
  status: "danger",
  pos: "bottom-center"
}

export const choicesOptions = inputElement => ({
  removeItemButton: true,
  shouldSort: false,
  loadingText: "Načítám...",
  noResultsText: "Nebyly nalezeny žádné výsledky",
  noChoicesText: "Není ze čeho vybýrat",
  itemSelectText: "Stiskněte pro výběr",
  callbackOnCreateTemplates(template) {
    return {
      choice: (classNames, data) => {
        const rootValues = inputElement.dataset.rootValues
          ? inputElement.dataset.rootValues.split(",")
          : []
        return template(`
                  <div 
                      class="${classNames.item} ${classNames.itemChoice} ${
          data.disabled ? classNames.itemDisabled : classNames.itemSelectable
        }" 
                      data-select-text="${this.config.itemSelectText}" 
                      data-choice 
                      ${
                        data.disabled
                          ? 'data-choice-disabled aria-disabled="true"'
                          : "data-choice-selectable"
                      }
                      data-id="${data.id}"
                      data-value="${data.value}"
                      ${data.groupId > 0 ? 'role="treeitem"' : 'role="option"'}
                  >
                        ${
                          rootValues.indexOf(data.value) >= 0
                            ? `<strong>${data.label}</strong>`
                            : `<span>${data.label}</span>`
                        }
                  </div>
                `)
      }
    }
  }
})
