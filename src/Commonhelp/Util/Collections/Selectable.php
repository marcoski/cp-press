<?php

namespace Commonhelp\Util\Collections;

use Closure;
interface Selectable
{
    /**
     * Selects all elements from a selectable that match the expression and
     * returns a new collection containing these elements.
     *
     *
     * @return Collection
     */
    public function matching(Closure $match);
}