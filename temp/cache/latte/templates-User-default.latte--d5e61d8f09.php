<?php
// source: /var/www/webs/skripta.ssps.cz/public_html/app/presenters/templates/User/default.latte

use Latte\Runtime as LR;

class Templated5e61d8f09 extends Latte\Runtime\Template
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
		/* line 3 */
		$this->createTemplate('../_header.latte', ['plain' => false, 'active' => 'profile'] + $this->params, "include")->renderToContentType('html');
?>
	<div id='main_wrapper__users'>
		<el-row :gutter='50'>
			<el-col :xs='24' :sm='6'>
				<el-menu router
						 :default-active='$router.currentRoute.path'
						 class='user-menu'>
					<el-menu-item index='/books'><i class='el-icon-s-management'></i> Knihy</el-menu-item>
					<el-menu-item index='/notifications'><i class='el-icon-message-solid'></i> Upozornění</el-menu-item>
					<el-menu-item index='/account'><i class='el-icon-s-custom'></i> Účet</el-menu-item>
					<el-menu-item index='/management'><i class='el-icon-s-tools'></i> Správa</el-menu-item>
					<el-menu-item index='/feedback'><i class='el-icon-s-opportunity'></i> Zpětná vazba</el-menu-item>
				</el-menu>
			</el-col>
			<el-col :xs='24' :sm='18'>
				<div>
					<router-view></router-view>
					<div v-if=" $router.currentRoute.path == '/account' ">
						<h3>Profilový obrázek</h3>
<?php
		$form = $_form = $this->global->formsStack[] = $this->global->uiControl["profilePictureForm"];
		?>					    <form<?php
		echo Nette\Bridges\FormsLatte\Runtime::renderFormBegin(end($this->global->formsStack), array (
		), false) ?>>
							
<?php
		if (isset($error)) {
			?>				                <el-alert class='sign-error' title='Chyba' type='error' description='<?php echo LR\Filters::escapeHtmlAttr($error) /* line 33 */ ?>' show-icon></el-alert>
<?php
		}
		?>					        <input type='file' placeholder='C:/images/image_01.jpg'<?php
		$_input = end($this->global->formsStack)["file"];
		echo $_input->getControlPart()->addAttributes(array (
		'type' => NULL,
		'placeholder' => NULL,
		))->attributes() ?>>
					        <el-button type='primary' native-type='submit'>Změnit</el-button>
<?php
		echo Nette\Bridges\FormsLatte\Runtime::renderFormEnd(array_pop($this->global->formsStack), false);
?>					    </form>
						<h3>Heslo</h3>
					    <form>
							<label>Změna hesla
								<a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Sign:restore")) ?>">
									<el-button type='primary' icon='el-icon-top-right'>Vyžádat změnu hesla</el-button>
								</a>
							</label>
					    </form>
					</div>
				</div>
			</el-col>
		</el-row>
        <footer>
            <div class='credits'>
                <span class='credits__copyright'>Copyright © <?php echo LR\Filters::escapeHtmlText(date('Y')) /* line 57 */ ?> | Smíchovská střední průmyslová škola</span>
                <span class='credits__author'>Code & Design by <a href='https://200solutions.com'>200solutions</a></span>
            </div>
        </footer>
	</div>
<?php
	}

}
