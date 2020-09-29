window.model.CodeBlock = class CodeBlock {

    // Getter pro zobrazení v tool-boxu
    static get toolbox() {
        return {
            // Název
            title: 'Blokový kód',
            // Ikonka pro blokový kód
            icon: `<svg width="14" height="14" viewBox="0 -1 14 14" xmlns="http://www.w3.org/2000/svg" > <path d="M3.177 6.852c.205.253.347.572.427.954.078.372.117.844.117 1.417 0 .418.01.725.03.92.02.18.057.314.107.396.046.075.093.117.14.134.075.027.218.056.42.083a.855.855 0 0 1 .56.297c.145.167.215.38.215.636 0 .612-.432.934-1.216.934-.457 0-.87-.087-1.233-.262a1.995 1.995 0 0 1-.853-.751 2.09 2.09 0 0 1-.305-1.097c-.014-.648-.029-1.168-.043-1.56-.013-.383-.034-.631-.06-.733-.064-.263-.158-.455-.276-.578a2.163 2.163 0 0 0-.505-.376c-.238-.134-.41-.256-.519-.371C.058 6.76 0 6.567 0 6.315c0-.37.166-.657.493-.846.329-.186.56-.342.693-.466a.942.942 0 0 0 .26-.447c.056-.2.088-.42.097-.658.01-.25.024-.85.043-1.802.015-.629.239-1.14.672-1.522C2.691.19 3.268 0 3.977 0c.783 0 1.216.317 1.216.921 0 .264-.069.48-.211.643a.858.858 0 0 1-.563.29c-.249.03-.417.076-.498.126-.062.04-.112.134-.139.291-.031.187-.052.562-.061 1.119a8.828 8.828 0 0 1-.112 1.378 2.24 2.24 0 0 1-.404.963c-.159.212-.373.406-.64.583.25.163.454.342.612.538zm7.34 0c.157-.196.362-.375.612-.538a2.544 2.544 0 0 1-.641-.583 2.24 2.24 0 0 1-.404-.963 8.828 8.828 0 0 1-.112-1.378c-.009-.557-.03-.932-.061-1.119-.027-.157-.077-.251-.14-.29-.08-.051-.248-.096-.496-.127a.858.858 0 0 1-.564-.29C8.57 1.401 8.5 1.185 8.5.921 8.5.317 8.933 0 9.716 0c.71 0 1.286.19 1.72.574.432.382.656.893.671 1.522.02.952.033 1.553.043 1.802.009.238.041.458.097.658a.942.942 0 0 0 .26.447c.133.124.364.28.693.466a.926.926 0 0 1 .493.846c0 .252-.058.446-.183.58-.109.115-.281.237-.52.371-.21.118-.377.244-.504.376-.118.123-.212.315-.277.578-.025.102-.045.35-.06.733-.013.392-.027.912-.042 1.56a2.09 2.09 0 0 1-.305 1.097c-.2.323-.486.574-.853.75a2.811 2.811 0 0 1-1.233.263c-.784 0-1.216-.322-1.216-.934 0-.256.07-.47.214-.636a.855.855 0 0 1 .562-.297c.201-.027.344-.056.418-.083.048-.017.096-.06.14-.134a.996.996 0 0 0 .107-.396c.02-.195.031-.502.031-.92 0-.573.039-1.045.117-1.417.08-.382.222-.701.427-.954z" /> </svg>`
        };
    }

    /**
    * Umožní zmáčknout enter
    * @returns {boolean}
    * @public
    */
    static get enableLineBreaks() {
        return true;
    }

    // Render funkce
    render() {
        // Obalovač a element pro editor
        this.wrapper = document.createElement('div');
        this.textarea = document.createElement('textarea');
        this.input = document.createElement('input');

        // Přidá třídu
        this.wrapper.classList.add('ce-code-block');
        this.textarea.classList.add('cdx-input');

        // Další nastavení
        this.input.placeholder = 'Moje ukázka kódu';
        this.input.classList.add('cdx-input');
        this.textarea.placeholder = 'var i = 0';
        this.textarea.classList.add('cdx-input');

        // Vytvoří select
        this.select = document.createElement('select');
        this.select.classList.add('cdx-select');

        // Vytvoří a vloží možnosti do selectu
        this.languages.forEach((language) => {
            // Vytvoří možnost
            let option = document.createElement('option');
            option.text = language.label;
            option.value = language.value;

            // Vloží ji do selectu
            this.select.appendChild(option);
        });

        // Naplní blok předešlými daty
        this.textarea.value = this.data && this.data.code ? this.data.code : '';
        this.input.value = this.data && this.data.name ? this.data.name : '';
        this.select.value = this.data && this.data.language ? this.data.language : 'language-csharp';

        // Vloží vše do obalovače (wrapperu)
        this.wrapper.appendChild(this.textarea);
        this.wrapper.appendChild(this.input);
        this.wrapper.appendChild(this.select);

        // Vrátí připravený element
        return this.wrapper;
    }

    // Save funkce
    save(content) {
        // Vrátí data
        return {
            // Obsah
            code: this.textarea.value,
            // Jazyk
            language: this.select.options[this.select.selectedIndex].value,
            // Název
            name: this.input.value
        }
    }


    // Konstruktor
    constructor({ data }){
        // Inicializace vnitřního stavu instance
        this.data = data;

        this.languages = [
            { label: 'Io', value: 'language-io' },
            { label: 'Ebnf', value: 'language-ebnf' },
            { label: 'Javadoclike', value: 'language-javadoclike' },
            { label: 'Iecst', value: 'language-iecst' },
            { label: 'Gdscript', value: 'language-gdscript' },
            { label: 'Bash', value: 'language-bash' },
            { label: 'Rest', value: 'language-rest' },
            { label: 'Batch', value: 'language-batch' },
            { label: 'Haml', value: 'language-haml' },
            { label: 'Vhdl', value: 'language-vhdl' },
            { label: 'Markup-templating', value: 'language-markup-templating' },
            { label: 'Robotframework', value: 'language-robotframework' },
            { label: 'Prolog', value: 'language-prolog' },
            { label: 'Jsx', value: 'language-jsx' },
            { label: 'Powershell', value: 'language-powershell' },
            { label: 'Moonscript', value: 'language-moonscript' },
            { label: 'Liquid', value: 'language-liquid' },
            { label: 'Perl', value: 'language-perl' },
            { label: 'Typescript', value: 'language-typescript' },
            { label: 'Cmake', value: 'language-cmake' },
            { label: 'Aspnet', value: 'language-aspnet' },
            { label: 'Pure', value: 'language-pure' },
            { label: 'Smalltalk', value: 'language-smalltalk' },
            { label: 'Cpp', value: 'language-cpp' },
            { label: 'Docker', value: 'language-docker' },
            { label: 'Lilypond', value: 'language-lilypond' },
            { label: 'Verilog', value: 'language-verilog' },
            { label: 'Markup', value: 'language-markup' },
            { label: 'Shell-session', value: 'language-shell-session' },
            { label: 'Smarty', value: 'language-smarty' },
            { label: 'Visual-basic', value: 'language-visual-basic' },
            { label: 'Js-templates', value: 'language-js-templates' },
            { label: 'N4js', value: 'language-n4js' },
            { label: 'Erb', value: 'language-erb' },
            { label: 'Pug', value: 'language-pug' },
            { label: 'Renpy', value: 'language-renpy' },
            { label: 'Csp', value: 'language-csp' },
            { label: 'Dart', value: 'language-dart' },
            { label: 'Excel-formula', value: 'language-excel-formula' },
            { label: 'D', value: 'language-d' },
            { label: 'Flow', value: 'language-flow' },
            { label: 'Sass', value: 'language-sass' },
            { label: 'Matlab', value: 'language-matlab' },
            { label: 'Ruby', value: 'language-ruby' },
            { label: 'Fsharp', value: 'language-fsharp' },
            { label: 'Abnf', value: 'language-abnf' },
            { label: 'Livescript', value: 'language-livescript' },
            { label: 'Graphql', value: 'language-graphql' },
            { label: 'Git', value: 'language-git' },
            { label: 'Crystal', value: 'language-crystal' },
            { label: 'Firestore-security-rules', value: 'language-firestore-security-rules' },
            { label: 'Css-extras', value: 'language-css-extras' },
            { label: 'Sas', value: 'language-sas' },
            { label: 'Solution-file', value: 'language-solution-file' },
            { label: 'Gherkin', value: 'language-gherkin' },
            { label: 'Sparql', value: 'language-sparql' },
            { label: 'Java', value: 'language-java' },
            { label: 'Objectivec', value: 'language-objectivec' },
            { label: 'Glsl', value: 'language-glsl' },
            { label: 'Javadoc', value: 'language-javadoc' },
            { label: 'Applescript', value: 'language-applescript' },
            { label: 'Ada', value: 'language-ada' },
            { label: 'Icon', value: 'language-icon' },
            { label: 'Arff', value: 'language-arff' },
            { label: 'Lua', value: 'language-lua' },
            { label: 'Xeora', value: 'language-xeora' },
            { label: 'Django', value: 'language-django' },
            { label: 'Rip', value: 'language-rip' },
            { label: 'Lisp', value: 'language-lisp' },
            { label: 'Xquery', value: 'language-xquery' },
            { label: 'Properties', value: 'language-properties' },
            { label: 'J', value: 'language-j' },
            { label: 'Solidity', value: 'language-solidity' },
            { label: 'Tsx', value: 'language-tsx' },
            { label: 'R', value: 'language-r' },
            { label: 'Ejs', value: 'language-ejs' },
            { label: 'Tt2', value: 'language-tt2' },
            { label: 'Gml', value: 'language-gml' },
            { label: 'Antlr4', value: 'language-antlr4' },
            { label: 'Nix', value: 'language-nix' },
            { label: 'Handlebars', value: 'language-handlebars' },
            { label: 'Neon', value: 'language-neon' },
            { label: 'T4-vb', value: 'language-t4-vb' },
            { label: 'Erlang', value: 'language-erlang' },
            { label: 'Hsts', value: 'language-hsts' },
            { label: 'Mizar', value: 'language-mizar' },
            { label: 'Ocaml', value: 'language-ocaml' },
            { label: 'Hcl', value: 'language-hcl' },
            { label: 'Powerquery', value: 'language-powerquery' },
            { label: 'Abap', value: 'language-abap' },
            { label: 'Fortran', value: 'language-fortran' },
            { label: 'Elm', value: 'language-elm' },
            { label: 'Haskell', value: 'language-haskell' },
            { label: 'Splunk-spl', value: 'language-splunk-spl' },
            { label: 'Bnf', value: 'language-bnf' },
            { label: 'Latex', value: 'language-latex' },
            { label: 'Julia', value: 'language-julia' },
            { label: 'Ichigojam', value: 'language-ichigojam' },
            { label: 'Javascript', value: 'language-javascript' },
            { label: 'Q', value: 'language-q' },
            { label: 'N1ql', value: 'language-n1ql' },
            { label: 'Textile', value: 'language-textile' },
            { label: 'Latte', value: 'language-latte' },
            { label: 'Nsis', value: 'language-nsis' },
            { label: 'Phpdoc', value: 'language-phpdoc' },
            { label: 'Qml', value: 'language-qml' },
            { label: 'Protobuf', value: 'language-protobuf' },
            { label: 'Xojo', value: 'language-xojo' },
            { label: 'Pcaxis', value: 'language-pcaxis' },
            { label: 'Php-extras', value: 'language-php-extras' },
            { label: 'Less', value: 'language-less' },
            { label: 'Json5', value: 'language-json5' },
            { label: 'Bison', value: 'language-bison' },
            { label: 'Processing', value: 'language-processing' },
            { label: 'Asciidoc', value: 'language-asciidoc' },
            { label: 'Oz', value: 'language-oz' },
            { label: 'Php', value: 'language-php' },
            { label: 'Ini', value: 'language-ini' },
            { label: 'Sql', value: 'language-sql' },
            { label: 'Vbnet', value: 'language-vbnet' },
            { label: 'Ftl', value: 'language-ftl' },
            { label: 'Dns-zone-file', value: 'language-dns-zone-file' },
            { label: 'Toml', value: 'language-toml' },
            { label: 'Hpkp', value: 'language-hpkp' },
            { label: 'Nand2tetris-hdl', value: 'language-nand2tetris-hdl' },
            { label: 'Scala', value: 'language-scala' },
            { label: 'Qore', value: 'language-qore' },
            { label: 'Rust', value: 'language-rust' },
            { label: 'Groovy', value: 'language-groovy' },
            { label: 'Etlua', value: 'language-etlua' },
            { label: 'Arduino', value: 'language-arduino' },
            { label: 'T4-templating', value: 'language-t4-templating' },
            { label: 'Gcode', value: 'language-gcode' },
            { label: 'Scheme', value: 'language-scheme' },
            { label: 'Autoit', value: 'language-autoit' },
            { label: 'Autohotkey', value: 'language-autohotkey' },
            { label: 'Haxe', value: 'language-haxe' },
            { label: 'Clojure', value: 'language-clojure' },
            { label: 'Regex', value: 'language-regex' },
            { label: 'Brainfuck', value: 'language-brainfuck' },
            { label: 'Zig', value: 'language-zig' },
            { label: 'Plsql', value: 'language-plsql' },
            { label: 'Wiki', value: 'language-wiki' },
            { label: 'Concurnas', value: 'language-concurnas' },
            { label: 'Js-extras', value: 'language-js-extras' },
            { label: 'Velocity', value: 'language-velocity' },
            { label: 'Json', value: 'language-json' },
            { label: 'C#', value: 'language-csharp' },
            { label: 'C', value: 'language-c' },
            { label: 'Tcl', value: 'language-tcl' },
            { label: 'Jolie', value: 'language-jolie' },
            { label: 'Tap', value: 'language-tap' },
            { label: 'Eiffel', value: 'language-eiffel' },
            { label: 'Stylus', value: 'language-stylus' },
            { label: 'Clike', value: 'language-clike' },
            { label: 'Basic', value: 'language-basic' },
            { label: 'Pascaligo', value: 'language-pascaligo' },
            { label: 'Wasm', value: 'language-wasm' },
            { label: 'Elixir', value: 'language-elixir' },
            { label: 'Nginx', value: 'language-nginx' },
            { label: 'Twig', value: 'language-twig' },
            { label: 'T4-cs', value: 'language-t4-cs' },
            { label: 'Nim', value: 'language-nim' },
            { label: 'Inform7', value: 'language-inform7' },
            { label: 'Roboconf', value: 'language-roboconf' },
            { label: 'Cil', value: 'language-cil' },
            { label: 'Css', value: 'language-css' },
            { label: 'Brightscript', value: 'language-brightscript' },
            { label: 'Jsonp', value: 'language-jsonp' },
            { label: 'Yaml', value: 'language-yaml' },
            { label: 'Pascal', value: 'language-pascal' },
            { label: 'Sqf', value: 'language-sqf' },
            { label: 'Turtle', value: 'language-turtle' },
            { label: 'Vala', value: 'language-vala' },
            { label: 'Nasm', value: 'language-nasm' },
            { label: 'Llvm', value: 'language-llvm' },
            { label: 'Jsdoc', value: 'language-jsdoc' },
            { label: 'Coffeescript', value: 'language-coffeescript' },
            { label: 'Parser', value: 'language-parser' },
            { label: 'Reason', value: 'language-reason' },
            { label: 'Jq', value: 'language-jq' },
            { label: 'Keyman', value: 'language-keyman' },
            { label: 'Kotlin', value: 'language-kotlin' },
            { label: 'Javastacktrace', value: 'language-javastacktrace' },
            { label: 'Apacheconf', value: 'language-apacheconf' },
            { label: 'Soy', value: 'language-soy' },
            { label: 'Diff', value: 'language-diff' },
            { label: 'Factor', value: 'language-factor' },
            { label: 'Bbcode', value: 'language-bbcode' },
            { label: 'Makefile', value: 'language-makefile' },
            { label: 'Monkey', value: 'language-monkey' },
            { label: 'Python', value: 'language-python' },
            { label: 'Vim', value: 'language-vim' },
            { label: 'Go', value: 'language-go' },
            { label: 'Apl', value: 'language-apl' },
            { label: 'Asm6502', value: 'language-asm6502' },
            { label: 'Http', value: 'language-http' },
            { label: 'Actionscript', value: 'language-actionscript' },
            { label: 'Swift', value: 'language-swift' },
            { label: 'Opencl', value: 'language-opencl' },
            { label: 'Lolcode', value: 'language-lolcode' },
            { label: 'Puppet', value: 'language-puppet' },
            { label: 'Markdown', value: 'language-markdown' },
            { label: 'Gedcom', value: 'language-gedcom' },
            { label: 'Dax', value: 'language-dax' },
            { label: 'Scss', value: 'language-scss' },
            { label: 'Aql', value: 'language-aql' },
            { label: 'Mel', value: 'language-mel' },
            { label: 'Parigp', value: 'language-parigp' },
            { label: 'Bro', value: 'language-bro' }
        ];
    }
}
