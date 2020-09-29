<template>
    <!-- Komponenta zobrazí knihy uživatele -->
    <div class='wrapper' v-if='!loading'>
        <!-- Taby -->
        <el-tabs type='card'
                 v-model='activeTab'
                 @tab-click='onTabChange()'
                 v-if='!error'>
            <!-- Přehled -->
            <el-tab-pane label='Přehled' name='list'>
                <!-- Vyhledávací panel -->
                <el-input size='small'
                          placeholder='Hledat...'
                          prefix-icon='el-icon-search'
                          class='search-input float-right'
                          v-model='searchQuery'
                          :disabled='books.length == 0'></el-input>
                <!-- Počet výsledků -->
                <div class='search-result float-left'>
                    <i v-if="searchQuery == ''">Máte celkem {{ books.length }} učebnic</i>
                    <i v-else>Nalezeno {{ filterBooks(searchQuery).length }} výsledků</i>
                </div>
                <!-- Tabulka -->
                <el-table :data='filterBooks(searchQuery)'
                          border
                          v-loading='booksLoading'
                          class='table-books'>
                    <!-- Sloupce -->
                    <el-table-column prop='id'
                                     label='ID'
                                     width='60'
                                     sortable></el-table-column>
                    <el-table-column prop='title'
                                     label='Název'
                                     width='360'></el-table-column>
                    <el-table-column prop='subject.title'
                                     label='Předmět'
                                     width='180'></el-table-column>
                    <el-table-column type='expand'
                                      width='60'>
                        <!-- Obsah-->
                        <template slot-scope='props'>
                            <p class='table-desc'>{{ props.row.description }}</p>
                        </template>
                    </el-table-column>
                    <!-- Akce -->
                    <el-table-column fixed='right'>
                        <template slot-scope='scope'>
                            <!-- Detail -->
                            <el-button size='mini'
                                       type='text'
                                       class='float-right'
                                       @click='viewBook(scope.$index, scope.row)'>detail</el-button>
                            <!-- Editovat -->
                            <n-link to='Books:default' :params="{ id: scope.row.id }">
                                <el-button size='mini'
                                           type='text'
                                           class='float-right btn-margin'>editovat</el-button>
                            </n-link>
                            <!-- Smazat -->
                            <el-button size='mini'
                                       type='text'
                                       class='float-right btn-margin'
                                       @click='deleteBook(scope.$index, scope.row)'>smazat</el-button>
                        </template>
                    </el-table-column>
                </el-table>
                <!-- Akce -->
                <el-button type='primary'
                           class='float-right btn-new-book'
                           icon='el-icon-plus'
                           @click='newBookDialog = true'>Nová kniha</el-button>
            </el-tab-pane>
            <!-- Detail knihy -->
            <el-tab-pane label='Detail' name='detail' :disabled="activeTab != 'detail'">
                <!-- Název -->
                <label class='lb'>Název</label>
                <el-input placeholder='Moje kniha' v-model='detail.title'></el-input>
                <!-- Předmět -->
                <label class='lb'>Předmět</label>
                <el-select placeholder='Český jazyk'
                           v-model='detail.subjectId'
                           class='full-width'>
                    <!-- Položky -->
                    <el-option v-for='subject in subjects'
                               :key='subject.id'
                               :label='subject.title'
                               :value='subject.id'></el-option>
                </el-select>
                <!-- Popisek -->
                <label class='lb'>Popisek</label>
                <el-input type='textarea'
                          placeholder='Tato kniha pojednává o...'
                          v-model='detail.description'
                          :rows='3'>
                </el-input>
                <!-- Barva -->
                <label class='lb'>Barva</label>
                <el-color-picker v-model='detail.color'
                                 :predefine='colors'></el-color-picker>
                <!-- Vytvořeno -->
                <label class='lb'>Vytvořeno</label>
                <el-input v-model='detail.createdAt'
                          :disabled='true'>
                    <i slot='prefix' class='el-icon-date el-input__icon'></i>
                </el-input>
                <!-- Publikováno -->
                <label class='lb'>Publikováno</label>
                <el-checkbox v-model='detail.published'>{{ detail.published ? 'Ano' : 'Ne' }}</el-checkbox>
                <!-- Oddělovač -->
                <el-divider></el-divider>
                <!-- Akce -->
                <el-button type='primary' class='float-right' @click='updateBook()'>Uložit</el-button>
                <el-button class='float-right btn-margin' @click='viewList()'>Zpět</el-button>
            </el-tab-pane>
        </el-tabs>
        <!-- Chybový box -->
        <div v-else
             class='warning-box'><i class='el-icon-warning'></i> Pouze redaktoři mohou přidávat a upravovat knihy. Pokud si myslíte, že jde o omyl, kontaktujte správce systému.</div>
        <!-- Dialog pro přidání nové učebnice -->
        <el-dialog title='Nová kniha' :visible.sync='newBookDialog'>
            <!-- Název -->
            <el-input v-model='bookTitle' placeholder='Moje kniha' :class="{ 'invalid': !validateBookName() && canDisplayError }" @change='canDisplayError = true'></el-input>
            <div class='el-message-box__errormsg' v-if='!validateBookName() && canDisplayError'>Název musí mít alespoň 3 znaky a méně než 40 znaků.</div>
            <!-- Select -->
            <el-select v-model='bookSubject'
                       placeholder='Vyberte předmět...'
                       class='subjects-select full-width'>
                <!-- Položky -->
                <el-option v-for='subject in subjects'
                           :key='subject.id'
                           :label='subject.title'
                           :value='subject.id'></el-option>
            </el-select>
            <!-- Footer -->
            <span slot='footer' class='dialog-footer'>
                <el-button @click='newBookDialog = false'>Zpět</el-button>
                <el-button type='primary' @click='addBook()' :disabled='!validateBookName() || bookSubject == null'>Vytvořit</el-button>
            </span>
        </el-dialog>
    </div>
</template>

<script>
    module.exports = {
        // Data komponenty
        data: function() {
            return {
                // Knihy
                books: [],
                booksLoading: true,
                // Předměty
                subjects: [],
                // Aktivní tab
                activeTab: 'list',
                // Detail knihy
                detail: {
                    // Název
                    title: '',
                    // Předmět
                    subjectId: null,
                    // Popisek
                    description: ''
                },
                // Detail knihy před editací (pro revert změn)
                detailBeforeEdit: null,
                // Předdefinované barvy
                colors: [
                    '#E3F2FD',
                    '#FFFDE7',
                    '#E0F2F1',
                    '#FEDACA',
                    '#FCE3E2',
                    '#F6E2FC'
                ],
                // Pravda, pokud dialog pro vytvoření nové učebnice má být vidět
                newBookDialog: false,
                // Název a předmět nové učebnice v dialogu pro novou učebnici
                bookTitle: '',
                bookSubject: null,
                // Pravda, pokud můžeme zobrazit error pro název učebnice v
                // dialogu. Toto je nezbytné, aby se error zobrazil až po
                // napsání prvního znaku.
                canDisplayError: false,
                // Vyhledávaný řetězec
                searchQuery: '',
                // Pokud nemáme dostatečná práva an C(R)UD knih
                error: false,
                // Pravda, pokud se komponenta ještě načítá
                loading: true
            }
        },
        // Když je komponenta vytvořena
        created() {
            // Získá ID právě přihlášeného uživatele
            $.get('/user/get-logged-user-id', id => {
                // Máme právo na C(R)UD akce učebnic?
                $.get('/user/get-user', { id: id }, user => {
                    // Má uživatel potřebnou roli?
                    if (!user.roles.includes('redactor')) {
                        // Uživatel nemá potřebou roli - zobrazíme chybu
                        this.error = true;
                    } else {
                        // Pošle požadavek na server a získá knihy
                        $.get('/books/get-books-by-user-rights', { id: id }, (res) => {
                            // Uloží výsledek
                            this.books = res;

                            // Ukončí načítání
                            this.booksLoading = false;
                        });

                        // Pošle požadavek na server a získá předměty
                        $.get('/books/get-subjects', (res) => {
                            // Uloží výsledek
                            this.subjects = res;
                        });
                    }

                    // Ukončí načítání
                    this.loading = false;
                });
            });
        },
        // Metody
        methods: {
            // Smaže knihu
            deleteBook(index, row) {
                // Vyvoláme potvrzovací nabídku
                this.$confirm('Smazání knihy je nevratné.', `Opravdu chcete smazat \"${row.title}\"?`, {
                    type: 'warning',
                    confirmButtonText: 'Smazat',
                    confirmButtonClass: 'el-button--danger',
                    cancelButtonText: 'Zpět'
                }).then(() => {
                    // ID dokumentů, jenž mají být smazány
                    // Pošle požadavek na server
                    $.post('/books/delete-book', { id: row.id }, res => {
                        // Proběhlo vše v pořádku?
                        if (res.status == 'error') {
                            // Uvědomí uživatele o neúspěchu
                            this.$notify.error({
                                title: 'Nezdar',
                                message: res.message,
                                duration: 5500
                            });
                        } else {
                            // Vše proběhlo v pořádku. Odebere knihu z modelu:
                            this.books.splice(index, 1);
                        }
                    });
                });
            },
            // Uloží knihu
            updateBook(book) {
                // Pošle požadavek na aktualizaci
                $.post('/books/update-book', {
                    id: this.detail.id,
                    subjectId: this.detail.subjectId,
                    title: this.detail.title,
                    description: this.detail.description,
                    color: this.detail.color,
                    published: this.detail.published
                }, res => {
                    // Dostali jsme chybu?
                    if (res.status == 'error') {
                        // Uvědomí uživatele o neúspěchu
                        this.$notify.error({
                            title: 'Nezdar',
                            message: res.message,
                            duration: 5500
                        });
                    } else {
                        // Vše v pořádku. Uvědomí uživatele o výsledku procesu:
                        this.$notify.success({
                            title: 'Úspěch',
                            message: 'Uložení proběhlo v pořádku.',
                            duration: 5500
                        });
                    }
                });
            },
            // Zobrazí knihu
            viewBook(index, row) {
                // Změní aktivní tab na detail
                this.activeTab = 'detail';

                // Uloží současný state objektu
                this.detailBeforeEdit = Object.assign({}, row);

                // Nastaví nový detail
                this.detail = row;
            },
            // Voláno, když se přesouváme z detailu zpět na přehled
            viewList() {
                // Změní aktivní tab na detail
                this.activeTab = 'list';

                // Zahodí původní změny
                Object.assign(this.detail, this.detailBeforeEdit);
            },
            // Přidá novou knihu
            addBook() {
                // Pošle request na vytvoření knihy
                $.post('/books/create-book', { title: this.bookTitle, subjectId: this.bookSubject }, res => {
                    // Přidá novou knihu i do klientské části. Nejdříve si
                    // ji vyžádáme od serveru:
                    $.get('/books/get-book', { id: res.id }, book => {
                        // Přidá knihu do kolekce
                        this.books.push(book);

                        // Zavře dialog
                        this.newBookDialog = false;
                    });
                });
            },
            // Vrátí true nebo false na základě toho, zda je název knihy validní
            validateBookName() {
                return this.bookTitle.length > 3 && this.bookTitle.length < 40;
            },
            // Když změníme aktivní tab
            onTabChange() {
                // Když se překlikneme z detailu na přehled
                if (this.activeTab == 'list') {
                    // Zahodí původní změny
                    Object.assign(this.detail, this.detailBeforeEdit);
                }
            },
            // Vyfiltruje knihy podle hledaného řetězce
            filterBooks(q) {
                return q != '' ? this.books.filter(book => { return book.title.includes(q); }) : this.books;
            }
        },
        // Sledovače
        watch: {
            // Sleduje změny vlastnosti 'bookTitle' (název knihy v dialogu pro novou knihu),
            // protože událost @change el-inputu je retardovaná a nefunguje jak by jeden očekával :-)
            bookTitle: function (now, prev) {
                // Umožní zobrazení erroru v dialogu
                this.canDisplayError = true;
            }
        }
    }
</script>

<!-- Styly -->
<style scoped lang='scss'>
    // Průsvitnost popisku knihy
    .table-desc {
        opacity: .55;
    }

    // Tlačítko pro přidání nové učebnice
    .btn-new-book {
        margin-top: 25px;
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

    .float-left {
        float: left;
    }

    // Použito když potřebujeme odsadit dvě tlačítka vedle sebe
    .btn-margin {
        margin-right: 15px;
    }

    // Select pro výběr předmětu v dialogu
    .subjects-select {
        margin-top: 20px;
    }

    // Zobraazí červený rámeček kolem inputu při erroru v dialogu
    .invalid .el-input__inner {
        border-color: #F56C6C;
    }

    // Vyhledávání
    .search-input {
        // Šířka
        width: 200px;

        // Odsazení
        margin-bottom: 20px;
    }

    .search-result {
        opacity: .35;
        font-size: .8em;
        height: 32px;
        line-height: 32px;
    }

    // Hlavní tabulka
    .table-books {
        border-bottom-left-radius: 4px;
        border-bottom-right-radius: 4px;
    }

    // Varování - nedostatečná práva
    .warning-box {
        opacity: .55;
        font-style: italic;
    }
</style>
