<?php

namespace Fnp\Dto\Contracts;

interface AccessesModel
{
    /**
     * Sets the model for the usage in the attribute
     *
     * @param  object  $model
     *
     * @return void
     */
    public function setModel(object $model): void;
}
