<?php
// source: /var/www/webs/skripta.ssps.cz/public_html/app/presenters/templates/Sign/in.latte

use Latte\Runtime as LR;

class Template0fb1937502 extends Latte\Runtime\Template
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
            <h1>Přihlásit se</h1>
<?php
		if (isset($error)) {
			?>                <el-alert class='sign-error' title='Chyba' type='error' description='<?php echo LR\Filters::escapeHtmlAttr($error) /* line 11 */ ?>' show-icon></el-alert>
<?php
		}
		if (isset($success)) {
			?>                <el-alert class='sign-success' title='Úspěch' type='success' description='<?php echo LR\Filters::escapeHtmlAttr($success) /* line 15 */ ?>' show-icon></el-alert>
<?php
		}
		$form = $_form = $this->global->formsStack[] = $this->global->uiControl["signInForm"];
		?>            <form class='sign-form'<?php
		echo Nette\Bridges\FormsLatte\Runtime::renderFormBegin(end($this->global->formsStack), array (
		'class' => NULL,
		), false) ?>>
                
                <label>E-Mail
                    <input type='email' placeholder='Jan.Novak@email.cz'<?php
		$_input = end($this->global->formsStack)["email"];
		echo $_input->getControlPart()->addAttributes(array (
		'type' => NULL,
		'placeholder' => NULL,
		))->attributes() ?>>
                </label>
                <label>Heslo
                    <span><a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Sign:restore")) ?>">Zapomenuté heslo?</a></span>
                    <input type='password' placeholder='****'<?php
		$_input = end($this->global->formsStack)["password"];
		echo $_input->getControlPart()->addAttributes(array (
		'type' => NULL,
		'placeholder' => NULL,
		))->attributes() ?>>
                </label>
                <el-button type='primary' native-type='submit'>Přihlásit se</el-button>
<?php
		echo Nette\Bridges\FormsLatte\Runtime::renderFormEnd(array_pop($this->global->formsStack), false);
?>            </form>
            <div class='info-box'>
                Ještě nemáte účet? <span><a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Sign:up")) ?>">Založte si ho!</a></span>
            </div>
            <div class='info-credit'>Copyright © <?php echo LR\Filters::escapeHtmlText(date('Y')) /* line 36 */ ?> | SSPŠ</div>
        </div>
    </div>
<?php
	}

}
