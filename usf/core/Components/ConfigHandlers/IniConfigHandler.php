<?php

namespace Usf\Components\ConfigHandlers;

use Usf\Base\ConfigHandler;

/**
 * Class IniConfigHandler
 * @package Usf\Core\Components
 */
class IniConfigHandler extends ConfigHandler
{

    /**
     * File match
     * @var string
     */
    protected $fileMatch = '~\.ini$~';

    /**
     * Read configuration from file
     * @return bool
     */
    protected function read()
    {
        $result = parse_ini_file( $this->file, true );
        if ( $result !== false ) {
            $this->configuration = $result;
        }
        return $result;
    }

    /**
     * Write configuration to file
     * @return bool
     */
    protected function write()
    {
        $content = $this->convertArrayToIni( $this->configuration );
        if ( ! $result = file_put_contents( $this->filePath, $content, LOCK_EX ) ) {
            $this->addErrorMessage( 'Could not write file "' . $this->filePath . '"' );
        }
        return (bool) $result;
    }

    /**
     * Converts array to ini-string
     * @param array $arr
     * @param array $parent
     * @return string
     */
    private function convertArrayToIni(array $arr, array $parent = [] )
    {
        $result = '';
        foreach ( $arr as $key => $value )
        {
            if ( is_array( $value ) )
            {
                //subsection case
                //merge all the sections into one array...
                $sec = array_merge( (array) $parent, (array) $key );
                //add section information to the output
                $result .= '[' . join( '.', $sec ) . ']' . PHP_EOL;
                //recursively traverse deeper
                $result .= $this->convertArrayToIni( (array) $key, $sec );
            }
            else
            {
                //plain key->value case
                $result .= "$key=$value" . PHP_EOL;
            }
        }
        return $result;
    }


}