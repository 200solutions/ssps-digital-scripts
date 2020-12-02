<template>
    <!-- Carousel knih -->
    <div class='book-carousel-component book-carousel owl-carousel'>
        <!-- Iterace itemy -->
        <div class='book-item' v-for='book in books' :key='book.id'>
            <!-- Kniha -->
            <book :bid='book.id'></book>
            <!-- Informace -->
            <div class='book-info'>
                <!-- Nadpis -->
                <h3>{{ book.title }}</h3>
                <!-- Autoři -->
                <p class='authors'>{{ book.authors.join(', ') }}</p>
                <!-- Popis -->
                <p class='description'>{{ book.description }}</p>
                <!-- Tlačítko (odkaz) -->
                <div v-if='book.published'>
                    <!-- Odkaz -->
                    <n-link to='Books:default' :params='{ id: book.id }'>
                        <el-button round class='book-info__button' icon='el-icon-reading'>Studovat</el-button>
                    </n-link>
                </div>
                <!-- Tlačítko (v přípravě) -->
                <div v-else>
                    <el-button round class='book-info__button' :disabled='true' icon='el-icon-time'>V přípravě</el-button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    module.exports = {
        // Data komponenty
        data: function() {
            return {
            }
        },
        // Atributy
        props: {
            // Knihy k zobrazení
            books: {
                required: true
            }
        },
        // Když je komponenta vytvořena
        created() {
            // Když je DOM up-to-date, provedeme DOM dependent
            // operace tzn. inicializujeme owl-carousel a získáme
            // barvičky knih:
            Vue.nextTick(() => {
                // Inicializace owl carouselu knih
                $('.book-carousel').owlCarousel({
                    // Navigace
                    nav: true,
                    // Zobrazovat číselník položek
                    dots: false,
                    // Třída pro navigaci
                    navClass: ['nav__prev', 'nav__next'],
                    // Třída containeru pro navigaci
                    navContainerClass: 'nav',
                    // Osazení mezi itemy
                    margin: 25,
                    // Odsazení ze stran
                    stagePadding: 25,
                    // Automatické posouvání
                    autoplay: false,
                    // Zastavení posouvání při najetí myši
                    autoplayHoverPause: true,
                    // Mění počty zobrazených položek dle šířky view-portu
                    responsive: {
                        0: {
                            items: 1
                        },
                        768: {
                            items: 1
                        },
                        1200: {
                            items: 2
                        },
                        1920: {
                            items: 3
                        },
                        2250: {
                            items: 4
                        }
                    }
                });
            });
        },
        // Komponenty
        components: {
            'book': httpVueLoader('/client/components/Book.vue'),
        }
    }
</script>

<style lang='scss'>
.book-carousel-component {
    // Carousel knih
    .book-carousel {
        // Pozice
        position: relative;
    }

    // Item carouselu
    .book-item {
        // Výška
        height: 250px;

        // Odsazení
        padding: 25px;
        padding-left: 50px;
    }

    // Informační část
    .book-info {
        // Odsazení
        margin-left: 225px;
    }

    // Informace o knize
    .description {
        // Odsazení
        margin-top: 15px;

        // Vzhled
        opacity: .55;
        text-align: justify;
    }

    // Název knihy
    h3 {
        // Vzhled
        opacity: .75;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    // Autoři
    .authors {
        // Odsazení a vzhled
        margin-top: 10px;
        font-size: .75em;
        opacity: .55;
    }

    // Tlačítko pro vstup do materiálu
    .button {
        // Pozice
        position: absolute;
        bottom: 25px;
    }

    // Navigace carouselu
    .nav {
        // Pozice
        position: absolute;
        transform: translate(0, -50%);
        top: 50%;

        // Šířka
        width: 100%;

        // Zruší detekci mouse eventů (umožní ovládat carousel v místě navigation divu)
        pointer-events: none;

        // Přidá odsazení po stranách
        padding-left: 25px;
        padding-right: 25px;
    }

    // Zapne detekci mouse eventů na šipkách
    .nav__prev, .nav__next {
        pointer-events: auto;

        // Vzhled a přechody
        border: none;
        background-color: rgba(0, 0, 0, 0);
        opacity: .5;
        transition: opacity .5s;

        // Font
        font-family: 'system-ui';

        // Ukáže správný cursor na hoveru
        &:hover {
            cursor: pointer;
        }

        &.disabled {
            opacity: 0;
            cursor: default;
        }
    }

    // Šipka - další
    .nav__next {
        // Zarovná element do prava
        float: right;

        // Odstraní defaultní šipku
        & span {
            display: none;
        }

        // Přidá šipku
        &::after {
            font-size: 3em;
            content: '→';
        }
    }

    // Šipka - předchozí
    .nav__prev {
        // Zarovná element do prava
        float: left;

        // Odstraní defaultní šipku
        & span {
            display: none;
        }

        // Přidá šipku
        &::after {
            font-size: 3em;
            content: '←';
        }
    }

    // SCSS pro telefony
    @media only screen and (max-width: 768px) {
        // Položka carouselu
        .book-item {
            height: 75vh;
            padding: 0;
            padding-top: 25px;

            // Kniha
            .book {
                // Šířka knihy
                width: 150px;

                // Posune knihu
                left: 50%;

                // Vycentruje knihu
                transform: translate(-50%, 0);
            }
        }

        // Upraví popisek
        .book-info {
            margin-left: 0;
            margin-top: 0;
            padding-top: 250px;
        }
    }
}
</style>
