<template>
    <!-- Nadpis -->
    <div :class=" 's-header-' + data.level " :id='`header-${$vnode.key}`' v-html='data.text'></div>
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
    // Všechny nadpisy
    .s-header-1, .s-header-2, .s-header-3, .s-header-4, .s-header-5, .s-header-6 {
        font-smoothing: auto !important;
        -webkit-font-smoothing: auto !important;
    }

    // Hlavní nadpis
    .s-header-1 {
        // Písmo
        font-size: 2em;
        color: #00497B;

        // Odsazení
        padding-bottom: 20px;
    }

    // Druhý nadpis
    .s-header-2 {
        // Písmo
        font-size: 22px;
        font-family: 'Source Sans Pro' !important;
        font-weight: 400;
        color: #2c3e50;

        // Pozice
        position: relative;
        padding-bottom: 40px;

        // After (podtržení)
        &::before {
            // Obsah
            content: '';

            // Pozice
            position: absolute;
            margin-top: 50px;

            // Velikost a vzhled
            height: 1px;
            width: 100%;
            background-color: #ddd;
        }
    }

    // Třetí nadpis
    .s-header-3 {
        // Písmo
        font-size: 18px;
        color: #2c3e50;
        font-weight: 600;

        // Pozice
        position: relative;
        padding-bottom: 20px;

        // Hash symbol
        &::before {
            // Obsah
            content: '#';

            // Vzhled
            padding-right: 10px;
            color: #00497B;
        }
    }

    // Nadpisy 4-6
    @for $i from 4 through 6 {
        // Třída
        .s-header-#{$i} {
            // Písmo
            font-size: #{20 - $i}px;
            font-weight: 600;
            color: #2c3e50;

            // Pozice
            padding-bottom: 20px;

            // Hash symbol
            &::before {
                // Obsah
                content: '|';

                // Vzhled
                padding-right: 10px;
                color: #1790FF;
            }
        }
    }

    /* Pravidla pro tisk */
    @media print
    {
        .s-header-2 {
            // Rámeček
            border-bottom: 1px solid #ddd;

            // Odsazení
            padding-bottom: 10px !important;
            margin-bottom: 10px !important;
        }
    }
</style>
