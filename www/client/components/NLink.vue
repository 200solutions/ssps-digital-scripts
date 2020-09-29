<template>
    <a :href='url' v-if='url'>
        <slot />
    </a>
</template>

<script>
    module.exports = {
        // Data komponenty
        data: function() {
            return {
                url: null
            }
        },
        // Atributy komponenty
        props: ['to', 'params', 'anchor'],
        // Když je komponenta vytvořena
        created() {
            // Když je DOM načten, požádáme o link
            Vue.nextTick(() => {
                // Zažádá server o adresu
                $.get('/api/get-link', {
                    // Cílová destinace
                    dest: this.to,
                    // Query parametry cílové URL
                    params: JSON.stringify(this.params)
                }).done((res) => {
                    // Aktualizace URL proměnné
                    this.url = this.anchor ? res + `#${this.anchor}` : res;
                });
            });
        }
    }
</script>
