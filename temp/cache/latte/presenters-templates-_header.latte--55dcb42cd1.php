<?php
// source: /var/www/webs/skripta.ssps.cz/public_html/app/presenters/templates/_header.latte

use Latte\Runtime as LR;

class Template55dcb42cd1 extends Latte\Runtime\Template
{
	public $blocks = [
		'header' => 'blockHeader',
	];

	public $blockTypes = [
		'header' => 'html',
	];


	function main()
	{
		extract($this->params);
		if ($this->getParentName()) return get_defined_vars();
		$this->renderBlock('header', get_defined_vars());
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockHeader($_args)
	{
		extract($_args);
?>
    <div class='top-bar' style=''>
		<div class='container'>
			<el-row>
				<el-col :xs='24' :sm='18'>
					<ul class='contact-details'>
						<li>
                            <a target='_blank' href='https://bakalari.ssps.cz'><i class='fa fa-list-ol'></i> Bakaláři</a>
						</li>
						<li>
                            <a target='_blank' href='https://www.plus4u.net'><i class='fa fa-university'></i> Virtuální škola</a>
						</li>
						<li>
                            <a target='_blank' href='http://ples.ssps.cz'><i class='fas fa-ticket-alt'></i> Rezervace - Maturitní ples</a>
						</li>
                        <li>
                            <a target='_blank' href='http://bezpecnost.ssps.cz'><i class='fa fa-lock'></i> Kybernetická bezpečnost</a>
						</li>
                        <li>
                            <a target='_blank' href='http://presloviny.cz'><i class='fab fa-product-hunt'></i> Presloviny</a>
						</li>
                        <li>
                            <a target='_blank' href='http://divcispolek.cz'><i class='fa fa-venus'></i> Dívčí spolek</a>
						</li>
                        <li>
                            <a target='_blank' href='https://ssps.cz'><i class='fas fa-compass'></i> SSPŠ</a>
                        </li>
					</ul>
				</el-col>
				<el-col :xs='24' :sm='6'>
					<ul class='social-list'>
						<li><a href='https://www.facebook.com/smichovskasps'><i class='fab fa-facebook-f'></i></a></li>
                        <li><a href='https://www.instagram.com/sspsprague/'><i class='fab fa-instagram'></i></a></li>
                        <li><a href='https://www.youtube.com/channel/UCMNG0yuP9X1cuoqrxrSVijA'><i class='fab fa-youtube'></i></a></li>
					</ul>
				</el-col>
			</el-row>
		</div>
	</div>
    <div<?php if ($_tmp = array_filter([$plain ? 'header--plain' : null, 'header'])) echo ' class="', LR\Filters::escapeHtmlAttr(implode(" ", array_unique($_tmp))), '"' ?>>
        <a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Homepage:")) ?>">
            <div class='header__logo'>
                <img src='<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 46 */ ?>/client/images/logo.svg'>
            </div>
        </a>
        <h1>Skripta</h1>
        <h2 style='display: none;'>Digitální skripta Smíchovské střední průmyslové školy</h2>
        <div id='menu'>
            <span class='line line1'></span>
            <span class='line line2'></span>
            <span class='line line3'></span>
        </div>
        <div id='menu-items'>
<?php
		if ($presenter->getUser()->isLoggedIn()) {
			?>                <span class='header__item'><a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Sign:out")) ?>">Odhlásit</a></span>
<?php
		}
		else {
			?>                <span class='header__item'><a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Sign:in")) ?>">Přihlásit</a></span>
<?php
		}
		if ($presenter->getUser()->isLoggedIn()) {
			?>                <span<?php if ($_tmp = array_filter([$active == 'profile' ? 'is-active' : null, 'header__item'])) echo ' class="', LR\Filters::escapeHtmlAttr(implode(" ", array_unique($_tmp))), '"' ?>>
                    <a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("User:default#books", [$presenter->getUser()->id])) ?>">Můj profil</a>
                </span>
<?php
		}
		?>            <span<?php if ($_tmp = array_filter([$active == 'home' ? 'is-active' : null, 'header__item'])) echo ' class="', LR\Filters::escapeHtmlAttr(implode(" ", array_unique($_tmp))), '"' ?>>
                <a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Homepage:default")) ?>">Domů</a>
            </span>
        </div>
    </div>
<?php
	}

}
