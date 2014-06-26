<?php

namespace Langosh\component\dataGrid\filter\condition\doctrine;

/**
 * LikeWrapped - doctrine filter condition
 * @author Jan Svatoš <svatosja@gmail.com>
 */
class LikeWrapped extends Like {

	protected function getQueryValue() {
		return '%' . $this->getValue() . '%';
	}

}

