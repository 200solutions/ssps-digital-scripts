<template>
    <!-- Image blok -->
    <div class='wrapper'>
        <!-- Obrázek -->
        <a :href='data.file.url' target='_blank'>
            <img :src='data.file.url' :alt='data.file.caption'>
        </a>
        <!-- Popisek -->
        <p v-if='data.caption' v-html='data.caption'></p>
    </div>
</template>

<script>
    module.exports = {
        // Data komponenty
        data: function() {
            return {
            }
        },
        props: ['data']
    }
</script>

<!-- Styly -->
<style scoped lang='scss'>
    // Obalovač
    .wrapper {
        // Rozměry
        width: 100%;

        // Odsazení
        margin-bottom: 20px;

        // Vzhled
        border: 1px solid #DDDDDD;
    }

    // Obrázek
    img {
        // Zobrazení
        display: block;

        // Rozměry
        max-width: 100%;
        max-height: 60vh;

        // Odsazení
        padding: 10px;
    }

    // Popisek
    p {
        // Odsazení
        margin: 10px;
        margin-top: 0;

        // Písmo
        font-style: italic;
    }
</style>
