<?php

namespace restapp\models\delivery;

use marketplace\common\models\delivery\Meest as Core;
use restapp\components\MarketException;

/**
 * Class Meest
 * @package restapp\models\delivery
 */
class Meest extends Core
{

    /**
     * @param array $params
     * @return array
     * @throws MarketException
     */
    public function search(array $params): array
    {
        try {
            $result = parent::search($params);
        } catch (\Throwable $e) {
            throw new MarketException(MarketException::ERROR_UNEXPECTED_ERROR);
        }
        $this->checkErrors();
        return $result;
    }

    /**
     * @param array $params
     * @return array
     * @throws MarketException
     */
    public function calculate(array $params): array
    {
        try {
            $result = parent::calculate($params);
        } catch (\Throwable $e) {
            throw new MarketException(MarketException::ERROR_UNEXPECTED_ERROR);
        }
        $this->checkErrors();
        return $result;
    }

    /**
     * @param array $params
     * @return array
     * @throws MarketException
     */
    public function createTtn(array $params): array
    {
        try {
            parent::createTtn($params);
        } catch (\Throwable $e) {
            throw new MarketException(MarketException::ERROR_UNEXPECTED_ERROR);
        }
        $this->checkErrors();
        return $this->registerTtn();
    }

    /**
     * @return array
     * @throws MarketException
     */
    public function registerTtn(): array
    {
        try {
            $result = parent::registerTtn();
        } catch (\Throwable $e) {
            throw new MarketException(MarketException::ERROR_UNEXPECTED_ERROR);
        }
        $this->checkErrors();
        return $result;
    }

    /**
     * @throws MarketException
     */
    private function checkErrors(): void
    {
        if ($this->hasErrors()) {
            throw new MarketException(MarketException::ERROR_CHECK_CORRECTNESS_DATA, $this->errors);
        }
    }
}