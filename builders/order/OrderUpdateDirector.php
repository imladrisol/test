<?php

namespace restapp\models\builders\order;

use marketplace\common\models\log\LogError;
use restapp\components\MarketException;
use restapp\models\Order;
use Yii;

/**
 * Class OrderUpdateDirector
 *
 * Ensures the creation of an order in fixed combinations
 *
 * @package builders
 * @property OrderBuilderInterface $builder
 * @property array $params
 */
class OrderUpdateDirector
{
    /**
     * @var OrderBuilderInterface $builder
     */
    private $builder;

    private $params = [];

    /**
     * @param OrderBuilderInterface $builder
     */
    public function setBuilder(OrderBuilderInterface $builder): void
    {
        $this->builder = $builder;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * OrderUpdateDirector constructor.
     * @param OrderBuilderInterface $builder
     * @param array $params
     */
    public function __construct(OrderBuilderInterface $builder, array $params)
    {
        $this->setBuilder($builder);
        $this->setParams($params);
    }

    /**
     * Update Order without nested objects
     *
     * @return Order
     * @throws MarketException
     */
    public function updateOrder(): Order
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $this->builder->addOrderParams($this->params);

            /**
             * @var Order $order
             */
            $order = $this->builder->getOrder();

            if (!$order->save()) {
                throw new MarketException(MarketException::ERROR_CHECK_CORRECTNESS_DATA, $order->errors);
            }

            $transaction->commit();
        } catch (MarketException $e) {
            $transaction->rollBack();
            if (empty($e->details)) {
                LogError::insertException($e);
                throw new MarketException($e->getCode(), $e->getMessage());
            } else {
                throw new MarketException($e->getCode(), $e->details);
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            LogError::insertException($e, "Rest_order_update_exception");
            throw new MarketException(MarketException::ERROR_UNEXPECTED_ERROR, $e->getMessage());
        }

        $order->refresh();
        return $order;
    }

    /**
     * Update Order with Purchase
     *
     * @return Order
     * @throws MarketException
     */
    public function updateOrderWithPurchase(): Order
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $this->builder->addOrderParams($this->params);
            $this->builder->addOrderPurchase($this->params);

            /**
             * @var Order $order
             */
            $order = $this->builder->getOrder();

            if (!$order->save()) {
                throw new MarketException(MarketException::ERROR_CHECK_CORRECTNESS_DATA, $order->errors);
            }

            $transaction->commit();
        } catch (MarketException $e) {
            $transaction->rollBack();
            if (empty($e->details)) {
                LogError::insertException($e);
                throw new MarketException($e->getCode(), $e->getMessage());
            } else {
                throw new MarketException($e->getCode(), $e->details);
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            LogError::insertException($e, "Rest_order_with_purchase_update_exception");
            throw new MarketException(MarketException::ERROR_CHECK_CORRECTNESS_DATA, $e->getMessage());
        }

        $order->refresh();
        return $order;
    }

    /**
     * @return Order
     * @throws MarketException
     */
    public function updateOrderAction(): Order
    {
        $this->beforeAction();

        if ($this->builder->isOrderCanceledByUser($this->params)) {
            /**
             * @var Order $order
             */
            $order = $this->builder->getOrder();
            $order->refresh();
        } elseif ($this->builder->getOrderScenario() == Order::SCENARIO_UPDATE_BY_API_WITH_PURCHASES) {
            $order = $this->updateOrderWithPurchase();
        } else {
            $order = $this->updateOrder();
        }

        return $order;
    }


    public function beforeAction(): void
    {
        $this->builder->setOrderScenario($this->params);
        $this->builder->checkOrderParams($this->params);
        $this->builder->logOrderSellerAction($this->params);
    }
}