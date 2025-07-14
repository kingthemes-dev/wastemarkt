<?php
/**
 * Created by PhpStorm.
 * User: Villatheme-Thanh
 * Date: 30-09-19
 * Time: 8:18 AM
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//$_SERVER['HTTP_USER_AGENT']='/google.com';

class WOOMULTI_CURRENCY_Plugin_Google_Index {
	protected $settings;

	public function __construct() {
		$this->settings = WOOMULTI_CURRENCY_Data::get_ins();
		add_action( 'init', array( $this, 'set_default_currency_if_isbot' ), 999 );
	}

	public function set_default_currency_if_isbot() {
		if ( $this->settings->get_enable() && empty( $_GET['wmc-currency'] ) ) {
			if ( $this->is_google_bot() ) {
				$this->settings->set_current_currency( apply_filters( 'wmc_set_currency_for_google_bot_index', $this->settings->get_default_currency() ) );
			} elseif ( $this->isBot() ) {
				$this->settings->set_current_currency( apply_filters( 'wmc_set_currency_for_bot_index', $this->settings->get_default_currency() ) );
			}
		}
	}

	public function is_google_bot() {
		$google_bots = apply_filters( 'wmc_google_bots_list', array(
			'googlebot',
			'google-sitemaps',
			'appEngine-google',
			'feedfetcher-google',
			'googlealert.com',
			'AdsBot-Google',
			'google'
		) );
		foreach ( $google_bots as $bot ) {
			if ( self::check_bot( $bot ) ) {
				return true;
			}
		}

		return false;
	}

	public function isBot() {
		$bots = apply_filters( 'wmc_other_bots_list', array(
//			'pixel',//confused with google pixel device
			'facebook',
			'rambler',
			'aport',
			'yahoo',
			'msnbot',
			'turtle',
			'mail.ru',
			'omsktele',
			'yetibot',
			'picsearch',
			'sape.bot',
			'sape_context',
			'gigabot',
			'snapbot',
			'alexa.com',
			'megadownload.net',
			'askpeter.info',
			'igde.ru',
			'ask.com',
			'qwartabot',
			'yanga.co.uk',
			'scoutjet',
			'similarpages',
			'oozbot',
			'shrinktheweb.com',
			'aboutusbot',
			'followsite.com',
			'dataparksearch',
			'liveinternet.ru',
			'xml-sitemaps.com',
			'agama',
			'metadatalabs.com',
			'h1.hrn.ru',
			'seo-rus.com',
			'yaDirectBot',
			'yandeG',
			'yandex',
			'yandexSomething',
			'Copyscape.com',
			'domaintools.com',
			'Nigma.ru',
			'bing.com',
			'dotnetdotcom',
		) );
		foreach ( $bots as $bot ) {
			if ( self::check_bot( $bot ) ) {
				return true;
			}
		}

		return false;
	}

	private static function check_bot( $bot ) {
		return isset( $_SERVER['HTTP_USER_AGENT'] ) && ( stripos( $_SERVER['HTTP_USER_AGENT'], $bot ) !== false || preg_match( '/bot|crawl|slurp|spider|mediapartners/i', $_SERVER['HTTP_USER_AGENT'] ) );
	}
}