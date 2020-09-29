<template>
    <!-- Přílohy -->
    <a class='wrapper' :href='data.file.url' download>
        <!-- Ikona -->
        <img class='file-icon' :src='getIcon()' onerror="this.onerror=null;this.src='/client/assets/icons/generic.png';">
        <!-- Detaily -->
        <div class='details'>
            <!-- Název -->
            <p class='title'>{{ data.title }}</p>
            <!-- Velikost -->
            <p class='size'>{{ data.file.size | size }}</p>
        </div>
    </a>
</template>

<script>
    module.exports = {
        // Data komponenty
        data: function() {
            return {
            }
        },
        // Vlastnosti
        props: ['data'],
        // Filtry
        filters: {
            size: function(value) {
                // Jednotky
                const units = ['bytes', 'KiB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

                // Převod
                let l = 0, n = parseInt(value, 10) || 0;

                while (n >= 1024 && ++l) {
                    n = n / 1024;
                }

                return (n.toFixed(n < 10 && l > 0 ? 1 : 0) + ' ' + units[l]);
            }
        },
        // Metody
        methods: {
            getIcon() {
                return '/client/assets/icons/' + this.data.file.extension + '.png';
            }
        }
    }
</script>

<!-- Styly -->
<style scoped lang='scss'>
    // Obalovač
    .wrapper {
        // Odkaz
        display: block;
        text-decoration: none;
        color: black;

        // Vzhled
        border: 1px solid #E9EBEE;
        border-radius: 4px;

        // Odsazení
        padding: 10px;
        margin-bottom: 20px;

        // Přetékání
        overflow: auto;

        // Hover
        &:hover {
            .title {
                color: rgb(0, 73, 123);
            }
        }
    }

    .file-icon {
        // Rozměry a pozice
        float: left;
        height: 55px;
    }

    .details {
        // Odsazení a pozice
        float: left;
        padding-left: 15px;

        // Velikost
        .size {
            opacity: .55;
            padding-top: 5px;
            font-size: .8em;
        }
    }
</style>

<style>
    /* Vizuálně spojí přílohy do jedné, pokud jsou za sebou  */
    div[data-type="attaches"] + div[data-type="attaches"] .wrapper {
        margin-top: -22px;
        padding-top: 10px;
        background-color: white;
        border-top: none !important;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }

    .editor-document-link select {
        width: calc(33% - 8px);
    }

    .editor-document-link select:not(.editor-document-link select:first-child) {
        margin-left: 8px;
    }
</style>
