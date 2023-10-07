<?php declare(strict_types = 1);

namespace PSR2R;

class FixMe
{
    public function notEnoughWhitespace()
    {
        if ($x >$y) {
			return ;;
        }
        if ($y> $z) {
        }
    }

    public function tooMuchWhitespace()
    {
        if ($x  > $y) {
        }
        if ($y >  $z) {
        }
    }
}
