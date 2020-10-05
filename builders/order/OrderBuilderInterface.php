<?php

namespace restapp\models\builders\order;

interface OrderBuilderInterface
{
    /**
     * Order update.
     * Change the order fields except nested objects
     *
     * @param array $params
     */
    public function add11OrderParams(array $params): void;


    /**
     * @param array $params
     */
    public function setOrderScenario(array $params): void;
}