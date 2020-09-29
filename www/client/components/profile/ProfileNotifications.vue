<template>
    <!-- Komponenta pro zprávu účtu -->
    <div class='wrapper'>
        <el-table v-model='notifications'>
            <!-- Sloupce -->
            <el-table-column prop='date'
                             label='Datum'
                             width='120'
                             sortable></el-table-column>
            <!-- Sloupce -->
            <el-table-column prop='content'
                             label='Obsah'></el-table-column>
        </el-table>
    </div>
</template>

<script>
    module.exports = {
        // Data komponenty
        data: function() {
            return {
                // Notifikace
                notifications: []
            }
        },
        // Když je komponenta vytvořena
        created() {

        },
        // Metody
        methods: {

        }
    }
</script>

<!-- Styly -->
<style scoped lang='scss'>

</style>
