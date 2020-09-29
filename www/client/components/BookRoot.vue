<!-- Obsah -->
<template>
    <!-- Kniha -->
    <div>
        <!-- Levý sloupec -->
        <aside v-if='!edit' class='no-print'>
            <!-- Menu -->
            <el-menu class='aside'
                     :default-openeds='opened'
                     @open='onItemOpen'
                     @close='onItemClose'>
                <!-- Link domů -->
                <n-link to='Homepage:default'>
                    <el-menu-item>
                        <!-- Ikona -->
                        <i class='el-icon-house'></i>
                        <!-- Popisek -->
                        Domů
                    </el-menu-item>
                </n-link>
                <!-- Kniha -->
                <el-submenu index='-1'>
                    <template slot='title'>
                        <!-- Ikonka -->
                        <i class='el-icon-collection'></i>
                        <!-- Název knihy -->
                        <span>Kniha</span>
                    </template>
                    <!-- Vygeneruje položky -->
                    <el-submenu v-for='doc in documents' :index='doc.id' :key='doc.id'>
                        <!-- Název a ikona -->
                        <template slot='title'>
                            <!-- Dynamická ikona -->
                            <i :class='folderIcon(doc.id)'></i>
                            <!-- Název -->
                            <span>{{ doc.name }}</span>
                        </template>
                        <!-- Sekce -->
                        <el-menu-item v-for='section in doc.sections'
                                      :index='`${doc.id}-${section.id}`'
                                      :key='`${doc.id}-${section.id}`'
                                      @click='menuItemClick'>{{ section.name }}</el-menu-item>
                    </el-submenu>
                </el-submenu>
            </el-menu>
        </aside>
        <!-- Pravý sloupec -->
        <main :class="{ 'edit-mode' : edit }">
            <!-- Obsah -->
            <document v-if='!edit && activeDoc'
                      :did='activeDoc'
                      @change='onActiveDocumentChange'></document>
            <!-- Editor -->
            <editor v-if='edit'
                    :target='activeDoc'
                    :book='target'
                    :documents='documents'
                    ref='editor'></editor>
        </main>
        <!-- Editovat -->
        <el-button type='primary'
                   class='btn-edit no-print'
                   :class="{ 'btn-edit--invisible': !isRedactor }"
                   @click='onEditClick'
                   :icon=" edit ? 'el-icon-close' : 'el-icon-edit' ">{{ edit ? 'Ukončit editaci' : 'Upravit knihu' }}</el-button>
    </div>
</template>

<!-- Logika -->
<script>
    module.exports = {
        // Data komponenty
        data: function() {
            return {
                // Aktivní (právě vybraný) dokument
                activeDoc: null,
                // Editace
                edit: false,
                // Dokumenty
                documents: [],
                // Pole indexů právě otevřených
                // dokumentů v menu
                opened: ['-1'],
                // Pravda, pokud je okno prohlížeče moc malé
                // na zobrazení editoru
                editorError: false,
                // Pravda, pokud má být menu minimalizované
                isCollapsed: false,
                // Pravda, pokud je uživatel redaktor
                isRedactor: false
            }
        },
        // Vlastnosti
        props: {
            // ID knihy
            target: {
                required: true
            }
        },
        // Komponenty
        components: {
            'editor':  httpVueLoader('/client/components/Editor.vue'),
            'document': httpVueLoader('/client/components/Document.vue')
        },
        // Při vytvoření
        created() {
            // Inicializace
            this.init();
        },
        // Metody
        methods: {
            // Inicializace
            init() {
                // Získá dokumenty dané učebnice
                $.get('/books/get-published-documents', { id: this.target }, (res) => {
                    // Uloží dokumenty
                    this.documents = res;

                    // Projde všechny dokumenty a
                    // získá sekce:
                    this.documents.forEach((doc) => {
                        // Založí vlastnost pro uložení sekcí
                        doc.sections = [{ name: 'Výchozí sekce', id: '-1' }];

                        // Má dokument data?
                        if (doc.data) {
                            // Projde všechny bloky dokumentu
                            JSON.parse(doc.data).blocks.forEach((block, index) => {
                                // Je prvek druhý nadpis?
                                if (block.type == 'header' && block.data.level == 2) {
                                    // Uloží záznam o sekci
                                    doc.sections.push({ name: block.data.text, id: index });
                                }
                            });
                        }

                        // Smaže výchozí sekci, pokud máme nějaké sekce
                        // vytvořené redaktorem
                        if (doc.sections.length > 1) {
                            // Odstraní výchozí sekci
                            doc.sections = doc.sections.filter(section => section.id != -1);
                        }
                    });

                    // ID dokumentu v URL. Prázdný string, pokud URL dokument neobsahuje.
                    let param = this.$router.currentRoute.fullPath.substr(1);

                    // Dostali jsme nějaký dokument v URL ?
                    if (param != '') {
                        // Přejde na získaný dokument a
                        // orevře ho
                        this.activeDoc = param;
                        this.opened.push(param);
                    } else {
                        // Máme nějaké dokumenty?
                        if (this.documents.length >= 1) {
                            // Iterace všemi dokumenty
                            for (let item of this.documents) {
                                // Je dokument publikovaný?
                                if (item.published) {
                                    // Defaultně otevře první dokument v menu
                                    this.opened.push(item.id);

                                    // Zobrazí první dokument z menu
                                    this.activeDoc = item.id;

                                    // Ukončí cyklus
                                    break;
                                }
                            }
                        }
                    }
                });

                // Zjistí, zda je uživatel redaktor
                $.get('/user/is-redactor', res => {
                    // Uloží hodnotu
                    this.isRedactor = res.result;
                });
            },
            // Voláno při kliknutí na hlavní menu
            menuItemClick(e) {
                // Rozdělí klíč po pomlčce a vybere
                // jeho první část, jež odkazuje na
                // ID dokumentu, do kterého patří
                // daná sekce.
                let [documentId, sectionId] = e.index.split('-');

                // Chceme otevřít jiný dokument?
                if (this.activeDoc != documentId) {
                    // Uloží aktivní dokument do URL
                    this.$router.push(documentId);

                    // Aktivujeme clonu
                    $('#cover').fadeIn(300, () => {
                        // Přejde na nový dokument
                        this.activeDoc = documentId;

                        // Otevře položku v menu
                        this.opened = ['-1', documentId];

                        // Vrátíme se na vrchol stránky
                        window.scrollTo(0, 0);
                    }).delay(500).fadeOut(300);
                } else {
                    // Chceme přejít na jinou sekci. Spustí animaci:
                    $([document.documentElement, document.body]).animate({
                        scrollTop: $('#header-' + sectionId).offset().top - 50
                    }, 500);
                }
            },
            // Voláno při kliknutí na editaci dokumentu
            onEditClick(e) {
                // Máme nějaké dokumenty?
                if (this.documents.length > 0) {
                    // Zjistíme, zda máme editační práva na dokument:
                    $.get('/books/can-edit-document', { id: this.activeDoc }, (res) => {
                        // Máme práva?
                        if (res.result) {
                            // Ano.
                            this.toggleEdit();
                        } else {
                            // Nemáme práva - vytvoří modal s informací
                            // o nezdaru:
                            this.$alert('Pro editaci dokumentu nemáte dostatečná práva. Obraťe se na vlastníka dokumentu nebo správce systému.','Neoprávněný přístup', {
                                type: 'error'
                            });
                        }
                    });
                } else {
                    // Zjistíme, zda máme editační práva na knihu
                    $.get('/books/can-edit-book', { id: this.target }, (res) => {
                        // Máme práva?
                        if (res.result) {
                            // Ano.
                            this.toggleEdit(false);
                        } else {
                            // Nemáme práva - vytvoří modal s informací
                            // o nezdaru:
                            this.$alert('Pro editaci knihy nemáte dostatečná práva. Obraťe se na vlastníka knihy nebo správce systému.','Neoprávněný přístup', {
                                type: 'error'
                            });
                        }
                    });
                }
            },
            // Zapne nebo vypne editační režim. Pokud je openPrevious
            // true, po opuštění editačního režimu zobrazíme dokument,
            // který jsme naposled upravovali v editoru.
            toggleEdit(openPrevious = true) {
                // Jsme v editačním režimu?
                if (this.edit) {
                    // Máme nějaké neuložené změny?
                    if (this.$refs.editor.pendingChanges) {
                        // Vytvoří nový modal
                        this.$confirm('Dokument obsahuje neuložené změny. Pokud nyní odejdete, dojde k jejich ztrátě.','Opravdu chcete ukončit úpravy?', {
                            type: 'warning',
                            confirmButtonText: 'Odejít bez uložení',
                            confirmButtonClass: 'el-button--danger',
                            cancelButtonText: 'Zpět'
                        }).then(() => {
                            // Načte dokument, jež byl naposled upravován
                            // v editoru
                            this.activeDoc = this.$refs.editor.activeDoc;

                            // Ukončí editaci bez uložení
                            this.edit = false;
                        });
                    } else {
                        // Načte dokument, jež byl naposled upravován
                        // v editoru
                        if (openPrevious) {
                            this.activeDoc = this.$refs.editor.activeDoc;
                        }

                        // Všechny změny uloženy. Ukončí
                        // editaci
                        this.edit = false;
                    }

                    // Znovu načteme kapitoly
                    this.init();
                } else {
                    // Vstoupíme do editačního režimu
                    this.edit = true;
                }
            },
            // Když otevřeme položku v hlavním menu
            onItemOpen(e) {
                // Vloží index itemu do pole
                // otevřených položek
                this.opened.push(e);
            },
            // Když zavřeme položku v hlavním menu
            onItemClose(e) {
                // Odstraníme index z otevřených položek
                this.opened = this.opened.filter(item => item != e);
            },
            // Získá správnou ikonku pro postraní panel (hlavní menu)
            folderIcon(index) {
                return this.opened.includes(index) ? 'el-icon-folder-opened' : 'el-icon-folder';
            },
            // Když byl změněn aktivní dokument skrze "document" komponentu
            onActiveDocumentChange(id) {
                // Uloží aktivní dokument do URL
                this.$router.push(id.toString());

                // Aktivujeme clonu
                $('#cover').fadeIn(300, () => {
                    // Přejde na nový dokument
                    this.activeDoc = id;

                    // Otevře položku v menu
                    this.opened = ['-1', id.toString()];

                    // Vrátíme se na vrchol stránky
                    window.scrollTo(0, 0);
                }).delay(500).fadeOut(300);
            }
        }
    }
</script>

<!-- Styly -->
<style scoped lang='scss'>
    // Proměnné
    // Šířka menu
    $menu-width: 400px;

    // Menu
    aside {
        // Rozměry
        width: $menu-width;

        // Umístění
        float: left;
        position: fixed;
        z-index: 999;

        // Zakáže možnost vybírat text
        * {
            -webkit-touch-callout: none;
              -webkit-user-select: none;
               -khtml-user-select: none;
                 -moz-user-select: none;
                  -ms-user-select: none;
                      user-select: none;
        }

        // Vykreslí rámeček na poslední položku menu
        & > ul > li {
            border-bottom: 1px solid #EAEEF5;
        }

        // Kořenový prvek menu
        ul:first-child {
            // Protáhne menu přes celou
            // výšku okna
            height: 100vh;

            // Barva pozadí a vzhled
            background-color: #FDFDFD;

            // Chování
            overflow-y: scroll;

            // Odsazení
            padding-bottom: 150px;
        }
    }

    // Na mozzile nefunguje spodní
    // odsazení v menu
    @-moz-document url-prefix() {
        // Menu
        aside {
            // Kořenový prvek menu
            ul:first-child > li:last-child {
                // Odsazení
                margin-bottom: 150px;
            }
        }
    }

    // Obsah
    main {
        float: right;
        width: calc(100% - #{$menu-width});
    }

    // Když jsme v editačním režimu, roztáhne dokument
    // přes celou šíři okna
    main.edit-mode {
        width: auto;
        float: none;
    }

    // Editační tlačítko
    .btn-edit {
        // Rozměry
        width: #{$menu-width - 50px};
        height: 40px;

        // Pozice
        position: fixed;
        left: 25px;
        bottom: 25px;
        z-index: 1000;

        // Vzhled
        box-shadow: 0px 5px 20px 0 rgba(0, 0, 0, .22);

        // Pokud uživatel není redaktor
        &--invisible {
            // Zakryje tlačíko
            display: none;
        }
    }

    // Přepíše styly postraního menu
    .el-menu-item.is-active {
        // Přepíše modrou barvu
        color: #303133 !important;
    }

    // Změní barvu ikony
    .el-menu-item.is-active i {
        // Původní šedá barva
        color: #909399 !important;
    }

    // Odstraní pozadí
    .el-menu-item:focus {
        background-color: transparent !important;
    }

    // Když se menu ani dokument nevejdou vedle sebe. Max-width je
    // roven šířce menu + šířce dokumentu + 100px pro odsazení.
    @media only screen and (max-width: #{$menu-width + 650 + 100}) {
        // Posune menu na horní část obrazovky
        aside {
            clear: both;
            width: 100vw !important;
            position: relative !important;

            ul {
                height: auto !important;
            }

            // Kořenový prvek menu
            ul:first-child {
                // Odsazení
                padding-bottom: 0 !important;
            }
        }

        // Posune dokument pod menu
        main {
            clear: both;
            width: 100vw !important;
        }

        // Obalovač dokumentu
        .document-wrapper {
            // Zvětší šířku dokumentu
            width: 90% !important;
        }

        // Editace na telefonu není podporovaná
        .btn-edit {
            // Zakryje tlačítko
            display: none;
        }
    }

    /* Pravidla pro tisk */
    @media print
    {
        main {
            width: 100% !important;
        }
    }
</style>
