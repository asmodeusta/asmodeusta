<?php

namespace Usf\Core\Base;

/**
 * Class EmptyConfigHandler
 * @package Usf\Core\Components
 */
class EmptyConfigHandler extends ConfigHandler
{

    /**
     * @return bool
     */
    protected function read()
    {
        return true;
    }

    /**
     * @return bool
     */
    protected function write()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function save()
    {
        return true;
    }
}