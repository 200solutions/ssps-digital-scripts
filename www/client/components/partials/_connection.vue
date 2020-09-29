<template>
    <!-- Spojení na objekt -->
    <div class='wrapper connection'>
        <n-link v-if='book'
                class='n-link'
                to='Books:default'
                :params='{ id: data.book }'
                :anchor='data.document'
                @error='error = true'>
            <!-- Kniha -->
            <book class='connection-book' :bid='book.id' type='small'></book>
            <!-- Informace -->
            <div class='connection-info'>
                <!-- Název -->
                <h4>{{ book.title }}</h4>
                <!-- Autoři -->
                <p class='authors'>{{ book.authors.join(', ') }}</p>
                <!-- Popisek -->
                <h5>{{ book.description }}</h5>
            </div>
        </n-link>
    </div>
</template>

<script>
    module.exports = {
        // Data komponenty
        data: function() {
            return {
                book: null
            }
        },
        // Vlastnosti
        props: ['data'],
        // Když je komponenta vytvořena
        created() {
            // Zažádá si o data
            $.get('/books/get-book', { id: this.data.book }, book => {
                // Uloží knihu
                this.book = book;
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
    // Obalovač
    .wrapper {
        // Odsazení
        padding: 10px;
        margin-bottom: 20px;

        // Vzhled
        border: 1px solid #E9EBEE;
        border-radius: 4px;

        // Výška
        height: 150px;

        // Hover
        &:hover {
            // Kurzor
            cursor: pointer;

            h4 {
                color: rgb(0, 73, 123);
            }
        }
    }

    // Odkaz
    .n-link {
        display: block;
    }

    a {
        // Barva
        color: black;
    }

    // Informační část
    .connection-info {
        // Odsazení
        padding-left: 125px;

        // Autoři
        .authors {
            // Odsazení a vzhled
            margin-top: 10px;
            font-size: .75em;
            opacity: .55;
        }

        h4 {
            font-size: 1.05em;
        }

        // Popisek
        h5 {
            margin-top: 15px;
            opacity: .55;
            font-weight: normal;
            font-size: 1em;
            letter-spacing: .05em;
            text-align: justify;
        }
    }
</style>
