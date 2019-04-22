<?php

namespace Usf\Base\Interfaces;

use Usf\Base\ModuleUsf;

/**
 * Interface FactoryInterface
 * @package Usf\Base\Interfaces
 */
interface FactoryInterface
{

    /**
     * @param $params
     * @return ModuleUsf|Null
     */
    public function create($params);

}