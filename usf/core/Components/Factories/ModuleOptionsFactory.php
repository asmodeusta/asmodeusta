<?php

namespace Usf\Components\Factories;

use Usf\Base\Interfaces\FactoryInterface;
use Usf\Base\Traits\Cacheable;
use Usf\Models\Options;

class ModuleOptionsFactory implements FactoryInterface
{

    use Cacheable;

    public function create($module)
    {
        if ($this->issetCache($module)) {
            return $this->cache($module);
        } else {
            $options = new Options($module);
            $this->cache($module, $options);
            return $options;
        }
    }

}