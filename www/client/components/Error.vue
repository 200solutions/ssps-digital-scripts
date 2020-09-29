<template>
    <div class='error-box'>
        <!-- Nadpis -->
        <h2 class='error-title'>{{ title }}</h2>
        <!-- Obsah -->
        <p class='error-message'>{{ message }}</p>
        <!-- Chybový kód -->
        <p class='error-code'>{{ code }}</p>
    </div>
</template>

<script>
    module.exports = {
        // Data komponenty
        data: function() {
            return {
            }
        },
        // Atributy komponenty
        props: ['title', 'message', 'code']
    }
</script>

<style scoped lang='scss'>
    // Styly chyby
    .error {
        // Obalovač
        &-box {
            // Odsazení
            padding: 25px;
        }

        // Název chyby
        &-title {
            // Písmo
            font-size: 2em;
            letter-spacing: .01em;

            // Odsazení
            margin-bottom: 25px;
        }

        // Obsah
        &-message {
            // Odsazení
            margin-bottom: 35px;

            // Písmo
            opacity: .75;
        }

        // Chybový kód
        &-code {
            // Písmo
            color: #F56C6C;
        }
    }
</style>
