<template>
  <header class='ssps-header' ref="header">
    <div class="carousel" id="carousel-backgrounds" ref="carousel">
      <div
        class="item"
        v-for="(carousel, i) in carousels"
        v-bind:key="carousel.name + i"
        v-bind:class="{
          'image': carousel.type == 'image',
          'video': carousel.type == 'video',
          'active': carouselIndex == i}"
      >
        <video v-if="carousel.type == 'video'" :src="carousel.media" loop autoplay muted></video>
        <img v-else-if="carousel.type == 'image'" :src="validateMedia(carousel.media)" alt />
      </div>
    </div>

    <div class="darkener"></div>

    <div class="bar container">
      <div class="logo">
        <slot name="logo"></slot>
      </div>

      <nav>
        <slot name="navigation"></slot>

        <div class="mobile">
          <a @click="toggleMenu(true)">
            <span class="fal fa-bars"></span>
          </a>
        </div>
      </nav>

      <nav
        class="mobile"
        ref="mobileMenu"
        @click="toggleMenu(false)"
        v-bind:class="{'active': menu}"
      >
        <div class="menu" @click="wait">
          <div class="logo">
            <slot name="logo"></slot>

            <span class="fal fa-times"></span>
          </div>

          <div class="menu-container">
            <slot name="navigation"></slot>
          </div>
        </div>
      </nav>
    </div>

    <div class="content container">
      <div class="grid">
        <div class="carousel">
          <div
            class="item"
            v-for="(carousel, i) in carousels"
            v-bind:key="carousel.name + i"
            v-bind:class="{'active': carouselIndex == i}"
          >
            <h2 class='item-heading'>{{carousel.name}}</h2>
            <p class='item-text'>{{carousel.content}}</p>
          </div>

          <div class="loader">
            <div class="bar" ref="bar"></div>
          </div>

          <div class="controls">
            <div class="toggle" @click="toggleCarousel">
              <span
                class="fa"
                v-bind:class="{'fa-pause': carouselEnabled, 'fa-play': !carouselEnabled}"
              ></span>
            </div>
            <div
              v-for="(carousel, i) in carousels"
              v-bind:key="'control' + carousel.name + i"
              class="carousel"
              @click="changeCarousel(i)"
              v-bind:class="{'active': carouselIndex == i}"
            >
              <span class="fa fa-circle"></span>
            </div>
          </div>
        </div>

        <div class="links">
            <a href="https://ssps.cz" class="link">
                <i class="fal fa-home" aria-hidden="true"></i> Web školy
            </a>
            <a href="https://bakalari.ssps.cz" class="link">
                <i class="fal fa-list-ol" aria-hidden="true"></i> Bakaláři
            </a>
            <a  href="https://portal.office.com/" class="link">
                <i class="fal fa-video" aria-hidden="true"></i> MS Teams
            </a>
            <a  href="https://moodle.ssps.cz" class="link">
                <i class="fal fa-graduation-cap" aria-hidden="true"></i> Moodle
            </a>
            <a  href="https://plus4u.net/ues/sesm?client_id=5XexFx14EX879j56k081S8U1&amp;UES_Gate=ues:UNIVERSE:SSPSGATE" class="link">
                <i class="fal fa-project-diagram" aria-hidden="true"></i> Virtuální škola
            </a>

            <a href="https://www.ssps.cz/studenti/systemy/" id='featured-link' class="link featured">Více</a>
        </div>

        <div class="news">
          <div class="title">
            <h3 id='news-title'>Novinky</h3>
          </div>

          <div class="news">
            <div v-for="(newa, i) in news" v-bind:key="newa + i" class="new">
              <div class="info">
                <div class="date">
                  <span>{{newa.date.day}}</span>
                  <br />
                  {{newa.date.month}}
                </div>
                <div class="icon">
                  <span :class="'fal fa-' + newa.icon"></span>
                </div>
              </div>

              <div class="text">
                <h3>
                  <a :href="newa.link">{{newa.title}}</a>
                </h3>
                <p v-html="newa.content"></p>
              </div>
            </div>
          </div>
        </div>

        <!--header-video :video="video"></header-video-->
      </div>
    </div>
  </header>
</template>

<script>
module.exports = {
  props: {
    minimal: {
      type: Boolean,
      default: false,
    },
    carousels: Array,
    video: Object,
    systems: String,
    more: String,
    defaultbackground: String,
  },
  data() {
    return {
      news: [],
      menu: false,
      carouselEnabled: true,
      carouselIndex: 0,
      carouselTime: 10 * 1000,
      carouselInterval: undefined,
      carouselBarAnimation: undefined,
    };
  },
  methods: {
    toggleMenu(state) {
      this.menu = state;
    },
    wait() {},
    validateMedia(media) {
      if (media == undefined || media.length == 0 || media == false)
        return this.defaultbackground;
      return media;
    },
    // Přepnutí carouselu
    toggleCarousel() {
      if (this.carouselEnabled) this.stopCarousel();
      else this.startCarousel();
    },
    // Vypnutí carouselu
    stopCarousel() {
      if (this.carouselInterval) {
        clearInterval(this.carouselInterval);
        this.carouselInterval = undefined;
      }
      this.carouselEnabled = false;
      this.resetBar();
    },
    // Zapnutí carouselu
    startCarousel() {
      this.carouselEnabled = true;
      this.carouselInterval = setInterval(() => {
        this.cycle();
      }, this.carouselTime);
      const bar = this.$refs.bar;
      this.carouselBarAnimation = bar.animate(
        [
          {
            width: "0%",
          },
          {
            width: "100%",
          },
        ],
        {
          duration: this.carouselTime,
          fill: "forwards",
        }
      );
    },
    resetBar() {
      const bar = this.$refs.bar;
      this.carouselBarAnimation.pause();
      const currentWidth =
        (parseFloat(getComputedStyle(bar, null).width.replace("px", "")) /
          parseFloat(
            getComputedStyle(bar.parentNode, null).width.replace("px", "")
          )) *
        100;
      this.carouselBarAnimation = bar.animate(
        [
          {
            width: currentWidth + "%",
          },
          {
            width: "0%",
          },
        ],
        {
          duration: 500,
          fill: "forwards",
        }
      );
    },
    // Rotování carouselu
    cycle() {
      let carouselIndex = this.carouselIndex + 1;
      // Resetování indexu
      if (carouselIndex > this.carousels.length - 1) carouselIndex = 0;
      this.changeCarousel(carouselIndex);
    },
    // Změna aktuálního prvku
    changeCarousel(i) {
      // Je-li načtený carousel, nic nedělej.
      if (this.carouselIndex == i) return;
      // Pokuď existuje timer a máme zapnutý rotaci carouselu, vypnulujem ho.
      if (this.carouselInterval && this.carouselEnabled) {
        clearInterval(this.carouselInterval);
        this.carouselInterval = setInterval(() => {
          this.cycle();
        }, this.carouselTime);
      }
      const carouselNodes = this.$refs.carousel.childNodes;
      const bar = this.$refs.bar;
      // Animace skrytí pozadí aktuálního prvku
      carouselNodes[this.carouselIndex].animate(
        [
          {
            opacity: "1",
          },
          {
            opacity: "0",
          },
        ],
        {
          duration: 600,
          fill: "forwards",
        }
      );
      // Nastavení nového prvku
      this.carouselIndex = i;
      // Animace zobrazení pozadí nového prvku
      carouselNodes[this.carouselIndex].animate(
        [
          {
            opacity: "0",
          },
          {
            opacity: "1",
          },
        ],
        {
          duration: 1000,
          fill: "forwards",
        }
      );
      // Pokud je pozadí video
      if (carouselNodes[this.carouselIndex].classList.contains("video")) {
        // Resetuj čas elementu video
        carouselNodes[this.carouselIndex].querySelector(
          "video"
        ).currentTime = 0;
      }
      // Animace baru
      if (this.carouselEnabled)
        this.carouselBarAnimation = bar.animate(
          [
            {
              width: "0%",
            },
            {
              width: "100%",
            },
          ],
          {
            duration: this.carouselTime,
            fill: "forwards",
          }
        );
    },
  },
  mounted() {
      $.get('https://www.ssps.cz/api-v1/news', res => {
          this.news = res;
      });
    // Získáme z aktuálního mobile-menu všechny linky
    const links = this.$refs.mobileMenu.querySelectorAll(
      "li.menu-item-has-children"
    );
    // Pro každý link
    for (let link of links) {
      // Při kliku
      link.onclick = function (e) {
        // Přidáme classu elementu, na který jsme klikli, a zastavíme propagaci eventu
        e.target.classList.toggle("show-sub-menu");
        e.stopPropagation();
      };
    }
    this.$refs.header.style.minHeight = window.innerHeight + "px";
    const carouselNodes = this.$refs.carousel.childNodes;
    // Animace při načtení
    carouselNodes[0].animate(
      [
        {
          opacity: "0",
        },
        {
          opacity: "1",
        },
      ],
      {
        duration: 100,
        fill: "forwards",
      }
    );

    this.startCarousel();
  },
};
</script>

<style lang="scss">
#news-title {
    // Nastavení pozice
    font-family: 'Raleway' !important;
    font-size: 16px !important;
    font-weight: normal !important;
}

#featured-link {
    font-weight: bold !important;
}

.item-heading {
    font-family: 'Raleway' !important;
}

.item-text {
    font-family: 'Roboto' !important;
}

// Základní barvy, neměli by se používat v komponentách
$primary: #00497b;
$secondary: #c69211;
$black: #000000;
$white: #ffffff;
$colors: (
  header-background: $primary,
  header-carousel-darkener: rgba(darken($primary, 15), 0.6),
  header-minimal-carousel-darkener: rgba($primary, 0.8),
  header-content-carousel-loader: $secondary,
  header-content-carousel-controls-button-text: $white,
  header-content-carousel-controls-button-hover-text: $secondary,
  header-content-news-background: $white,
  header-content-news-text: lighten($black, 10),
  header-content-news-title-background: darken($primary, 3),
  header-content-video-darkener: rgba($primary, 0.4),
  header-content-video-text: darken($white, 5),
  header-content-video-darkener-hover: rgba(lighten($primary, 5), 0.3),
  header-content-links-link: $white,
  header-content-links-link-border: rgba($white, 0.3),
  header-content-links-link-hover: $secondary,
  header-content-links-link-featured: $secondary,
  header-content-links-link-featured-hover: $white,
  header-content-links-text: darken($white, 16),
  header-content-background: rgba(darken($primary, 3), 0.95),
  navigation-text: $white,
  navigation-text-hover: lighten($secondary, 10),
  navigation-submenu-background: $white,
  navigation-submenu-border: darken($white, 5),
  navigation-submenu-text: $black,
  navigation-submenu-text-hover: lighten($secondary, 10),
  navigation-mobile-close: $secondary,
  navigation-mobile-back-background: rgba(0, 0, 0, 0.3),
  navigation-mobile-background: darken($primary, 10),
  navigation-mobile-text: $white,
  navigation-mobile-text-hover: $secondary,
  navigation-mobile-text-submenu: darken($white, 15),
  news-text: lighten($black, 15),
  news-text-hover: $primary,
  news-date-text: $black,
  news-date-background: transparent,
  news-icon-background: rgba(lighten($primary, 20), 0.1),
  news-border: rgba($black, 0.1),
  news-primary-background: $primary,
  news-primary-icon: $white,
  news-primary-border: rgba($black, 0.2),
  news-list-border: rgba($black, 0.1),
  new-info-color: lighten($black, 40),
);
@function color($name) {
  @return map-get($colors, $name);
}
$break-small: 768px;
$break-large: 1024px;
@mixin respond-to($media) {
  @if $media == handhelds {
    @media only screen and (max-width: $break-small) {
      @content;
    }
  } @else if $media == medium {
    @media only screen and (min-width: $break-small) and (max-width: $break-large) {
      @content;
    }
  } @else if $media == wide {
    @media only screen and (min-width: $break-large) {
      @content;
    }
  }
}
@mixin transition($args...) {
  -webkit-transition: $args;
  -moz-transition: $args;
  -ms-transition: $args;
  -o-transition: $args;
  transition: $args;
}
// Nastavení základního kontejneru pro zobrazování na všech zařízeních.
.container {
  width: 100%; // Oprava vůči stínům, které jsou na boku

  padding: 75px;
  padding-top: 0;
  padding-bottom: 0;
  @include respond-to(handhelds) {
    // max-width: 300px !important;
     padding: 25px;
     padding-bottom: 0;
  }
  @include respond-to(medium) {

  }
}
// Novinka
.new {
  // Zarovnání na jeden řádek
  display: flex;
  // Aby info nemělo 100% výšku
  align-items: flex-start;
  &.primary {
    > .info {
      box-shadow: inset 0 0 0 1px color("news-primary-border");
      .icon {
        background-color: color("news-primary-background") !important;
        color: color("news-primary-icon") !important;
      }
    }
  }
  // Info (datuma ikonka) novinky
  > .info {
    // Nastavení pevné velikosti
    width: 40px;
    // Flex si ale dělá co chce :-))
    flex: 0 0 40px;
    // Odsazení
    margin: 5px;
    // Zaokrouhlení ohraničení
    border-radius: 5px 5px 0 0px;
    // Zarovnání pod sebe a vyplnění místa
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    // Zarovnání
    text-align: center;
    box-shadow: inset 0 0 0 1px color("news-border");
    background-color: color("news-date-background");
    color: color("news-date-text");
    // Datum
    > .date {
      padding: 5px;
      line-height: 1.5em;
      font-size: 0.7em;
      // Zvětšení dne měsíce
      span {
        font-size: 1.3em;
      }
    }
    // Ikona
    > .icon {
      background-color: color("news-icon-background");
      color: $primary;
      // Odsazení
      padding: 10px;
      // Zvětšení ikonky
      span {
        font-size: 1.1em;
      }
    }
  }
  // Text novinky
  > .text {
    // Odsazení
    margin: 10px 0;
    padding: 0 10px;
    // Zarovnání pod sebe a vyplnění místa
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    // Nadpis
    h3 {
      // Odsazení aby se nespojilo s textem
      margin-bottom: 10px;
      // Tlusté písmo
      font-weight: bold;
      font-family: 'Raleway' !important;
      font-size: 16px !important;
      // Klikací nadpis
      a {
        // Vypnutí podtrhnutí
        text-decoration: none;
        color: color("news-text");
        // Přechod
        @include transition(all 0.2s ease);
        // Při najetí
        &:hover {
          // Podtrhnutí
          text-decoration: underline;
          color: color("news-text-hover");
          // Přechod
          @include transition(all 0.2s ease);
        }
      }
    }
    // Text
    p {
      // Zmenšení písma
      font-size: 0.95em;
      // Odkaz v textu (nejspíše použítí pouize pro tlačítko "zobrazit více")
      a {
        // Podtrhnutí
        text-decoration: underline;
        color: color("news-text");
        // Přechod
        @include transition(color 0.2s ease);
        // Při najetí
        &:hover {
          color: color("news-text-hover");
          // Přechod
          @include transition(color 0.2s ease);
        }
      }
    }
  }
}
// Novinky
section.news {
  // Zobrazení v 1 sloupci
  display: grid;
  grid-template-columns: repeat(1, 1fr);
  .new {
    // Odsazení
    padding: 20px 0;
    // Ohraničení mezi aktuality
    &:not(:last-of-type) {
      border-bottom: 1px solid color("news-list-border");
    }
  }
}
// Informace aktuality
section.new-info {
  // Zarovnání na střed
  display: flex;
  justify-content: flex-start;
  align-items: center;
  // Odsazení z dola
  margin-bottom: 40px;
  // Prvky
  > .date,
  > .author {
    // Zarovnání na střed
    display: flex;
    justify-content: center;
    align-items: center;
    // Odsazení
    margin: 10px;
    color: color("new-info-color");
    // Ikonka
    > .icon {
      // Odsazení z prava
      padding-right: 10px;
      // Zvětšení písma
      font-size: 1.05em;
    }
    // Text
    > .content {
      // Zmenšení písma
      font-size: 0.9em;
    }
  }
}
header {
  // Resetování pozice
  position: relative;
  // Zajíštění minimálně full-page headeur
  width: 100%;
  min-height: 100vh;
  // Barva pozadí když chybí jakýkoliv carousel
  background-color: color("header-background");
  // Nic co přesahuje header se nezobrazí.
  overflow: hidden;
  // Zarovnání pod sebe a vyplnění kontejneru
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  // Zobrazení headeru pro příspěvek - bez contentu
  &.minimal {
    // Vyplnění místa
    height: auto;
    min-height: auto;
    // Odsazení menu
    > .bar {
      margin-top: 10px;
      margin-bottom: 150px;
    }
    > .darkener {
      background-color: color("header-minimal-carousel-darkener");
    }
  }
  // Protáčení obrázků/videií na pozadí
  > .carousel {
    // Posunutí carouselu aby vždy vyplnil header
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    // Posunutí carouselu dozadu
    z-index: 1;
    > .item {
      // Zobrazení vše na sobě
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      // Skrytí
      opacity: 0;
      // Zarovnání na střed
      display: flex;
      justify-content: center;
      align-items: center;
      // Když je aktivní, zobraz.
      &.active {
        opacity: 1;
      }
      // Zobrazení statického obrázku
      &.image {
        img {
          // Vyplnění headeru
          width: 100%;
          height: 100%;
          // Aby se obrázek automaticky nadimenzoval a udržel si ratio
          object-fit: cover;
        }
      }
      // Zobrazení videa
      &.video {
        video {
          // Vyplnění headeru
          width: 100%;
          height: 100%;
          // Aby se video automaticky nadimenzovalo a udrželo si ratio
          object-fit: cover;
        }
      }
    }
  }
  // Ztmavení carouselu aby tolik nevyčníval ze stránky
  > .darkener {
    // Nastavení ztmavovače aby se zobrazoval nad carouselem
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 2;
    background-color: color("header-carousel-darkener");
  }
  // Horní bar - logo s navigací
  > .bar {
    // Nastavení baru aby se zobrazoval nad carouselem
    position: relative;
    z-index: 10;
    // Napozicování baru aby si udržel místo mezi logem a navigací
    display: flex;
    justify-content: space-between;
    align-items: center;
    // Odsazení ze shora
    margin-top: 10px;
    // Logo
    > .logo {
      max-width: 200px;
      img {
        width: 100%;
        height: auto;
      }
      @include respond-to("medium") {
        // Zmenšení velikosti loga na max 150px
        max-width: 150px;
      }
    }
    // Navigace na mobilu
    nav.mobile {
      // Zákaz zobrazování mimo mobil
      visibility: hidden;
      @include respond-to(wide) {
        // Skrytí
        visibility: hidden;
      }
      &.active {
        // Viditelné
        visibility: visible !important;
        background-color: color("navigation-mobile-back-background");
        // Přechod
        @include transition(all 0.3s linear);
        // Zobrazení menu
        > .menu {
            list-style: none;
          // Přesun z prava
          right: 0px;
          // Přechod
          @include transition(all 0.3s ease);
        }
      }
      // Pozice a zabrání celé stránky
      position: fixed;
      z-index: 20;
      top: 0;
      right: 0;
      width: 100%;
      height: 100vh;
      // Průhledné pozadí
      background-color: transparent;
      @include transition(all 0.5s linear);
      > .menu {
          list-style: none;
        position: absolute;
        top: 0;
        // Zabrání místa a 100% výšky zařízení
        right: -270px;
        width: 270px;
        height: 100vh;
        background-color: color("navigation-mobile-background");
        // Zarovnání do sloupce
        // auto - logo
        // 1fr - menu (zbytek plochy)
        display: grid;
        grid-template-rows: auto 1fr;
        @include transition(all 0.7s ease);
        // Logo
        > .logo {
          // Zarovnání od sebe na jednom řádku
          display: flex;
          justify-content: space-between;
          align-items: center;
          padding: 20px;
          // Velikost obrázku
          img {
            width: 70%;
            height: auto;
          }
          // Tlačítko zavření
          span {
            color: color("navigation-mobile-close");
            font-size: 1.7em;
          }
        }
        // Samotné menu (podobné pro ostatní zařízení)
        div.menu-container {
          padding-right: 32px;
          height: 100%;
          overflow: auto;
          -webkit-overflow-scrolling: touch;
          max-width: 100%;
        }
        ul.menu {
          list-style: none;
          font-family: 'Roboto' !important;
          // Odsazení
          padding: 10px 0px 10px 32px;
          li {
            font-family: 'Roboto' !important;
            // Resetování pozice
            position: relative;
            // Odsazení
            padding-top: 12px;
            > a {
              font-family: 'Roboto' !important;
              color: color("navigation-mobile-text");
              // Vynucení odkazů bez podtržení
              text-decoration: none;
              // Tlusté písmo
              font-weight: 500;
              // Velké písmena
              text-transform: uppercase;
              // Velikost písma
              font-size: 1.1em;
              // Přechod
              @include transition(0.2s color ease-in-out);
              &:hover {
                color: color("navigation-mobile-text-hover");
                // Přechod
                @include transition(0.2s color ease-in-out);
              }
            }
            // Aktuální stránka
            &.current-menu-item > a,
            &.current-page-ancestor a {
              color: color("navigation-mobile-text-hover") !important;
            }
          }
          // Submenu se samo nezobrazí
          .sub-menu {
            display: none;
          }
        }
      }
    }
    // Navigace
    nav:not(.mobile) {
      // Tlačítko na zobrazení menu v mobilní verzi
      > .mobile {
        // Skrytí
        display: none;
        @include respond-to(handhelds) {
          // Zobrazení na mobilu
          display: block;
        }
        @include respond-to(medium) {
          // Zobrazení na tabletu
          display: block;
        }
        > a {
          color: color("navigation-text");
          padding: 20px 10px;
          // Vynucení odkazů bez podtržení
          text-decoration: none;
          // Tlusté písmo
          font-weight: 500;
          // Velké písmena
          text-transform: uppercase;
          // Zvětšení
          font-size: 2em;
          // Přechod
          @include transition(0.2s color ease-in-out);
          &:hover {
            color: color("navigation-text-hover");
            // Přechod
            @include transition(0.2s color ease-in-out);
          }
        }
      }
      ul.menu {
         font-family: 'Roboto' !important;
        // Vypnutí odrážek
        list-style: none;
        // Zobrazení jako jednoho řádku
        display: flex;
        @include respond-to(handhelds) {
          // Menu se nezobrazí na mobilu
          display: none;
        }
        @include respond-to(medium) {
          // Menu se nezobrazí na tabletu
          display: none;
        }
        li {
              font-family: 'Roboto' !important;
          position: relative;
          > a {
               font-family: 'Roboto' !important;
            color: color("navigation-text");
            padding: 20px 10px;
            // Vynucení odkazů bez podtržení
            text-decoration: none;
            // Tlusté písmo
            font-weight: 500;
            // Velké písmena
            text-transform: uppercase;
            // Zvětšení místa kde se uživateli ukazuje že element je naklikávací
            cursor: pointer;
            // Přechod
            @include transition(0.2s color ease-in-out);
            @include respond-to(medium) {
              // Zmenšení odsazení
              padding: 12px 6px;
            }
            &:hover {
              color: color("navigation-text-hover");
              // Přechod
              @include transition(0.2s color ease-in-out);
            }
          }
          // Při najetí se zobrazí submenu
          &:hover {
            > ul.sub-menu {
              display: block;
            }
          }
          // Aktuální stránka
          &.current-menu-item > a,
          &.current-page-ancestor a {
            color: color("navigation-text-hover") !important;
          }
          // Pod menu
          > ul.sub-menu {
            // Automatické skrytí
            display: none;
          }
        }
      }
    }
  }
  // Obsah headeru
  > .content {
    // Nastavení obsahu aby se zobrazoval nad carouselem a v dolní části stránky
    position: relative;
    z-index: 7;
    bottom: 0;
    // Obsah je rozdělen do dvou sloupců a dvou řádků
    // Nastavení gridu
    > .grid {
      // Minimální výška je 300 px.
      min-height: 300px;
      display: grid;
      // Nastavení kolik místa zaberou sloupce
      grid-template-columns: 1fr minmax(210px, 20%);
      // Nastavení kolik místa zaberou řádky
      grid-template-rows: minmax(270px, 325px) 210px;
      @include respond-to(handhelds) {
        // Změna na flex - zobrazení pod sebou
        display: flex;
        flex-direction: column;
        // Video a novinky se na mobilu nezobrazí
        > .video,
        > .news {
          display: none !important;
        }
        // Carousel bude mít normální padding, zmenšení velikosti textů
        > .carousel {
          padding: 10px !important;
          padding-bottom: 25px !important;
          h2 {
            font-size: 1.6em !important;
          }
          p {
            font-size: 0.95em !important;
            line-height: 1.2em !important;
          }
          .loader {
            width: 100% !important;
            height: 3px !important;
          }
        }
        // Odkazy - zmenšení paddingu
        > .links {
            padding: 8px 10px !important;

            > .link {
                // Přidání odsazení
                color: white;
                font-family: 'Raleway';
                font-size: 16px;
                padding-bottom: 8px !important;
                margin: 5px 0 !important;

                // Smazání odsazení u "více"
                &.featured {
                    color: #C69211;
                    padding-bottom: 0 !important;
                     font-weight: 500 !important;

                    &:hover {
                        color: white;
                    }
                }

                &:hover {
                    color: #C69211;
                }
            }
        }
      }
      @include respond-to(medium) {
        // Zmenšení velikosti listu s odkazy
        grid-template-columns: 1fr minmax(180px, 20%);
        grid-template-rows: minmax(240px, 300px) 210px;
        // Zmenšení odsazení a textu
        > .carousel {
          padding: 10px 25px 25px 10px !important;
          h2 {
            font-size: 2.4em !important;
          }
          p {
            font-size: 1em !important;
          }
        }
        // Skrytí videa
        > .video {
          display: none !important;
        }
        // Zmenšení odsazení odkazů
        > .links {
            padding: 10px 20px !important;

            > .link {
                color: white;
                font-family: 'Raleway';
                font-size: 16px;
                // Přidání odsazení
                padding-bottom: 10px !important;
                margin: 5px 0 !important;

                &:hover {
                    color: #C69211;
                }

                // Smazání odsazení u "více"
                &.featured {
                    color: #C69211;
                    padding-bottom: 0 !important;
                    font-weight: 500 !important;

                    &:hover {
                        color: white;
                    }
                }
            }
        }

        // Novinky - roztažení na dva sloupce (místo videa)
        > .news {
          grid-row: 2;
          grid-column: 1 / 3;
          .new {
            // Zmenšení info boxu
            > .info {
              width: 35px !important;
              flex: 0 0 35px !important;
              // Zmenšení ikonky
              > .icon {
                font-size: 0.7em !important;
              }
            }
            // Zmenšení textu
            > .text {
              h3 {
                font-size: 0.95em !important;
              }
              p {
                font-size: 0.8em !important;
              }
            }
          }
        }
      }
      // Nastavení všech dětí
      > * {
        padding: 20px;
        color: white;
      }
      // Texty ke carouselu
      > .carousel {
        // Průhledné pozadí
        background-color: transparent;
        // Odsazení od prava a leva (nesmí se spojit s ostatními boxy)
        padding: 10px 150px 40px 10px;
        // Každý text v carouselu
        > .item {
          // To co není aktivní se nezobrazí.
          display: none;
          // Nikdy nepřeteče kontejner
          overflow: hidden;
          // To co je aktivní ano.
          &.active {
            display: block;
          }
          // Nadpis itemu
          h2 {
            // Velké písmo
            font-size: 3.5em;
            // Tlusté písmo
            font-weight: bold;
            // Odsazení
            margin-bottom: 30px;
            margin-top: 10px;
            // Mezery mezi písmeny
            letter-spacing: 1px;
            // Kapitálky
            text-transform: uppercase;
          }
          // Text
          p {
            // Velké písmo
            font-size: 1.2em;
            // Zvětšení řádku
            line-height: 1.5em;
            font-weight: 300;
          }
        }
        // Nastavení načítavače carouselu (ukazuje kolik již bylo času zobrazeno)
        > .loader {
          // Odsazení ze shora
          margin-top: 20px;
          // Nastavení šířky a výšky
          width: 250px;
          height: 4px;
          outline: 2px color("header-content-carousel-loader") solid;
          // Nastavení ohraničení
          // Zobrazovač postupu
          > .bar {
            // Zabere celou výšku
            height: 100%;
            // Ze začátku se nezobrazuje
            width: 0%;
            background-color: color("header-content-carousel-loader");
          }
        }
        // Navigace carouselu
        > .controls {
          // Zobrazení na jeden řádek
          display: flex;
          // Vertikální zarovnání na střed
          align-items: center;
          // Odsazení
          margin-top: 5px;
          // Jedno tlačítko
          > div {
            // Zarovnání do středu
            display: flex;
            justify-content: center;
            align-items: center;
            // Zmenšení velikosti
            font-size: 0.5em;
            // Kurzor
            cursor: pointer;
            color: color("header-content-carousel-controls-button-text");
            // Ikonka
            span {
              // Odsazení
              padding: 15px 10px;
            }
            // Přechod
            @include transition(all 0.2s ease);
            // První tlačítko nebudeme mít odsazení
            &:first-child span {
              padding-left: 0;
            }
            // Přepínací tlačítko
            &.toggle {
              font-size: 1.1em;
              span {
                padding: 15px 10px;
                padding-right: 20px;
              }
            }
            // Zvýraznění aktuálního carouselu
            &.active {
              color: color(
                "header-content-carousel-controls-button-hover-text"
              );
            }
            // Při najetí
            &:hover {
              color: color(
                "header-content-carousel-controls-button-hover-text"
              );
              // Přechod
              @include transition(all 0.2s ease);
            }
          }
        }
      }
      // Odkazy
       > .links {
           // Odsazení
           padding: 15px 20px;

           // Vyplnění místa
           display: flex;
           flex-direction: column;
           justify-content: space-evenly;

           background-color: color("header-content-background");

           // Jednotlivý odkaz
           > .link {
               color: white;
               font-family: 'Raleway';
               font-size: 16px;
               // Přidání odsazení
               margin: 7px 0;
               // Vypnutí podtržení
               text-decoration: none;

               i {
                   padding: 0 7px;
               }

               // Pokud není speciální, nezobrazí se dolní ohraničení
               &:not(.featured) {
                   padding-bottom: 14px;
                   border-bottom: 1px solid color("header-content-links-link-border");
               }

               &:hover {
                   color: #C69211;
               }


               // Pokud je speciální
               &.featured {
                      color: #C69211;
                   // Menší odsazení
                   margin: 3px 0;
                   // Menší text
                   font-size: 0.9em;
                   // Kapitálky
                   text-transform: uppercase;
                   // Tlusté písmo
                   font-weight: 500;

                   a {
                       color: color("header-content-links-link-featured");
                       // Zrušení podtržení
                       text-decoration: none;

                       @include transition(0.2s ease color);
                   }

                   // Při najetí
                   &:hover {
                       color: color("header-content-links-link-featured-hover");
                       @include transition(0.2s ease color);
                   }
               }

               // Nadpis systému
               h3 {
                   // Odsazení ze shora aby nebyl blízko textu
                   margin-bottom: 5px;

                   a {
                       color: color("header-content-links-link");
                       // Zrušení podtržení
                       text-decoration: none;

                       @include transition(0.2s ease color);
                   }
               }
               // Při najetí
               &:hover {
                   color: color("header-content-links-link-hover");
                   @include transition(0.2s ease color);
               }

               // Text - popis systému
               p {
                   // Zmenšení textu
                   font-size: 0.85em;
                   line-height: 1.5em;
                   color: color("header-content-links-text");
               }
           }
       }
      // Novinky
      > .news {
        // TODO: Chceme?
        grid-row: 2;
        grid-column: 1 / 3;
        // Resetování pozice
        position: relative;
        // Nastavení barvy
        background-color: color("header-content-news-background");
        // Odsazení kvůli nadpisu
        padding: 10px 10px 10px 60px;
        // Nadpis z boku
        > .title {
          // Nastavení pozice
          font-family: 'Raleway' !important;
          font-size: 16px !important;
          font-weight: normal !important;

          position: absolute;
          top: 0;
          left: 0;
          // Nastavení velikosti (vyplní výšku)
          height: 100%;
          width: 50px;
          background-color: color("header-content-news-title-background");
          // Zarovnání textu na střed
          display: flex;
          justify-content: center;
          align-items: center;
          // Samotný text nadpisu
          h3 {
            // Otočení o 270 stupňů
            transform: rotate(-90deg);
            // Text bude z kapitálek
            text-transform: uppercase;
          }
        }
        // Novinky
        > .news {
          color: color("header-content-news-text");
          // Zarovnání a vyplnění místa
          display: flex;
          flex-wrap: wrap;
          justify-content: space-between;
          height: 100%;
          // Novinky
          > .new {
            // Půlku kontejneru
            width: 50%;
          }
        }
      }
      // Video
      > .video {
        // Resetování pozice a odsazení
        position: relative;
        padding: 0;
        // Zarovnání na střed ikonky a textu
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        // Přetečení se nezobrazí
        overflow: hidden;
        // Napověda uživateli že se dá na kontejner klikat
        cursor: pointer;
        // Pokud je aktivni
        &.active {
          // Skryj všechno krom iframe
          > img,
          > .darkener,
          > .content {
            display: none;
          }
          // Zobraz iframe a napozicuj
          iframe {
            display: block;
            // Nastavení pozice a velikosti
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            // Aby se ifrmae automaticky nadimenzoval a udržel si ratio
            object-fit: cover;
          }
        }
        // Pokud neni aktivní, skryj iframe
        &:not(.active) {
          iframe {
            display: none;
          }
        }
        // Při najetí
        &:hover {
          > .darkener {
            background-color: color("header-content-video-darkener-hover");
            @include transition(0.3s linear background-color);
          }
          > .content {
            .icon {
              // Zvětšení ikonky o 1.2x
              transform: scale(1.2);
              @include transition(0.1s ease transform);
            }
          }
        }
        // Obrázek v pozadi
        > img {
          // Nastavení pozice a velikosti
          position: absolute;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          // Aby se obrázek automaticky nadimenzoval a udržel si ratio
          object-fit: cover;
        }
        // Ztmavovač
        > .darkener {
          // Nastavení pozice a velikosti
          position: absolute;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          // Zobrazení nad obrázkem
          z-index: 2;
          background-color: color("header-content-video-darkener");
          @include transition(0.3s linear background-color);
        }
        > .content {
          // Resetování pozice
          position: relative;
          z-index: 3;
          // Zarovnání
          text-align: center;
          color: color("header-content-video-text");
          // Ikonka
          .icon {
            font-size: 4em;
            @include transition(0.1s ease transform);
          }
          // Text
          h3 {
            // Kapitálky
            text-transform: uppercase;
            font-size: 0.9em;
            letter-spacing: -1px;
            // Odsazení aby se nespojilo s ikonkou
            margin-top: 20px;
          }
        }
      }
    }
  }
}
</style>
