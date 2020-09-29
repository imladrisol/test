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
    public function addOrderParams(array $params): void;

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