<?php

namespace Usf\Base\Interfaces;

/**
 * Interface ModuleInterface
 * @package Core\Base\Interfaces
 */
interface ModuleInterface
{

    /**
     * @param array $params
     * @return callable|false
     */
    public function getCallback(array $params);

}