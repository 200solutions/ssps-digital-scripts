<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;

/**
* Presenter pro domovskou obrazovku
*
* @author Marek Kejda <Kejda.Marek@outlook.cz>
*
*/
final class HomepagePresenter extends Nette\Application\UI\Presenter
{
    /** @var \Nette\Database\Context */
    public $database;

    /**
    * Konstruktor presenteru. Vyžádá si všechny potřebné závislosti
	* @param Context $database databázové spojení
    */
   	public function __construct(\Nette\Database\Context $database) {
        // Inicializuje vnitřní stav třídy
        $this->database = $database;
   	}

    /**
	* Defaultní render metoda
	*/
	public function renderDefault(): void {
        // Získá novinky
        $this->template->news = $this->database->table('NEWS')
                                               ->fetchAll();
    }
}
