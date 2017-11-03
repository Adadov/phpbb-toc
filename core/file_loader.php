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

class file_loader {
	protected $filename;
	protected $content;
	protected $output;

	public function __construct($filename) {
		$this->filename = $filename;
		$this->content = file_get_contents($filename);
		$this->output = $this->execute($this->content);
	}

	public function execute($content) {
		$content = $this->stripComments($content);
		$content = $this->stripWhitespace($content);

		return $content;
	}

	static function load($file) {
		$o = new file_loader($file);
		return $o->output();
	}

	public function output() {
		return $this->output;
	}

	protected function stripWhitespace($content) {
		$content = str_replace(array("\r\n", "\r"), "\n", $content);
		$content = preg_replace('/[^\S\n]+/', ' ', $content);
		$content = str_replace(array(" \n", "\n "), "\n", $content);
		$content = preg_replace('/\n+/', "\n", $content);
		error_log('[DEBUG] Content 1:'.$content);
		$contentf = preg_replace('/\n/', " ", $content);
		error_log('[DEBUG] Content 2:'.$contentf);

		return $contentf;
	}

	protected function stripComments($content) {
        // single-line comments
        $content = preg_replace('/\/\/.*$/m', '', $content);
        // multi-line comments
        $content = preg_replace('/\/\*.*?\*\//s', '', $content);

        return $content;
    }
}
