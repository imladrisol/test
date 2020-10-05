<?php

namespace restapp\models\builders\order;

interface OrderBuilderInterface
{
    public function add11OrderParams(array $params): void;
    11

    /**
     * @param array $params
     */
    public function setOrderScenario(array $params): void;
}