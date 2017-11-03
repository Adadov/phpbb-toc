<?php
/**
*
* PhpBB TOC
*
* @copyright (c) 2017 David OLIVIER (adadov@adadov.net)
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace adadov\pbbtoc\migrations;

use adadov\pbbtoc\core\file_loader;


class v100_install_bbcodes extends \adadov\pbbtoc\core\bbcodes_migration {

	/**
	 * {@inheritdoc}
	 */
	public function effectively_installed() {
		return false;
//		return isset($this->config['pbbtoc_version']) && version_compare($this->config['pbbtoc_version'], '1.0.0', '>=');
	}

	/**
	 * {@inheritdoc}
	 */
	public function update_data() {
		return array(
			array('config.add', array('pbbtoc_version', '1.0.0')),
			array('custom', array(array($this, 'install_bbcodes'))),
		);
	}

	/**
	 * {@inheritdoc}
	 */
	protected static $bbcode_data = array(
		// 'planet' => array(
		// 	'bbcode_helpline'	=> 'Lien vers une planète',
		// 	'bbcode_match'		=> '[planet]{NUMBER1}:{NUMBER2}:{NUMBER3}[/planet]',
		// 	'bbcode_tpl'		=> '<a href="https://s150-fr.ogame.gameforge.com/game/index.php?page=galaxy&galaxy={NUMBER1}&system={NUMBER2}&position={NUMBER3}">[{NUMBER1}:{NUMBER2}:{NUMBER3}]</a>',
		// ),
		'h1' => array(
			'bbcode_helpline'	=> 'Titre de premier niveau',
			'bbcode_match'		=> '[h1 myvalue={TEXT;useContent} linkid={ANYTHING;optional}]{TEXT1}[/h1]',
			'bbcode_tpl'		=> '<h5 id="{@linkid}" class="phead level1">{@myvalue}</h5>',
		),
		'h2' => array(
			'bbcode_helpline'	=> 'Titre de second niveau',
			'bbcode_match'		=> '[h2 myvalue={TEXT;useContent} linkid={ANYTHING;optional}]{TEXT1}[/h2]',
			'bbcode_tpl'		=> '<h6 id="{@linkid}" class="phead level2">{@myvalue}</h6>',
		),
		'h3' => array(
			'bbcode_helpline'	=> 'Titre de second niveau',
			'bbcode_match'		=> '[h3 myvalue={TEXT;useContent} linkid={ANYTHING;optional}]{TEXT1}[/h3]',
			'bbcode_tpl'		=> '<h7 id="{@linkid}" class="phead level2">{@myvalue}</h7>',
		),
		'h4' => array(
			'bbcode_helpline'	=> 'Titre de second niveau',
			'bbcode_match'		=> '[h4 myvalue={TEXT;useContent} linkid={ANYTHING;optional}]{TEXT1}[/h4]',
			'bbcode_tpl'		=> '<h8 id="{@linkid}" class="phead level2">{@myvalue}</h8>',
		),
		'toc' => array(
			'bbcode_helpline'	=> 'Afficher la table des matières',
			'bbcode_match'		=> '[toc][/toc]',
			'bbcode_tpl'		=> '<div id="toc"></div><script type="text/javascript">##FILE#ext/adadov/pbbtoc/toc-loader.js#</script>',
		),
	);
}
