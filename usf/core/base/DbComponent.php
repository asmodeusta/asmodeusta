<?php

namespace Usf\Core\Base;


class DbComponent extends Component
{

    protected $db;

    public function __construct()
    {
        $this->db = db();
    }

}