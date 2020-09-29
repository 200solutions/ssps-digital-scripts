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
			if (isset($this->params['post'])) trigger_error('Variable $post overwritten in foreach on line 27');
		}
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockContent($_args)
	{
		extract($_args);
		/* line 3 */
		$this->createTemplate('../_header.latte', ['plain' => false, 'active' => 'home'] + $this->params, "include")->renderToContentType('html');
?>
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
                                            <div class='news-list__date__day'><?php echo LR\Filters::escapeHtmlText(($this->filters->date)($post->created_at, '%d')) /* line 31 */ ?></div>
                                            <div class='news-list__date__month'><?php echo LR\Filters::escapeHtmlText(($this->filters->truncate)(($this->filters->upper)(($this->filters->date)($post->created_at, 'F')), 3, '')) /* line 32 */ ?></div>
                                        </div>
                                    </div>
                                    <div class='news-list__content'>
                                        <h4><?php echo LR\Filters::escapeHtmlText($post->title) /* line 36 */ ?></h4>
                                        <div class='news-meta'>
                                            <span><i class='el-icon-date'></i> <?php echo LR\Filters::escapeHtmlText(($this->filters->date)($post->created_at, '%d.%m. %Y')) /* line 39 */ ?></span>
                                        </div>
                                        <p><?php echo LR\Filters::escapeHtmlText($post->content) /* line 41 */ ?></p>
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
                <span class='credits__copyright'>Copyright © <?php echo LR\Filters::escapeHtmlText(date('Y')) /* line 59 */ ?> | Smíchovská střední průmyslová škola</span>
                <span class='credits__author'>Code & Design by Marek Kejda</span>
            </div>
        </footer>
    </div>
<?php
	}

}
