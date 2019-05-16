<?php

namespace Usf\Base\Interfaces;

use Usf\Base\Extension;

/**
 * Interface FactoryInterface
 * @package Usf\Base\Interfaces
 */
interface FactoryInterface
{

    /**
     * @param $params
     * @return Extension|Null
     */
    public function create($params);

}