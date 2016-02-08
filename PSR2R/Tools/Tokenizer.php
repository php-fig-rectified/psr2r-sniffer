<?php
namespace PSR2R\Tools;

class Tokenizer {

	const STANDARD = 'PSR2R/ruleset.xml';

	/**
	 * @param string $path Path to file to tokenize.
	 * @param bool $verbose Verbose flag.
	 * @return void
	 */
	public function tokenize($path, $verbose) {
		$path = realpath($path);

		$_SERVER['argv'] = [];
		$_SERVER['argv'][] = '--encoding=utf8';

		$standard = self::STANDARD;
		$_SERVER['argv'][] = '--standard=' . $standard;

		$_SERVER['argv'][] = $path;
		$_SERVER['argc'] = count($_SERVER['argv']);
		$res = [];
		$tokens = $this->_getTokens($path);
		$array = file($path);
		foreach ($array as $key => $row) {
			$res[] = rtrim($row);
			if ($tokenStrings = $this->_tokenize($key + 1, $tokens, $verbose)) {
				foreach ($tokenStrings as $string) {
					$res[] = '// ' . $string;
				}
			}
		}
		$content = implode(PHP_EOL, $res);
		echo 'Tokenizing: ' . $path . PHP_EOL;
		$newPath = dirname($path) . DIRECTORY_SEPARATOR . pathinfo($path, PATHINFO_FILENAME) . '.tokens.' . pathinfo($path, PATHINFO_EXTENSION);
		file_put_contents($newPath, $content);
		echo 'Token file: ' . $newPath . PHP_EOL;
	}

	/**
	 * @param string $path Path
	 * @return array Tokens
	 */
	protected function _getTokens($path) {
		$phpcs = new \PHP_CodeSniffer();
		$phpcs->process([], self::STANDARD, []);
		$file = $phpcs->processFile($path);
		$file->start();
		return $file->getTokens();
	}

	/**
	 * @param int $row Current row
	 * @param array $tokens Tokens array
	 * @param bool $verbose Verbose flag
	 * @return array
	 */
	protected function _tokenize($row, $tokens, $verbose) {
		$pieces = [];
		foreach ($tokens as $key => $token) {
			if ($token['line'] > $row) {
				break;
			}
			if ($token['line'] < $row) {
				continue;
			}
			if ($verbose) {
				$type = $token['type'];
				$content = $token['content'];
				$content = str_replace(["\r\n", "\n", "\r", "\t"], ['\r\n', '\n', '\r', '\t'], $content);

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
		if ($verbose) {
			return $pieces;
		}
		return [implode(' ', $pieces)];
	}

	/**
	 * Convert options to string
	 *
	 * @param array $options Options array
	 * @return string Results
	 */
	protected static function _optionsToString($options) {
		if (empty($options) || !is_array($options)) {
			return '';
		}
		$results = '';
		foreach ($options as $option => $value) {
			if (strlen($results) > 0) {
				$results .= ' ';
			}
			if (empty($value)) {
				$results .= "--$option";
			}
			else {
				$results .= "--$option=$value";
			}
		}
		return $results;
	}

}
