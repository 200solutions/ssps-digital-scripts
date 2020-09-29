<template>
    <!-- Embed -->
    <div class='wrapper'>
        <!-- IFRAME -->
        <iframe :src='data.embed' frameborder='0' allowfullscreen></iframe>
        <!-- Popisek -->
        <p class='caption' v-if='data.caption'>{{ data.caption }}</p>
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
        // Vzhled
        border: 1px solid #DDD;

        // Odsazení
        padding: 10px;
        margin-bottom: 20px;
    }

    // Iframe
    iframe {
        // Rozměry
        width: 100%;
        height: 315px;
    }

    // Popisek
    .caption {
        // Odsazení
        margin: 10px;
        margin-left: 0;
        margin-bottom: 0;

        // Písmo
        font-style: italic;
    }
</style>
