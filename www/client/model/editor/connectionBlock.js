window.model.ConnectionBlock = class ConnectionBlock {
    // Konstruktor
    constructor({ data }){
        // Inicializace vnitřního stavu instance
        this.data = data;
        this.wrapper = null;
        this.subjects = null;
        this.books = null;
        this.documents = null;

        this.documentsData = null;
        this.subjectsData = null;
    }

    // Getter pro zobrazení v tool-boxu
    static get toolbox() {
        return {
            // Název
            title: 'Spojení na objekt',
            // Ikonka pro blokový kód
            icon: `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="13.761" viewBox="0 0 14 13.761"><path d="M6.539,3.321.851,5.454A1.315,1.315,0,0,0,0,6.685v6.156a1.312,1.312,0,0,0,.725,1.173l5.689,2.844a1.3,1.3,0,0,0,1.173,0l5.689-2.844A1.31,1.31,0,0,0,14,12.841V6.685a1.311,1.311,0,0,0-.851-1.228L7.461,3.324A1.29,1.29,0,0,0,6.539,3.321ZM7,5.02l5.251,1.969v.03L7,9.152,1.75,7.019v-.03Zm.875,9.736V10.684l4.376-1.778v3.662Z" transform="translate(0 -3.237)"/></svg>`
        };
    }

    // Render funkce
    render() {
        // Obalovač s načítáním
        this.wrapper = document.createElement('div');
        this.wrapper.classList.add('cdx-block');
        this.wrapper.classList.add('editor-document-link');

        // Vytvoří select na předměty, knihy a dokumenty
        this.subjects = document.createElement('select');
        this.books = document.createElement('select');
        this.documents = document.createElement('select');

        // Přidá třídy
        this.subjects.classList.add('cdx-select');
        this.books.classList.add('cdx-select');
        this.documents.classList.add('cdx-select');

        // Zažádá si o data sekcí a jejich knih
        $.get('/books/get-subjects-and-books', subjectsData => {
            // Uloží předměty a jejich knihy
            this.subjectsData = subjectsData;

            // Vloží data do selectu předmětů
            subjectsData.forEach(subject => {
                // Vytvoří možnost
                let option = document.createElement('option');
                option.text = subject.title;
                option.value = subject.id;
                option.setAttribute('id', `subject-${subject.id}`);

                // Vloží ji do selectu
                this.subjects.appendChild(option);
            });

            // Máme nějaký předchozí uložený předmět? Pokud ne, vynulujeme výběr.
            this.subjects.value = this.data && this.data.subject ? this.data.subject : -1;

            // Máme nějaký vybraný předmět?
            if (this.data && this.data.subject) {
                // Vypíše knihy vybraného předmětu
                subjectsData.find(subject => {
                    return subject.id == this.data.subject;
                }).books.forEach(book => {
                    // Vytvoří možnost
                    let option = document.createElement('option');
                    option.text = book.title;
                    option.value = book.id;
                    option.setAttribute('id', `book-${book.id}`);

                    // Vloží ji do selectu
                    this.books.appendChild(option);
                });
            }

            // Máme nějakou vybranou čebnici? Pokud ne, vynulujeme výběr.
            this.books.value = this.data && this.data.book ? this.data.book : -1;

            // Máme kromě knihy vybraný i nějaký dokument?
            if (this.data && this.data.document) {
                // Získá dokumenty dané učebnice
                $.get('/books/get-published-documents', { id: this.data.book }, documents => {
                    // Uloží dokumenty
                    this.documentsData = documents;

                    // Vloží dokumenty do selectu
                    documents.forEach(doc => {
                        // Vytvoří možnost
                        let option = document.createElement('option');
                        option.text = doc.name;
                        option.value = doc.id;

                        // Vloží ji do selectu
                        this.documents.appendChild(option);
                    });

                    // Vynuluje vybrané prvky
                    this.documents.value = this.data.document;
                });
            }
        });

        // Vloží selecty do obalovače
        this.wrapper.appendChild(this.subjects);
        this.wrapper.appendChild(this.books);
        this.wrapper.appendChild(this.documents);

        // Přiřadí event handler k výběru předmětu
        this.subjects.addEventListener('change', event => {
            // Vybrali jsme nějaký předmět?
            if (this.subjects.selectedIndex != -1) {
                // Vynuluje předchozí knihy
                this.books.innerHTML = '';

                // Vypíše knihy vybraného předmětu
                this.subjectsData.find(subject => {
                    return subject.id == this.subjects.options[this.subjects.selectedIndex].value;
                }).books.forEach(book => {
                    // Vytvoří možnost
                    let option = document.createElement('option');
                    option.text = book.title;
                    option.value = book.id;

                    // Vloží ji do selectu
                    this.books.appendChild(option);
                });

                // Vynuluje vybrané prvky
                this.books.selectedIndex = -1;
                this.documents.selectedIndex = -1;
            }
        });

        // Přiřadí event handler k výběru knihy
        this.books.addEventListener('change', event => {
            // Vybrali jsme nějakou knihu?
            if (this.books.selectedIndex != -1) {
                // Vynuluje předchozí dokumenty
                this.documents.innerHTML = '';

                // Získá vybranou knihu
                let id = this.books.options[this.books.selectedIndex].value;

                // Získá dokumenty dané učebnice
                $.get('/books/get-published-documents', { id: id }, documents => {
                    // Uloží dokumenty
                    this.documentsData = documents;

                    // Vloží dokumenty do selectu
                    documents.forEach(doc => {
                        // Vytvoří možnost
                        let option = document.createElement('option');
                        option.text = doc.name;
                        option.value = doc.id;

                        // Vloží ji do selectu
                        this.documents.appendChild(option);
                    });

                    // Vynuluje vybrané prvky
                    this.documents.selectedIndex = -1;
                })
            }
        });

        // Vrátí připravený element
        return this.wrapper;
    }

    // Save funkce
    save(content) {
        // Vrátí data
        /*
        return {
            // Obsah
            subject: this.subjects.options[this.subjects.selectedIndex].value,
            book: this.books.options[this.books.selectedIndex].value,
            document: this.documents.options[this.documents.selectedIndex].value
        }
        */
    }
}
