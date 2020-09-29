<?php
// source: /var/www/webs/skripta.ssps.cz/public_html/app/config/common.neon
// source: /var/www/webs/skripta.ssps.cz/public_html/app/config/local.neon
// source: array

/** @noinspection PhpParamsInspection,PhpMethodMayBeStaticInspection */

declare(strict_types=1);

class Container_8f84f7657c extends Nette\DI\Container
{
	protected $tags = [
		'nette.inject' => [
			'application.1' => true,
			'application.2' => true,
			'application.3' => true,
			'application.4' => true,
			'application.5' => true,
			'application.6' => true,
			'application.7' => true,
			'application.8' => true,
			'application.9' => true,
		],
	];

	protected $types = ['container' => 'Nette\DI\Container'];

	protected $aliases = [
		'application' => 'application.application',
		'cacheStorage' => 'cache.storage',
		'database.default' => 'database.default.connection',
		'httpRequest' => 'http.request',
		'httpResponse' => 'http.response',
		'nette.cacheJournal' => 'cache.journal',
		'nette.database.default' => 'database.default',
		'nette.database.default.context' => 'database.default.context',
		'nette.httpRequestFactory' => 'http.requestFactory',
		'nette.latteFactory' => 'latte.latteFactory',
		'nette.mailer' => 'mail.mailer',
		'nette.presenterFactory' => 'application.presenterFactory',
		'nette.templateFactory' => 'latte.templateFactory',
		'nette.userStorage' => 'security.userStorage',
		'router' => 'routing.router',
		'session' => 'session.session',
		'user' => 'security.user',
	];

	protected $wiring = [
		'Nette\DI\Container' => [['container']],
		'Nette\Application\Application' => [['application.application']],
		'Nette\Application\IPresenterFactory' => [['application.presenterFactory']],
		'Nette\Application\LinkGenerator' => [['application.linkGenerator']],
		'Nette\Caching\Storages\IJournal' => [['cache.journal']],
		'Nette\Caching\IStorage' => [['cache.storage']],
		'Nette\Database\Connection' => [['database.default.connection']],
		'Nette\Database\IStructure' => [['database.default.structure']],
		'Nette\Database\Structure' => [['database.default.structure']],
		'Nette\Database\IConventions' => [['database.default.conventions']],
		'Nette\Database\Conventions\DiscoveredConventions' => [['database.default.conventions']],
		'Nette\Database\Context' => [['database.default.context']],
		'Nette\Http\RequestFactory' => [['http.requestFactory']],
		'Nette\Http\IRequest' => [['http.request']],
		'Nette\Http\Request' => [['http.request']],
		'Nette\Http\IResponse' => [['http.response']],
		'Nette\Http\Response' => [['http.response']],
		'Nette\Bridges\ApplicationLatte\ILatteFactory' => [['latte.latteFactory']],
		'Nette\Application\UI\ITemplateFactory' => [['latte.templateFactory']],
		'Nette\Mail\IMailer' => [['mail.mailer']],
		'Nette\Routing\RouteList' => [['routing.router']],
		'Nette\Routing\Router' => [['routing.router']],
		'Nette\Application\IRouter' => [['routing.router']],
		'ArrayAccess' => [
			2 => [
				'routing.router',
				'application.1',
				'application.2',
				'application.3',
				'application.5',
				'application.6',
				'application.7',
			],
		],
		'Countable' => [2 => ['routing.router']],
		'IteratorAggregate' => [2 => ['routing.router']],
		'Traversable' => [2 => ['routing.router']],
		'Nette\Application\Routers\RouteList' => [['routing.router']],
		'Nette\Security\Passwords' => [['security.passwords']],
		'Nette\Security\IUserStorage' => [['security.userStorage']],
		'Nette\Security\User' => [['security.user']],
		'Nette\Http\Session' => [['session.session']],
		'Tracy\ILogger' => [['tracy.logger']],
		'Tracy\BlueScreen' => [['tracy.blueScreen']],
		'Tracy\Bar' => [['tracy.bar']],
		'Nette\Security\IAuthenticator' => [['01']],
		'App\Model\UserManager' => [['01']],
		'App\Model\PermissionManager' => [['02']],
		'App\Repositories\UserRepository' => [['userRepository']],
		'App\Repositories\BookRepository' => [['bookRepository']],
		'App\Repositories\DocumentRepository' => [['documentRepository']],
		'Nette\Application\UI\Presenter' => [
			2 => [
				'application.1',
				'application.2',
				'application.3',
				'application.5',
				'application.6',
				'application.7',
			],
		],
		'Nette\Application\UI\Control' => [
			2 => [
				'application.1',
				'application.2',
				'application.3',
				'application.5',
				'application.6',
				'application.7',
			],
		],
		'Nette\Application\UI\Component' => [
			2 => [
				'application.1',
				'application.2',
				'application.3',
				'application.5',
				'application.6',
				'application.7',
			],
		],
		'Nette\ComponentModel\Container' => [
			2 => [
				'application.1',
				'application.2',
				'application.3',
				'application.5',
				'application.6',
				'application.7',
			],
		],
		'Nette\ComponentModel\Component' => [
			2 => [
				'application.1',
				'application.2',
				'application.3',
				'application.5',
				'application.6',
				'application.7',
			],
		],
		'Nette\Application\IPresenter' => [
			2 => [
				'application.1',
				'application.2',
				'application.3',
				'application.4',
				'application.5',
				'application.6',
				'application.7',
				'application.8',
				'application.9',
			],
		],
		'Nette\Application\UI\IStatePersistent' => [
			2 => [
				'application.1',
				'application.2',
				'application.3',
				'application.5',
				'application.6',
				'application.7',
			],
		],
		'Nette\Application\UI\ISignalReceiver' => [
			2 => [
				'application.1',
				'application.2',
				'application.3',
				'application.5',
				'application.6',
				'application.7',
			],
		],
		'Nette\ComponentModel\IComponent' => [
			2 => [
				'application.1',
				'application.2',
				'application.3',
				'application.5',
				'application.6',
				'application.7',
			],
		],
		'Nette\ComponentModel\IContainer' => [
			2 => [
				'application.1',
				'application.2',
				'application.3',
				'application.5',
				'application.6',
				'application.7',
			],
		],
		'Nette\Application\UI\IRenderable' => [
			2 => [
				'application.1',
				'application.2',
				'application.3',
				'application.5',
				'application.6',
				'application.7',
			],
		],
		'App\Presenters\ApiPresenter' => [2 => ['application.1']],
		'App\Presenters\BooksPresenter' => [2 => ['application.2']],
		'App\Presenters\Error4xxPresenter' => [2 => ['application.3']],
		'App\Presenters\ErrorPresenter' => [2 => ['application.4']],
		'App\Presenters\UserPresenter' => [2 => ['application.5']],
		'App\Presenters\SignPresenter' => [2 => ['application.6']],
		'App\Presenters\HomepagePresenter' => [2 => ['application.7']],
		'NetteModule\ErrorPresenter' => [2 => ['application.8']],
		'NetteModule\MicroPresenter' => [2 => ['application.9']],
	];


	public function __construct(array $params = [])
	{
		parent::__construct($params);
		$this->parameters += [
			'appDir' => '/var/www/webs/skripta.ssps.cz/public_html/app',
			'wwwDir' => '/var/www/webs/skripta.ssps.cz/public_html/www',
			'vendorDir' => '/var/www/webs/skripta.ssps.cz/public_html/vendor',
			'debugMode' => false,
			'productionMode' => true,
			'consoleMode' => false,
			'tempDir' => '/var/www/webs/skripta.ssps.cz/public_html/app/../temp',
		];
	}


	public function createService01(): App\Model\UserManager
	{
		$service = new App\Model\UserManager($this->getService('database.default.context'), $this->getService('security.passwords'));
		return $service;
	}


	public function createService02(): App\Model\PermissionManager
	{
		$service = new App\Model\PermissionManager(
			$this->getService('database.default.context'),
			$this->getService('userRepository'),
			$this->getService('bookRepository'),
			$this->getService('documentRepository')
		);
		return $service;
	}


	public function createServiceApplication__1(): App\Presenters\ApiPresenter
	{
		$service = new App\Presenters\ApiPresenter($this->getService('application.linkGenerator'), $this->getService('database.default.context'));
		$service->injectPrimary(
			$this,
			$this->getService('application.presenterFactory'),
			$this->getService('routing.router'),
			$this->getService('http.request'),
			$this->getService('http.response'),
			$this->getService('session.session'),
			$this->getService('security.user'),
			$this->getService('latte.templateFactory')
		);
		$service->invalidLinkMode = 1;
		return $service;
	}


	public function createServiceApplication__2(): App\Presenters\BooksPresenter
	{
		$service = new App\Presenters\BooksPresenter(
			$this->getService('database.default.context'),
			$this->getService('bookRepository'),
			$this->getService('documentRepository'),
			$this->getService('userRepository'),
			$this->getService('02')
		);
		$service->injectPrimary(
			$this,
			$this->getService('application.presenterFactory'),
			$this->getService('routing.router'),
			$this->getService('http.request'),
			$this->getService('http.response'),
			$this->getService('session.session'),
			$this->getService('security.user'),
			$this->getService('latte.templateFactory')
		);
		$service->invalidLinkMode = 1;
		return $service;
	}


	public function createServiceApplication__3(): App\Presenters\Error4xxPresenter
	{
		$service = new App\Presenters\Error4xxPresenter;
		$service->injectPrimary(
			$this,
			$this->getService('application.presenterFactory'),
			$this->getService('routing.router'),
			$this->getService('http.request'),
			$this->getService('http.response'),
			$this->getService('session.session'),
			$this->getService('security.user'),
			$this->getService('latte.templateFactory')
		);
		$service->invalidLinkMode = 1;
		return $service;
	}


	public function createServiceApplication__4(): App\Presenters\ErrorPresenter
	{
		$service = new App\Presenters\ErrorPresenter($this->getService('tracy.logger'));
		return $service;
	}


	public function createServiceApplication__5(): App\Presenters\UserPresenter
	{
		$service = new App\Presenters\UserPresenter($this->getService('database.default.context'), $this->getService('userRepository'));
		$service->injectPrimary(
			$this,
			$this->getService('application.presenterFactory'),
			$this->getService('routing.router'),
			$this->getService('http.request'),
			$this->getService('http.response'),
			$this->getService('session.session'),
			$this->getService('security.user'),
			$this->getService('latte.templateFactory')
		);
		$service->invalidLinkMode = 1;
		return $service;
	}


	public function createServiceApplication__6(): App\Presenters\SignPresenter
	{
		$service = new App\Presenters\SignPresenter(
			$this->getService('01'),
			$this->getService('database.default.context'),
			$this->getService('mail.mailer'),
			$this->getService('application.linkGenerator'),
			$this->getService('latte.templateFactory')
		);
		$service->injectPrimary(
			$this,
			$this->getService('application.presenterFactory'),
			$this->getService('routing.router'),
			$this->getService('http.request'),
			$this->getService('http.response'),
			$this->getService('session.session'),
			$this->getService('security.user'),
			$this->getService('latte.templateFactory')
		);
		$service->invalidLinkMode = 1;
		return $service;
	}


	public function createServiceApplication__7(): App\Presenters\HomepagePresenter
	{
		$service = new App\Presenters\HomepagePresenter($this->getService('database.default.context'));
		$service->injectPrimary(
			$this,
			$this->getService('application.presenterFactory'),
			$this->getService('routing.router'),
			$this->getService('http.request'),
			$this->getService('http.response'),
			$this->getService('session.session'),
			$this->getService('security.user'),
			$this->getService('latte.templateFactory')
		);
		$service->invalidLinkMode = 1;
		return $service;
	}


	public function createServiceApplication__8(): NetteModule\ErrorPresenter
	{
		$service = new NetteModule\ErrorPresenter($this->getService('tracy.logger'));
		return $service;
	}


	public function createServiceApplication__9(): NetteModule\MicroPresenter
	{
		$service = new NetteModule\MicroPresenter($this, $this->getService('http.request'), $this->getService('routing.router'));
		return $service;
	}


	public function createServiceApplication__application(): Nette\Application\Application
	{
		$service = new Nette\Application\Application(
			$this->getService('application.presenterFactory'),
			$this->getService('routing.router'),
			$this->getService('http.request'),
			$this->getService('http.response')
		);
		$service->catchExceptions = true;
		$service->errorPresenter = 'Error';
		Nette\Bridges\ApplicationTracy\RoutingPanel::initializePanel($service);
		return $service;
	}


	public function createServiceApplication__linkGenerator(): Nette\Application\LinkGenerator
	{
		$service = new Nette\Application\LinkGenerator(
			$this->getService('routing.router'),
			$this->getService('http.request')->getUrl(),
			$this->getService('application.presenterFactory')
		);
		return $service;
	}


	public function createServiceApplication__presenterFactory(): Nette\Application\IPresenterFactory
	{
		$service = new Nette\Application\PresenterFactory(new Nette\Bridges\ApplicationDI\PresenterFactoryCallback($this, 1, null));
		$service->setMapping(['*' => 'App\*Module\Presenters\*Presenter']);
		return $service;
	}


	public function createServiceBookRepository(): App\Repositories\BookRepository
	{
		$service = new App\Repositories\BookRepository(
			$this->getService('database.default.context'),
			$this->getService('documentRepository'),
			$this->getService('userRepository')
		);
		return $service;
	}


	public function createServiceCache__journal(): Nette\Caching\Storages\IJournal
	{
		$service = new Nette\Caching\Storages\SQLiteJournal('/var/www/webs/skripta.ssps.cz/public_html/app/../temp/cache/journal.s3db');
		return $service;
	}


	public function createServiceCache__storage(): Nette\Caching\IStorage
	{
		$service = new Nette\Caching\Storages\FileStorage('/var/www/webs/skripta.ssps.cz/public_html/app/../temp/cache', $this->getService('cache.journal'));
		return $service;
	}


	public function createServiceContainer(): Container_8f84f7657c
	{
		return $this;
	}


	public function createServiceDatabase__default__connection(): Nette\Database\Connection
	{
		$service = new Nette\Database\Connection('mysql:host=172.27.0.20;dbname=skriptasspscz', 'skriptasspscz', 'Ohquier8eay5', ['lazy' => true]);
		$this->getService('tracy.blueScreen')->addPanel(['Nette\Bridges\DatabaseTracy\ConnectionPanel', 'renderException']);
		return $service;
	}


	public function createServiceDatabase__default__context(): Nette\Database\Context
	{
		$service = new Nette\Database\Context(
			$this->getService('database.default.connection'),
			$this->getService('database.default.structure'),
			$this->getService('database.default.conventions'),
			$this->getService('cache.storage')
		);
		return $service;
	}


	public function createServiceDatabase__default__conventions(): Nette\Database\Conventions\DiscoveredConventions
	{
		$service = new Nette\Database\Conventions\DiscoveredConventions($this->getService('database.default.structure'));
		return $service;
	}


	public function createServiceDatabase__default__structure(): Nette\Database\Structure
	{
		$service = new Nette\Database\Structure($this->getService('database.default.connection'), $this->getService('cache.storage'));
		return $service;
	}


	public function createServiceDocumentRepository(): App\Repositories\DocumentRepository
	{
		$service = new App\Repositories\DocumentRepository($this->getService('database.default.context'));
		return $service;
	}


	public function createServiceHttp__request(): Nette\Http\Request
	{
		$service = $this->getService('http.requestFactory')->createHttpRequest();
		return $service;
	}


	public function createServiceHttp__requestFactory(): Nette\Http\RequestFactory
	{
		$service = new Nette\Http\RequestFactory;
		$service->setProxy([]);
		return $service;
	}


	public function createServiceHttp__response(): Nette\Http\Response
	{
		$service = new Nette\Http\Response;
		return $service;
	}


	public function createServiceLatte__latteFactory(): Nette\Bridges\ApplicationLatte\ILatteFactory
	{
		return new class ($this) implements Nette\Bridges\ApplicationLatte\ILatteFactory {
			private $container;


			public function __construct(Container_8f84f7657c $container)
			{
				$this->container = $container;
			}


			public function create(): Latte\Engine
			{
				$service = new Latte\Engine;
				$service->setTempDirectory('/var/www/webs/skripta.ssps.cz/public_html/app/../temp/cache/latte');
				$service->setAutoRefresh(false);
				$service->setContentType('html');
				Nette\Utils\Html::$xhtml = false;
				return $service;
			}
		};
	}


	public function createServiceLatte__templateFactory(): Nette\Application\UI\ITemplateFactory
	{
		$service = new Nette\Bridges\ApplicationLatte\TemplateFactory(
			$this->getService('latte.latteFactory'),
			$this->getService('http.request'),
			$this->getService('security.user'),
			$this->getService('cache.storage'),
			null
		);
		return $service;
	}


	public function createServiceMail__mailer(): Nette\Mail\IMailer
	{
		$service = new Nette\Mail\SendmailMailer;
		return $service;
	}


	public function createServiceRouting__router(): Nette\Application\Routers\RouteList
	{
		$service = App\Router\RouterFactory::createRouter();
		return $service;
	}


	public function createServiceSecurity__passwords(): Nette\Security\Passwords
	{
		$service = new Nette\Security\Passwords;
		return $service;
	}


	public function createServiceSecurity__user(): Nette\Security\User
	{
		$service = new Nette\Security\User($this->getService('security.userStorage'), $this->getService('01'));
		return $service;
	}


	public function createServiceSecurity__userStorage(): Nette\Security\IUserStorage
	{
		$service = new Nette\Http\UserStorage($this->getService('session.session'));
		return $service;
	}


	public function createServiceSession__session(): Nette\Http\Session
	{
		$service = new Nette\Http\Session($this->getService('http.request'), $this->getService('http.response'));
		$service->setExpiration('14 days');
		$service->setOptions(['savePath' => '/var/www/webs/skripta.ssps.cz/public_html/app/../temp/sessions']);
		return $service;
	}


	public function createServiceTracy__bar(): Tracy\Bar
	{
		$service = Tracy\Debugger::getBar();
		return $service;
	}


	public function createServiceTracy__blueScreen(): Tracy\BlueScreen
	{
		$service = Tracy\Debugger::getBlueScreen();
		return $service;
	}


	public function createServiceTracy__logger(): Tracy\ILogger
	{
		$service = Tracy\Debugger::getLogger();
		return $service;
	}


	public function createServiceUserRepository(): App\Repositories\UserRepository
	{
		$service = new App\Repositories\UserRepository($this->getService('database.default.context'));
		return $service;
	}


	public function initialize()
	{
		(function () {
			$response = $this->getService('http.response');
			$response->setHeader('X-Powered-By', 'Nette Framework 3');
			$response->setHeader('Content-Type', 'text/html; charset=utf-8');
			$response->setHeader('X-Frame-Options', 'SAMEORIGIN');
			$response->setCookie('nette-samesite', '1', 0, '/', null, null, true, 'Strict');
		})();
		$this->getService('session.session')->exists() && $this->getService('session.session')->start();
		Tracy\Debugger::$editorMapping = [];
		Tracy\Debugger::getLogger()->mailer = [new Tracy\Bridges\Nette\MailSender($this->getService('mail.mailer'), null), 'send'];
	}
}
