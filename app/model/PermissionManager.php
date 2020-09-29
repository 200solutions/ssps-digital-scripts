<?php

// Striktní režim
declare(strict_types=1);

// Namespace
namespace App\Model;

// Usingy
use Nette;
use Nette\Utils\Arrays;

/**
 * Správa oprávnění
 *
 * @author Marek Kejda <Kejda.Marek@outlook.cz>
 */
final class PermissionManager
{
	// Trait
	use Nette\SmartObject;

	/** @var Nette\Database\Context */
	private $database;

	/** @var App\Repositories\UserRepository */
	private $userRepository;

	/** @var App\Repositories\BookRepository */
	private $bookRepository;

	/** @var App\Repositories\DocumentRepository */
	private $documentRepository;

	/**
	* Konstruktor
	*/
	public function __construct(
		\Nette\Database\Context $database,
		\App\Repositories\UserRepository $userRepository,
		\App\Repositories\BookRepository $bookRepository,
		\App\Repositories\DocumentRepository $documentRepository
	) {
		// Inicializace vnitřního stavu objektu
		$this->database = $database;
		$this->userRepository = $userRepository;
		$this->bookRepository = $bookRepository;
		$this->documentRepository = $documentRepository;
	}

    /**
    * Metoda vrátí ano, pokud předaný uživatel může editovat
    * dokument s předaným ID
    * @param int $uid indentifikátor uživatele
    * @param int $docId identifikátor dokumentu
    * @return bool ano, pokud uživatel může editovat dokument
    */
    public function canEditDocument(int $uid, int $docId): bool {
		// Získá podrobnosti o dokumentu a uživateli
		$roles = $this->userRepository->getRoles($uid);				// Role uživatele
		$authors = $this->documentRepository->getAuthors($docId);	// Autoři dokumentu
		$accesses = $this->documentRepository->getAccesses($docId); // Exkluzivní přístupy k dokumentu
		$group = $this->documentRepository->getAccessGroup($docId); // Přístupová skupina dokumentu
		$bookId = $this->database->table('DOCUMENTS')				// ID knihy, do které dokument patří
							     ->get($docId)
							     ->book_id;

		// Je uživatel admin?
		if (in_array('admin', $roles)) {
			// Admin může cokoliv
			return true;
		}

		// Je uživatel vůbec redaktor?
		if (in_array('redactor', $roles)) {
			// Je redaktor vlastníkem knihy?
			if ($this->database->table('BOOKS')->get($bookId)->owner_id == $uid) {
				// Ano je - udělíme přístup
				return true;
			}

			// Je redaktor vlastníkem dokumentu?
			if (in_array($uid, $authors)) {
				// Redaktor je vlastníkem dokumentu. Udělíme
				// uživateli přístup:
				return true;
			} else {
				// Redaktor není vlastníkem dokumentu. Zkontrolujeme,
				// zda redaktor nedostal od vlastníků (vlastníka)
				// exkluzivní přístup:
				if (in_array($uid, $accesses)) {
					// Uživatel dostal exkluzivní přístup. Udělíme
					// mu proto editační práva:
					return true;
				}
			}

			// Je dokument přístupný pouze pro pedagogy?
			if ($group == 1) {
				// Je uživatel v roli pedagoga?
				if (in_array('teacher', $roles)) {
					// Uživatel je pedagog. Udělíme přístup:
					return true;
				}
			}

			// Je dokument přístupný pro všechny redaktory?
			if ($group == 2) {
				// Dokument je zpřístupněn pro všechny
				// redaktory. Udělíme přístup:
				return true;
			}
		}

		// Uživatel není redaktor (ani admin) a proto nemá
		// oprávnění provádět jakékoliv úpravy
		// na dokumentech.
		return false;
    }

	/**
    * Zjistí, zda přihlášený uživatel může editovat
    * knihu s daným ID
    * @param int $id identifikátor knihy
    */
    public function canEditBook(int $uid, int $id) {
		// Vrátí výsledek
		return $this->database->table('BOOKS')->get($id)->owner_id == $uid || in_array('admin', $this->userRepository->getRoles($uid));
    }
}
