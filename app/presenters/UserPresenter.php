<?php

// Striktní typy
declare(strict_types=1);

// Namespace
namespace App\Presenters;

// Usingy
use Nette;
use Nette\Application\UI;
use Nette\Application\Responses\JsonResponse;
use Nette\Utils\Arrays;

/**
* UserPresenter je třída zajišťující správu uživatelů
*
* @author Marek Kejda <Kejda.Marek@outlook.cz>
*
*/
final class UserPresenter extends Nette\Application\UI\Presenter
{
    /** @var \Nette\Database\Context */
    public $database;

    /** @var App\Repositories\UserRepository */
    private $userRepository;

    /**
    * Konstruktor presenteru. Vyžádá si všechny potřebné závislosti
    * @param Context $database databázové spojení
    */
    public function __construct(\Nette\Database\Context $database, \App\Repositories\UserRepository $userRepository) {
        // Inicializuje vnitřní stav třídy
        $this->database = $database;
        $this->userRepository = $userRepository;
    }

    /**
    * Funkce spuštěná před renderem
    */
    public function beforeRender() {
        // Pokud není uživatel přihlášený
        if (!$this->getUser()->isLoggedIn()) {
            // Přesměrujeme uživatele na domovskou
            // stránku, protože není přihlášen
            $this->redirect('Homepage:');
        }
    }

    /**
    * Endpointa vrátí seznam všech uživatelů,
    * kteří vyhovují kritériu. Pokud byl předán query,
    * vyfiltruje shody.
    * @param string $query hledaný kus řetězce
    */
    public function actionGetUsers($query = '') {
        // Odešle odpověď
        $this->sendResponse(new JsonResponse(
            // Získá usery z prepositáře
            $this->userRepository->getUsers($query)
        ));
    }

    /**
    * Endpointa vrátí uživatele s daným identifikátorem
    * @param int $id identifikátor uživatele
    */
    public function actionGetUser(int $id) {
        // Odešle odpověď
        $this->sendResponse(new JsonResponse(
            // Získá usera z prepositáře
            $this->userRepository->get($id)
        ));
    }

    /**
    * Endpointa vrátí seznam všech redaktorů. Pokud byl předán query,
    * vyfiltruje shody.
    * @param string $query hledaný kus řetězce
    */
    public function actionGetRedactors($query = '') {
        // Odešle odpověď
        $this->sendResponse(new JsonResponse(
            // Získá usery z prepositáře
            $this->userRepository->getRedactors($query)
        ));
    }

    /**
    * Endpointa vrátí uživatele pro každý identifikátor
    * v předaném poli
    * @param array $uids identifikátory
    * @return array uživatelé
    */
    public function actionGetUsersById(array $uids) {
        // Pošle odpověď
        $this->sendResponse(new JsonResponse(
            // Získá data
            $this->userRepository->getUsersById($uids)
        ));
    }

    /**
	* Default render metoda
	*/
	public function renderDefault($id, $error = null)
	{
        // Předá uživatele a error do šablony
        $this->template->user = $this->userRepository->get($id);
        $this->template->error = $error;
    }

    /**
	* Vytvoří instanci formu pro změnu profilového obrázku
	* @return Form Instance formu
	*/
	protected function createComponentProfilePictureForm() : UI\Form
	{
		// Vytvoříme nový formulář
        $form = new UI\Form;

		// File upload
		$form->addUpload('file');

		// Potvrzovací tlačítko
		$form->addSubmit('change');

		// Při úspěšném odeslání
        $form->onSuccess[] = [$this, 'profilePictureFormSucceeded'];

        // Vrátíme formulář
        return $form;
	}

	/**
	* Volá se po úspěšném odeslání formuláře pro změnu profilového obrázku
    * @param Form $form formulář
    * @param stdClass $values hodnoty
	*/
    public function profilePictureFormSucceeded(UI\Form $form, \stdClass $values) : void {
        // Oprávnění
        if ($this->getParameter('id') != $this->getUser()->getId())
            // Přesměrujeme uživatele na stránku s chybou
            $this->redirect('this#account', ['error' => 'Nelze změnit profilový obrázek jinému uživateli']);

        // Soubor
        $file = $values->file;

		// Nahrál uživatel obrázek?
		if ($file->isImage()) {
			// Získá cestu k souboru
            $type = array_slice(explode('.', $file->getSanitizedName()), -1)[0];
			$path = '/uploaded/' . uniqid('IMG_') . '.' . $type;

			// Přesune soubor
			$file->move('.' . $path);

			// Změní cestu k souboru v DB
			$this->userRepository->changeProfileImage($this->getParameter('id'), $path);

			// Přesměrujeme uživatele
			$this->redirect('User:default#account', ['id' => $this->getParameter('id')]);
		} else {
			// Přesměrujeme uživatele na stránku s chybou
			$this->redirect('this#account', ['error' => 'Mezi podporované formáty patří pouze gif, png nebo jpg!']);
		}
	}

    /**
    * Vrátí identifikátor přihlášeného usera
    */
    public function actionGetLoggedUserId() {
        // Pošle odpověď
        $this->sendResponse(new JsonResponse(
            // Získá data
            $this->getUser()->id
        ));
    }

    /**
    * Nastaví role uživateli s daným identifikátorem
    * @param int $id identifikátor uživatele
    * @param array $roles pole rolí
    */
    public function actionSetRoles(int $id, array $roles) {
        // Je uživatel admin?
        if (in_array('admin', $this->userRepository->getRoles($this->getUser()->id))) {
            // Smaže všechny role daného uživatele
            $this->database->table('ROLES')
                           ->where('user_id', $id)
                           ->delete();

            // Přiřkne nové role
            foreach ($roles as $role) {
                // Insert role
                $this->database->table('ROLES')
                               ->insert([
                    // Potřebná data
                    'user_id' => $id,
                    'name' => $role
                ]);
            }

            // Odpoví
    		$this->sendResponse(new JsonResponse([
    			'status' => 'ok'
    		]));
        } else {
            // Nemáme potřebná oprávnění. Pošleme chybovou zprávu:
    		$this->sendResponse(new JsonResponse([
    			'status' => 'error',
    			'message' => 'Na úpravu rolí nemáte dostatečné oprávnění.'
    		]));
        }
    }

    /**
    * Zablokuje nebo odblokuje uživatele s daným ID
    * @param int $id identifikátor uživatele k blokaci
    */
    public function actionToggleBlock(int $id) {
        // Je uživatel admin?
        if (in_array('admin', $this->userRepository->getRoles($this->getUser()->id))) {
            // Získá active record
            $record = $this->database->table('USERS')
                                  ->where('id', $id);

            // Získá řádek uživatele
            $row = $this->database->table('USERS')
                                  ->where('id', $id)
                                  ->fetch();

            // Zablokuje (nebo odblokuje) uživatele
            $record->update([
                // Zneguje blokaci
                'blocked' => $res = !($row['blocked'] == 0 ? false : true)
            ]);

            // Vrátí výsledek
            $this->sendResponse(new JsonResponse([
    			'status' => 'ok',
                'result' => $res
    		]));
        } else {
            // Nemáme potřebná oprávnění. Pošleme chybovou zprávu:
    		$this->sendResponse(new JsonResponse([
    			'status' => 'error',
    			'message' => 'Na blokaci uživatelů nemáte dostatečné oprávnění.'
    		]));
        }
    }

    /**
    * Vrátí true, pokud je příhlášený uživatel redaktor
    */
    public function actionIsRedactor() {
        // Vrátí výsledek
        $this->sendResponse(new JsonResponse([
            // Data
            'status' => 'ok',
            'result' => $this->getUser()->isLoggedIn() ? in_array('redactor', $this->userRepository->getRoles($this->getUser()->id)) : false
        ]));
    }
}
