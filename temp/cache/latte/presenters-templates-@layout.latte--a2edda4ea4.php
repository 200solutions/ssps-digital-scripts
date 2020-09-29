<?php
// source: /var/www/webs/skripta.ssps.cz/public_html/app/presenters/templates/@layout.latte

use Latte\Runtime as LR;

class Templatea2edda4ea4 extends Latte\Runtime\Template
{
	public $blocks = [
		'scripts' => 'blockScripts',
	];

	public $blockTypes = [
		'scripts' => 'html',
	];


	function main()
	{
		extract($this->params);
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset='utf-8'>
		<meta name='viewport' content='width=device-width'>
		<title>Smíchovská střední průmyslová škola | Skripta</title>
		<link rel='stylesheet' href='<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 11 */ ?>/client/styles/element.css'>
		<link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.7.2/css/all.css'>
		<link rel='stylesheet' href='<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 13 */ ?>/client/styles/style.css'>
		<link rel='stylesheet' href='<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 14 */ ?>/client/styles/editor.css'>
		<link rel='stylesheet' href='<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 15 */ ?>/client/styles/prism.css'>
		<link rel='stylesheet' href='<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 16 */ ?>/client/styles/owl-carousel.css'>
		<link rel='stylesheet' href='<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 17 */ ?>/client/styles/_home.css'>
		<link rel='stylesheet' href='<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 18 */ ?>/client/styles/_header.css'>
		<link rel='stylesheet' href='<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 19 */ ?>/client/styles/_user.css'>
		<link rel='stylesheet' href='<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 20 */ ?>/client/styles/_sign.css'>
		<link rel='stylesheet' href='<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 21 */ ?>/client/styles/_book.css'>
		<link rel='stylesheet' href='<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 22 */ ?>/client/styles/code-mirror.css'>
		<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/katex@0.11.1/dist/katex.min.css'>
		<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Source+Sans+Pro&display=swap'>
		<script async src='https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js'></script>
		<script>
			(adsbygoogle = window.adsbygoogle || []).push({
			google_ad_client: 'ca-pub-2934445377419280',
			enable_page_level_ads: true
			});
		</script>
		<script async src='https://www.googletagmanager.com/gtag/js?id=UA-161327025-1'></script>
	</head>
	<body class='tex2jax_ignore'>
<?php
		$iterations = 0;
		foreach ($flashes as $flash) {
			?>		<div<?php if ($_tmp = array_filter(['flash', $flash->type])) echo ' class="', LR\Filters::escapeHtmlAttr(implode(" ", array_unique($_tmp))), '"' ?>><?php
			echo LR\Filters::escapeHtmlText($flash->message) /* line 40 */ ?></div>
<?php
			$iterations++;
		}
?>
		<div id='ie-fallback'>
			<div class='error-box'>
		        <!-- Nadpis -->
		        <h2 class='error-title'>Nepodporovaný prohlížeč</h2>
		        <!-- Obsah -->
		        <p class='error-message'>Tyto stránky nejsou optimalizované pro Internet Explorer. Použijte prosím jakýkoliv jiný prohlížeč.</p>
		        <!-- Chybový kód -->
		        <p class='error-code'>error glb-01</p>
		    </div>
		</div>
		<noscript>
			<div class='error-box error-no-js'>
		        <!-- Nadpis -->
		        <h2 class='error-title'>JavaScript je vypnutý</h2>
		        <!-- Obsah -->
		        <p class='error-message'>Tyto stránky nemohou fungovat bez povoleného JavaScriptu. Zkontrolujte nastavení svého prohlížeče.</p>
		        <!-- Chybový kód -->
		        <p class='error-code'>error glb-02</p>
		    </div>
		</noscript>
		<div id='cover'></div>
	    <div id='app'>
<?php
		$this->renderBlock('content', $this->params, 'html');
?>
	    </div>
<?php
		if ($this->getParentName()) return get_defined_vars();
		$this->renderBlock('scripts', get_defined_vars());
?>
	</body>
</html>
<?php
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		if (!$this->getReferringTemplate() || $this->getReferenceType() === "extends") {
			if (isset($this->params['flash'])) trigger_error('Variable $flash overwritten in foreach on line 40');
		}
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockScripts($_args)
	{
		extract($_args);
		?>			<script src='<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 73 */ ?>/client/lib/vue-dev.js'></script>
			<script src='<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 74 */ ?>/client/lib/vue-loader.js'></script>
			<script src='<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 75 */ ?>/client/lib/vue-router.js'></script>
			<script src='<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 76 */ ?>/client/lib/sass-compiler.js'></script>
			<script src='<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 77 */ ?>/client/lib/jquery.js'></script>
			<script src='<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 78 */ ?>/client/lib/element.js'></script>
			<script src='<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 79 */ ?>/client/lib/element-czech.js'></script>
			<script src='<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 80 */ ?>/client/lib/prism.js'></script>
			<script src='<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 81 */ ?>/client/lib/owl-carousel.js'></script>
			<script src='https://cdn.jsdelivr.net/npm/lodash@4.17.15/lodash.min.js'></script>

			<script src='<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 85 */ ?>/client/model/ie-fallback.js'></script>

			<script defer src='https://cdn.jsdelivr.net/npm/katex@0.11.1/dist/katex.min.js'></script>

			<script src='//cdn.jsdelivr.net/npm/sortablejs@1.8.4/Sortable.min.js'></script>
			<script src='//cdnjs.cloudflare.com/ajax/libs/Vue.Draggable/2.20.0/vuedraggable.umd.min.js'></script>

			<script src='<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 95 */ ?>/bootstrap.js'></script>
<?php
	}

}