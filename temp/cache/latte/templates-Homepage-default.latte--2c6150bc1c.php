<?php
// source: /var/www/webs/skripta.ssps.cz/public_html/app/presenters/templates/Homepage/default.latte

use Latte\Runtime as LR;

class Template2c6150bc1c extends Latte\Runtime\Template
{
	public $blocks = [
		'content' => 'blockContent',
	];

	public $blockTypes = [
		'content' => 'html',
	];


	function main()
	{
		extract($this->params);
		if ($this->getParentName()) return get_defined_vars();
		$this->renderBlock('content', get_defined_vars());
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		if (!$this->getReferringTemplate() || $this->getReferenceType() === "extends") {
			if (isset($this->params['post'])) trigger_error('Variable $post overwritten in foreach on line 78');
		}
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockContent($_args)
	{
		extract($_args);
?>
    <ssps-header
    :carousels='[
    {"name":"Učení ti teď půjde lépe","content":"Skripta o tom co učíme, od těch co to učí, pro ty co se to učí.","type":"image","media":"https://skripta.ssps.cz/client/images/hero2-min.jpg"},
    {"name":"Digitální skripta Smíchovské průmyslovky","content":"Vzdělávání mimo školní lavice.","type":"image","media":"https://skripta.ssps.cz/client/images/hero1-min.jpg"},
    {"name":"Vzdělávání online","content":"Brána k modernímu vzdělávání.","type":"image","media":"https://skripta.ssps.cz/client/images/hero4-min.jpg"}]'

:news='[{"date":{"day":"03","month":"SRP"},"icon":"exclamation-triangle","link":"http:\/\/ssps.200solutions.com\/ucitelum-pomaha-i-studentske-radio\/","title":"U\u010ditel\u016fm pom\u00e1h\u00e1 i studentsk\u00e9 r\u00e1dio","content":"\u010cl\u00e1nek z 7.4. z MF Dnes o na\u0161ich aktivit\u00e1ch v dob\u011b pandemie najdete ZDE:"},{"date":{"day":"02","month":"SRP"},"icon":"calendar-alt","link":"http:\/\/ssps.200solutions.com\/informace-pro-rodice-studentu\/","title":"Informace pro rodi\u010de student\u016f","content":"V\u00e1\u017een\u00ed rodi\u010de, dovolte mi abych&#8230;"},{"date":{"day":"02","month":"SRP"},"icon":"bullhorn","link":"http:\/\/ssps.200solutions.com\/testovaci-aktualita\/","title":"Testovac\u00ed aktualita","content":"Na p\u0159elomu pond\u011bln\u00edho ve\u010dera a \u00fatern\u00edho r\u00e1na jsme vyrazili z Prahy sm\u011brem..."},{"date":{"day":"29","month":"\u010cVC"},"icon":"file-alt","link":"http:\/\/ssps.200solutions.com\/2-kolo-prijimaciho-rizeni-neorganizujeme\/","title":"2. kolo p\u0159ij\u00edmac\u00edho \u0159\u00edzen\u00ed &#8211; NEORGANIZUJEME","content":"2. kolo p\u0159ij\u00edmac\u00edho \u0159\u00edzen\u00ed SSP\u0160 neorganizuje. SSP\u0160 naplnila kapacitu v\u0161ech obor\u016f vzd\u011bl\u00e1n\u00ed v 1. kole."}]'
:video='{"video":"<iframe title=\"Sm\u00edchovsk\u00e1 st\u0159edn\u00ed pr\u016fmyslov\u00e1 \u0161kola\" width=\"640\" height=\"360\" src=\"https:\/\/www.youtube.com\/embed\/lrfJOG97VMU?feature=oembed\" frameborder=\"0\" allow=\"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen><\/iframe>","image":"http:\/\/ssps.200solutions.com\/wp-content\/uploads\/2020\/07\/miachel.png","text":"O na\u0161\u00ed \u0161kole"}'
systems="http://ssps.200solutions.com/studenti/systemy/"
more="Více"
:defaultbackground="'http://ssps.200solutions.com/wp-content/themes/ssps/images/school.jpg'"
    >
    <template v-slot:logo>
 <a href="https://skripta.ssps.cz" aria-label="Hlavní stránka">
   <img src="https://www.ssps.cz/wp-content/themes/ssps-wordpress-theme/images/logo.svg" alt>
 </a>
</template>
<template v-slot:navigation>
 <div class="menu-menu-1-container">
   <ul id="menu-menu-1" class="menu">
     <li
       id="menu-item-209"
       class="menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-22 current_page_item menu-item-209"
     >
       <a aria-current="page" href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Homepage:default")) ?>">Hlavní stránka</a>
     </li>
<?php
		if ($presenter->getUser()->isLoggedIn()) {
?>
     <li
       id="menu-item-285"
       class="menu-item menu-item-type-post_type menu-item-object-page menu-item-285"
     >
            <a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("User:default#books", [$presenter->getUser()->id])) ?>">Můj profil</a>
     </li>
<?php
		}
?>
     <li
       id="menu-item-215"
       class="menu-item menu-item-type-post_type menu-item-object-page menu-item-215"
     >
<?php
		if ($presenter->getUser()->isLoggedIn()) {
			?>             <a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Sign:out")) ?>">Odhlásit</a>
<?php
		}
		else {
			?>             <a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Sign:in")) ?>">Přihlásit</a>
<?php
		}
?>
     </li>
   </ul>
 </div>
</template>
    </ssps-header>
    <div id='main_wrapper__homepage'>
        <section class='section-book-carousel'>
            <book-section></book-section>
        </section>
        <section id='section-news'>
            <h2>Novinky</h2>
            <el-row :gutter='50'>
                <el-col :xs='24' :sm='12'>
                    <h3>Naši redaktoři</h3>
                    <div class='top-authors'>
                        <redactors-list></redactors-list>
                    </div>
                </el-col>
                <el-col :xs='24' :sm='12'>
                    <div>
                        <h3>Aktuality</h3>
                        <ul class='news-list'>
<?php
		$iterations = 0;
		foreach ($news as $post) {
?>
                                <li>
                                    <div class='news-list__icon'>
                                        <div class='news-list__date'>
                                            <div class='news-list__date__day'><?php echo LR\Filters::escapeHtmlText(($this->filters->date)($post->created_at, '%d')) /* line 82 */ ?></div>
                                            <div class='news-list__date__month'><?php echo LR\Filters::escapeHtmlText(($this->filters->truncate)(($this->filters->upper)(($this->filters->date)($post->created_at, 'F')), 3, '')) /* line 83 */ ?></div>
                                        </div>
                                    </div>
                                    <div class='news-list__content'>
                                        <h4><?php echo LR\Filters::escapeHtmlText($post->title) /* line 87 */ ?></h4>
                                        <div class='news-meta'>
                                            <span><i class='el-icon-date'></i> <?php echo LR\Filters::escapeHtmlText(($this->filters->date)($post->created_at, '%d.%m. %Y')) /* line 90 */ ?></span>
                                        </div>
                                        <p><?php echo LR\Filters::escapeHtmlText($post->content) /* line 92 */ ?></p>
                                    </div>
                                </li>
<?php
			$iterations++;
		}
?>
                        </ul>
                    </div>
                </el-col>
            </el-row/>
        </section>
        <section id='section-ladder'>
            <h2>Žebříček</h2>
            <ladder></ladder>
        </section>
        <footer>
            <div class='credits'>
                <span class='credits__copyright'>Copyright © <?php echo LR\Filters::escapeHtmlText(date('Y')) /* line 110 */ ?> | Smíchovská střední průmyslová škola</span>
                <span class='credits__author'>Code & Design by <a href='https://200solutions.com'>200solutions</a></span>
            </div>
        </footer>
    </div>
<?php
	}

}
