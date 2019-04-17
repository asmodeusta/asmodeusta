<?php

namespace Usf\Components\ConfigHandlers;

use Usf\Base\ConfigHandler;

/**
 * Class PhpConfigHandler
 * @package Usf\Core\Components
 */
class PhpConfigHandler extends ConfigHandler
{

    /**
     * File match
     * @var string
     */
    protected $fileMatch = '~\.([\w]+)$~';

    /**
     * Read configuration from file
     * @return bool
     */
    protected function read()
    {
        $result = false;
        $this->configuration = include $this->file;
        if ( is_array( $this->configuration ) ) {
            $result = true;
        } else {
            $this->configuration = [];
        }
        return $result;
    }

    /**
     * Write configuration to file
     * @return bool
     */
    protected function write()
    {
        $result = false;
        if ( file_put_contents( $this->filePath,
            '<?php return ' . var_export( $this->configuration, true ) . ';',
            LOCK_EX ) ) {
            $result = true;
        }
        return $result;
    }


}