<?php

namespace Usf\Core\Base;

use Usf\Core\Components\Database;

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