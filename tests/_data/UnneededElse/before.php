<?php declare(strict_types = 1);

namespace PSR2R;

class UnneededElseExample {

	/**
	 * All branches have returns - should trigger on elseif and else.
	 *
	 * @param int $value
	 *
	 * @return string
	 */
	public function allBranchesHaveReturns(int $value): string {
		if ($value > 0) {
			return 'positive';
		} elseif ($value < 0) {
			return 'negative';
		} else {
			return 'zero';
		}
	}

	/**
	 * First branch has NO return - should NOT trigger.
	 *
	 * @param int $id
	 * @param string $referer
	 *
	 * @return void
	 */
	public function firstBranchNoReturn(int $id, string $referer): void {
		if ($id > 0 && $this->isPosted()) {
			$value = $this->toggle($id);
		} elseif ($id > 0 && !empty($referer)) {
			$value = $this->toggle($id);

			return;
		} else {
			$this->error();

			return;
		}

		$this->autoRender = false;
	}

	/**
	 * Only else needs to be removed - elseif also has no return.
	 *
	 * @param int $value
	 *
	 * @return string|null
	 */
	public function onlyElseUnneeded(int $value): ?string {
		if ($value > 0) {
			return 'positive';
		} elseif ($value === 0) {
			$x = 'zero';
		} else {
			return 'negative';
		}

		return null;
	}

	/**
	 * Long elseif chain with all returns.
	 *
	 * @param int $value
	 *
	 * @return string
	 */
	public function longChainAllReturns(int $value): string {
		if ($value > 100) {
			return 'very positive';
		} elseif ($value > 0) {
			return 'positive';
		} elseif ($value < 0) {
			return 'negative';
		} else {
			return 'zero';
		}
	}

	/**
	 * @param int $id
	 *
	 * @return void
	 */
	private function isPosted(): void {
	}

	/**
	 * @param int $id
	 *
	 * @return void
	 */
	private function toggle(int $id): void {
	}

	/**
	 * @return void
	 */
	private function error(): void {
	}

}
