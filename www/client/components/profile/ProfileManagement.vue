<template>
    <!-- Komponenta pro zprávu účtu -->
    <div class='wrapper' v-if='!componentLoading'>
        <!-- Taby -->
        <el-tabs v-if='!error'
                 type='card'>
            <!-- Přehled a vyhledávání -->
            <el-tab-pane label='Uživatelé'>
                <!-- Druhá úroveň tabů -->
                <el-tabs v-model='activeTab'>
                    <!-- Přehled -->
                    <el-tab-pane label='Přehled'
                                 name='users'>
                        <!-- Vyhledávací pole -->
                        <el-input placeholder='Zkuste například „Jan Novák“'
                                  :prefix-icon=" loading ? 'el-icon-loading' : 'el-icon-search' "
                                  v-model='query'
                                  @input='queryChanged'
                                  clearable></el-input>
                        <!-- Počet výsledků -->
                        <div class='search-status' v-if=" query != '' ">
                            <i>Nalezeno {{ users.length }} výsledků</i>
                        </div>
                        <!-- Výsledky -->
                        <div class='search-results'>
                            <!-- Uživatel -->
                            <user-row v-for='user in users'
                                      :key='user.id'
                                      :first-name='user.firstName'
                                      :last-name='user.lastName'
                                      :email='user.email'
                                      :image-path='user.imagePath'
                                      :roles='user.roles'
                                      @click='onUserClick(user)'></user-row>
                        </div>
                    </el-tab-pane>
                    <!-- Detail -->
                    <el-tab-pane label='Detail'
                                 name='detail'
                                 :disabled=" activeTab != 'detail' ">
                        <!-- Detail uživatele  -->
                        <user-row v-if='detail'
                                  :first-name='detail.firstName'
                                  :last-name='detail.lastName'
                                  :email='detail.email'
                                  :image-path='detail.imagePath'
                                  :roles='detail.roles'
                                  class='no-hover'></user-row>
                        <!-- Blokace -->
                        <h3>Blokace</h3>
                        <el-button v-if='detail'
                                   type='danger'
                                   @click='toggleBlock(detail.id)'
                                   :icon="detail.blocked ? 'el-icon-unlock' : 'el-icon-lock'">
                                       {{ detail.blocked ? 'Odblokovat uživatele' : 'Zablokovat uživatele' }}
                        </el-button>
                        <!-- Role -->
                        <h3>Role</h3>
                        <el-transfer class='transfer'
                                     v-model='transferModel'
                                     :data='transferData'
                                     :titles="['Role', 'Role uživatele']">
                        </el-transfer>
                        <!-- Oddělovač -->
                        <el-divider></el-divider>
                        <!-- Akce -->
                        <el-button type='primary' class='float-right' @click='save()'>Uložit</el-button>
                        <el-button class='float-right btn-margin' @click="activeTab = 'users'">Zpět</el-button>
                    </el-tab-pane>
                </el-tabs>
            </el-tab-pane>
            <!-- Předměty -->
            <el-tab-pane label='Předměty' name='subjects'>
                <!-- Tabulka -->
                <el-table :data='subjects'
                          border
                          v-loading='subjectsLoading'
                          class='table-subjects'>
                    <!-- Sloupce -->
                    <el-table-column prop='id'
                                     label='ID'
                                     width='60'
                                     sortable></el-table-column>
                    <el-table-column prop='title'
                                     label='Název'
                                     width='180'
                                     sortable></el-table-column>
                    <!-- Akce -->
                    <el-table-column fixed='right'>
                        <template slot-scope='scope'>
                            <!-- Editovat -->
                            <el-button size='mini'
                                       type='text'
                                       class='float-right'
                                       @click='updateSubject(scope.$index, scope.row)'>upravit</el-button>
                            <!-- Smazat -->
                            <el-button size='mini'
                                       type='text'
                                       class='float-right btn-margin'
                                       @click='deleteSubject(scope.$index, scope.row)'>smazat</el-button>
                        </template>
                     </el-table-column>
                </el-table>
                <!-- Akce -->
                <el-button type='primary'
                           class='float-right btn-new-subject'
                           icon='el-icon-plus'
                           @click='addNewSubject()'>Nový předmět</el-button>
            </el-tab-pane>
        </el-tabs>
        <!-- Chybový box -->
        <div v-else
             class='warning-box'><i class='el-icon-warning'></i> Pouze admini (správci) mohou spravovat systém. Pokud si myslíte, že jde o omyl, kontaktujte správce systému.</div>
    </div>
</template>

<script>
    module.exports = {
        // Data komponenty
        data: function() {
            return {
                // Aktivní tab
                activeTab: 'users',
                // Vyhledávaný řetězec
                query: '',
                // Pokud probíhá vyhledávání
                loading: false,
                // Nalezení uživatelé
                users: [],
                // Detail uživatele
                detail: null,
                // Transfer
                transferModel: [],
                transferData: [
                    { key: 'admin', label: 'Admin', disabled: false },
                    { key: 'teacher', label: 'Teacher', disabled: false },
                    { key: 'redactor', label: 'Redactor', disabled: false }
                ],
                // Pokud nemáme dostatečná práva
                error: false,
                // Pokud se komponenta načítá
                componentLoading: true,
                // Předměty
                subjects: [],
                // Načítání předmětů
                subjectsLoading: true,
                // Pravda, pokud má být vidět dialog pro přidání nového předmětu
                newSubjectDialog: false
            }
        },
        // Když je komponenta vytvořena
        created() {
            // Funkce z knihovny lodash, která zařídí, že výpočetně
            // náročná funkce nebude volána zbytečně moc často. Toto
            // je nutné, protože funkce bude volána při změně textu
            // ve vyhledávacím poli.
            this.debouncedGetUsers = _.debounce(this.getUsers, 300);

            // Získá ID právě přihlášeného uživatele
            $.get('/user/get-logged-user-id', id => {
                // Získá přihlášeného uživatele
                $.get('/user/get-user', { id: id }, user => {
                    // Máme právo na zobrazení správy?
                    if (!user.roles.includes('admin')) {
                        // Nemáme - zobrazíme chybu
                        this.error = true;
                    }

                    // Získá předměty
                    $.get('/books/get-subjects/', subjects => {
                        // Uloží předměty
                        this.subjects = subjects;

                        // Ukončí načítání
                        this.subjectsLoading = false;
                    });

                    // Ukončí načítání komponenty
                    this.componentLoading = false;
                });
            });
        },
        // Metody
        methods: {
            // Když uživatel změní vyhledávaný řetězec
            queryChanged() {
                // Máme nějaký hledaný řetězec?
                if (this.query == '') {
                    // Vynuluje výsledky
                    this.users = [];

                    // Opustí metodu
                    return;
                }

                // Získáme uživatele
                this.debouncedGetUsers();
            },
            // Získá uživatele
            getUsers() {
                // Započne načítání
                this.loading = true;

                // Pošle požadavek na server
                $.get('/user/get-users', { query: this.query }, users => {
                    // Uloží uzýivatele
                    this.users = users;

                    // Ukončí načítání
                    this.loading = false;
                });
            },
            // Po kliknutí na uživatele ze seznamu vyhledávání
            onUserClick(user) {
                // Změní aktivní tab
                this.activeTab = 'detail';

                // Změní detail
                this.detail = user;

                // Změní binding transferu
                this.transferModel = this.detail.roles;
            },
            // Uloží změny
            save() {
                // Pošle požadavek na server
                $.post('/user/set-roles', { id: this.detail.id, roles: this.transferModel }, res => {
                    // Proběhlo vše v pořádku?
                    if (res.status == 'ok') {
                        // Uvědomíme uživatele o úspěchu
                        this.$notify.success({
                            title: 'Úspěch',
                            message: 'Uložení proběhlo v pořádku.',
                            duration: 5500
                        });
                    }

                    // Aktualizuje model
                    this.detail.roles = this.transferModel;
                });
            },
            // Voláno po kliknutí na přidání nového předmětu
            addNewSubject() {
                // Zobrazí nový dialog
                this.$prompt('Napište prosím název nového předmětu', 'Nový předmět', {
                    confirmButtonText: 'Vytvořit',
                    cancelButtonText: 'Zpět',
                    inputPattern: /^.{3,40}$/,
                    inputErrorMessage: 'Zvolte název dlouhý minimálně 3 znaky a maximálně 40'
                }).then(({ value }) => {
                    // Když uživatel dialog potvrdí
                    $.post('/books/create-subject', { title: value }, res => {
                        // Pokud vše proběhlo v pořádku
                        if (res.status == 'ok') {
                            // Započne načítání
                            this.subjectsLoading = true;

                            // Znovu stáhne aktuální předměty
                            $.get('/books/get-subjects/', subjects => {
                                // Uloží předměty
                                this.subjects = subjects;

                                // Ukončí načítání
                                this.subjectsLoading = false;
                            });
                        }
                    });
                });
            },
            // Smaže předmět
            deleteSubject(index, row) {
                // Zobrazí potvrzovací okno
                this.$confirm('Smazání předmětu je nevratné. Budou vymazány i všechny knihy patřící do předmětu.', `Opravdu chcete smazat \"${row.title}\"?`, {
                    type: 'warning',
                    confirmButtonText: 'Smazat',
                    confirmButtonClass: 'el-button--danger',
                    cancelButtonText: 'Zpět'
                }).then(() => {
                    // Pošle požadavek na smazání
                    $.post('/books/delete-subject', { id: row.id }, res => {
                        // Proběhla akce v pořádku?
                        if (res.status = 'ok') {
                            // Odebere řádek
                            this.subjects.splice(index, 1);
                        }
                    });
                });
            },
            // Aktualizuje předmět
            updateSubject(index, row) {
                // Zobrazí nový dialog
                this.$prompt('Napište prosím nový název předmětu', 'Úprava předmětu', {
                    confirmButtonText: 'Upravit',
                    cancelButtonText: 'Zpět',
                    inputPattern: /^.{3,40}$/,
                    inputErrorMessage: 'Zvolte název dlouhý minimálně 3 znaky a maximálně 40'
                }).then(({ value }) => {
                    // Když uživatel dialog potvrdí
                    $.post('/books/update-subject', { id: row.id, title: value }, res => {
                        // Pokud vše proběhlo v pořádku
                        if (res.status == 'ok') {
                            // Upraví název
                            row.title = value;
                        }
                    });
                });
            },
            // Zablokuje nebo odblokuje uživatele
            toggleBlock(id) {
                // Pošle požadavek
                $.post('/user/toggle-block', { id: id }, res => {
                    // Proběhlo vše v pořádku?
                    if (res.status == 'ok') {
                        // Upraví data detailu
                        this.detail.blocked = res.result;

                        // Uvědomíme uživatele o úspěchu
                        this.$notify.success({
                            title: 'Úspěch',
                            message: 'Uživatel byl úspěšně ' + (res.result ? 'zablokován' : 'odblokován'),
                            duration: 5500
                        });
                    }
                });
            }
        },
        // Komponenty
        components: {
            'user-row': httpVueLoader('/client/components/UserRow.vue')
        }
    }
</script>

<!-- Styly -->
<style scoped lang='scss'>
    // Status hledání (čísla)
    .search-status {
        // Odsazení
        margin-top: 15px;

        // Rozměry
        height: 32px;

        // Vzhled
        opacity: .35;
        font-size: .8em;
        line-height: 32px;
    }

    // Nadpis
    h3 {
        margin-top: 25px;
    }

    // Transfer
    .transfer {
        margin-top: 25px;
    }

    // Pomocné třídy
    .full-width {
        width: 100%;
    }

    .float-right {
        float: right;
    }

    .float-left {
        float: left;
    }

    // Použito když potřebujeme odsadit dvě tlačítka vedle sebe
    .btn-margin {
        margin-right: 15px;
    }

    // Tlačítko pro přidání nového předmětu
    .btn-new-subject {
        margin-top: 25px;
    }

    // Varování - nedostatečná práva
    .warning-box {
        opacity: .55;
        font-style: italic;
    }
</style>
