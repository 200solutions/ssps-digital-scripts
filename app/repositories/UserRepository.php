<?php

// Striktní režim
declare(strict_types=1);

// Namespace
namespace App\Repositories;

// Usingy
use Nette\Utils\Arrays;
use Nette\Utils\Image;

/**
* UserRepository slouží k poskytování informací o uživatelích
*
* @author Marek Kejda <Kejda.Marek@outlook.cz>
*
*/
class UserRepository
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
    * Získá uživatele s daným ID
    * @param int $uid user identifikátor
    * @return class user s daným ID
    */
    public function get($uid) {
        // Získá uživatele s danýmy user-identifikátory
        $user = $this->database->table('USERS')
                               ->where('id', $uid)
                               ->fetch();

        // Odkaz na momentální kontext
        $_this = $this;

        // Vrátí uživatele
		return new class($user, $_this) {
			/**
			* Konstruktor anonymní třídy
			*/
			public function __construct($user, $_this)
			{
				// Inicializace vnitřního stavu
				$this->id = $user['id'];
                $this->email = $user['email'];
				$this->firstName = $user['first_name'];
				$this->lastName = $user['last_name'];
                $this->registeredAt = $user['registered_at'];
                $this->imagePath = $user['image_path'];
                $this->roles = $_this->getRoles($this->id);
                $this->blocked = $user['blocked'] == 0 ? false : true;
			}
		};
    }

    /**
    * Vrátí role uživatele s daným identifikátorem
    * @param int $uid indentifikátor uživatele
    * @return array pole rolí, ve kterých předaný uživatel vystupuje
    */
    public function getRoles(int $uid) {
        // Vrátí role
        return Arrays::map(array_values(
			// Získá uživatelovi role z DB
			$this->database->table('USERS')
                           ->get($uid)
                           ->related('ROLES')
					       ->fetchAssoc('id')), function ($role) {
				// Vrátí roli
				return $role['name'];
		});
    }

    /**
    * Vrátí data uživatelů s danými identifikátory
    * @param array $uids indetifikátory uživatelů
    * @return array data uživatelů
    */
    public function getUsersById(array $uids) {
        // Získá uživatele s danýmy user-identifikátory
        $users = $this->database->table('USERS')
                                ->where('id', $uids)
                                ->fetchAll();

        return array_values(Arrays::map($users, function ($user) {
            /**
            * Anonymní třída uživatele
            * @param object $user uživatel
            * @param string $query hledaný řetězec
            */
			return new class($user) {
				/**
				* Konstruktor anonymní třídy
				*/
				public function __construct($user)
				{
					// Inicializace vnitřního stavu
					$this->id = $user->id;
                    $this->email = $user->email;
					$this->firstName = $user->first_name;
					$this->lastName = $user->last_name;
				}
			};
        }));
    }

    /**
    * Vrátí seznam všech uživatelů. Pokud byl předán query, vyfiltruje shody.
    * @param string $query hledaný kus řetězce
    */
    public function getUsers($query = '') {
        // Získá usery, které vyhovují vyhledávání
        $users = $this->database->table('USERS')
                                ->where("first_name LIKE ? OR last_name LIKE ? OR CONCAT(first_name, ' ', last_name) LIKE ? OR CONCAT(last_name, ' ', first_name) LIKE ?", "%$query%", "%$query%", "%$query%", "%$query%")
                                ->limit(50)
                                ->fetchAll();

        // Odkaz na momentální scope
        $_this = $this;

        // Vytvoří odpověď
        return array_values(Arrays::map($users, function ($user) use ($query, $_this) {
            /**
            * Anonymní třída uživatele
            * @param object $user uživatel
            * @param string $query hledaný řetězec
            */
			return new class($user, $query, $_this) {
				/**
				* Konstruktor anonymní třídy
				*/
				public function __construct($user, $query, $_this)
				{
					// Inicializace vnitřního stavu
					$this->id = $user->id;
                    $this->email = $user->email;
					$this->firstName = $user->first_name;
					$this->lastName = $user->last_name;
                    $this->imagePath = $user->image_path;
                    $this->roles = $_this->getRoles($user->id);
                    $this->blocked = $user->blocked == 0 ? false : true;

                    // Zjistí a uloží podobnost mezi jménem a hledaným výrazem
                    similar_text("$this->firstName $this->lastName", $query, $this->similarity);
				}
			};
        }));
    }

    /**
    * Vrátí seznam všech redaktorů. Pokud byl předán query, vyfiltruje shody.
    * @param string $query hledaný kus řetězce
    */
    public function getRedactors($query = '') {
        // Získá usery, které vyhovují vyhledávání
        $users = $this->database->table('USERS')
                                ->where("first_name LIKE ? OR last_name LIKE ? OR CONCAT(first_name, ' ', last_name) LIKE ? OR CONCAT(last_name, ' ', first_name) LIKE ?", "%$query%", "%$query%", "%$query%", "%$query%")
                                ->limit(50)
                                ->select('id, email, first_name, last_name, image_path')
                                ->fetchAll();

        // Předpřipraví pole na redaktory
        $redactors = array();

        // Zkontroluje, zda jsou useři redaktoři:
        foreach($users as $user) {
            // Zkontroluje přítomnost role
            if (in_array('redactor', $this->getRoles($user->id))) {
                // Vloží uživatele do pole redaktorů
                array_push($redactors, $user);
            }
        }

        // Odkaz na momentální scope
        $_this = $this;

        // Vytvoří odpověď
        return array_values(Arrays::map($redactors, function ($redactor) use ($query, $_this) {
            /**
            * Anonymní třída uživatele
            * @param object $user uživatel
            * @param string $query hledaný řetězec
            */
			return new class($redactor, $query, $_this) {
				/**
				* Konstruktor anonymní třídy
				*/
				public function __construct($redactor, $query, $_this)
				{
					// Inicializace vnitřního stavu
					$this->id = $redactor->id;
                    $this->email = $redactor->email;
					$this->firstName = $redactor->first_name;
					$this->lastName = $redactor->last_name;
                    $this->imagePath = $redactor->image_path;
                    $this->roles = $_this->getRoles($redactor->id);

                    // Zjistí a uloží podobnost mezi jménem a hledaným výrazem
                    similar_text("$this->firstName $this->lastName", $query, $this->similarity);
				}
			};
        }));
    }

    /**
    * Změní profilový obrázek uživatele s daným ID za obrázek pod danou cestou
    * @param int $id ID uživatele
    * @param string $path cesta k obrázku
    */
    public function changeProfileImage($id, string $path)
    {
        // Načte obrázek
        $image = Image::fromFile('.' . $path);

        // Změnší veliksot obrázku
        $image = $image->resize(512, 512, Image::EXACT);

        // Uloží obrázek
        $image->save('./' . $path, 80, Image::JPEG);

        // Uloží cestu k obrázku
        $this->database->table('USERS')
                       ->where('id', $id)
                       ->update([
            // Cesta k souboru
            'image_path' => $path
        ]);
    }
}
