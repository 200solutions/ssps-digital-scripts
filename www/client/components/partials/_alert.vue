<template>
    <!-- Odstavec -->
    <div class='alert' :class='data.type' v-html='data.text'></div>
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
<style lang='scss'>
    // Styly alertu
    .alert {
        // Pozice
        position: relative;

        // Odsazení
        padding: 18px;
        padding-left: 25px;
        padding-right: 25px;
        margin-bottom: 20px;

        // Vzhled
        color: #5e6d82;
        background-color: #f8f8f8;
        text-align: justify;
        font-size: 16px;
    	letter-spacing: 0.01em;
        line-height: 1.75em;

        // Před
        &::before {
            position: absolute;
            top: 14px;
            left: -12px;
            width: 20px;
            height: 20px;
            border-radius: 100%;
            text-align: center;
            line-height: 20px;
            font-weight: normal;
            font-size: 14px;
            color: #fff;
        }
    }

    .danger {
        border-left: 4px solid #f66 !important;

        &::before {
            content: '×';
            background-color: #f66;
        }
    }

    .info {
        border-left: 4px solid #00497B;

        &::before {
            content: 'i';
            background-color: #00497B;
        }
    }

    .warning {
        border-left: 4px solid #FFAA2C;

        &::before {
            content: '!';
            background-color: #FFAA2C;
        }
    }

    // Přepíše styly inline-kódu uvnitř alertu
    .alert .inline-code {
        background: transparent;
    }

    // Odkaz v alertu
    .alert a {
        // Standardní barva
        color: #1790FF;

        // Pozice a vzhled
        padding: 1px 3px;
        margin: 0 -3px;
        border-radius: 2px;
        text-decoration: underline;
        text-decoration-color: rgba(0, 106, 235, .2);

        // Po najetí myší
        &:hover {
            background-color: rgba(0, 152, 255, .1);
        }

        // After
        &::after {
            content: '';
            margin-left: 3px;
            padding-right: 12px;
            background: url("data:image/svg+xml,%3Csvg%20width%3D%2216%22%20height%3D%2216%22%20version%3D%221.1%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M0%2C4%20L8%2C4%20L6%2C6%20L2%2C6%20L2%2C14%20L10%2C14%20L10%2C10.5%20L12%2C8.5%20L12%2C16%20L0%2C16%20Z%20M0%2C4%22%20fill%3D%22rgba(0%2C0%2C0%2C0.25)%22%3E%3C%2Fpath%3E%3Cpath%20d%3D%22M6%2C0%20L16%2C0%20L16%2C10%20L14%2C10%20L14%2C3.5%20L7.5%2C10%20L6%2C8.5%20L12.5%2C2%20L6%2C2%20Z%20M6%2C0%22%20fill%3D%22rgba(0%2C0%2C0%2C0.25)%22%3E%3C%2Fpath%3E%3C%2Fsvg%3E") no-repeat left center;
            background-size: 10px;
        }
    }

    /* Pravidla pro tisk */
    @media print
    {
        // Styly alertu
        .alert {
            page-break-inside: avoid;
            
            // Před
            &::before {
                // Zakryje kuličku se symbolem
                display: none !important;
            }
        }
    }
</style>
