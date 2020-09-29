<?php
// source: /var/www/webs/skripta.ssps.cz/public_html/app/presenters/templates/Books/default.latte

use Latte\Runtime as LR;

class Template38ea151da8 extends Latte\Runtime\Template
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
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockContent($_args)
	{
		extract($_args);
		if ($id != -1) {
			?>        <book-root :target="<?php echo LR\Filters::escapeHtmlAttr($id) /* line 5 */ ?>"></book-root>
<?php
		}
		else {
?>
        <error title='Učebnice nenalezena'
               message='Učebnice s tímto identifikátorem nebyla v naší databázi nalezena. Pravděpodobně došlo k jejímu odebrání nebo učebnice neexistuje.'
               code='error 404'></error>
<?php
		}
		
	}

}
