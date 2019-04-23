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
     * @return callable|false Callable on success. False when callback not found.
     */
    public function getCallback(array $params);

}