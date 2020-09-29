<?php

// Namespace
namespace App\Presenters;

// Usingy
use Nette;
use Nette\Application\LinkGenerator;
use Nette\Application\Responses\JsonResponse;
use Nette\Utils\Finder;
use Nette\Utils\Image;

/**
* ApiPresenter je třída, jenž zajišťuje API
*
* @author Marek Kejda <Kejda.Marek@outlook.cz>
*
*/
final class ApiPresenter extends Nette\Application\UI\Presenter
{
    /** @var Nette\Application\LinkGenerator */
    private $linkGenerator;

    /** @var \Nette\Database\Context */
    public $database;

    /**
    * Konstruktor presenteru
    */
    function __construct(LinkGenerator $generator, \Nette\Database\Context $database) {
        // Inicializace vnitřního stavu objektu
        $this->linkGenerator = $generator;
        $this->database = $database;
    }

    /**
    * Endpoint převezme URL v Nette formátu a vygeneruje
    * absolutní cestu k předané URL
    * @param string $dest adresa v Nette formátu
    * @param string $params query parametry
    * @return string absolutní URL adresa
    */
    public function actionGetLink(string $dest, string $params = null) {
        // Vrátí json response
        $this->sendResponse(new JsonResponse(
            // Vygeneruje link
            $this->linkGenerator->link($dest, $params != null ? json_decode($params, true) : [])
        ));
    }

    /**
    * Endpoint najde všechny .js soubory ve složce /www/model/
    * a předá odkazy na jejich načtení frontendu
    * @return array pole modelových tříd a jejich cest
    */
    public function actionGetModel() {
        // Získá cestu k modelovému adresáři
        $dir =  dirname(__FILE__) . '/../../www/client/model';

        // Připraví pole pro výsledky
        $files = array();

        // Prohledáme modelovou složku
        foreach (Finder::findFiles('*.js')->from($dir) as $file) {
            // Cesta k souboru
            $path = $file->getRealPath();

        	// Přidáme záznam o souboru do pole výsledků
            array_push($files, [
                // Název
                'name' => $file->getFilename(),
                // Cesta k souboru zbavena serverové části
                'path' => substr($path, strpos($path, '/client'))
            ]);
        }

        // Vrátí výsledek
        $this->sendResponse(new JsonResponse($files));
    }

    /**
    * Endpoint pro nahrání obrázku. Využíváno editorem (Editor.vue)
    */
    public function actionUploadByFile() {
        // Získá HTTP request
        $request = $this->getHttpRequest();

        // Získá obrázek a příponu
        $file = $request->getFile('image');
        $type = array_slice(explode('.', $file->getSanitizedName()), -1)[0];

        // Vytvoří název souboru
        $name = uniqid('IMG_') . '.' . $type;

        // Přesune soubor do adresáře pro
        // nahrané (uploadnuté) soubory
        $file->move(dirname(__FILE__) . '/../../www/uploaded/' . $name);

        // Odešle odpověď
        $this->sendResponse(new JsonResponse([
            // Data určená pro klienta
            'success' => 1,
            'file' => [
                // Cesta k obrázku
                'url' => '/uploaded/' . $name
            ]
        ]));
    }

    /**
    * Endpoint pro nahrání "libovolných" souborů. Využíváno editorem (Editor.vue)
    */
    public function actionUploadFile() {
        // Získá HTTP request
        $request = $this->getHttpRequest();

        // Získá obrázek a příponu
        $file = $request->getFile('file');
        $type = array_slice(explode('.', $file->getSanitizedName()), -1)[0];

        // Vytvoří název souboru
        $name = uniqid('FILE_') . '.' . $type;

        // Přesune soubor do adresáře pro
        // nahrané (uploadnuté) soubory
        $file->move(dirname(__FILE__) . '/../../www/uploaded/' . $name);

        // Odešle odpověď
        $this->sendResponse(new JsonResponse([
            // Data určená pro klienta
            'success' => 1,
            'file' => [
                // Cesta k obrázku
                'url' => '/uploaded/' . $name,
                // Jméno
                'name' => $name,
                // Velikost
                'size' => $file->getSize(),
                // Přípona
                'extension' => $type
            ]
        ]));
    }

    /**
    * Endpoint pro nahrání obrázku z předané URL. Využíváno editorem (Editor.vue)
    */
    public function actionUploadByUrl() {
        // Získá předanou URL
        $url = json_decode($this->getHttpRequest()->getRawBody())->url;

        // Získá obrázek z URL
        $image = Image::fromFile($url);

        // Připraví název a cestu pro uložení
        $name =  uniqid('IMG_') . '.jpg';
        $path = dirname(__FILE__) . '/../../www/uploaded/' . $name;

        // Uloží obrázek jako JPEG
        $image->save($path, 100, Image::JPEG);

        // Odešle odpověď
        $this->sendResponse(new JsonResponse([
            // Data určená pro klienta
            'success' => 1,
            'file' => [
                // Cesta k obrázku
                'url' => '/uploaded/' . $name
            ]
        ]));
    }

    /**
    * Endpoint pro uložení zpětné vazby
    * @param string $subject předmět zpětné vazby
    * @param string $content obsah zprávy
    */
    public function actionPostFeedback(string $subject, string $content) {
        // Uloží feedback
        $this->database->table('FEEDBACK')
                       ->insert([
            'subject' => $subject,
            'content' => $content
        ]);

        // Odešle odpověď
        $this->sendResponse(new JsonResponse([
            // Data určená pro klienta
            'status' => 'ok'
        ]));
    }
}
