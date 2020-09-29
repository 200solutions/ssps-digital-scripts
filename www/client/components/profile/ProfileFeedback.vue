<template>
    <!-- Komponenta pro zprávu účtu -->
    <div class='wrapper'>
        <!-- Předmět -->
        <label class='lb'>Předmět</label>
        <el-input placeholder='Moje postřehy a nápady' v-model='feedback.subject'></el-input>
        <!-- Obsah -->
        <label class='lb'>Obsah sdělení <span class='red bigger-text'>*</span></label>
        <el-input type='textarea'
                  :rows='4'
                  placeholder='Myslím si, že...'
                  v-model='feedback.content'
                  :maxlength='2000'
                  show-word-limit></el-input>
        <!-- Oddělovač -->
        <el-divider></el-divider>
        <!-- Akce -->
        <el-button type='primary'
                   class='float-right'
                   :loading='loading'
                   @click='send()'>Odeslat</el-button>
        <!-- Disclaimer -->
        <div class='disclaimer'><i><span class='red bigger-text'>*</span> Veškerý obsah je odesílán na server anonymně. Zpětná vazba a Vaše nápady pomáhají zlepšovat tuto stránku.</i></div>
    </div>
</template>

<script>
    module.exports = {
        // Data komponenty
        data: function() {
            return {
                // Zpětná vazba
                feedback: {
                    subject: '',
                    content: ''
                },
                // Načítání
                loading: false
            }
        },
        // Metody
        methods: {
            // Odešle zpětnou vazbu
            send() {
                // Vyplnili jsme předmět nebo obsah?
                if (this.feedback.subject == '' || this.feedback.content == '') {
                    // Obrazí upozornění
                    this.$alert('Je nutné vyplnit předmět a obsah zpětné vazby.', 'Upozornění', {
                        type: 'warning',
                        confirmButtonText: 'OK',
                    });

                    // Ukončí činnost funkce
                    return;
                }

                // Započne načítání
                this.loading = true;

                // Pošle požadavek
                $.post('/api/post-feedback', { subject: this.feedback.subject, content: this.feedback.content }, res => {
                    // Ukončí načítání
                    this.loading = false;

                    // Zobrazí poděkování
                    this.$notify({
                        title: 'Úspěch',
                        message: 'Děkujeme za zpětnou vazbu. Vašich názorů si vážíme.',
                        type: 'success'
                    });

                    // Promaže formulářové hodnoty
                    this.feedback.subject = '';
                    this.feedback.content = '';
                });
            }
        }
    }
</script>

<!-- Styly -->
<style scoped lang='scss'>
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

    .red {
        color: #F56C6C;
    }

    .bigger-font {
        font-size: 1.2em;
    }

    .float-right {
        float: right;
    }

    // Disclaimer
    .disclaimer {
        // Odsazení
        padding-top: 20px;
    }
</style>
