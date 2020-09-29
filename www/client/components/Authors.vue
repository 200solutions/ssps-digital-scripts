<template>
    <div class='authors'>
        <!-- Výpis autorů -->
        <el-row :gutter='20' class='list'>
            <!-- Autor -->
            <el-col :xs='24' :sm='8' :lg='6' :xl='4' class='author' v-for='author in authors' :key='author.id'>
                <div>
                    <!-- Profilový obrázek -->
                    <img :src="author.imagePath ? author.imagePath : '/client/images/default-avatar.png'">
                    <!-- Informace -->
                    <p>{{ author.firstName }} {{ author.lastName }}</p>
                </div>
            </el-col>
        </el-row>
        <!-- Licence -->
        <div class='notice'>Tento dokument je majetkem Smíchovské střední průmyslové školy. Jakékoliv šíření dokumentu bez souhlasu vlastníka je zakázáno.</div>
    </div>
</template>

<script>
    module.exports = {
        // Data komponenty
        data: function() {
            return {
                // Autoři
                authors: []
            }
        },
        // Atributy komponenty
        props: {
            // Identifikátor dokumentu
            did: {
                required: true
            }
        },
        // Když je komponenta vytvořena
        created() {
            // Inciializace
            this.init();
        },
        // Metody
        methods: {
            // Inicializace
            init() {
                // Vyresetuje autory
                this.authors = [];

                // Získá všechny autory dokumentu
                $.get('/books/get-document-authors', { id: this.did }, authors => {
                    // Získá data o autorech. Projdeme všechny
                    // identifikátory a vyžádáme si data uživatelů:
                    authors.forEach(id => {
                        // Pošle požadavek na server
                        $.get('/user/get-user', { id: id }, user => {
                            // Uloží uživatele
                            this.authors.push(user);
                        });
                    });
                });
            }
        },
        // Sledovače
        watch: {
            // Sleduje změny vlastnosti 'did' (ID dokumentu) a
            // v případě načtení nového dokumentu
            // aktualizuje autory
            did: function (now, prev) {
                // Znovu načte autory
                this.init();
            }
        }
    }
</script>

<style scoped lang='scss'>
    // Hlavní obalovač
    .authors {
        // Vzhled
        background-color: #F8F8F8;
        border-radius: 4px;

        // List autorů
        .list {
            // Odsazení
            padding: 20px;
            padding-bottom: 0;
        }

        // Textová část s dodatečným textem
        .notice {
            // Odsazení
            margin: 20px;
            margin-top: 0;
            padding: 20px 0;
            padding-top: 16px;

            // Vzhled
            border-top: 1px solid #ddd;
            opacity: .55;
            line-height: 1.9em;
        }
    }

    // Autor dokumentu
    .author {
        // Odsazení
        margin-bottom: 20px;

        // Profilový obrázek
        img {
            // Rozměry
            width: 55px;
            height: 55px;

            // Vzhled
            outline: 1px solid rgba(0, 0, 0, .3);
            outline-offset: -1px;

            // Poloha
            float: left;

            // Odsazení
            margin-right: 20px;
        }
    }

    /* Pravidla pro tisk */
    @media print
    {
        // Hlavní obalovač
        .authors {
            border: 1px solid rgb(221, 221, 221);
            page-break-inside: avoid;
        }
    }
</style>
