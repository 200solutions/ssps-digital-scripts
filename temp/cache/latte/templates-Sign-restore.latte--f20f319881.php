<?php
// source: /var/www/webs/skripta.ssps.cz/public_html/app/presenters/templates/Sign/restore.latte

use Latte\Runtime as LR;

class Templatef20f319881 extends Latte\Runtime\Template
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
?>
    <div class='background'></div>
    <div class='sign-page'>
        <div class='form-wrapper'>
            <h1>Obnova nebo změna hesla</h1>
<?php
		if (isset($error)) {
			?>                <el-alert class='sign-error' title='Chyba' type='error' description='<?php echo LR\Filters::escapeHtmlAttr($error) /* line 11 */ ?>' show-icon></el-alert>
<?php
		}
		if (isset($success)) {
			?>                <el-alert class='sign-success' title='Úspěch' type='success' description='<?php echo LR\Filters::escapeHtmlAttr($success) /* line 15 */ ?>' show-icon></el-alert>
<?php
		}
		$form = $_form = $this->global->formsStack[] = $this->global->uiControl["passwordRestoreForm"];
		?>            <form class='sign-form'<?php
		echo Nette\Bridges\FormsLatte\Runtime::renderFormBegin(end($this->global->formsStack), array (
		'class' => NULL,
		), false) ?>>
                
                <label>E-Mail
                    <input type='email'
                           placeholder='Jan.Novak@email.cz'
                           value="<?php echo LR\Filters::escapeHtmlAttr($user ? $user->getIdentity()->email : '') /* line 24 */ ?>"<?php
		$_input = end($this->global->formsStack)["email"];
		echo $_input->getControlPart()->addAttributes(array (
		'type' => NULL,
		'placeholder' => NULL,
		'value' => NULL,
		))->attributes() ?>>
                </label>
                <el-button type='primary' native-type='submit'>Odeslat</el-button>
<?php
		echo Nette\Bridges\FormsLatte\Runtime::renderFormEnd(array_pop($this->global->formsStack), false);
?>            </form>
            <div class='info-box'>
                <span>E-mail nedorazil? Nezapomeňte zkontrolovat spam adresář.</span>
            </div>
            <div class='info-credit'>Copyright © <?php echo LR\Filters::escapeHtmlText(date('Y')) /* line 34 */ ?> | SSPŠ</div>
        </div>
    </div>
<?php
	}

}
