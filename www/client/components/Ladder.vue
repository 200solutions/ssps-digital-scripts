<!-- Šablona -->
<template>
    <!-- Žebříček -->
    <div class='ladder-carousel owl-carousel'>
        <div class='ladder-carousel__item' v-for='category in categories'>
            <!-- Název -->
            <h3>{{ category.name }}</h3>
            <!-- List -->
            <ul>
                <!-- Item -->
                <li v-for='book in category.books' class='book-item'>
                    <!-- Odkaz -->
                    <n-link to='Books:default' :params='{ id: book.id }'>
                        <book :bid='book.id' type='small'></book>
                        <!-- Název -->
                        <h4>{{ book.title }}</h4>
                        <!-- Autoři -->
                        <h5>{{ book.authors.join(', ') }}</h5>
                        <!-- Informace -->
                        <div class='book-meta'>
                            <!-- Datum přidání -->
                            <span><i class='el-icon-date'></i> {{ book.createdAt }}</span>
                        </div>
                    </n-link>
                </li>
            </ul>
        </div>
    </div>
</template>

<!-- Logika -->
<script>
    module.exports = {
        // Data komponenty
        data: function() {
            return {
                // Kategorie žebříčku
                categories: [
                    // Nejnovější knihy
                    { name: 'Nejnovější', endpoint: '/books/get-latest', books: [] },
                    { name: 'Nejstarší', endpoint: '/books/get-oldest', books: [] }
                ]
            }
        },
        created() {
            // Získá data každé kategorie
            this.categories.forEach(category => {
                // Získá data
                $.get(category.endpoint, res => {
                    // Uloží data
                    category.books = res;
                });
            });

            // Když je DOM up-to-date, provedeme DOM dependent
            // operace tzn. inicializujeme owl-carousel a získáme
            // barvičky knih:
            Vue.nextTick(() => {
                // Carousel knih pro žebříček
                $('.ladder-carousel').owlCarousel({
                    // Osazení mezi itemy
                    margin: 50,
                    // Odsazení ze stran
                    stagePadding: 75,
                    // Navigace
                    nav: true,
                    // Položky na řádek
                    // Mění vlastnosti dle šířky view-portu
                    responsive: {
                        0: {
                            // Počet položek a padding
                            items: 1,
                            stagePadding: 25
                        },
                        768: {
                            // Počet položek a padding
                            items: 2,
                            stagePadding: 75
                        },
                        1200: {
                            items: 3
                        }
                    },
                    // Třída pro navigaci
                    navClass: ['nav__prev', 'nav__next'],
                    // Třída containeru pro navigaci
                    navContainerClass: 'nav',
                });
            });
        },
        // Komponenty
        components: {
            'book': httpVueLoader('/client/components/Book.vue'),
        }
    }
</script>

<!-- Styly -->
<style scoped lang='scss'>
    // Vynuluje výchozí odsazení
    .book-item {
        padding: 0 !important;
    }

    // SCSS pro telefony
    @media only screen and (max-width: 768px) {
        // Kniha
        .ladder-carousel .book-item .book {
            // Vynuluje výchozí transform a posunutí
            transform: none !important;
            left: 0 !important;
        }
    }
</style>
