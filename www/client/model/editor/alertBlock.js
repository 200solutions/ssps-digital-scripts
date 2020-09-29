/**
 * @typedef {Object} Alert
 * @description Blok pro přidání informace | upozornění | varování
 */
window.model.AlertBlock = class Alert {
    /**
     * Výchozí placeholder pro alert
     *
     * @return {string}
     * @constructor
     */
    static get DEFAULT_PLACEHOLDER() {
        return '';
    }

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

        // Styly
        this._CSS = {
            block: this.api.styles.block,
            wrapper: 'ce-paragraph'
        };

        // Bind key-up eventu
        this.onKeyUp = this.onKeyUp.bind(this);

        /**
        * Placeholder pro alert, pokud je prvním blokem
        * @type {string}
        */
        this._placeholder = config.placeholder ? config.placeholder : Alert.DEFAULT_PLACEHOLDER;

        // Založí data a získá view
        this._data = {};
        this._element = this.drawView();

        // Získá typ alertu a uloží ho
        this.type = data.type;
        this._element.classList.add(data.type);

        // Nastavení bloku
        this.settings = [
        {
            name: 'info',
            icon: `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"><path d="M17,8a9,9,0,1,0,9,9A9,9,0,0,0,17,8Zm0,16.258A7.258,7.258,0,1,1,24.258,17,7.254,7.254,0,0,1,17,24.258Zm0-12.266a1.524,1.524,0,1,1-1.524,1.524A1.524,1.524,0,0,1,17,11.992Zm2.032,9.218a.436.436,0,0,1-.435.435H15.4a.436.436,0,0,1-.435-.435v-.871A.436.436,0,0,1,15.4,19.9h.435V17.581H15.4a.436.436,0,0,1-.435-.435v-.871a.436.436,0,0,1,.435-.435h2.323a.436.436,0,0,1,.435.435V19.9H18.6a.436.436,0,0,1,.435.435Z" transform="translate(-8 -8)"/></svg>`
        },
        {
            name: 'warning',
            icon: `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"><path d="M17,8a9,9,0,1,0,9,9A9,9,0,0,0,17,8Zm0,16.258A7.258,7.258,0,1,1,24.258,17,7.254,7.254,0,0,1,17,24.258Zm1.524-3.774A1.524,1.524,0,1,1,17,18.96,1.526,1.526,0,0,1,18.524,20.484Zm-2.953-7.672.247,4.935a.435.435,0,0,0,.435.414h1.494a.435.435,0,0,0,.435-.414l.247-4.935a.435.435,0,0,0-.435-.457H16.006A.435.435,0,0,0,15.571,12.812Z" transform="translate(-8 -8)"/></svg>`
        },
        {
            name: 'danger',
            icon: `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"><path d="M17,8a9,9,0,1,0,9,9A9,9,0,0,0,17,8Zm0,16.258A7.258,7.258,0,1,1,24.258,17,7.256,7.256,0,0,1,17,24.258Zm3.694-9.515L18.437,17l2.257,2.257a.436.436,0,0,1,0,.617l-.82.82a.436.436,0,0,1-.617,0L17,18.437l-2.257,2.257a.436.436,0,0,1-.617,0l-.82-.82a.436.436,0,0,1,0-.617L15.563,17l-2.257-2.257a.436.436,0,0,1,0-.617l.82-.82a.436.436,0,0,1,.617,0L17,15.563l2.257-2.257a.436.436,0,0,1,.617,0l.82.82a.436.436,0,0,1,0,.617Z" transform="translate(-8 -8)"/></svg>`
        }];

        // Vytvoří a naplní data property
        this.data = data;
    }

    /**
     * Zjistí, zda je obsah prázdný a nastavý prázdný string jako obsah.
     * Některé prohlížeče (např. Safari) vkládá <br> tagy do prázdných
     * contenteditable elementů.
     *
     * @param {KeyboardEvent} e - key up event
     */
    onKeyUp(e) {
        if (e.code !== 'Backspace' && e.code !== 'Delete') {
            return;
        }

        // Získá text-content
        const {textContent} = this._element;

        // Máme prázdný obsah?
        if (textContent === '') {
            // Vynuluje potencionální <br> tag
            this._element.innerHTML = '';
        }
    }

    /**
     * Vytvoří view
     * @return {HTMLElement}
     * @private
     */
    drawView() {
        // Vytvoří <div> tag
        let div = document.createElement('div');

        // Přidá CSS třídy a atributy
        div.classList.add(this._CSS.wrapper, this._CSS.block, 'ce-alert');
        div.contentEditable = true;
        div.dataset.placeholder = this._placeholder;

        // Zaregistruje key-up event
        div.addEventListener('keyup', this.onKeyUp);

        // Vrátí element
        return div;
    }

    /**
     * Vyrenderuje view
     * @returns {HTMLDivElement}
     * @public
     */
    render() {
        return this._element;
    }

    /**
     * Metoda popisující spojení dvou bloků.
     * Voláno codex-editorem zmáčknutím klávesy backspace na začátku bloku
     * @param {AlertData} data
     * @public
     */
    merge(data) {
        // Vytvoří nová data
        let newData = {
            text : this.data.text + data.text
        };

        // Aktualizuje data
        this.data = newData;
    }

    /**
     * Zvaliduje data alertu:
     * - kontrola obsahu
     *
     * @param {AlertData} savedData — data získána po uložení
     * @returns {boolean} false pokud nejsou uložená data validní, jinak true
     * @public
     */
    validate(savedData) {
        // Máme nějaká data?
        if (savedData.text.trim() === '') {
            return false;
        }

        return true;
    }

    /**
     * Extrahuje data bloku z view
     * @param {HTMLDivElement} toolsContent - Vyrenderovaný blok
     * @returns {ParagraphData} - uložená data
     * @public
     */
    save(toolsContent) {
        return {
            text: toolsContent.innerHTML,
            type: this.type
        };
    }

    /**
    * Povolí conversion toolbar. Alert může být převeden do/z ostatních bloků
    */
    static get conversionConfig() {
        return {
            export: 'text',
            import: 'text'
        };
    }

    /**
     * Sanitizační pravidla
     */
    static get sanitize() {
        return {
            text: {
                br: true,
            }
        };
    }

    /**
     * Získá data bloku
     * @returns {AlertData} Momentální data
     * @private
     */
    get data() {
        this._data.text = this._element.innerHTML;
        return this._data;
    }

    /**
     * Uloží data pluginu:
     * - do vlastnosti this._data
     * - do HTML
     *
     * @param {AlertData} data — data, která ukládáme
     * @private
    */
    set data(data) {
        this._data = data || {};
        this._element.innerHTML = this._data.text || '';
    }

    /**
     * API pro vkládání obsahu z clipboardu.
     * Konfigurace pro řešení P tagů.
     *
     * @returns {{tags: string[]}}
     */
    static get pasteConfig() {
        return {
            tags: [ 'P' ]
        };
    }

    /**
     * Ikonka a název pro zobrazení v toolbaru
     *
     * @return {{icon: string, title: string}}
     */
    static get toolbox() {
        return {
            // Název
            title: 'Upozornění',
            // Ikonka pro blokový kód
            icon: `<svg width="16" height="17" viewBox="0 0 320 294" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M160.5 97c12.426 0 22.5 10.074 22.5 22.5v28c0 12.426-10.074 22.5-22.5 22.5S138 159.926 138 147.5v-28c0-12.426 10.074-22.5 22.5-22.5zm0 83c14.636 0 26.5 11.864 26.5 26.5S175.136 233 160.5 233 134 221.136 134 206.5s11.864-26.5 26.5-26.5zm-.02-135c-6.102 0-14.05 8.427-23.842 25.28l-74.73 127.605c-12.713 21.444-17.806 35.025-15.28 40.742 2.527 5.717 8.519 9.175 17.974 10.373h197.255c5.932-1.214 10.051-4.671 12.357-10.373 2.307-5.702-1.812-16.903-12.357-33.603L184.555 70.281C174.608 53.427 166.583 45 160.48 45zm154.61 165.418c2.216 6.027 3.735 11.967 4.393 18.103.963 8.977.067 18.035-3.552 26.98-7.933 19.612-24.283 33.336-45.054 37.586l-4.464.913H61.763l-2.817-.357c-10.267-1.3-19.764-4.163-28.422-9.16-11.051-6.377-19.82-15.823-25.055-27.664-4.432-10.03-5.235-19.952-3.914-29.887.821-6.175 2.486-12.239 4.864-18.58 3.616-9.64 9.159-20.55 16.718-33.309L97.77 47.603c6.469-11.125 12.743-20.061 19.436-27.158 4.62-4.899 9.562-9.07 15.206-12.456C140.712 3.01 150.091 0 160.481 0c10.358 0 19.703 2.99 27.989 7.933 5.625 3.356 10.563 7.492 15.193 12.354 6.735 7.072 13.08 15.997 19.645 27.12l.142.24 76.986 134.194c6.553 10.46 11.425 19.799 14.654 28.577z"/></svg>`
        };
    }

    /**
    * Interní funkce codex editoru pro
    * render nastavení
    * @return {HTMLElement}
    */
    renderSettings(){
        // Obalovač voleb
        const wrapper = document.createElement('div');

        // Projdeme všechny nastavení
        this.settings.forEach(tune => {
            // Vytvoří nový button
            let button = document.createElement('div');

            // Přidá adekvátní CSS třídu pro button,
            // doplní ikonku a vloží element do DOMu
            button.classList.add('cdx-settings-button');
            button.innerHTML = tune.icon;
            wrapper.appendChild(button);

            // Nastaví button na aktivní, pokud aktivní skutečně je
            if (this.type == tune.name) {
                // přidá aktivní třídu
                button.classList.add('cdx-settings-button--active');
            }

            // Click event listener
            button.addEventListener('click', () => {
                // Klikli jsme na již aktivní tlačítko?
                if (button.classList.contains('cdx-settings-button--active')) {
                    // Odebreme aktivní třídu z buttonu i bloku
                    button.classList.remove('cdx-settings-button--active');
                    this._element.classList.remove(tune.name);

                    // Neutralizujeme typ alertu
                    this.type = '';
                } else {
                    // Na tlačítko ještě nebylo kliknuto. Odebereme
                    // všechny předchozí aktivní třídy
                    for (let element of wrapper.getElementsByClassName('cdx-settings-button--active')) {
                        // Odebere třídu
                        element.classList.remove('cdx-settings-button--active');
                    }

                    // Očistí blok element od existujících tříd
                    this._element.classList.remove('info');
                    this._element.classList.remove('warning');
                    this._element.classList.remove('danger');

                    // Přidá třídu na právě kliknuté tlačítko a blok samotný
                    button.classList.add('cdx-settings-button--active');
                    this._element.classList.add(tune.name);

                    // Aktualizuje typ alertu
                    this.type = tune.name;
                }
            });
        });

        return wrapper;
    }
}
