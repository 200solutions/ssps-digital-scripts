<?php

// Namespace
namespace App\Presenters;

// Usingy
use Nette;
use Nette\Application\UI;
use Nette\Application\UI\Form;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use App\Model\User;

/**
* SignPresenter je třída zajišťující registraci, přihlašování
* a ostatní agendy spojené se správou uživatelů
*
* @author Marek Kejda <Kejda.Marek@outlook.cz>
*
*/
final class SignPresenter extends Nette\Application\UI\Presenter
{
	/** @var Nette\Database\Context */
	private $database;

	/** @var Model\UserManager */
	private $userManager;

	/** @var Nette\Mail\IMailer */
	private $mailer;

	/** @var Nette\Application\LinkGenerator */
    private $linkGenerator;

    /** @var Nette\Application\UI\ITemplateFactory */
    private $templateFactory;

	/**
	* Konstruktor
	*/
	public function __construct(
		\App\Model\UserManager $userManager,
		\Nette\Database\Context $database,
		\Nette\Mail\IMailer $mailer,
		\Nette\Application\LinkGenerator $linkGenerator,
		\Nette\Application\UI\ITemplateFactory $templateFactory
	) {
		// Inicializace vnitřního stavu objektu
		$this->database = $database;
		$this->userManager = $userManager;
		$this->mailer = $mailer;
		$this->linkGenerator = $linkGenerator;
		$this->templateFactory = $templateFactory;
	}

	/**
	* Render přihlašovací stránky
	*/
	public function renderIn($error = null, $success = null)
	{
		// Předá všechny proměnné
		$this->template->error = $error;
		$this->template->success = $success;
	}

	/**
	* Render registrační obrazovky
	*/
	public function renderUp($error = null)
	{
		// Předá všechny proměnné
		$this->template->error = $error;
	}

	/**
	* Render obnovovací obrazovky (zapomenuté heslo)
	*/
	public function renderRestore($error = null, $success = null)
	{
		// Předá všechny proměnné
		$this->template->user = $this->getUser();
		$this->template->error = $error;
		$this->template->success = $success;
	}

	/**
	* Render obrazovky pro změnu hesla
	*/
	public function renderChange($token)
	{
		// Máme token?
		if (!$token)
		{
			// Vrátíme uživatele na obnovu hesla s chybovým hlášením
			$this->redirect('Sign:restore', ['error' => 'Odkaz není validní!']);
		}

		// Najdeme token v databázi
		$token = $this->database->table('TOKENS')
					            ->where('token = ?', $token)
					            ->fetch();

		// Pokud jsme nenalezli token nebo platnost
		// tokenu vypršela
		if (!$token || date('Y-m-d H:i:s') > date($token->expire_at))
		{
			// Vrátíme uživatele na obnovu hesla s chybovým hlášením
			$this->redirect('Sign:restore', ['error' => 'Odkaz není validní nebo vypršela jeho platnost!']);
		}

		// Zkontrolujeme, zda token již nebyl použit
		if ($token->used == 1)
		{
			// Vrátíme uživatele na obnovu hesla s chybovým hlášením
			$this->redirect('Sign:restore', ['error' => 'Tento odkaz již byl jednou použit! Pokud si přejete znovu změnit heslo, vytvořte prosím nový požadavek.']);
		}
	}

	/**
	* Vytvoří instanci formu pro přihlašování
	* @return Form Instance formu
	*/
	protected function createComponentSignInForm() : UI\Form
	{
		// Vytvoříme nový formulář
        $form = new UI\Form;
		$form->addProtection('Vypršel časový limit, odešlete formulář znovu');

		// Přidá do formuláře potřebná pole - email
		$form->addEmail('email')
		     ->setRequired('Zadejte prosím svůj email!');

		// Heslo
		$form->addPassword('password')
			 ->setRequired('Zadejte prosím své heslo!');

		// Potvrzovací tlačítko
		$form->addSubmit('login');

		// Při úspěšném odeslání
        $form->onSuccess[] = [$this, 'signInFormSucceeded'];

        // Vrátíme formulář
        return $form;
	}

	/**
	* Volá se po úspěšném odeslání přihlašovacího formuláře
	*/
    public function signInFormSucceeded(UI\Form $form, \stdClass $values) : void
    {
		// Pokusíme se přihlásit uživatele
		try
		{
			// Přihlášení
			$this->getUser()->login($values->email, $values->password);
		}
		catch(\Nette\Security\AuthenticationException $e)
		{
			// Nastala chyba. Přesměrujeme uživatele na přihlášení
			// s výpisem chyby:
			$this->redirect('Sign:in', ['error' => $e->getMessage()]);
		}

		// Redirect na domovskou obrazovku
		$this->redirect('Homepage:default');
	}

	/**
	* Vytvoří instanci formu pro registraci
	* @return Form Instance formu
	*/
	protected function createComponentSignUpForm() : UI\Form
	{
		// Vytvoříme nový formulář
        $form = new UI\Form;
		$form->addProtection('Vypršel časový limit, odešlete formulář znovu');

		// Přidá do formuláře potřebná pole - jméno
		$form->addText('firstName')
		     ->setRequired('Zadejte prosím své jméno!')
			 ->addRule(Form::MIN_LENGTH, 'Jméno musí mít alespoň %d znaky!', 3);

		// Příjmení
	    $form->addText('lastName')
		     ->setRequired('Zadejte prosím své příjmení!')
			 ->addRule(Form::MIN_LENGTH, 'Příjmení musí mít alespoň %d znaky!', 3);

		// Email
 		$form->addEmail('email')
 		     ->setRequired('Zadejte prosím svůj email!');

		// Heslo
		$form->addPassword('password')
			 ->setRequired('Zadejte prosím své heslo!')
			 ->addRule(Form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků!', 5);

		// Potvrzovací tlačítko
		$form->addSubmit('register');

		// Při úspěšném odeslání
        $form->onSuccess[] = [$this, 'signUpFormSucceeded'];

        // Vrátíme formulář
        return $form;
	}

	/**
	* Volá se po úspěšném odeslání registračního formuláře
	*/
    public function signUpFormSucceeded(UI\Form $form, \stdClass $values) : void
    {
		// Pokusíme se zaregistrovat nového uživatele
		try
		{
			// Registrace nového uživatele
			$this->userManager->register(new User(
				// Jméno
				$values->firstName,
				// Příjmení
				$values->lastName,
				// E-Mail
				$values->email,
				// Heslo
				$values->password,
				// Blokace
				0
			));
		}
		catch(\Exception $e)
		{
			// Nastala chyba. Přesměrujeme uživatele na registraci
			// s výpisem chyby:
			$this->redirect('Sign:up', ['error' => $e->getMessage()]);
		}

		// Přihlásí uživatele
		$this->getUser()->login($values->email, $values->password);

		// Přesměruje na homepage
		$this->redirect('Homepage:default');
	}

	/**
	* Vytvoří instanci formu pro požadavek na obnovu nebo změnu hesla
	* @return Form Instance formu
	*/
	protected function createComponentPasswordRestoreForm() : UI\Form
	{
		// Vytvoříme nový formulář
        $form = new UI\Form;
		$form->addProtection('Vypršel časový limit, odešlete formulář znovu');

		// Email
 		$form->addEmail('email')
 		     ->setRequired('Zadejte prosím svůj email!');

	    // Potvrzovací tlačítko
 		$form->addSubmit('restore');

 		// Při úspěšném odeslání
        $form->onSuccess[] = [$this, 'passwordRestoreSucceeded'];

        // Vrátíme formulář
        return $form;
	}

	/**
	* Volá se po úspěšném odeslání formuláře pro požadavek na obnovu nebo reset hesla
	*/
    public function passwordRestoreSucceeded(UI\Form $form, \stdClass $values) : void
    {
		// Zkontrolujeme, zda uvedená e-mailová adresa v systému existuje
		$row = $this->database->table('USERS')
							  ->where('email = ?', $values->email)
							  ->fetch();

		// Pokud jsme našli iživatele s tímto e-mailem
		if ($row)
		{
			// Vygenerujeme token
			$token = bin2hex(openssl_random_pseudo_bytes(16));

			// Čas expirace
			$expire = date('Y.m.d H:i:s', strtotime('+1 hour'));

			// Vložíme token do databáze
			$this->database->table('TOKENS')->insert([
				// Žadatel (email)
				'email' => $values->email,
				// Typ tokenu
				'type' => 'password_change',
				// Vygenerovaný token
				'token' => $token,
				// Datum vytvoření
				'created_at' => date('Y-m-d H:i:s'),
				// Datum expirace
				'expire_at' => $expire
			]);

			// Odešleme e-mail
			$this->sendPasswordChangeMail($values->email, $token);

			// Přesměrujeme uživatele
			$this->redirect('Sign:restore', ['success' => 'Odkaz pro obnovu hesla byl odeslán na Váš email. Čas expirace je ' . $expire . '.']);
		}
		else
		{
			// Žádný uživatel s touto e-mailovou
			// adresou v systému neexistuje
			$this->redirect('Sign:restore', ['error' => 'Uživatel s danou e-mailovou adresou neexistuje!']);
		}
	}

	/**
	* Vytvoří instanci formu pro změnu hesla
	* @return Form Instance formu
	*/
	protected function createComponentPasswordChangeForm() : UI\Form
	{
		// Vytvoříme nový formulář
        $form = new UI\Form;
		$form->addProtection('Vypršel časový limit, odešlete formulář znovu');

		// Heslo
		$form->addPassword('password')
			 ->setRequired('Zadejte prosím své heslo!')
			 ->addRule(Form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků!', 5);

	    // Potvrzovací tlačítko
 		$form->addSubmit('change');

 		// Při úspěšném odeslání
        $form->onSuccess[] = [$this, 'passwordChangeSucceeded'];

        // Vrátíme formulář
        return $form;
	}

	/**
	* Volá se po úspěšném odeslání formuláře na změnu hesla
	*/
    public function passwordChangeSucceeded(UI\Form $form, \stdClass $values) : void
    {
		// Najdeme token v databázi
		$token = $this->database->table('TOKENS')
					            ->where('token = ?', $this->getParameter('token'))
					            ->fetch();

		// Změníme heslo uživatele
		$this->userManager->changePassword($token->email, $values->password);

		// Nastavíme, že token byl použit
		$this->database->table('TOKENS')
					   ->where('token = ?', $this->getParameter('token'))
					   ->update([
			// Token byl použit
			'used' => 1
		]);

		// Vše proběhlo v pořádku - přesměrujeme uživatele
		$this->redirect('Sign:in', ['success' => 'Heslo bylo úspěšně změněno.']);
	}

	/**
	* Pomocná metoda pro odeslání e-mailu s odkazem na reset hesla
	*/
	private function sendPasswordChangeMail($email, $token)
	{
		// Vytvoříme template
		$template = $this->templateFactory->createTemplate();
	    $template->getLatte()->addProvider('uiControl', $this->linkGenerator);

		// Určíme cestu k šabloně mailu
		$template->setFile(__DIR__ . '/templates/email.latte');

		// Vložíme data
		$template->token = $token;

		// Vytvoříme nový e-mail
		$mail = new Message;

		// Vyplníme důležitá data e-mailu
		$mail->setFrom('Digitální skripta <Marek.Kejda@ssps.cz>')
    		 ->addTo($email)
    		 ->setSubject('Změna hesla')
        	 ->setHtmlBody($template);

		// Odešleme e-mail
		$mailer = new SendmailMailer;
		$mailer->send($mail);
	}

	/**
	* Akce pro odhlášení
	*/
	public function actionOut()
	{
		// Odhlásíme uživatele
		$this->getUser()->logout();

		// Realoadnem stránku
		$this->redirect('Sign:in');
	}
}
