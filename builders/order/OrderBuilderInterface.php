<?php

namespace restapp\models\builders\order;

interface OrderBuilderInterface
{

    /**
     * Change Order fields and purchase object
     *
     * @param array $params
     */
    public function addOrderPurchase(array $params): void;

    /**
     * @param array $params
     */
    public function setOrderScenario(array $params): void;
}