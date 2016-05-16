<?php

namespace App\Repositories\Traits;

trait TransformableTrait
{
    /**
     * @return array
     */
    public function transform()
    {
        return $this->toArray();
    }
}
