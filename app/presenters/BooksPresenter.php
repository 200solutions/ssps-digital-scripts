<?php

// Namespace
namespace App\Presenters;

// Usingy
use Nette;
use Nette\Application\Responses\JsonResponse;
use Nette\Utils\Arrays;
use App\Model\PermissionManager;
use App\Repositories\BookRepository;

/**
* BooksPresenter je třída zajišťující správné zobrazení knih
*
* @author Marek Kejda <Kejda.Marek@outlook.cz>
*
*/
final class BooksPresenter extends Nette\Application\UI\Presenter
{
	/** @var \Nette\Database\Context */
	public $database;

	/** @var App\Repositories\BookRepository */
	private $bookRepository;

	/** @var App\Repositories\DocumentRepository */
	private $documentRepository;

	/** @var App\Repositories\UserRepository */
	private $userRepository;

	/** @var App\Model\PermissionManager */
	private $permissionManager;

	/**
    * Konstruktor presenteru. Vyžádá si všechny potřebné závislosti
	* @param Context $database databázové spojení
    */
   	public function __construct(\Nette\Database\Context $database,
							    \App\Repositories\BookRepository $bookRepository,
								\App\Repositories\DocumentRepository $documentRepository,
								\App\Repositories\UserRepository $userRepository,
								\App\Model\PermissionManager $permissionManager) {
        // Inicializuje vnitřní stav třídy
        $this->database = $database;
		$this->bookRepository = $bookRepository;
		$this->documentRepository = $documentRepository;
		$this->userRepository = $userRepository;
		$this->permissionManager = $permissionManager;
   	}

    /**
	* Defaultní render metoda
	* @param int $id identifikátor knihy
	*/
	public function renderDefault(int $id): void {
		// Zjistí, zda kniha vůbec existuje. Založí i
		// pomocnou proměnnou pro pozdější ověření
		// toho, zda kniha byla publikována. Ve výchozím stavu
		// předpokládáme, že kniha publikována není.
		$exists = $this->database->table('BOOKS')->get($id) != null;

		// Předá ID knihy do šablony. Pokud kniha
		// neexistuje nebo ještě nebyla publikována,
		// předá do šavlony hodnotu -1
		$this->template->id = $exists ? $id : -1;
    }

	/**
	* Metoda pro uložení existujícího dokumentu
	* @param int $id identifikátor dokumentu
	*/
	public function actionSaveDocument(int $id): void {
		// Máme na akci oprávnění?
		if ($this->permissionManager->canEditDocument(
			// Momentálně přihlášený uživatel
			$this->getUser()->getId(),
			// ID dokumentu
			$id
		)) {
			// Získá request
			$req = $this->getHttpRequest();

			// Uloží hodnotu
			$this->database->table('DOCUMENTS')
						   ->where('id', $id)
						   ->update([
				// Aktualizované hodnoty
				'name' => $req->getPost('name'),
				'data' => $req->getPost('data'),
				'access_group' => $req->getPost('accessGroup'),
				'published' => $req->getPost('published') == 'false' ? 0 : 1,
				'updated_at' => date('Y-m-d H:i:s')
			]);

			// Vrátí výsledek
	        $this->sendResponse(new JsonResponse([
				'status' => 'ok'
			]));
		}

		// Nemáme potřebná oprávnění. Pošleme
		// chybovou zprávu:
		$this->sendResponse(new JsonResponse([
			'status' => 'error',
			'message' => 'Na uložení dokumentu nemáte dostatečné oprávnění.'
		]));
	}

	/**
	* Uloží pořadí všech dokumentů v rámci knihy. Očekává
	* pole ve formátu [id => pořadí]
	* @param int $id identifikátor knihy
	* @param array $data pár identifikátor dokumentu - pořadí
	*/
	public function actionSaveOrder(int $id, array $data) {
		// Máme oprávnění na operaci s knihou?
		if ($this->permissionManager->canEditBook(
			// Momentálně přihlášený uživatel
			$this->getUser()->getId(),
			// ID knihy
			$id
		)) {
			// Projde pole s daty
			foreach ($data as $item) {
				// Aktualizuje hodnotu v databázi
				$this->database->table('DOCUMENTS')
							   ->where('id', $item['id'])
							   ->update([
					// Pořadí
					'order' => $item['order']
				]);
			}

			// Vrátí výsledek
	        $this->sendResponse(new JsonResponse([
				'status' => 'ok'
			]));
		}

		// Nemáme potřebná oprávnění. Pošleme
		// chybovou zprávu:
		$this->sendResponse(new JsonResponse([
			'status' => 'error',
			'message' => 'Na změnu pořadí kapitol v rámci knihy nemáte dostatečné oprávnění.'
		]));
	}

	/**
	* Uloží autory dokumentu
	* @param int $id identifikátor dokumentu
	* @param array $uids pole autorů (jejich ID)
	*/
	public function actionSaveAuthors(int $id, array $uids) {
		// Máme na akci oprávnění?
		if ($this->permissionManager->canEditDocument(
			// Momentálně přihlášený uživatel
			$this->getUser()->getId(),
			// ID dokumentu
			$id
		)) {
			// Smaže všechny přechozí autory dokumentu
			$this->database->table('AUTHORS')
						   ->where('document_id', $id)
						   ->delete();

			// Projde identifikátory a uloží
			// nové autory:
			foreach($uids as $uid) {
				$this->database->table('AUTHORS')
							   ->where('id', $id)
							   ->insert([
					'user_id' => $uid,
					'document_id' => $id
				]);
			}

			// Vrátí výsledek
	        $this->sendResponse(new JsonResponse([
				'status' => 'ok'
			]));
		}

		// Nemáme potřebná oprávnění. Pošleme
		// chybovou zprávu:
		$this->sendResponse(new JsonResponse([
			'status' => 'error',
			'message' => 'Na změnu autorů dokumentu nemáte dostatečné oprávnění.'
		]));
	}

	/**
	* Uloží unikátní přístupy k dokumentu
	* @param int $id identifikátor dokumentu
	* @param array $uids pole userů (jejich ID), kterým chceme udělit unikátní přístup
	*/
	public function actionSaveAccesses(int $id, array $uids) {
		// Máme na akci oprávnění?
		if ($this->permissionManager->canEditDocument(
			// Momentálně přihlášený uživatel
			$this->getUser()->getId(),
			// ID dokumentu
			$id
		)) {
			// Smaže všechny přechozí autory dokumentu
			$this->database->table('ACCESSES')
						   ->where('document_id', $id)
						   ->delete();

			// Projde identifikátory a uloží
			// nové unikátní přístupy:
			foreach($uids as $uid) {
				$this->database->table('ACCESSES')
							   ->where('id', $id)
							   ->insert([
					'user_id' => $uid,
					'document_id' => $id
				]);
			}

			// Vrátí výsledek
	        $this->sendResponse(new JsonResponse([
				'status' => 'ok'
			]));
		}

		// Nemáme potřebná oprávnění. Pošleme
		// chybovou zprávu:
		$this->sendResponse(new JsonResponse([
			'status' => 'error',
			'message' => 'Na změnu přístupů k dokumentu nemáte dostatečné oprávnění.'
		]));
	}

	/**
	* Smaže všechny dokumenty s předanými ID
	* @param array $ids identifikátory dokumentů ke smazání
	*/
	public function actionDeleteDocuments(array $ids) {
		// Dokumenty, které se nepodařilo smazat
		$err = [];

		// Projde všechny identifikátory
		foreach ($ids as $id) {
			// Můžeme provádět C(R)UD akce s daným dokumentem?
			if ($this->permissionManager->canEditDocument($this->getUser()->id, $id))
				// Smaže dokument
				$this->database->table('DOCUMENTS')
							   ->where('id', $id)
							   ->delete();
			else
				// Vloží ID dokumentu do pole
				// neúspěšně smazaných položek
				array_push($err, $id);
		}

		// Vrátí výsledek
		$this->sendResponse(new JsonResponse([
			'status' => 'ok',
			'result' => $err
		]));
	}

	/**
	* Metoda pro načtení dokumentu. Slouží pouze k
	* fungování editoru
	* @param int $id identifikátor daného dokumentu
	*/
	public function actionLoadDocument(int $id): void {
		// Získá záznam z databáze
		$row = $this->database->table('DOCUMENTS')
			                  ->get($id);

		// Existuje dokument s daným ID?
		if ($row) {
			// Pošle odpověď
			$this->sendResponse(new JsonResponse([
				// ID
				'id' => $row->id,
				// Název
				'name' => $row->name,
				// Pořadí
				'order' => $row->order,
				// Data (JSON ve stringu)
				'data' => $row->data,
				// Přístupová skupina
				'accessGroup' => $row->access_group,
				// Dostupnost
				'published' => $row->published == 1 ? true : false,
				// Vytvořeno
				'createdAt' => $row->created_at,
				// Naposled aktualizováno
				'updatedAt' => $row->updated_at
			]));
		} else {
			// Dokument neexistuje
			$this->sendResponse(new JsonResponse([
				'status' => 'error',
				'message' => 'Dokument s daným identifikátorem nebyl nalezen.'
			]));
		}
	}

	/**
	* Metoda pro vytvoření dokumentu
	* @param int $bookId identifikátor knihy, do kterého má být dokument vložen
	* @param string $name název dokumentu
	* @param int pořadí dokumentu
	*/
	public function actionCreateDocument(int $bookId, string $name, int $order) {
		// Je uživatel redaktor?
        if (in_array('redactor', $this->userRepository->getRoles($this->getUser()->id))) {
			// Vytvoří nový dokument
			$id = $this->database->table('DOCUMENTS')
						         ->insert([
				// ID knihy
				'book_id' => $bookId,
				// Název
				'name' => $name,
				// Pořadí dokumentu v rámci knihy
				'order' => $order,
				// Data (obsah)
				'data' => null,
				// Přístupová skupina
				'access_group' => 0,
				// Je kniha publikovaná?
				'published' => 0,
				// Datum vytvoření a modifikace
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			])->id;

			// Nastaví uživatele jako vlastníka dokumentu
			$this->database->table('AUTHORS')
						   ->insert([
				// Data
				'user_id' => $this->getUser()->id,
				'document_id' => $id
			]);

			// Vrátí výsledek
			$this->sendResponse(new JsonResponse([
				'status' => 'ok'
			]));
		} else {
			// Chyba - nedostatečné oprávnění
			$this->sendResponse(new JsonResponse([
				'status' => 'error',
				'message' => 'Pouze redaktoři mohou vytvářet nové dokumenty.'
			]));
		}
	}

	/**
	* Vrátí všechny dokumenty vybrané knihy. Neveřejná
	* endpointa.
	* @param int $id identifikátor dané knihy
	*/
	public function actionGetAllDocuments(int $id): void {
		// Je uživatel redaktor?
		if (in_array('redactor', $this->userRepository->getRoles($this->getUser()->id))) {
			// Vrátí odpověď
			$this->sendResponse(new JsonResponse(
				// Získá odpověď
				$this->documentRepository->getAll($id)
			));
		} else {
			// Chyba - nedostatečné oprávnění
			$this->sendResponse(new JsonResponse([
				'status' => 'error',
				'message' => 'Pouze redaktoři mohou získat všechny dokumenty dané knihy.'
			]));
		}
	}

	/**
	* Vrátí dokumenty vybrané knihy, které jsou přístupné
	* pro čtení. Veřejná endpointa.
	* @param int $id identifikátor dané knihy
	*/
	public function actionGetPublishedDocuments(int $id): void {
		// Získá danou knihu
		$book = $this->database->table('BOOKS')
							   ->get($id);

		// Získá dokumenty dané knihy
		$documents = $book->related('DOCUMENTS.book_id')
						  ->where('published', 1)
						  ->order('order')
						  ->fetchAll();

		// Vrátí výsledek
		$this->sendResponse(new JsonResponse(array_values(Arrays::map($documents, function ($item) {
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
					$this->data = $item->data;
					$this->order = $item->order;
					$this->accessGroup = $item->access_group;
					$this->published = $item->published == 0 ? false : true;
					$this->createdAt = $item->created_at;
					$this->updatedAt = $item->updated_at;
				}
			};
		}))));
	}

	/**
	* Vrátí všechny autory vybraného dokumentu
	* @param int $id identifikátor daného dokumentu
	* @return array pole identifikátorů autorů
	*/
	public function actionGetDocumentAuthors(int $id) {
		// Vrátí autory
		$this->sendResponse(new JsonResponse($this->documentRepository->getAuthors($id)));
	}

	/**
	* Vrátí všechny uživatele s exkluzivním přístupem k
	* dokumentu s daným ID
	* @param int $id identifikátor dané knihy
	* @return array pole identifikátorů autorů
	*/
	public function actionGetDocumentAccesses(int $id) {
		// Vrátí autory
		$this->sendResponse(new JsonResponse($this->documentRepository->getAccesses($id)));
	}

	/**
    * Endpoint zjistí, zda přihlášený uživatel může editovat
    * dokument s daným ID
    * @param int $id identifikátor dokumentu
    */
    public function actionCanEditDocument(int $id) {
        // Připraví odpověď
        $response = false;

        // Je uživatel přihlášený?
        if ($this->getUser()->isLoggedIn()) {
            // Má uživatel dostatečná práva?
            $response = $this->permissionManager->canEditDocument($this->getUser()->id, $id);
        }

        // Vrátí odpověď
        $this->sendResponse(new JsonResponse([
            'result' => $response
        ]));
    }

    /**
    * Endpoint zjistí, zda přihlášený uživatel může editovat
    * knihu s daným ID
    * @param int $id identifikátor knihy
    */
    public function actionCanEditBook(int $id) {
		// Je uživatel přihlášen?
		if (!$this->getUser()->isLoggedIn()) {
			// Uživatel není přihlášen a nemůže
			// tudíš nic editovat
			$this->sendResponse(new JsonResponse([
	            'result' => false
	        ]));
		}

		// Vrátí odpověď
        $this->sendResponse(new JsonResponse([
			// Získá výsledek
            'result' => $this->permissionManager->canEditBook($this->getUser()->id, $id)
        ]));
    }

	/**
    * Endpoint vrátí všechny sekce a k nim přiřazené knihy
    */
	public function actionGetSubjectsAndBooks() {
		// Vrátí odpověď
		$this->sendResponse(new JsonResponse(
			// Získá data z repositáře
            $this->bookRepository->getSubjectsAndBooks()
        ));
	}

	/**
	* Endpoint vrátí všechny knihy, které patří danému uživateli
	* @param int $id identifikátor uživatele
	*/
	public function actionGetBooksByUserRights($id) {
		// Vrátí odpověď
        $this->sendResponse(new JsonResponse(
			// Získá data z repositáře
            $this->bookRepository->getBooksByUserRights($id)
        ));
	}

	/**
	* Endpoint vrátí všechny předměty
	*/
	public function actionGetSubjects() {
		// Vrátí odpověď
        $this->sendResponse(new JsonResponse(
			// Získá data z repositáře
            $this->bookRepository->getSubjects()
        ));
	}

	/**
	* Vytvoří předmět s daným názvem
	* @param string $title název předmetu
	*/
	public function actionCreateSubject(string $title) {
		// Je uživatel admin?
        if (in_array('admin', $this->userRepository->getRoles($this->getUser()->id))) {
			// Přidá nový předmět
			$this->database->table('SUBJECTS')
						   ->insert([
				// Data
				'title' => $title
			]);

			// Odešle odpověď
			$this->sendResponse(new JsonResponse([
				'status' => 'ok'
			]));
		} else {
			// Nemáme potřebná oprávnění. Pošleme chybovou zprávu:
			$this->sendResponse(new JsonResponse([
				'status' => 'error',
				'message' => 'Na přidání nového předmětu nemáte dostatečné oprávnění.'
			]));
		}
	}

	/**
	* Smaže předmět s daným indentifikátorem
	* @param int $id identifikátor předmětu
	*/
	public function actionDeleteSubject(int $id) {
		// Je uživatel admin?
        if (in_array('admin', $this->userRepository->getRoles($this->getUser()->id))) {
			// Smaže knihy předmětu
			$this->database->table('BOOKS')
						   ->where('subject_id', $id)
						   ->delete();

			// Smaže samotný předmět
			$this->database->table('SUBJECTS')
						   ->where('id', $id)
						   ->delete();

			// Odešle odpověď
			$this->sendResponse(new JsonResponse([
				'status' => 'ok'
			]));
		} else {
			// Nemáme potřebná oprávnění. Pošleme chybovou zprávu:
			$this->sendResponse(new JsonResponse([
				'status' => 'error',
				'message' => 'Na smazání předmětu nemáte dostatečné oprávnění.'
			]));
		}
	}

	/**
	* Aktualizuje předmět
	* @param int $id identifikátor předmětu
	* @param string $title nový název
	*/
	public function actionUpdateSubject($id, $title) {
		// Je uživatel admin?
        if (in_array('admin', $this->userRepository->getRoles($this->getUser()->id))) {
			// Aktualizuje řádek
			$this->database->table('SUBJECTS')
						   ->where('id', $id)
						   ->update([
				// Data
				'title' => $title
			]);

			// Odešle odpověď
			$this->sendResponse(new JsonResponse([
				'status' => 'ok'
			]));
		} else {
			// Nemáme potřebná oprávnění. Pošleme chybovou zprávu:
			$this->sendResponse(new JsonResponse([
				'status' => 'error',
				'message' => 'Na úpravu předmětu nemáte dostatečné oprávnění.'
			]));
		}
	}

	/**
	* Endpoint smaže knihu s daným ID
	* @param int $id identifikátor knihy, kterou budeme mazat
	*/
	public function actionDeleteBook($id) {
		// Máme oprávnění na smazání knihy?
		if ($this->permissionManager->canEditBook(
			// Momentálně přihlášený uživatel
			$this->getUser()->getId(),
			// ID dokumentu
			$id
		)) {
			// Smaže knihu
			$this->database->table('BOOKS')
						   ->where('id', $id)
						   ->delete();

			// Vrátí výsledek
			$this->sendResponse(new JsonResponse([
				'status' => 'ok'
			]));
		}

		// Nemáme oprávnění smazání knihy. Pošleme
		// chybovou odpověď:
		$this->sendResponse(new JsonResponse([
			'status' => 'error',
			'message' => 'Nemáte potřebná oprávnění pro smázání této knihy.'
		]));
	}

	/**
	* Endpoint vrátí barvu čebnice s daným ID
	* @param int $id identifikátor učebnice
	*/
	public function actionGetColor($id) {
		// Vrátí odpověď
		$this->sendResponse(new JsonResponse(
			// Získá učebnici a její barvu
			$this->database->table('BOOKS')
								  ->get($id)
								  ->color
		));
	}

	/**
    * Endpoint vrátí nadcházející dokument
    * @param int $id identifikátor současného dokumentu
    */
	public function actionGetNextDocument($id) {
		// Vrátí odpověď
		$this->sendResponse(new JsonResponse(
			// Získá dokument
			$this->documentRepository->getNext($id)
		));
	}

	/**
    * Endpoint vrátí předchozí dokument
    * @param int $id identifikátor současného dokumentu
    */
	public function actionGetPreviousDocument($id) {
		// Vrátí odpověď
		$this->sendResponse(new JsonResponse(
			// Získá dokument
			$this->documentRepository->getPrevious($id)
		));
	}

	/**
	* Vrátí všechny autory knihy
	* @param int $id identifikátor knihy
	*/
	public function actionGetBookAuthors(int $id) {
		// Vrátí odpověď
		$this->sendResponse(new JsonResponse(
			// Získá autory
			$this->bookRepository->getAuthors($id)
		));
	}

	/**
	* Vrátí 3 nejnovější knihy
	*/
	public function actionGetLatest() {
		// Vrátí odpověď
		$this->sendResponse(new JsonResponse(
			// Získá odpověď
			$this->bookRepository->getLatest()
		));
	}

	/**
	* Vrátí 3 nejstarší knihy
	*/
	public function actionGetOldest() {
		// Vrátí odpověď
		$this->sendResponse(new JsonResponse(
			// Získá odpověď
			$this->bookRepository->getOldest()
		));
	}

	/**
	* Získá knihu s daným ID
	* @param int indentifikátor knihy
	*/
	public function actionGetBook($id) {
		// Vrátí odpověď
		$this->sendResponse(new JsonResponse(
			// Získá odpověď
			$this->bookRepository->get($id)
		));
	}

	/**
	* Přidá novou knihu s daným názvem
	* @param string $title název knihy
	* @param int $subjectId identifikátor předmětu
	*/
	public function actionCreateBook(string $title, int $subjectId) {
		// Je uživatel redaktor?
        if (in_array('redactor', $this->userRepository->getRoles($this->getUser()->id))) {
			// Insert statement
			$id = $this->database->table('BOOKS')
						         ->insert([
				// Vlastník
				'owner_id' => $this->getUser()->id,
				'subject_id'=> $subjectId,
				'title' => $title,
				'description' => '',
				'color' => '#ECEFF1',
				'published' => 0,
				'created_at' => date('Y-m-d H:i:s')
			])->id;

			// Vrátí výsledek
			$this->sendResponse(new JsonResponse([
				'status' => 'ok',
				'id' => $id
			]));
		} else {
			// Vrátí chybu
			$this->sendResponse(new JsonResponse([
				'status' => 'error',
				'message' => 'Pouze redaktoři mohou přidávat nové knihy'
			]));
		}
	}

	/**
	* Endpoint aktualizuje knihu s daným ID
	* @param int $id identifikátor knihy, kterou budeme aktualizovat
	* @param int $subjectId identifikátor předmětu knihy
	* @param string $title název knihy
	* @param string $description popisek knihy
	* @param bool $published pravda, pokud je kniha publikována
	*/
	public function actionUpdateBook($id, $subjectId, $title, $description, $color, $published) {
		// Máme oprávnění na aktualizaci knihy?
		if ($this->permissionManager->canEditBook(
			// Momentálně přihlášený uživatel
			$this->getUser()->getId(),
			// ID dokumentu
			$id
		)) {
			// Aktualizuje knihu
			$this->database->table('BOOKS')
						   ->where('id', $id)
						   ->update([
				// Nové hodnoty
				'id' => $id,
				'subject_id' => $subjectId,
				'title' => $title,
				'description' => $description,
				'color' => $color,
				'published' => $published == 'false' ? 0 : 1
			]);

			// Vrátí výsledek
			$this->sendResponse(new JsonResponse([
				'status' => 'ok'
			]));
		}

		// Nemáme oprávnění k aktualizaci knihy. Pošleme
		// chybovou odpověď:
		$this->sendResponse(new JsonResponse([
			'status' => 'error',
			'message' => 'Nemáte potřebná oprávnění pro aktualizaci této knihy.'
		]));
	}
}
