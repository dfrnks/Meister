<?php

namespace Meister\Meister\Libraries;

use Pimple\Container;

class i18n{

	private $lang;

	private $app;

	public function __construct(Container $app){
		$this->app = $app;

		$language = 'pt_BR';

		if (isset($_REQUEST['lang']) && $_REQUEST['lang']) {
			if (preg_match("/^\w{2}_\w{2}$/", $_REQUEST['lang'])) {
				$language = $_REQUEST['lang'];
			}
		}
		if (!$language && isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
			if (function_exists('locale_accept_from_http')) {
				$language = locale_accept_from_http($_SERVER["HTTP_ACCEPT_LANGUAGE"]);
			}
		}

		$this->lang = $language;
	}

	public function init(){
		putenv('LANGUAGE='. $this->lang);
		setlocale(LC_ALL, "C.UTF-8");
		bindtextdomain('messages', $this->app['BASE_DIR'] . '/i18n');
		bind_textdomain_codeset('messages', 'UTF-8');
		textdomain('messages');
	}
}