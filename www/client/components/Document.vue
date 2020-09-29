<template>
    <!-- Obalovač -->
    <div class='document-wrapper'>
        <!-- Dokument -->
        <div class='document' id='document' v-if='!error'>
            <!-- Render loop -->
            <div v-for='(block, index) in data.blocks' :data-type='block.type'>
                <!-- Reklama
                <adsense v-if='isAdVisible(block, index)'
                         format='fluid'
                         layout-key='-gy-k+1c-64+d6'
                         client='ca-pub-2934445377419280'
                         ad-slot='6262404253'></adsense> -->
                <!-- Komponenta -->
                <component :is=`partial-${block.type}` :data='block.data' :key='index'></component>
            </div>
            <!-- Autoři -->
            <authors :did='did'></authors>
            <!-- Paginátor -->
            <div class='paginator no-print'>
                <!-- Předchozí -->
                <span v-if='previous'
                      class='previous'
                      @click='onDocumentChange(previous.id)'>{{ previous.name }}</span>
                <!-- Další -->
                <span v-if='next'
                      class='next'
                      @click='onDocumentChange(next.id)'>{{ next.name }}</span>
            </div>
            <!-- Akce a info -->
            <div class='no-print'>
                <!-- PDF -->
                <el-button type='text' icon='el-icon-download' @click='downloadPdf()' :disabled='true'>Stáhnout jako .pdf</el-button>
                <!-- Tisk -->
                <el-button type='text' icon='el-icon-printer' @click='print()'>Tisk</el-button>
                <!-- Poslední editace -->
                <div class='fl-right last-edit'>Vytvořeno {{ createdAt|date }} | Upraveno {{ updatedAt|date }}</div>
            </div>
            <!-- Komentáře -->
            <div class='comments-notice no-print'>Máte dotaz? Našli jste chybu nebo chcete poskytnout zpětnou vazbu? Napište nám komentář!</div>
            <comments :key='commentsKey' :did='did' class='no-print'></comments>
        </div>
        <!-- Error -->
        <error v-else
               title='Dokument nenalezen'
               message='Dokument s daným identifikátorem nebyl nalezen.'
               code='error dcm-01'></error>
    </div>
</template>

<script>
    module.exports = {
        // Data komponenty
        data: function() {
            return {
                // Obsah dokumentu
                data: [],
                // Když se obsah načítá
                loading: true,
                // Klíč komentářové sekce sloužící
                // k vynucení reloadu komentářové sekce
                commentsKey: 0,
                // Další dokument
                next: [],
                // Předchozí dokument
                previous: [],
                // Pravda, pokud daný dokument nebyl nalezen
                error: false,
                // Data
                updatedAt: null,
                createdAt: null
            }
        },
        // Atributy
        props: {
            // ID dokumentu k editaci
            did: {
                required: true
            }
        },
        // Filtry
        filters: {
            date: function(value) {
                return value ? `${value.getDate()}. ${value.getMonth() + 1}. ${value.getFullYear()}` : '';
            }
        },
        // Když je komponenta vytvořena
        created() {
            // Inicializuje komponentu
            this.init();
        },
        // Když je komponenta mountnuta
        updated() {
            // Když je hotová manipulace s DOMem
            Vue.nextTick(_ => {
                // Render matematických zápisů
                // Projdeme všechny bloky s matematickými rovnicemi
                $('.katex-math').each(i => {
                    // Získá element
                    let element = $('.katex-math').eq(i);

                    // Pokud element má nějaký text
                    if (element.text() != '') {
                        // Vyrenderuje výraz
                        katex.render(element.text(), element.get(0), {
                            throwOnError: false
                        });

                        // Odebere třídu
                        element.removeClass('katex-math');
                    }
                });
            });
        },
        // Metody
        methods: {
            // Inicializace
            init() {
                // Začne načítat
                this.loading = true;

                // Vynuluje počet nadpisů
                window.tmpHeaderCount = 0;

                // Vyžádáme si aktuální dokument + předchozí a další. Požadavky
                // provedeme sériově kvůli zjednodušení logiky.
                $.get('/books/load-document', { id: this.did }, (res) => {
                    // Pokud daný dokument nebyl nalezen
                    if (res.status == 'error') {
                        // Ukončí načítání
                        this.loading = false;

                        // Zobrazíme error
                        this.error = true;
                    } else {
                        // Vše proběhlo v pořádku, rozparsujeme a uložíme data
                        this.data = res.data == null ? [] : JSON.parse(res.data);
                        this.updatedAt = new Date(res.updatedAt);
                        this.createdAt = new Date(res.createdAt);

                        // Získá následující dokument
                        $.get('/books/get-next-document', { id: this.did }, (next) => {
                            // Uloží odpověď
                            this.next = next;

                            // Získá předchozí dokument
                            $.get('/books/get-previous-document', { id: this.did }, (previous) => {
                                // Uloží odpověď
                                this.previous = previous;
                            });
                        })

                        // Ukončíme animaci načítání
                        this.loading = false;
                    }
                });
            },
            // Když změníme aktivní dokument. Parametr ID
            // odpovídá novému identifikátoru dokumentu
            onDocumentChange(id) {
                // Upozorníme rodiče na změnu
                this.$emit('change', id);
            },
            // Vrátí true, pokud by měla být reklama zobrazena
            isAdVisible(block, index) {
                // Máme nadpis druhého levelu?
                if (block.type == 'header' && block.data.level == 2) {
                    // Navýšíme počet nadpisů
                    window.tmpHeaderCount++;

                    // Jedná se o třetí nadpis?
                    if (window.tmpHeaderCount % 2 == 0 && index > 3) {
                        // Zobrazíme reklamu
                        return true;
                    }
                }

                // Nezobrazovat
                return false;
            },
            // Stáhne soubor jako PDF
            downloadPdf() {
                // TODO: Dodělat :-)
            },
            // Zobrazí nabídku pro tisk
            print() {
                window.print();
            }
        },
        // Komponenty
        components: {
            // Adsense
            'adsense': httpVueLoader('/client/components/Adsense.vue'),
            // Komentáře
            'comments': httpVueLoader('/client/components/Comments.vue'),
            // Autoři
            'authors': httpVueLoader('/client/components/Authors.vue'),
            // Paragraf
            'partial-paragraph': httpVueLoader('/client/components/partials/_paragraph.vue'),
            // Nadpis
            'partial-header': httpVueLoader('/client/components/partials/_header.vue'),
            // Blokový kód
            'partial-code': httpVueLoader('/client/components/partials/_code.vue'),
            // Alert
            'partial-alert': httpVueLoader('/client/components/partials/_alert.vue'),
            // Image
            'partial-image': httpVueLoader('/client/components/partials/_image.vue'),
            // Embed
            'partial-embed': httpVueLoader('/client/components/partials/_embed.vue'),
            // List
            'partial-list': httpVueLoader('/client/components/partials/_list.vue'),
            // Asciinema
            'partial-asciinema': httpVueLoader('/client/components/partials/_asciinema.vue'),
            // Citace
            'partial-quote': httpVueLoader('/client/components/partials/_quote.vue'),
            // Přílohy
            'partial-attaches': httpVueLoader('/client/components/partials/_attaches.vue'),
            // Odkaz na dokument
            'partial-connection': httpVueLoader('/client/components/partials/_connection.vue')
        },
        // Sledovače
        watch: {
            // Sleduje změny vlastnosti 'did' (ID dokumentu) a
            // v případě načtení nového dokumentu
            // aktualizuje výpis obsahu
            did: function (now, prev) {
                // Inicializace nového
                // dokumentu
                this.init();

                // Pře-renderuje komentářovou sekci
                this.commentsKey += 1;

                // Když je hotová manipulace s DOMem
                Vue.nextTick(() => {
                    // Znovu inicializujeme komentáře
                    FB.XFBML.parse(document.getElementById('comment-section'));
                });
            }
        }
    }
</script>

<!-- Styly -->
<style scoped lang='scss'>
    // Vystředí dokument
    .document-wrapper {
        // Šířka obsahu
        width: 80%;

        // Odsazení
        margin: 0 auto;
        padding-top: 8%;
        padding-bottom: 8%;
    }

    // Paginátor
    .paginator {
        // Výška a vzhled
        height: 40px;
        border-bottom: 1px solid #ddd;

        // Předchozí a další
        span {
            // Barva
            color: #00497B;

            // Po najetí myší
            &:hover {
                // Změní ikonu
                cursor: pointer;

                // Podrthne odkaz
                text-decoration: underline;
            }
        }

        // Předchozí
        .previous {
            // Poloha
            float: left;

            // Doplní šipku
            &::before {
                // Obsah
                content: '← ';
            }
        }

        // Další
        .next {
            // Poloha
            float: right;

            // Doplní šipku
            &::after {
                // Obsah
                content: ' →';
            }
        }
    }

    // Upozornění ke komentářům
    .comments-notice {
        // Odsazení
        margin-top: 20px;

        // Vzhled
        color: #5e6d82;
        font-size: .9em;
        line-height: 1.9em;
    }

    // Poslední editace
    .last-edit {
        color: #C0C4CC;
        margin: 12px 0;
        font-size: 14px;
    }

    /* Pravidla pro tisk */
    @media print {
        // Šířka obsahu
        width: 100% !important;

        // Odsazení
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }
</style>

<style>
    /* Přepíše vzhled inline-kódu */
    .inline-code {
        border-radius: 4px !important;
        white-space: nowrap !important;
        font-family: 'Consolas' !important;
        background-color: rgba(250, 239, 240, 0.78) !important;
        color: #b44437 !important;
        padding: 3px 4px !important;
        border-radius: 4px !important;
        font-size: 16px !important;
    }
</style>
