<template>
    <div>
        <!-- Iterace sekcemi -->
        <div v-for='section in data'>
            <!-- Obsah -->
            <div v-if='section.books.length > 0'>
                <!-- Nadpis -->
                <h2>{{ section.title }}</h2>
                <!-- Knihy -->
                <book-carousel :books='section.books'></book-carousel>
            </div>
        </div>
    </div>
</template>

<script>
    module.exports = {
        // Data komponenty
        data: function() {
            return {
                // Sekce a jejich knihy
                data: []
            }
        },
        // Když je componenta vytvořena
        created() {
            // Zažádá si o data sekcí a jejich knih
            $.get('/books/get-subjects-and-books', (res) => {
                // Uloží data
                this.data = res;
            });
        },
        // Komponenty
        components: {
            'book-carousel':  httpVueLoader('/client/components/BookCarousel.vue')
        }
    }
</script>
