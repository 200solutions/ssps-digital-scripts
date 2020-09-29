<template>
    <!-- Komponenta pro zprávu účtu -->
    <div class='wrapper'>
        <!-- Taby -->
        <el-tabs type='card' v-model='activeTab'>
            <!-- Přehled -->
            <el-tab-pane label='Základní informace' name='info'>
                <!-- Náhled -->
                <user-row v-if='!userLoading'
                          :first-name='user.firstName'
                          :last-name='user.lastName'
                          :email='user.email'
                          :image-path='user.imagePath'
                          :roles='user.roles'
                          class='no-hover'></user-row>
                <!-- Sloupce -->
                <el-row :gutter='20'>
                    <!-- Levá část -->
                    <el-col :xs='24' :sm='6'>

                    </el-col>
                    <!-- Pravá část -->
                    <el-col :xs='24' :sm='18'>

                    </el-col>
                </el-row>
            </e-tab-pane>
        </el-tabs>
    </div>
</template>

<script>
    module.exports = {
        // Data komponenty
        data: function() {
            return {
                // Aktivní tab
                activeTab: 'info',
                // Uživatel
                userLoading: true,
                user: {
                    firstName: '',
                    lastName: '',
                    email: '',
                    imagePath: '',
                    roles: []
                }
            }
        },
        // Když je komponenta vytvořena
        created() {
            // Získá ID právě přihlášeného uživatele
            $.get('/user/get-logged-user-id', id => {
                // Získá přihlášeného uživatele
                $.get('/user/get-user', { id: id }, user => {
                    // Uloží uživatele
                    this.user = user;
                    console.log('uaaa');

                    // Zruší načítání
                    this.userLoading = false;
                });
            });
        },
        // Metody
        methods: {

        },
        // Komponenty
        components: {
            'user-row': httpVueLoader('/client/components/UserRow.vue')
        }
    }
</script>

<!-- Styly -->
<style scoped lang='scss'>
    // Obalovač
    .wrapper {
        margin-bottom: 25px;
    }

    // Průsvitnost popisku knihy
    .table-desc {
        opacity: .55;
    }

    // Popisek
    label.lb {
        // Pozice a zobrazení
        display: block;
        padding-bottom: 10px;
        padding-top: 15px;

        // Vzhled
        opacity: .55;
    }

    // Pomocné třídy
    .full-width {
        width: 100%;
    }

    .float-right {
        float: right;
    }
</style>
