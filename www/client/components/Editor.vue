<!-- Obsah -->
<template>
    <!-- Editor dokumentů -->
    <div class='wrapper'>
        <div class='editor-wrapper'>
            <!-- Postraní panel -->
            <aside>
                <!-- Dokumenty -->
                <draggable :list='docs'
                           group='documents'
                           handle='.handle'
                           ghost-class='ghost'
                           class='documents-group'
                           :options="{ animation: 200, dragClass: 'drag', forceFallback: true }"
                           @start='onDragStart'
                           @end='onDragEnd'
                           @change='pendingChanges = true'>
                        <!-- Položky -->
                        <div v-for='doc in docs'
                             class='doc-item'
                             :class="{ 'can-hover': !drag, 'active': activeDoc == doc.id }"
                             :key='doc.id'
                             :sort='false'
                             @click='onDocumentClick(doc, $event)'>
                                <!-- Checkbox -->
                                <el-checkbox v-model='doc.checked'
                                             class='checkbox'
                                             size='medium'
                                             :disabled='doc.id == activeDoc'></el-checkbox>
                                <!-- Název -->
                                <span class='name' :class="{ 'active': doc.checked }">{{ doc.name }}</span>
                                <!-- Ikona pro posun -->
                                <div class='handle' :class="{ 'dragging': drag, 'hover': !drag }">
                                    <span>⣶</span>
                                </div>
                        </div>
                </draggable>
                <!-- Panel akcí -->
                <div class='action-panel'>
                    <!-- Přidat -->
                    <el-button size='mini'
                               icon='el-icon-plus'
                               @click='onDocumentAdd()'>Přidat</el-button>
                    <!-- Smazat -->
                    <el-button size='mini'
                               icon='el-icon-minus'
                               :type="selectedDocs == 0 ? 'default' : 'danger' "
                               plain
                               @click='onDocumentDelete()'
                               :disabled='selectedDocs == 0'>Smazat {{ selectedDocs > 0 ? `(${selectedDocs})` : '' }}</el-button>
                </div>
                <!-- Další nastavení -->
                <div :class="{ 'codex-editor__loader': loading }"
                     v-if='loading && activeDoc'></div>
                <el-collapse class='settings-panel'
                             v-show='!loading'
                             v-if='activeDoc'>
                    <!-- Obecné -->
                    <el-collapse-item title='Obecné' name='1'>
                        <!-- Název -->
                        <label class='settings-label'>Název</label>
                        <el-input v-model='getActiveDocument().name'
                                  @change='setChanges'
                                  type='text'
                                  maxlength='50'
                                  show-word-limit></el-input>
                        <!-- Autor -->
                        <label class='settings-label'>Autor</label>
                        <el-select v-model='form.author.selected'
                                   multiple
                                   filterable
                                   remote
                                   clearable
                                   class='settings-input'
                                   placeholder='Vyhledávejte...'
                                   :multiple-limit='4'
                                   :remote-method='findAuthors'
                                   :loading='form.author.loading'
                                   @change='setChanges'>
                            <el-option v-for='author in form.author.list'
                                       :key='author.id'
                                       :value='author.id'
                                       :label='`${author.firstName} ${author.lastName}`'></el-option>
                        </el-select>
                        <!-- Poslední aktualizace -->
                        <label class='settings-label'>Naposled aktualizováno</label>
                        <el-date-picker v-model='form.updatedAt'
                                        class='settings-input'
                                        type='datetime'
                                        format='dd.MM. yyyy v HH:mm'
                                        :disabled='true'></el-date-picker>
                        <!-- Vytvořeno -->
                        <label class='settings-label'>Vytvořeno</label>
                        <el-date-picker v-model='form.createdAt'
                                        class='settings-input'
                                        type='datetime'
                                        :disabled='true'
                                        format='dd.MM. yyyy v HH:mm'></el-date-picker>
                    </el-collapse-item>
                    <!-- Soukromí -->
                    <el-collapse-item title='Soukromí' name='2' :disabled='privacyDisabled'>
                        <!-- Přístup -->
                        <label class='settings-label'>Přístup</label>
                        <el-select v-model='form.accessGroup'
                                   class='settings-input'
                                   @change='setChanges'>
                            <el-option :key='0' :value='0' label='Pouze autoři'></el-option>
                            <el-option :key='1' :value='1' label='Všichni pedagogové'></el-option>
                            <el-option :key='2' :value='2' label='Všichni redaktoři'></el-option>
                        </el-select>
                        <!-- Exkluzivní přístup -->
                        <label class='settings-label'>Exkluzivní přístup</label>
                        <el-select v-model='form.access.selected'
                                   multiple
                                   filterable
                                   remote
                                   clearable
                                   class='settings-input'
                                   placeholder='Vyhledávejte...'
                                   :remote-method='findUsers'
                                   :loading='form.access.loading'
                                   @change='setChanges'>
                            <el-option v-for='user in form.access.list'
                                       :key='user.id'
                                       :value='user.id'
                                       :label='`${user.firstName} ${user.lastName}`'></el-option>
                        </el-select>
                        <!-- Dostupnosst -->
                        <label class='settings-label'>Dostupnost</label>
                        <el-checkbox v-model='form.published'
                                     @change='setChanges'>{{ form.published ? 'Publikováno' : 'Neveřejné' }}</el-checkbox>
                    </el-collapse-item>
                    <!-- Obecné -->
                    <el-collapse-item title='Komentáře' name='3'>
                    </el-collapse-item>
                </el-collapse>
            </aside>
            <!-- Akce -->
            <div id='toolbar'>
                <!-- Uložení -->
                <el-button type='primary'
                           :loading='saving'
                           :disabled='!pendingChanges'
                           @click='save'>Uložit</el-button>
               <!-- Zobrazit bloky -->
               <el-button @click='viewBlocks = !viewBlocks'>{{ viewBlocks ? 'Skrýt bloky' : 'Zobrazit bloky' }}</el-button>
            </div>
            <!-- Načítání -->
            <div class='editor__loader' :class="{ 'codex-editor__loader': loading }" v-if='loading && activeDoc'>
            </div>
            <!-- Root element editoru -->
            <div id='codex-editor' v-show='!loading && activeDoc' :class="{ 'view-blocks': viewBlocks }">

            </div>
        </div>
        <!-- Chybové hlášení -->
        <div class='error-wrapper'>
            <!-- Chybový box -->
            <error title='Ups!'
                   message='Okno prohlížeče je příliš malé na zobrazení editoru. Zkuste okno rozšířit nebo použijte zařízení s větší obrazovkou. Rovněž není doporučeno používat editor na telefonu či tabletu, neboť na to nebyl navržen.'
                   code='error edt-01'></error>
        </div>
    </div>
</template>

<!-- Logika -->
<script>
    module.exports = {
        data: function() {
            return {
                // Instance editoru
                editor: null,
                // Pravda, pokud se dokument ukládá
                saving: false,
                // Pravda, pokud stahujeme data o dokumentu
                loading: false,
                // Pravda, pokud máme neuložené změny
                pendingChanges: false,
                // Dokumenty, které se mají zobrazit k editaci
                docs: [],
                // Aktivní dokument
                activeDoc: null,
                // Pravda, když měníme pořadí dokumentů knihy
                drag: false,
                // Pravda, pokud máme zobrazit hranice bloků
                viewBlocks: false,
                // Pravda, pokud můžeme upravovat kartu soukromí
                privacyDisabled: false,
                // Formulář
                form: {
                    author: {
                        loading: false,
                        selected: [],
                        list: []
                    },
                    updatedAt: new Date(),
                    createdAt: new Date(),
                    access: {
                        loading: false,
                        selected: [],
                        list: []
                    },
                    accessGroup: 0,
                    published: false
                }
            }
        },
        // Vlastnosti
        props: {
            // ID dokumentu k editaci
            target: {
                required: true
            },
            // Všechny dokumenty dané knihy
            documents: {
                required: true
            },
            // ID knihy
            book: {
                required: true
            }
        },
        // Když je komponenta vytvořena
        created() {
            // Závislosti editoru
            let dependencies = [
                // Editor.js (blokový editor)
                '/client/lib/editor.js',
                // Editor.js - nadpis
                '/client/lib/editor-header.js',
                // Editor.js - inline kód
                '/client/lib/editor-inline-code.js',
                // Editor.js - obrázek
                '/client/lib/editor-image.js',
                // Editor.js - embed plugin
                '/client/lib/editor-embed.js',
                // Editor.js - list
                '/client/lib/editor-list.js',
                // Editor.js - zvírazňovač
                '/client/lib/editor-marker.js',
                // Editor.js - citace
                '/client/lib/editor-quote.js',
                // Editor.js - přílohy
                '/client/lib/editor-attaches.js',
                // Code mirror - editace kódu
                '/client/lib/code-mirror.js'
            ];

            // Vyžádá si závislosti
            dependencies.forEach(dependency => {
                // Vytvoří script tag
                let script = document.createElement('script');

                // Přiřadí src atribut
                script.setAttribute('src', dependency);

                // Vloží script do DOMu
                document.head.appendChild(script);
            });
        },
        // Když je komponenta dosazena do DOMu
        mounted() {
            // Vyžádá si všechny dokumenty dané knihy
            $.get('/books/get-all-documents', { id: this.book }, (res) => {
                // Uloží dokumenty
                this.docs = res;
            }).then(() => {
                // Přesune props do modelu
                this.activeDoc = this.target;

                // Inicializuje editor
                this.init(this.target);
            });
        },
        // Computed vlastnosti
        computed: {
            // Získá počet vybraných dokumentů
            // z postraního panelu
            selectedDocs: function() {
                return this.docs.filter(item => item.checked).length;
            }
        },
        // Metody
        methods: {
            // Vytvoří a nakonfiguruje editor
            init(id) {
                // Započne načítání
                this.loading = true;

                // Konfigurační objekt
                let config = {
                    // Rootovský element
                    holder: 'codex-editor',
                    // Nástroje
                    tools: {
                        // Odstavec
                        paragraph: {
                            config: {
                                placeholder: 'Zde začněte psát...'
                            }
                        },
                        // Nadpis
                        header: {
                            class: Header,
                            config: {
                                placeholder: 'Nový nadpis'
                            }
                        },
                        // Inline kód
                        inlineCode: {
                            class: InlineCode,
                            shortcut: 'CMD+SHIFT+M',
                        },
                        // Blokový kód
                        code: {
                            class: window.model.CodeBlock
                        },
                        // Upozornění
                        alert: {
                            class: window.model.AlertBlock,
                            inlineToolbar: true
                        },
                        // Obrázek
                        image: {
                            class: ImageTool,
                            config: {
                                endpoints: {
                                    byFile: '/api/upload-by-file',
                                    byUrl: '/api/upload-by-url'
                                },
                                captionPlaceholder: 'Váš popisek...',
                                buttonContent: 'Vybrat obrázek'
                            }
                        },
                        // Embed
                        embed: {
                            class: Embed,
                            inlineToolbar: true,
                            config: {
                                services: {
                                    youtube: true
                                }
                            }
                        },
                        // List
                        list: {
                            class: List,
                            inlineToolbar: true
                        },
                        // Marker
                        marker: {
                            class: Marker
                        },
                        // Term tool
                        term: {
                            class: window.model.TermTool
                        },
                        // Asciinema
                        asciinema: {
                            class: window.model.AsciinemaBlock,
                            inlineToolbar: true
                        },
                        // Math tool
                        math: {
                            class: window.model.MathTool,
                            inlineToolbar: true
                        },
                        // Citace
                        quote: {
                            class: Quote,
                            inlineToolbar: true,
                            config: {
                                quotePlaceholder: 'Můj citát',
                                captionPlaceholder: 'Autor citátu'
                            }
                        },
                        // Přílohy
                        attaches: {
                            class: AttachesTool,
                            config: {
                                endpoint: '/api/upload-file',
                                buttonText: 'Vybrat soubor',
                                errorMessage: 'Něco se pokazilo'
                            }
                        },
                        // Spojení
                        /*
                        connection: {
                            class: window.model.ConnectionBlock
                        }
                        */
                    },
                    // Voláno při změně dokumentu
                    onChange: () => {
                        // Byly provedeny změny
                        this.pendingChanges = true;
                    },
                    // Zakáže výpis do konzole
                    logLevel: 'ERROR'
                }

                // Pokud již existuje instance editoru
                if (this.editor != null) {
                    // Zničí předchozí instanci
                    this.editor.destroy();
                }

                // Načítáme nějaký dokument?
                if (id) {
                    // Získá data ze serveru
                    $.get('/books/load-document', { id: id }, (res) => {
                        // Uloží data dokumentu, vytvoří
                        // novou instanci editoru:
                        config.data = JSON.parse(res.data);
                        this.editor = new EditorJS(config);

                        // Aktualizuje data dokumentu
                        this.form.createdAt = Date.parse(res.createdAt);
                        this.form.updatedAt = Date.parse(res.updatedAt);
                        this.form.accessGroup = res.accessGroup;
                        this.form.published = res.published;

                        // Získá nastavení dokumentu. Vyresetujeme
                        // předchozí data:
                        this.form.author.list = [];
                        this.form.access.list = [];

                        // Nejdříve získáme autory dokumentu. Nejlepší by bylo pochopitelně
                        // requesty provést paralelně, ale zde v rámci zjednodušení už tak
                        // složitého kódu získáme data sériově a to primárně kvůli ukončení
                        // načítání:
                        $.get('/books/get-document-authors', { id: id }, (uids) => {
                            // Uloží výsledek. Zatím jsme získali pouze
                            // identifikátory (ID) uživatelů.
                            this.form.author.selected = uids;

                            // Získá data o načtených uživatelích
                            // prostřednictvým získáných identifikátorů:
                            $.get('/user/get-users-by-id', { uids: uids }, (authors) => {
                                // Vloží data do selectu. Pro jistotu list
                                // nepřepíšeme, ale přidáme k  tomu současnému:
                                this.form.author.list = this.form.author.list.concat(authors);

                                // Pošle požadavek
                                $.get('/books/get-document-accesses', { id: id }, (_uids) => {
                                    // Uloží výsledek. Zatím jsme získali pouze
                                    // identifikátory (ID) uživatelů:
                                    this.form.access.selected = _uids;

                                    // Získá data o načtených uživatelích
                                    // prostřednictvým získáných identifikátorů:
                                    $.get('/user/get-users-by-id', { uids: _uids }, (users) => {
                                        // Vloží data do selectu. Pro jistotu list
                                        // nepřepíšeme, ale přidáme k  tomu současnému:
                                        this.form.access.list = this.form.access.list.concat(users);

                                        // Zjistí, zda můžeme upravovat přístup (karta soukromí)
                                        $.get('/books/can-edit-book', { id: this.book }, (res) => {
                                            // Aktualizuje hodnotu
                                            this.privacyDisabled = !res.result;
                                        });
                                    });

                                    // Ukončí načítání. Dáme drobný čas Vue, aby
                                    // data vepsal do controlů.
                                    setTimeout(() => { this.loading = false; }, 250);
                                });
                            });
                        });
                    });
                } else {
                    // Nenačítáme žádný konkrétní dokument. Vytvoříme
                    // novou instanci editoru:
                    this.editor = new EditorJS(config);
                }
            },
            // Uloží data editoru
            save() {
                // Pokud není formulář validní
                if (this.validateForm()) {
                    return;
                }

                // Započte načítání
                this.saving = true;

                // Uložení
                this.editor.save().then((data) => {
                    // Pošle požadavek na uložení dat dokumentu a na aktualizaci
                    // jejich pořadí bez ohledu na to, zda k změně jejich pořadí došlo.
                    // Požadavky provádíme v rámci zjednodušení procedury opět sériově:
                    $.post('/books/save-document', {
                        // Předá data do požadavku:
                        // ID dokumentu
                        id: this.activeDoc,
                        // Název dokumentu
                        name: this.getActiveDocument().name,
                        // Obsah dokumentu:
                        data: JSON.stringify(data),
                        // Přístupová skupina
                        accessGroup: this.form.accessGroup,
                        // Dostupnost
                        published: this.form.published
                    }, (res) => {
                        // Pošle požadavek na změnu pořadí dokumentů:
                        $.post('/books/save-order', { data: this.docs.map(
                            // Vytvoříme správný formát = objekt s ID a pořadím:
                            doc => ({
                                // Získáme ID
                                id: doc.id,
                                // Získáme pořadí dokumentu
                                order: this.docs.indexOf(this.docs.find(item => item.id == doc.id ))
                            })
                        // Pokud odeslání proběhne v pořádku
                        ), id: this.book }, (_res) => {
                            // Pokud uložení neproběhlo v pořádku
                            if (_res.result == false) {
                                // Uvědomí uživatele o výsledku procesu
                                this.$notify.error({
                                    title: 'Nezdar',
                                    message: _res.message,
                                    duration: 5500
                                });
                            }

                            // Pošle požadavek na uložení autorů
                            $.post('/books/save-authors', { id: this.activeDoc, uids: this.form.author.selected }, (__res) => {
                                // Pošle požadavek na uložení
                                // unikátních přístupů:
                                $.post('/books/save-accesses', { id: this.activeDoc, uids: this.form.access.selected }, (___res) => {
                                    // Ukončí načítání
                                    this.saving = false;

                                    // Aktualizuje stav změn v dokumentu
                                    this.pendingChanges = false;

                                    // Uvědomí uživatele o výsledku procesu
                                    this.$notify.success({
                                        title: 'Úspěch',
                                        message: 'Uložení proběhlo v pořádku.',
                                        duration: 5500
                                    });
                                });
                            });
                        });
                    });
                }).catch((error) => {
                    // Zachycení chyby při uložení
                });
            },
            // Načte data do editoru
            load(id) {
                // Znovu načte editor
                this.init(id);
            },
            // Když je editační menu uzavřeno
            onDrawerClosed() {
                this.editVisible = false;
            },
            // Když začneme měnit pořadí dokumentů
            onDragStart() {
                // Změna započala
                this.drag = true;

                // Změníme cursor. Toto je provedeno i
                // při najetí myší na "handle" (ikonku pro přesun)
                // skrze CSS. Změna skrze JS zajistí
                // správné zobrazení cursoru i při najetí
                // myší mimo handle.
                document.body.style.cursor = 'grabbing';
            },
            // Když skončíme se změnou pořadí dokumentů
            onDragEnd() {
                // Změna je u konce
                this.drag = false;

                // Změníme cursor na původní
                document.body.style.cursor = 'default';
            },
            // Když klikneme na dokument v postranním panelu
            onDocumentClick(doc, e) {
                // Neklikli jsme náhodou na checkbox?
                if (!e.srcElement.className.includes('el-checkbox')) {
                    // Máme potřebná práva?
                    $.get('/books/can-edit-document', { id: doc.id }, (res) => {
                        // Pokud máme práva
                        if (res.result) {
                            // Je dokument zaškrtlí?
                            if (doc.checked) {
                                // Zruší zaškrtnutí. Nemůžeme smazat
                                // aktivní (aktuální) dokument
                                doc.checked = false;
                            }

                            // Máme neuložené změny?
                            if (this.pendingChanges) {
                                // Vytvoří nový modal
                                this.$confirm('Dokument obsahuje neuložené změny. Pokud nyní odejdete, dojde k jejich ztrátě.','Opravdu chcete ukončit úpravy?', {
                                    type: 'warning',
                                    confirmButtonText: 'Odejít bez uložení',
                                    confirmButtonClass: 'el-button--danger',
                                    cancelButtonText: 'Zpět'
                                }).then(() => {
                                    // Změníme aktivní dokument
                                    this.activeDoc = doc.id;

                                    // Aktualizujeme editor novými daty
                                    this.init(this.activeDoc);

                                    // Vynulujeme změny
                                    this.pendingChanges = false;
                                }).catch(() => {
                                    // Uživatel zrušil přechod
                                });
                            } else {
                                // Změníme aktivní dokument a aktualizujeme editor
                                if (!this.isInInit) {
                                    this.activeDoc = doc.id;
                                    this.init(this.activeDoc);
                                }
                            }
                        } else {
                            // Na editace nemáme potřebná práva
                            this.$alert('Pro editaci dokumentu nemáte dostatečná práva. Obraťe se na vlastníka dokumentu nebo správce systému.','Neoprávněný přístup', {
                                type: 'error'
                            });
                        }
                    });
                }
            },
            // Získá autory ze serveru
            findAuthors(query) {
                // Získáme současné autory, které nebudeme chtít mazat
                let current = this.form.author.list.filter(item => this.form.author.selected.includes(item.id));

                // Hledá uživatel něco?
                if (query !== '') {
                    // Začneme načítat
                    this.form.author.loading = true;

                    // Pošleme požadavek na server
                    $.get('/user/get-redactors', { query: query }, (res) => {
                        // Seřadíme a uložíme odpověď do pole uživatelů.
                        this.form.author.list = res.sort((a, b) => a.similarity < b.similarity);

                        // Projdeme si všechny vybrané autory
                        current.forEach(author => {
                            // Existuje už autor ve výsledcích vyhledávání?
                            if (res.find(item => item.id == author.id) == null) {
                                this.form.author.list.unshift(author);
                            }
                        });

                        // Poté ukončíme načítání
                        this.form.author.loading = false;
                    });
                } else {
                    // Vyprázdní list
                    this.form.author.list = current;
                }
            },
            // Získá autory ze serveru
            findUsers(query) {
                // Získáme současné autory, které nebudeme chtít mazat
                let current = this.form.access.list.filter(item => this.form.access.selected.includes(item.id));

                // Hledá uživatel něco?
                if (query !== '') {
                    // Začneme načítat
                    this.form.access.loading = true;

                    // Pošleme požadavek na server
                    $.get('/user/get-redactors', { query: query }, (res) => {
                        // Seřadíme a uložíme odpověď do pole uživatelů.
                        this.form.access.list = res.sort((a, b) => a.similarity < b.similarity);

                        // Projdeme si všechny vybrané autory
                        current.forEach(user => {
                            // Existuje už autor ve výsledcích vyhledávání?
                            if (res.find(item => item.id == user.id) == null) {
                                this.form.access.list.unshift(user);
                            }
                        });

                        // Poté ukončíme načítání
                        this.form.access.loading = false;
                    });
                } else {
                    // Vyprázdní list
                    this.form.access.list = current;
                }
            },
            // Získá právě aktivní dokument
            getActiveDocument() {
                return this.docs.find(doc => doc.id == this.activeDoc);
            },
            // Nastaví dokument jako změněný bez uložení
            setChanges() {
                this.pendingChanges = true;
            },
            // Validace postranního panelu / formuláře
            validateForm() {
                // Získá název dokumentu
                let name = this.getActiveDocument().name.length;

                // Validace názvu
                if (name < 3 || name > 50) {
                    // Vytvoří modal s informací o nezdaru:
                    this.$alert('Název dokumentu by neměl být kratší než 3 znaky a delší než 50 znaků!','Neplatný název', {
                        type: 'error'
                    });

                    return true;
                }

                // Validace autorů
                if (this.form.author.selected.length == 0) {
                    // Vytvoří modal s informací o nezdaru:
                    this.$alert('Dokument musí mít alespoň jednoho autora!','Neplatný autor', {
                        type: 'error'
                    });

                    return true;
                }
            },
            // Smazání dokumentu (popř. dokumentů)
            onDocumentDelete() {
                // Vyvoláme potvrzovaací nabídku
                this.$confirm('Smazání dokumentů je nevratné.','Opravdu chcete smazat dokument(y)?', {
                    type: 'warning',
                    confirmButtonText: 'Smazat',
                    confirmButtonClass: 'el-button--danger',
                    cancelButtonText: 'Zpět'
                }).then(() => {
                    // ID dokumentů, jenž mají být smazány
                    let ids = [];

                    // Projdeme všechny dokumenty
                    this.docs.forEach(doc => {
                        // Je dokument zaškrtlí?
                        if (doc.checked) {
                            // Přidáme prvek do pole pro smazání
                            ids.push(doc.id);
                        }
                    });

                    // Pošle požadavek na smazání
                    $.post('/books/delete-documents', { ids: ids }, res => {
                        // Smaže prvky z listu
                        this.docs = this.docs.filter(doc => !ids.includes(doc.id) && !res.result.includes(doc.id));

                        // Podařilo se smazat všechny prvky?
                        if (res.result.length != 0) {
                            // Uvědomí uživatele o chybách
                            this.$notify.error({
                                title: 'Chyba',
                                message: 'Některé dokumenty nemohli být kvůli nedostatečnému oprávnění smazány.',
                                duration: 5500
                            });
                        }
                    });
                }).catch(() => {});
            },
            // Přidání nového dokumentu
            onDocumentAdd() {
                // Vyvolá modál
                this.$prompt('Napište název nového dokumentu', 'Nový dokument', {
                    confirmButtonText: 'Vytvořit',
                    cancelButtonText: 'Zpět',
                    inputPattern:  /^(.){3,50}$/,
                    inputErrorMessage: 'Název musí mít alespoň 3 znaky a méně než 50 znaků.'
                }).then(({ value }) => {
                    // Přidá prvek
                    $.post('/books/create-document', { bookId: this.book, name: value, order: this.docs.length }, () => {
                        // Aktualizuje dokumenty
                        $.get('/books/get-all-documents', { id: this.book }, (res) => {
                            // Uloží dokumenty
                            this.docs = res;
                        });
                    });
                }).catch(() => {});
            }
        }
    }
</script>

<!-- Styly -->
<style scoped lang='scss'>
    // Proměnné
    // Šířka postraního panelu
    $menu-width: 400px;

    // Opraví styly přepsané ant design knihovnou
    .ce-settings, .ce-settings {
        // Zabrání rozházení nástrojů uvnitř nastavení bloků
        box-sizing: content-box;
    }

    // Obalovač
    .wrapper {
        // Pozice pro správné umístění status-baru
        position: relative;
    }

    #toolbar {
        // Vzhled a rozměry
        overflow: auto;
        padding: 12px;
        background-color: rgba(255, 255, 255, .96);

        // Umístí toolbar na vršek obrazovky a
        // napravo od postraního panelu
        position: fixed;
        top: 0;
        left: $menu-width;
        width: calc(100% - #{$menu-width});
        z-index: 10;

        // Zarovná tlačítka do prava
        button {
            margin-left: 15px;
            float: right;
        }
    }

    // Postraní panel
    aside {
        // Rozměry
        width: $menu-width;
        height: 100vh;
        padding-bottom: 150px;

        // Umístění
        float: left;
        position: fixed;
        top: 0;

        // Vzhled
        border-right: 1px solid #E6E6E6;
        background-color: #FDFDFD;

        // Zakáže možnost vybírat text
        * {
            -webkit-touch-callout: none;
              -webkit-user-select: none;
               -khtml-user-select: none;
                 -moz-user-select: none;
                  -ms-user-select: none;
                      user-select: none;
        }

        // Umožní scrollování
        overflow-y: scroll;
    }

    // Item dokumentu (položky v postraním panelu)
    .doc-item {
        // Rozměry a pozice
        position: relative;
        height: 50px;
        line-height: 50px;

        // Vzhled
        font-size: 14px;
        color: #303133;

        // Zarovnání
        padding-left: 30px;

        // Checkbox
        .checkbox {
            // Zarovnání
            float: left;

            // Pozice
            position: absolute;
            top: 50%;
            transform: translate(0, -50%);
        }

        // Název itemu
        .name {
            float: left;
            padding-left: 25px;

            // Barva, když je item aktivní
            &.active {
                color: #00497B !important;
            }
        }

        // Po najetí myší, když zrovna
        // nepřetahujeme položky
        &.can-hover:hover {
            // Barva pozadí
            background-color: #ECF5FF;

            // Cursor
            cursor: pointer;
        }

        // Když je item aktivní
        &.active {
            // Přidá okraj
            border-left: 3px solid #00497B;

            // Sníží odsazení o 3 pixely jako
            // kompenzaci pro nově přidanou šířku okraje
            padding-left: 27px;
        }
    }

    // Náhled momentální pozice právě taženého prvku
    .ghost {
        background-color: #ECF5FF;
    }

    // Zakryje výchozí náhled taženého prvku
    .drag {
        visibility: hidden;
    }

    // Část itemu, za který mohou být
    // položky přesouvány
    .handle {
        // Pozice
        float: right;
        width: 50px;

        // Ikonka
        span {
            // Vzhled a pozice
            line-height: 40px;
            color: #909399;
            font-size: 1.5em;
            padding-left: 10px;
        }

        // Po najetí myší (třída přidána skrze JS)
        &.hover {
            // Změní cursor na ručičku indikující
            // možnost přesunutí
            cursor: move;
            cursor: grab;
            cursor: -moz-grab;
            cursor: -webkit-grab;
        }

        // Po kliknutí myší / přesunu (třída přidána skrze JS)
        &.dragging {
            // Změní cursor na sevřenou ručičku
            // indikující tažení
            cursor: move;
            cursor: grabbing;
            cursor: -moz-grabbing;
            cursor: -webkit-grabbing;
        }
    }

    // Akce nad postraním panelem
    .action-panel {
        // Odsazení
        padding: 30px;
        padding-top: 20px;
    }

    // Panel nastavení
    .settings-panel {
        // Umožní uskutečňovat výběry
        * {
            -webkit-touch-callout: auto;
              -webkit-user-select: auto;
               -khtml-user-select: auto;
                 -moz-user-select: auto;
                  -ms-user-select: auto;
                      user-select: auto;
        }

        // Nastyluje všechny labely
        label:not(.el-checkbox) {
            margin: 7px 0;
        }

        // Nastyluje labely controlů
        label.settings-label {
            display: block;
            opacity: .55;
        }

        // Třída pro ovládací prvky panelu
        .settings-input {
            width: 100%;
        }
    }

    // Collapse v postraním menu
    .el-collapse-item {
        padding-left: 30px;
        padding-right: 20px;
    }

    // Obalovač codex-editoru
    .codex-editor {
        // Odstraní limit šířky
        max-width: none;

        // Nastaví větší šířku
        width: 80%;

        // Vyrovná editor na střed
        margin: 0 auto;
        margin-top: 8%;
        margin-bottom: 8%;
    }

    // Upraví codex editor
    #codex-editor {
        // Odsazení
        padding-left: $menu-width;
    }

    // Zvětší šířku obsahu editačního bloku
    .ce-block__content {
        // Odstraní limit šířky
        max-width: none;

        // Přidá odsazení, aby bylo vidět
        // menu (tři tečky) pro nastavení bloku
        padding-right: 100px;
    }

    // Upraví toolbar, aby byl stejně široký
    // jako editační bloky
    .ce-toolbar__content {
        max-width: none;

        // Zarovná toolbar
        position: absolute;
        left: -40px;
        bottom: 3px;
    }

    // Odstraní původní in-line odsazení
    // codex editoru
    .codex-editor__redactor[style] {
        padding-bottom: 0 !important;
    }

    // Obalovač chybového hlášení
    .error-wrapper {
        // Zakryje hlášení
        display: none;
    }

    // Když se menu ani dokument nevejdou vedle sebe. Max-width je
    // roven šířce menu + šířce dokumentu + 100px pro odsazení.
    @media only screen and (max-width: #{$menu-width + 650 + 100}) {
        // Hlavní obalovač editoru
        .editor-wrapper {
            // Zakryje editor
            display: none;
        }

        // Hlavní obalovač chybového hlášení
        .error-wrapper {
            // Zobrazí hlášení
            display: block !important;
        }
    }
</style>
