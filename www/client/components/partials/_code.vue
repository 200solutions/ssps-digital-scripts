<template>
    <!-- Blokový kód -->
    <div class='wrapper'>
        <el-tabs>
            <el-tab-pane :label='data.name'>
                <!-- Obsah -->
                <pre class='line-numbers'><code :class='data.language'>{{ data.code }}</code></pre>
            </el-tab-pane>
        </el-tabs>
    </div>
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
        // Po dosazení do DOMU
        mounted() {
            // Cesta ke všem jazykům (autolaoder plugin)
            Prism.plugins.autoloader.languages_path = '/client/assets/grammars/';

            // Vynutí prism reload, aby se zobrazil
            // syntax highlighting nově přidaného textu
            Prism.highlightAll();
        }
    }
</script>

<!-- Styly -->
<style scoped lang='scss'>
    // Přepíše tloušťku headeru
    .el-tabs__nav-wrap::after {
        // Změní výšku
        height: 1px !important;
    }

    .wrapper {
        margin-bottom: 20px;
    }
</style>
