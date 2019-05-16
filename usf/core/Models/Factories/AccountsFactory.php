<?php

namespace Usf\Models\Factories;

use Usf\Base\Interfaces\FactoryInterface;
use Usf\Base\Traits\Cacheable;
use Usf\Components\Database;

class AccountsFactory implements FactoryInterface
{

    use Cacheable;

    /**
     * @var Database
     */
    protected $db;

    public function __construct()
    {
        $this->db = db();

    }

    public function create($id)
    {
        if ( $this->issetCache($id) ) {

        }
    }
}