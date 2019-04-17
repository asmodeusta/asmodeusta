<?php

namespace Usf\Base;

use Usf\Components\Database;

class Model extends Component
{

    /**
     * @var Database
     */
    protected static $db = null;

    public static function init()
    {
        if ( is_null( self::$db ) ) {
            self::$db = db();
        }
    }

}

Model::init();