<template>
    <!-- Citace -->
    <div class='wrapper'>
        <!-- Text -->
        <p class='text' :style="'text-align:' + data.alignment"><span class='quote-symbol'>“</span><span v-html='data.text'></span></p>
        <!-- Autor nebo titulek -->
        <p class='caption' v-html='data.caption'></p>
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
        // Odsazení
        margin-bottom: 20px;
        padding: 20px;

        // Vzhled
        border: 1px solid rgb(221, 221, 221);
        border-radius: 4px;
    }

    .text {
        font-style: italic;
        font-family: Georgia, serif;
        position: relative;
    }

    .caption {
        font-size: 1em;
        font-style: italic;
        margin-top: 20px;
        position: relative;
        padding-left: 15px;
        color: #bbb;

        &::before {
            content: '';
            height: 1px;
            width: 10px;
            background-color: #bbb;
            top: 10px;
            left: 1px;
            position: absolute;
        }
    }

    .quote-symbol {
        font-size: 2.5em;
        color: #bbb;
    }

    // Odkaz
    a {
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
</style>
