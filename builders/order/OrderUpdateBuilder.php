<?php

namespace restapp\models\builders\order;

use restapp\components\MarketException;
use restapp\models\Order;

/**
 * Class OrderUpdateBuilder
 * @package builder
 *
 * This class
 * @property Order $order
 * @property integer $orderId
 */
class OrderUpdateBuilder implements OrderBuilderInterface
{
    /**
     * @var Order $order
     */
    private $order;

    private $orderId;

    /**
     * @param mixed $orderId
     */
    public function setOrderId($orderId): void
    {
        $this->orderId = $orderId;
    }

    private function reset(): void
    {
        $this->order = Order::findOne($this->orderId);
    }

    /**
     * @return Order|null
     */
    public function getOrder()
    {
        /**
         * @var Order $order
         */
        $order = $this->order;
        $this->reset();
        return $order;
    }

    /**
     * OrderUpdateBuilder constructor.
     * @param $id
     */
    public function __construct($id)
    {
        $this->setOrderId($id);
        $this->reset();
    }

    /**
     * @param array $params
     */
    public function setOrderScenario(array $params): void
    {
        $scenario = $this->order->getUpdateScenario($params);
        $this->order->setScenario($scenario);
    }

    /**
     * @return string
     */
    public function getOrderScenario(): string
    {
        return $this->order->scenario;
    }

    /**
     * @param array $params
     * @throws MarketException
     */
    public function addOrderParams(array $params): void
    {
        if (!$this->order->load($params, '')) {
            $this->order->addError('status', 'empty_params');
            throw new MarketException(MarketException::ERROR_CHECK_CORRECTNESS_DATA, 'empty_params');
        }
    }

    /**
     * @param array $params
     * @throws MarketException
     */
    public function addOrderPurchase(array $params): void
    {
        if (!$this->order->loadPurchases($params)) {
            throw new MarketException(MarketException::ERROR_CHECK_CORRECTNESS_DATA, $this->order->errors);
        }
    }

    /**
     * @param array $params
     * @throws \restapp\components\MarketException
     */
    public function checkOrderParams(array $params): void
    {
        $this->order->checkTtn($params);
        $this->order->isUpdateLockedByPurchase($params);
    }

    /**
     * @param array $params
     * @return bool
     * @throws \Throwable
     * @throws \yii\httpclient\Exception
     */
    public function isOrderCanceledByUser(array $params): bool
    {
        return $this->order->checkAndCancelOrderByUser($params);
    }

    /**
     * @param array $params
     * @throws \ReflectionException
     */
    public function logOrderSellerAction(array $params): void
    {
        $this->order->logSellerAction($params);
    }
}