<?php

namespace Usf\Components\ConfigHandlers;

use Usf\Base\ConfigHandler;

/**
 * Class JsonConfigHandler
 * @package Usf\Core\Components
 */
class JsonConfigHandler extends ConfigHandler
{

    /**
     * File match
     * @var string
     */
    protected $fileMatch = '~\.json$~';

    /**
     * Read configuration from file
     * @return bool
     */
    protected function read()
    {
        $result = false;
        $json = file_get_contents( $this->file );
        $config = json_decode( $json, true, 512, JSON_BIGINT_AS_STRING );
        if ( ! is_null( $config ) ) {
            $this->configuration = $config;
            $result = true;
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
        $json = json_encode( $this->configuration );
        if ( $json !== false ) {
            if ( ! $result = file_put_contents( $this->filePath, $json, LOCK_EX ) ) {
                $this->addErrorMessage( 'Could not write file "' . $this->filePath . '"' );
            }
        }
        return (bool) $result;
    }

}