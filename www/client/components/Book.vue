<!-- Šablona -->
<template>
    <!-- Kniha -->
    <div class='book' :class="{ 'bk-small': type == 'small' }">
        <div class='bk-wrapper'>
            <div class='bk-book'>
                <div class='bk-front' :style="'background-color:' + book.color ">
                    <div class='bk-cover'>
                        <div class='bk-name' v-if="type != 'small' && book.title">{{ book.title }}</div>
                    </div>
                </div>
                <div class='bk-left' :style="'background-color:' + book.color ">
                    <div class='book-spine-text'></div>
                </div>
                <div class='bk-back' :style="'background-color:' + book.color "></div>
            </div>
        </div>
    </div>
</template>

<!-- Logika -->
<script>
    module.exports = {
        // Data komponenty
        data: function() {
            return {
                book: {}
            }
        },
        // Vlastnosti
        props: {
            // Identifikátor knihy
            bid: {
                required: true
            },
            type: {
                required: false
            }
        },
        // Když je kniha vytvořena
        created() {
            // Získá data o knize
            $.get('/books/get-book', { id: this.bid }, book => {
                // Uloží data
                this.book = book;
            });
        }
    }
</script>

<!-- Styly -->
<style scoped lang='scss'>
</style>
