// Google analytics
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'UA-161327025-1');

// Definuje modelový objekt, kde budou
// uloženy všechny modelové třídy
window.model = {};

// Získá modelové třídy od serveru
$.get('/api/get-model', (res) => {
    // Projdeme všechny .js soubory modelu
    for (file of res) {
        // Vyžádáme si soubor a provedeme inject
        // modelových tříd
        $.get(file.path, (script) => {
            // Spustíme zdrojový kód souboru. Provede se inject
            // modelové třídy do window.model objektu
            try {
                // Zavoláme obsah souboru
                eval(script);
            } catch (e) {
                // Ohlásí chybu do konzole
                throw e.constructor(`Error in evaled script: ${e.message} in ${file}`);
            }
        });
    }
}).done(() => {
    // Globálně zaregistruje NLink komponentu pro
    // generování Nette odkazů uvnitř Vue komponent
    Vue.component('n-link', httpVueLoader('/client/components/NLink.vue'));

    // GLobálně zaregistruje error komponentu pro zobrazování chyb
    Vue.component('error', httpVueLoader('/client/components/Error.vue'));

    // Vytvoří novou Vue instanci
    var vm = new Vue({
        // Element, na který instance bude mountnuta
        el: '#app',
        // Komponenty
        components: {
            'book':httpVueLoader('/client/components/Book.vue'),
            'book-root': httpVueLoader('/client/components/BookRoot.vue'),
            'book-section': httpVueLoader('/client/components/BookSection.vue'),
            'ladder': httpVueLoader('/client/components/Ladder.vue'),
            'book-overview': httpVueLoader('/client/components/BookOverview.vue'),
            'redactors-list': httpVueLoader('/client/components/RedactorsList.vue'),
            'ssps-header': httpVueLoader('/client/components/Header.vue')
        },
        // Router
        router: new VueRouter({
            // Pouze na
            base: '/user',
            // Routy
            routes: [
                { path: '/books', component: httpVueLoader('/client/components/profile/ProfileBooks.vue') },
                { path: '/account', component: httpVueLoader('/client/components/profile/ProfileAccount.vue') },
                { path: '/notifications', component: httpVueLoader('/client/components/profile/ProfileNotifications.vue') },
                { path: '/management', component: httpVueLoader('/client/components/profile/ProfileManagement.vue') },
                { path: '/feedback', component: httpVueLoader('/client/components/profile/ProfileFeedback.vue') }
            ]
        })
    });

    // Čeština pro element knihovnu
    ELEMENT.locale(ELEMENT.lang.csCZ);

    // Nastaví build proces pro kompilaci SASSu
    httpVueLoader.langProcessor.scss = function (scss) {
        // Vrátíme promise
        return new Promise(function(resolve, reject) {
            // Kompilace
            Sass.compile(scss, function (result) {
                // Povedlo se?
                if (result.status === 0)
                    // Vrátíme výsledek
                    resolve(result.text)
                else
                    // Vrátíme chybové hlášení
                    reject(result)
            });
        });
    }
});
