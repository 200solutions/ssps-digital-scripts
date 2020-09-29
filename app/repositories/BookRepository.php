<?php

// Striktní režim
declare(strict_types=1);

// Namespace
namespace App\Repositories;

// Usingy
use Nette\Utils\Arrays;
use Nette\Utils\DateTime;

/**
* BookRepository slouží k poskytování informací o knihách
*
* @author Marek Kejda <Kejda.Marek@outlook.cz>
*
*/
class BookRepository
{
    /** @var \Nette\Database\Context */
    public $database;

    /** @var App\Repositories\DocumentRepository */
    private $documentRepository;

    /** @var App\Repositories\UserRepository */
    public $userRepository;

    /**
    * Konstruktor repositáře. Vyžádá si všechny potřebné závislosti
    * @param Context $database databázové spojení
    */
    public function __construct(\Nette\Database\Context $database, \App\Repositories\DocumentRepository $documentRepository, \App\Repositories\UserRepository $userRepository) {
        // Inicializuje vnitřní stav třídy
        $this->database = $database;
        $this->documentRepository = $documentRepository;
        $this->userRepository = $userRepository;
    }

    /**
    * Získá knihu s daným ID
    * @param $id identifikátor knihy
    * @return class kniha s daným ID
    */
    public function get($id) {
        // Získá knihu
        $book = $this->database->table('BOOKS')
                               ->where('id', $id)
                               ->fetch();

        // Odkaz na momentální scope
        $_this = $this;

        // Vrátí knihudate_format(
		return new class($book, $_this){
			/**
			* Konstruktor anonymní třídy
			*/
			public function __construct($book, $_this)
			{
				// Inicializace vnitřního stavu
				$this->id = $book['id'];
                $this->subjectId = $book['subject_id'];
                $this->subject = $_this->getSubject($this->subjectId);
                $this->title = $book['title'];
                $this->description = $book['description'];
                $this->color = $book['color'];
                $this->published = $book['published'] == 1 ? true : false;
                $this->createdAt = date_format(DateTime::from($book['created_at']), 'd. m. Y');

                // Získá autory
                $this->authors = Arrays::map($_this->getAuthors($this->id), function($uid) use ($_this) {
                    // Získá autora
                    $user =  $_this->userRepository->get($uid);

                    // Vrátí jméno autora
                    return "$user->firstName $user->lastName";
                });
			}
		};
    }

    /**
    * Získá předmět s daným ID
    * @param int $id identifikátor předmětu
    */
    public function getSubject($id) {
        // Získá předmět
        $subject = $this->database->table('SUBJECTS')
                                  ->get($id);

        // Vrátí předmět
        return [
            'id' => $subject['id'],
            'title' => $subject['title'],
        ];
    }

    /**
    * Získá knihy na základě sekce
    * @param $id identifikátor sekce
    * @return array knihy patřící do dané sekce
    */
    public function getBooksBySubject($id) {
        // Vrátí knihy dané kategorie
        return Arrays::map(array_values($this->database->table('SUBJECTS')
                                                       ->get($id)
                                                       ->related('BOOKS')
                                                       ->fetchAssoc('id')), function($book) {
            // Vrátí knihy
            return array(
                // ID
                'id' => $book['id'],
                // Název
                'title' => $book['title'],
                // Získá autory
                'authors' => Arrays::map($this->getAuthors($book['id']), function($uid) {
                    // Získá autora na základě identifikátoru
                    $user = $this->userRepository->get($uid);

                    // Vrátíme autora
                    return "$user->firstName $user->lastName";
                }),
                // Popisek
                'description' => $book['description'],
                // Pravda, pokud je kniha publikována
                'published' => $book['published'] == 1 ? true : false
            );
        });
    }

    /**
    * Získá knihy na základě sekce
    * @param $id identifikátor sekce
    * @return array knihy patřící do dané sekce
    */
    public function getSubjectsAndBooks() {
        // Odkaz na momentální scope
        $_this = $this;

        // Vrátí předměty a jejich knihy ve formátu
        // požadovaným klientem
        return Arrays::map(array_values($this->database->table('SUBJECTS')
                                                       ->fetchAll()), function ($subject) use ($_this) {
            /**
            * Anonymní třída.
            */
            return new class($subject, $_this) {
                /**
				* Konstruktor anonymní třídy
				*/
				public function __construct($subject, $_this)
				{
                    // Incializace vnitřního stavu instance
                    $this->id = $subject->id;
                    $this->title = $subject->title;
                    $this->books = $_this->getBooksBySubject($subject->id);
                }
            };
        });
    }

    /**
    * Vrátí všechny knihy, které může editovat (provádět CRUD akce)
    * uživatel s daným user identifikátorem
    * @param int $uid identifikátor uživatele
    * @return array pole knih
    */
    public function getBooksByUserRights($uid) {
        // Odkaz na momentální scope
        $_this = $this;

        // Je uživatel admin?
        $isAdmin = in_array('admin', $this->userRepository->getRoles(intval($uid)));

        // Získá knihy
        $books = $isAdmin ? $this->database->table('BOOKS')
                                           ->fetchAssoc('id')
                          : $this->database->table('BOOKS')
                                           ->where('owner_id', $uid)
                                           ->fetchAssoc('id');

        // Vrátí knihy, které uživatel vlastní
        return Arrays::map(array_values($books), function($book) use ($_this) {
            // Získá knihu
            return $_this->get($book['id']);
        });
    }

    /**
    * Vrátí všechny předměty
    * @return array předměty
    */
    public function getSubjects() {
        // Získá a namapuje předměty
        return Arrays::map(array_values($this->database->table('SUBJECTS')
                                                       ->fetchAll()), function ($subject) {
            /**
            * Anonymní třída.
            */
            return new class($subject) {
                /**
				* Konstruktor anonymní třídy
				*/
				public function __construct($subject)
				{
                    // Incializace vnitřního stavu instance
                    $this->id = $subject->id;
                    $this->title = $subject->title;
                }
            };
        });
    }

    /**
    * Vrátí autory dané knihy
    * @param int $id identifikátor knihy
    * @return array identifikátory autorů
    */
    public function getAuthors(int $id) {
        // Má kniha nějaké autory?
        if ($this->documentRepository->getAll($id)) {
            // Získá všechny dokumenty
            return array_values(array_unique(array_merge(...Arrays::map($this->documentRepository->getAll($id), function($document) {
                // Vrátí autora / autory
                return $this->documentRepository->getAuthors(intval($document->id));
            }))));
        } else {
            // Nemá - vrátíme prázdné pole
            return [];
        }
    }

    /**
    * Vrátí 3 nejposlednější knihy
    */
    public function getLatest() {
        // Získá indetifikátory knih
        $ids = $this->database->table('BOOKS')
                              ->order('created_at DESC')
                              ->select('id')
                              ->where('published', 1)
                              ->limit(3)
                              ->fetchAll();

        // Projde získáné indentifikátory a vrátí knihy
        return array_values(Arrays::map($ids, function($id) {
            // Získá a výtí knihu
            return $this->get($id);
        }));
    }

    /**
    * Vrátí 3 nejstarší knihy
    */
    public function getOldest() {
        // Získá indetifikátory knih
        $ids = $this->database->table('BOOKS')
                              ->order('created_at ASC')
                              ->select('id')
                              ->where('published', 1)
                              ->limit(3)
                              ->fetchAll();

        // Projde získáné indentifikátory a vrátí knihy
        return array_values(Arrays::map($ids, function($id) {
            // Získá a výtí knihu
            return $this->get($id);
        }));
    }
}
