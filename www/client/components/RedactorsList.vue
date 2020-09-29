<template>
    <!-- Komponenta pro zobrazení listu redaktorů -->
    <div class='wrapper'>
        <!-- Redaktoři -->
        <user-row v-for='(redactor, i) in redactors'
                  :key='i'
                  :first-name='redactor.firstName'
                  :last-name='redactor.lastName'
                  :email='redactor.email'
                  :image-path='redactor.imagePath'
                  :roles='redactor.roles'
                  class='no-hover'></user-row>
    </div>
</template>

<script>
    module.exports = {
        // Data komponenty
        data: function() {
            return {
                // Redaktoři
                redactors: []
            }
        },
        // Když je komponenta vytvořena
        created() {
            // Získá redaktory
            $.get('/user/get-redactors', redactors => {
                // Uloží redaktory
                this.redactors = redactors;
            });
        },
        // Komponenty
        components: {
            'user-row': httpVueLoader('/client/components/UserRow.vue')
        }
    }
</script>

<!-- Styly -->
<style scoped lang='scss'>
    // Hlavní obalovač
    .wrapper {
        padding-left: 10px;

        // Výška
        max-height: 450px;

        // Chování
        overflow-y: scroll;
    }
</style>
