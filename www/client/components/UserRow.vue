<template>
    <!-- Komponenta pro zobrazení uživatele -->
    <div class='user-row'
         :class="{ 'teacher': roles.includes('teacher') }"
         @click="$emit('click')">
        <!-- Profilový obrázek -->
        <img :src="imagePath ? imagePath : '/client/images/default-avatar.png' "
             :class="{ 'bordered': !imagePath }"
             alt='profilový obrázek'>
        <!-- Detaily o uživateli -->
        <div class='details'>
            <!-- Jméno a příjmení -->
            <h4>{{ firstName }} {{ lastName }}</h4>
            <!-- E-mail -->
            <h5><i class='el-icon-message'></i> {{ email }}</h5>
            <!-- Role -->
            <div class='roles'>
                <el-tag class='role'
                        size='mini'
                        v-for='(role, i) in roles'
                        :key='i'>{{ translateRole(role) }}</el-tag>
            </div>
        </div>
    </div>
</template>

<script>
    module.exports = {
        // Data komponenty
        data: function() {
            return {
                // Aktivní tab
                activeTab: 'users'
            }
        },
        // Vlastnosti
        props: {
            // Jméno
            firstName: {
                required: true
            },
            // Příjmení
            lastName: {
                required: true
            },
            // E-Mail
            email: {
                required: true
            },
            // Cesta k profilovému obrázku
            imagePath: {
                required: true
            },
            // Role uživatele
            roles: {
                required: true
            }
        },
        // Když je komponenta vytvořena
        created() {

        },
        // Metody
        methods: {
            // Přeloží roli
            translateRole(role) {
                // Rozhodovací proces
                switch(role) {
                    // Admin
                    case 'admin':
                        return 'správce'
                    // Redaktor
                    case 'redactor':
                        return 'redaktor'
                    // Učitel
                    case 'teacher':
                        return 'učitel'
                    // Pokud nebyla nalezena shoda
                    default:
                        return 'neznámá role';
                }
            }
        }
    }
</script>

<!-- Styly -->
<style scoped lang='scss'>
    // Hlavní obalovač
    .user-row {
        // Rozměry
        height: 95px;

        // Odsazení
        padding-top: 15px;

        // Vzhled
        border-bottom: 1px solid #DDDDDD;

        // Poslední user row
        &:last-of-type {
            // Odstraní rámeček
            border-bottom: none;
        }

        // Profilový obrázek
        img {
            // Pozice
            float: left;

            // Rozměry
            height: 65px;
            width: 65px;

            // Vzhled
            border-radius: 4px;
            box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);
            transition: box-shadow .35s;

            // Odsazení
            margin-right: 20px;
        }

        // Detail
        .details {
            // Pozice
            float: left;
        }

        // Jméno a příjmení
        h4 {
            // Vzhled
            font-size: .9em;
            color: #555;
            font-weight: bold;
            letter-spacing: 1px;
        }

        // E-mail
        h5 {
            // Vzhled
            font-size: .65em;
            opacity: .55;

            // Odsazení
            margin-top: 2px;
        }

        // Role
        .roles {
            // Odsazení
            margin-top: 10px;

            // Tag
            .role {
                // Odsazení
                margin-right: 4px;
            }
        }

        // Při najetí myší
        &:not(.no-hover):hover {
            // Ukazatel
            cursor: pointer;

            // Jméno a příjmení
            & h4 {
                // Barva
                color: #00497B;
            }

            // Stín
            & img {
                box-shadow: 0 2px 13.5px 0 rgba(0, 0, 0, 0.175);
            }
        }
    }

    // Pomocné třídy
    .bordered {
        border: 1px solid #ddd;
    }

    // Role - učitel
    .teacher {
        // Pozice
        position: relative;

        // After
        &::after {
            // Obsah
            content: '';

            // Pozice a velikost
            position: absolute;
            left: 56px;
            top: 71px;
            width: 10px;
            height: 10px;

            // Vzhled
            border-radius: 50%;
            background-color: #409EFF;
            border: 2px solid white;
        }
    }

    // Přepíše element styly
    .role {
        // Odebere rámeček
        border-style: none !important;
    }
</style>
