<template>
    <div class='ad no-print'>
        <ins class='adsbygoogle'
             style='display:block'
             :data-ad-format='format'
             :data-ad-layout-key='layoutKey'
             :data-ad-client='client'
             :data-ad-slot='adSlot'></ins>
    </div>
</template>

<script>
    module.exports = {
        // Data komponenty
        data: function() {
            return {
            }
        },
        // Atributy
        props: ['format', 'layoutKey', 'client', 'adSlot'],
        // Když je komponenta vytvořena
        created() {
            Vue.nextTick(() => {
                // Inicializuje komponentu
                (adsbygoogle = window.adsbygoogle || []).push({});
            });
        }
    }
</script>

<style scoped>
    .ad {
        margin-bottom: 20px;
    }
</style>
