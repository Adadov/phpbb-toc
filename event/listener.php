<?php
namespace adadov\ubbc\event;

use phpbb\controller\helper;
use phpbb\template\template;
use phpbb\user;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use adadov\ubbc\ext;

/**
 * Event listener
 */
class listener implements EventSubscriberInterface {
	/** @var helper */
	protected $helper;

	/** @var template */
	protected $template;

	/** @var user */
	protected $user;

	/** @var string phpBB root path */
	protected $ext_root_path;

	/**
	 * Assign functions defined in this class to event listeners in the core
	 *
	 * @return array
	 * @static
	 * @access public
	 */
	public static function getSubscribedEvents()
	{
		return array(
			// ajout de pre-parser
			'core.text_formatter_s9e_configure_after'  	=> 'configure_titles',
			//'core.text_formatter_s9e_parse_before' 		=> 'format_toc',
		);
	}

	// public function configure_toc($event) {
	// 	$cnf = $event['configurator'];

	// 	$cnf->BBCodes->addCustom('[toc]{TEXT}[/toc]', '<div id="toc">{TEXT}</div>');
	// }

	public function configure_titles($event) {
		//unset($event['configurator']->BBCodes['h1']);
		//unset($event['configurator']->tags['h1']);
		//unset($event['configurator']->BBCodes['h2']);
		//unset($event['configurator']->tags['h2']);

		$event['configurator']->BBCodes->addCustom(
			'[h1 myvalue={TEXT;useContent} linkid={ANYTHING;optional}]{TEXT1}[/h1]',
			'<h5 id="{@linkid}" class="phead level1">{@myvalue}</h5>'
		);
		$event['configurator']->tags['h1']->filterChain->append(array(__CLASS__, "filter_titles"));


		$event['configurator']->BBCodes->addCustom(
			'[h2 myvalue={TEXT;useContent} linkid={ANYTHING;optional}]{TEXT1}[/h2]',
			'<h6 id="{@linkid}" class="phead level2">{@myvalue}</h6>'
		);
		$event['configurator']->tags['h2']->filterChain->append(array(__CLASS__, "filter_titles"));


		$event['configurator']->BBCodes->addCustom(
			'[tlink myvalue={TEXT;useContent}]{TEXT}[/tlink]',
			'<a href="#">{@myvalue}</a>'
		);
		$event['configurator']->tags['tlink']->filterChain->append(array(__CLASS__, "filter_toc"));

		// $event['configurator']->BBCodes->addCustom(
		// 	'[TOC1][/TOC1]',
		// 	'<script type="text/javscript">
		// 	  function createTOC() {
   		//	    var lnks = document.getElementsByClassName("phead");
		// 	    console.log(lnks);
		// 	  };
		// 	  document.addEventListener("onload", createTOC, true);
		// 	  </script>
		// 	<div id="toc"></div>'
		// );
		// $event['configurator']->tags['TOC']->autoClose(true);
	}

	public function filter_titles($tag) {
		preg_match('/H([0-9])/', $tag->getName(), $m);
		$tag->setAttribute('level', $m[1]);
		if(!$tag->hasAttribute('linkid')) {
			$tag->setAttribute('linkid', 'test');
		}
		//echo '<pre>'.print_r($tag).'</pre>';
		return true;
	}

	public function filter_toc($tag) {
		if(!$tag->hasAttribute('myvalue')) {
			return false;
		}
		return true;
	}

	public function format_toc($event) {
		if (preg_match('/\[toc\].*\[\/toc\]/',$event['text'])) {
			$toc = array();
			$last = 0;
			preg_match_all('/\[h([1-4])(?:linkid=([^]]*))?\]([^[]+)/', $event['text'], $matches, PREG_SET_ORDER);
			foreach($matches as $match) {
				$tmp = '';
				if($last != 0) {
					if($last < $match[1]) {
						$tmp .= '[list]';
					} elseif ($last > $match[1]) {
						$tmp .= '[/list]';
					}
				}
				$tmp .= '[*] '.$match[2];
				$toc[] = $tmp;
				$last = $match[1];
			}
			$toc = implode('', $toc);
			$event['text'] = preg_replace('/\[toc\].*\[\/toc\]/', '[toc][list]'.$toc.'[/list][/toc]', $event['text']);
		}
	}
}
