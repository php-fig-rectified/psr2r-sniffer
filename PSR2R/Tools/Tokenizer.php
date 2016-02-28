<?php

namespace PSR2R\Tools;

use Exception;
use PHP_CodeSniffer;

class Tokenizer {

	const STANDARD = 'PSR2R/ruleset.xml';

	/**
	 * @var string
	 */
	protected $root;

	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var bool
	 */
	protected $verbose;

	/**
	 * @param array $argv
	 * @throws \Exception
	 */
	public function __construct($argv) {

		$file = !empty($argv[1]) ? $argv[1] : null;
		if (!$file || !file_exists($file)) {
			throw new Exception('Please provide a valid file.');
		}
		$file = realpath($file);

		$this->root = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR;
		$this->path = $file;
		$this->verbose = !empty($argv[2]) && in_array($argv[2], ['--verbose', '-v']);
	}

	/**
	 * @return void
	 */
	public function tokenize() {
		$_SERVER['argv'] = [];
		$_SERVER['argv'][] = '--encoding=utf8';

		$standard = $this->root . self::STANDARD;
		$_SERVER['argv'][] = '--standard=' . $standard;

		$_SERVER['argv'][] = $this->path;
		$_SERVER['argc'] = count($_SERVER['argv']);
		$res = [];
		$tokens = $this->_getTokens($this->path);
		$array = file($this->path);
		foreach ($array as $key => $row) {
			$res[] = rtrim($row);
			$tokenStrings = $this->_tokenize($key + 1, $tokens);
			if ($tokenStrings) {
				foreach ($tokenStrings as $string) {
					$res[] = '// ' . $string;
				}
			}
		}
		$content = implode(PHP_EOL, $res);
		echo 'Tokenizing: ' . $this->path . PHP_EOL;
		$newPath = dirname($this->path) . DIRECTORY_SEPARATOR . pathinfo($this->path, PATHINFO_FILENAME) . '.tokens.' . pathinfo($this->path, PATHINFO_EXTENSION);
		file_put_contents($newPath, $content);
		echo 'Token file: ' . $newPath . PHP_EOL;
	}

	/**
	 * @param string $path Path
	 * @return array Tokens
	 */
	protected function _getTokens($path) {
		$phpcs = new PHP_CodeSniffer();
		$phpcs->process([], $this->root . self::STANDARD, []);
		$file = $phpcs->processFile($path);
		$file->start();
		return $file->getTokens();
	}

	/**
	 * @param int $row Current row
	 * @param array $tokens Tokens array
	 * @return array
	 */
	protected function _tokenize($row, $tokens) {
		$pieces = [];
		foreach ($tokens as $key => $token) {
			if ($token['line'] > $row) {
				break;
			}
			if ($token['line'] < $row) {
				continue;
			}
			if ($this->verbose) {
				$type = $token['type'];
				$content = $token['content'];
				$content = '`' . str_replace(["\r\n", "\n", "\r", "\t"], ['\r\n', '\n', '\r', '\t'], $content) . '`';

				unset($token['type']);
				unset($token['content']);
				$token['content'] = $content;

				$tokenList = [];
				foreach ($token as $k => $v) {
					if (is_array($v)) {
						if (empty($v)) {
							continue;
						}
						$v = json_encode($v);
					}
					$tokenList[] = $k . '=' . $v;
				}
				$pieces[] = $type . ' (' . $key . ') ' . implode(', ', $tokenList);
			} else {
				$pieces[] = $token['type'];
			}
		}
		if ($this->verbose) {
			return $pieces;
		}
		return [implode(' ', $pieces)];
	}

}
