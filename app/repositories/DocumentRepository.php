<?php

// Striktní režim
declare(strict_types=1);

// Namespace
namespace App\Repositories;

// Usingy
use Nette\Utils\Arrays;

/**
* DocumentRepository slouží k poskytování informací o dokumentech
*
* @author Marek Kejda <Kejda.Marek@outlook.cz>
*
*/
class DocumentRepository
{
    /** @var \Nette\Database\Context */
    public $database;

    /**
    * Konstruktor repositáře. Vyžádá si všechny potřebné závislosti
    * @param Context $database databázové spojení
    */
    public function __construct(\Nette\Database\Context $database) {
        // Inicializuje vnitřní stav třídy
        $this->database = $database;
    }

    /**
	* Vrátí všechny autory vybraného dokumentu
	* @param int $id identifikátor daného dokumentu
    * @return array pole ID autorů dokumentu
	*/
	public function getAuthors(int $id): array {
        // Vrátí role
        return Arrays::map(array_values(
			// Získá uživatelovi role z DB
			$this->database->table('DOCUMENTS')
                           ->get($id)
                           ->related('AUTHORS')
					       ->fetchAssoc('id')), function ($author) {
				// Vrátí ID uživatele (autora)
				return $author['user_id'];
		});
    }

    /**
    * Vrátí všechny dokumenty dané knihy
    * @param $id identifikátor knihy
    * @return array dokumenty dané knihy
    */
    public function getAll($id) {
        // Získá danou knihu
		$book = $this->database->table('BOOKS')
							   ->get($id);

		// Získá dokumenty dané knihy
		$documents = $book->related('DOCUMENTS.book_id')
						  ->order('order')
						  ->fetchAll();

		// Vrátí výsledek
		return array_values(Arrays::map($documents, function ($item) {
			// Anonymní třída dokumentu
			return new class($item) {
				/**
				* Konstruktor anonymní třídy
				*/
				public function __construct($item)
				{
					// Inicializace vnitřního stavu
					$this->id = strval($item->id);
					$this->name = $item->name;
					$this->order = $item->order;
					$this->data = $item->data;
					$this->accessGroup = $item->access_group;
					$this->published = $item->published == 0 ? false : true;
					$this->createdAt = $item->created_at;
					$this->updatedAt = $item->updated_at;
				}
			};
		}));
    }

    /**
	* Vrátí všechny uživatele (ID), jenž mají exkluzivní
    * přístup k dokumentu s předaným ID
	* @param int $id identifikátor daného dokumentu
    * @return array pole ID uživatelů s exkluzivním přístupem
	*/
	public function getAccesses(int $id): array {
        // Vrátí role
        return Arrays::map(array_values(
			// Získá uživatelovi role z DB
			$this->database->table('DOCUMENTS')
                           ->get($id)
                           ->related('ACCESSES')
					       ->fetchAssoc('id')), function ($access) {
				// Vrátí ID uživatele
				return $access['user_id'];
		});
    }

    /**
    * Vrátí přístupovou skupinu dokumentu:
    * 0: pouze autoři dokumentu
    * 1: všichni pedagogové
    * 2: všichni redaktoři
    * @param int $id identifikátor dokumentu
    * @return int přístupová skupina: 0 | 1 | 2
    */
    public function getAccessGroup(int $id) {
        // Získá dokument a jeho skupinu
        return $this->database->table('DOCUMENTS')
                              ->get($id)
                              ->access_group;
    }

    /**
    * Vrátí nadcházející dokument
    * @param int $id identifikátor současného dokumentu
    * @return array název a identifikátor dalšího dokumentu
    */
    public function getNext($id) {
        // Získá pořadí dokumentu
        $order = $this->database->table('DOCUMENTS')
                                ->get($id)
                                ->order;

        // Získá rodičovskou knihu dokumentu
        $bookId = $this->database->table('DOCUMENTS')
                                 ->get($id)
                                 ->book_id;

        // Získá následující dokument
        $row = $this->database->table('DOCUMENTS')
                              ->where('book_id', $bookId)
                              ->where('order', $order + 1)
                              ->where('published', 1)
                              ->fetch();

        // Vrátí výsledek
        return $row ? [
            // ID a název
            'id' => $row['id'],
            'name' => $row['name']
        ] : null;
    }

    /**
    * Vrátí předchozí dokument
    * @param int $id identifikátor současného dokumentu
    * @return array název a identifikátor předchozího dokumentu
    */
    public function getPrevious($id) {
        // Získá pořadí dokumentu
        $order = $this->database->table('DOCUMENTS')
                                ->get($id)
                                ->order;

        // Získá rodičovskou knihu dokumentu
        $bookId = $this->database->table('DOCUMENTS')
                                 ->get($id)
                                 ->book_id;

        // Získá následující dokument
        $row = $this->database->table('DOCUMENTS')
                              ->where('book_id', $bookId)
                              ->where('order', $order - 1)
                              ->where('published', 1)
                              ->fetch();

        // Vrátí výsledek
        return $row ? [
            // ID a název
            'id' => $row['id'],
            'name' => $row['name']
        ] : null;
    }
}
