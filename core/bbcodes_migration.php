<?php
/**
*
* phpBB TOC
*
* @copyright (c) 2017 David OLIVIER (adadov@adadov.net)
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace adadov\pbbtoc\core;

use \phpbb\db\migration\container_aware_migration;

abstract class bbcodes_migration extends container_aware_migration {

	/** @var array BBCodes to install */
	protected static $bbcode_data;

	/** @var string */
	protected $phpbb_root_path;

	/** @var string */
	protected $php_ext;

	/**
	 * Wrapper for bbcode installation
	 * @return void
	 */
	public function install_bbcodes() {
		$bb_installer = new \adadov\pbbtoc\core\bbcodes_installer($this->db, $this->phpbb_root_path, $this->php_ext);
		$bb_installer->install_bbcodes(static::$bbcode_data);
	}
}
