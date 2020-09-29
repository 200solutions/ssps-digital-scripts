<?php

// Namespace
namespace App\Model;

/**
* User je třída sloužící k uchovávání
* informací o uživateli
*
* @author Marek Kejda <Kejda.Marek@outlook.cz>
*
*/
class User
{
    // ID uživatele
    public $id;

    // E-Mail uživatele
    public $email;

    // heslo uživatele
    public $password;

    // Jméno uživatele
    public $firstName;

    // Příjmení uživatele
    public $lastName;

    // Datum registrace
    public $registeredAt;

    // Cesta k profilovému obrázku
    public $picturePath;

    // Je uživatel zablokován?
    public $blocked;

    /**
    * Konstruktor uživatele. Správně inicializuje
    * vnitřní stav instance na základě předaných hodnot
    * @param string $firstName jméno uživatele
    * @param string $lastName příjmení uživatele
    * @param string $email emailová adresa uživatele
    * @param string $password zahashované heslo
    * @param bool $blocked pravda, pokud byl uživatel zablokován
    */
    public function __construct($firstName, $lastName, $email, $password, $blocked) {
        // Inicializuje vnitřní stav instance
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
        $this->blocked = $blocked;
    }
}
