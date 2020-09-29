<template>
    <!-- Facebookové komentáře -->
    <div id='comment-section'>
        <!-- Komentářová sekce -->
        <div class='fb-comments'
             data-width='100%'
             data-numposts='5'
             :data-href='url'></div>
    </div>
</template>

<script>
    module.exports = {
        // Data komponenty
        data: function() {
            return {
                url: ''
            }
        },
        // Atributy komponenty
        props: {
            //  Identifikátor dokumentu
            did: {
                required: true
            }
        },
        // Když je komponenta vytvořena
        mounted() {
            // Získá aktuální URL a připojí kotvičku s ID dokumentu
            this.url = `${window.location.href}#${this.did}`;

            // Vytvoří script s odkazem na facebookovou SDK a vloží
            // script do hlavičky. Díky tomu SDK načítáme pouze tehdy,
            // kdy ji opravdu potřebujeme.
            let script = document.createElement('script');
            script.setAttribute('src', 'https://connect.facebook.net/cs_CZ/sdk.js#xfbml=1&version=v5.0');
            document.head.appendChild(script);
        }
    }
</script>

<style lang='scss'>
    // Facebook komentářový box má odsazení
    // 8 pixelů po každé straně. Upravíme
    // šířku kontejneru a vycentrujeme obsah
    // pro správné zarovnání se zbytkem dokumentu:
    #comment-section {
        // Roztáhne kontenjner o šíři odsazení
        width: calc(100% + 16px);

        // Posune kontenjner na střed
        margin-left: -8px;
    }

    // Roztáhne komentářovou sekci
    #comment-section iframe {
        // Šířka
        width: 100% !important;

        // Odsazení
        margin-top: 15px;
    }
</style>
