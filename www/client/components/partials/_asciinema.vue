<template>
    <!-- Asciinema -->
    <div class='asciinema' :id="'asciinema-' + data.id">
    </div>
</template>

<script>
    module.exports = {
        // Data komponenty
        data: function() {
            return {
            }
        },
        props: ['data'],
        // Když je komponenta vytvořena
        mounted() {
            // Vytvoří script tag
            let script = document.createElement('script');

            // Přiřadí atributy
            script.setAttribute('id', 'asciicast-' + this.data.id);
            script.setAttribute('src', 'https://asciinema.org/a/' + this.data.id + '.js');
            script.setAttribute('async', 'true');
            script.setAttribute('data-rows', '20');

            // Vloží script do DOMu
            document.getElementById('asciinema-' + this.data.id).appendChild(script);
        }
    }
</script>

<!-- Styly -->
<style lang='scss'>
    // Obalovač
    .asciinema {
        margin-bottom: 25px;
    }
</style>
