/**
 * @typedef {Object} Alert
 * @description Blok pro přidání asciinema videa
 */
window.model.AsciinemaBlock = class AsciinemaBlock {
    /**
     * Vyrenderuje hlavní element bloku a naplní ho uloženými daty
     *
     * @param {{data: AlertData, config: object, api: object}}
     *   data — předchozí uložená data
     *   config - config uživatele (programátora)
     *   api - API codex-editoru
     */
    constructor({data, config, api}) {
        // Předá odkaz na API
        this.api = api;

        // Vytvoří a naplní data property
        this.data = data;
    }

    /**
     * Výchozí placeholder pro blok
     *
     * @return {string}
     * @constructor
     */
    static get DEFAULT_PLACEHOLDER() {
        return '';
    }

    /**
    * Vytvoří UI bloku
    */
    render() {
        // Vytvoří input
        const input = document.createElement('input');
        input.placeholder = 'Asciinema ID';

        // Naplní input daty
        input.classList.add('ce-asciinema');
        input.value = this.data && this.data.id ? this.data.id : '';

        // Vrátí input
        return input;
    }

    /**
    * Uložení
    * @param {Object} content obsah bloku k uložení
    */
    save(content){
        return {
            id: content.value
        }
    }

    /**
    * Zobrazí náš blok v toolboxu
    */
    static get toolbox() {
        return {
            title: 'Asciinema',
            icon: '<svg xmlns="http://www.w3.org/2000/svg" width="12.533" height="14.485" viewBox="0 0 12.533 14.485"><path id="Subtraction_1" data-name="Subtraction 1" d="M681-360.515h0V-375l12.533,7.252L681-360.515Zm6.6-7.771-4.119,2.377.014,1.071,5.052-2.908Zm-4.115-2.376h0l0,3.015,2.608-1.511-2.612-1.5Z" transform="translate(-681 375)"/></svg>'
        };
    }
}
