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

use phpbb\db\driver\driver_interface;
use adadov\pbbtoc\core\file_loader;

class bbcodes_installer {
	/** @var \acp_bbcodes */
	protected $acp_bbcodes;

	/**
	 * Constructeur
	 * @param  driver_interface $db
	 * @param  type             $phpbb_root_path
	 * @param  type             $php_ext
	 * @return void
	 */
	public function __construct(driver_interface $db, $phpbb_root_path, $php_ext) {
		$this->db = $db;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;

		$this->acp_bbcodes = $this->get_acp_bbcodes();
	}

	protected function get_acp_bbcodes() {
		if (!class_exists('acp_bbcodes')) {
			include $this->phpbb_root_path . 'includes/acp/acp_bbcodes.' . $this->php_ext;
		}

		return new \acp_bbcodes();
	}

	/**
	 * Installation des bbcodes
	 * @param  array $bbcodes Tableau contenant les bbcodes à installer
	 * @return void
	 */
	public function install_bbcodes(array $bbcodes) {
		foreach ($bbcodes as $bb_name => $bb_data) {
			$bb_data = $this->build_bbcode($bb_data);

			if ($bbcode = $this->bbcode_exists($bb_name, $bb_data['bbcode_tag'])) {
				$this->update_bbcode($bbcode, $bb_data);
			} else {
				$this->add_bbcode($bb_data);
			}
		}
	}

	/**
	 * Obtenir l'id le plus élevé actuellement dans la table bbcodes
	 * @return int
	 * @access protected
	 */
	protected function get_max_id() {
		$sql = 'SELECT MAX(' . $this->db->sql_escape('bbcode_id') . ') AS maximum FROM ' . BBCODES_TABLE;
		$result = $this->db->sql_query($sql);
		$maximum = $this->db->sql_fetchfield('maximum');
		$this->db->sql_freeresult($result);

		return (int) $maximum;
	}

	/**
	 * Construire le bbcode
	 *
	 * @param  array $bb_data Tableau contenant les données initiales
	 * @return array Tableau complété
	 * @access protected
	 */
	protected function build_bbcode(array $bb_data) {
		error_log('BBTPL: '.$bb_data['bbcode_tpl']);
		if (preg_match('/##FILE#(.*)?#/', $bb_data['bbcode_tpl'], $m)) {
			error_log('File:'.$this->phpbb_root_path.$m[1]);
			if(!file_exists($this->phpbb_root_path.$m[1])) {
				error_log('Impossible de trouver le fichier !!');
			} else {
				$content = file_loader::load($this->phpbb_root_path.$m[1]);
				error_log('Content:'.print_r($content, true));
				$bb_data['bbcode_tpl'] = preg_replace('/##FILE#[^#]*#/', $content, $bb_data['bbcode_tpl']);
				error_log('BBTPL: '.$bb_data['bbcode_tpl']);
			}
		}
		$data = $this->acp_bbcodes->build_regexp($bb_data['bbcode_match'], $bb_data['bbcode_tpl']);

		$bb_data = array_replace($bb_data, array(
			'bbcode_tag'          => $data['bbcode_tag'],
			'first_pass_match'    => $data['first_pass_match'],
			'first_pass_replace'  => $data['first_pass_replace'],
			'second_pass_match'   => $data['second_pass_match'],
			'second_pass_replace' => $data['second_pass_replace'],
		));

		return $bb_data;
	}

	/**
	 * Vérifiee si ce bbcode est déjà existant
	 * @param  type $bb_name Nom du bbcode
	 * @param  type $bb_tag  Nom du tag
	 * @return mixed Renvoi le tableau de ses données si le bbcode existe or renvoi false
	 * @access protected
	 */
	protected function bbcode_exists($bb_name, $bb_tag) {
		$sql = 'SELECT bbcode_id FROM '.BBCODES_TABLE.
			" WHERE LOWER(bbcode_tag) = '".$this->db->sql_escape(strtolower($bb_name))."'".
			" OR LOWER(bbcode_tag) = '".$this->db->sql_escape(strtolower($bb_tag))."'";
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return $row;
	}

	/**
	 * Mettre à jour un bbcode
	 * @param  array $old_bb Données de l'ancien bbcode
	 * @param  array $new_bb Données du nouveau bbcode
	 * @return void
	 * @access protected
	 */
	protected function update_bbcode(array $old_bb, array $new_bb) {
		$sql = 'UPDATE '.BBCODES_TABLE.' SET '.$this->db->sql_build_array('UPDATE', $new_bb).
			' WHERE bbcode_id = '.(int) $old_bb['bbcode_id'];
		$this->db->sql_query($sql);
	}

	/**
	 * Ajouter un bbcode
	 * @param  array $bb_data Données du bbcode
	 * @return void
	 * @access protected
	 */
	protected function add_bbcode(array $bb_data) {
		$bbcode_id = $this->get_max_id() + 1;

		// L'ID ne doit pas être dans le range réservé du système
		if ($bbcode_id <= NUM_CORE_BBCODES) {
			$bbcode_id = NUM_CORE_BBCODES + 1;
		}

		// L'ID ne doit pas dépasser le max définit
		if ($bbcode_id <= BBCODE_LIMIT) {
			$bb_data['bbcode_id'] = (int) $bbcode_id;
			if (!array_key_exists('display_on_posting', $bb_data)) {
				$bb_data['display_on_posting'] = 1;
			}
			$this->db->sql_query('INSERT INTO '.BBCODES_TABLE.' '.$this->db->sql_build_array('INSERT', $bb_data));
		}
	}
}
