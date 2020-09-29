<?php

// Namespace
namespace App\Model;

// Usingy
use Nette;
use Nette\Security;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;
use Nette\Security\AuthenticationException;
use App\Model\User;

/**
 * Správa uživatelů
 *
 * @author Marek Kejda <Kejda.Marek@outlook.cz>
 */
final class UserManager implements Nette\Security\IAuthenticator
{
	// Trait
	use Nette\SmartObject;

	/** @var Nette\Database\Context */
	private $database;

	/** @var Nette\Security\Passwords */
 	private $passwords;

	/**
	* Konstruktor
	*/
	public function __construct(Nette\Database\Context $database, Passwords $passwords) {
		// Inicializace vnitřního stavu objektu
		$this->database = $database;
		$this->passwords = $passwords;
	}

	/**
	 * Provede přihlášení.
	 * @return Nette\Security\IIdentity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials) : IIdentity {
		// Rozparsuje data
		list($email, $password) = $credentials;

		// Získá všechny uživatele
		$row = $this->database->table('USERS')
							  ->where('email', $email)
							  ->fetch();

		// Validace:
		// Pokud uživatel nebyl nalezen
		if (!$row)
			throw new AuthenticationException('Uživatel s touto e-mailovou adresou nebyl nalezen!');

		// Pokud se neshoduje heslo
		if (!$this->passwords->verify($password, $row->password))
			throw new AuthenticationException('Nesprávné heslo!');

		// Pokud je uživatel zablokován
		if ($row->blocked)
			throw new AuthenticationException('Váš účet byl zablokován. Kontaktujte prosím správce.');

		// Validace proběhla v pořádku. Vrátíme identitu:
		return new Nette\Security\Identity(
			// ID uživatele
			$row->id,
			// Data uživatele
			[
				// E-Mail
				'email' => $row->email,
				// Jméno
				'firstName' => $row->first_name,
				// Přijmení
				'lastName' => $row->last_name,
				// Datum registrace
				'registeredAt' => $row->registered_at,
				// Profilová fotka (bude přiřazena později)
				'picturePath' => '',
				// Je uživatel blokován?
				'blocked' => $row->blocked == 0 ? false : true
			]
		);
	}

	/**
	 * Zaregistruje nového uživatele
	 * @param User $user uživatel, kterého cheme zaregistrovat
	 * @throws Exception
	 */
	public function register(User $user) {
		// Zkontrolujeme, zda e-mailová adresa již není použitá
		$unique = $this->database->table('USERS')
		                         ->where('email = ?', $user->email)
					             ->fetch() == null;

		// Pokud je emailová adresa volná
		if ($unique) {
			// Vloží data o uživateli do databáze
			$this->database->table('USERS')->insert([
				// E-Mail
				'email' => $user->email,
				// Heslo (hash)
				'password' => $this->passwords->hash($user->password),
				// Jméno
				'first_name' => $user->firstName,
				// Příjmení
				'last_name' => $user->lastName,
				// Cesta k profilovému obrázku (bude přiřazena později)
				'image_path' => '',
				// Datum registrace (teď)
				'registered_at' => date('Y-m-d H:i:s')
			]);
		} else {
			// Daná emailová adresa je již použitá
			throw new \Exception('E-Mailová adresa je již zaregistrovaná!');
		}
	}

	/**
	 * Změna hesla
	 * @param string $email Email uživatele
	 * @param string $password Nové heslo
	 */
	public function changePassword($email, $password) {
		// Vloží nová data uživatele do databáze
		$this->database->table('USERS')
					   ->where('email = ?', $email)
					   ->update([
			// Heslo
			'password' => $this->passwords->hash($password)
		]);
	}
}
