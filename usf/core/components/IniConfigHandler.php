<?php

namespace Usf\Core\Components;

use Usf\Core\Base\ConfigHandler;

class IniConfigHandler extends ConfigHandler
{

    protected $fileMatch = '~^([a-zA-Z][\w]+)\.ini$~';

    protected function validateFile( $file )
    {
        if ( $result = parent::validateFile( $file ) ) {
            $result = preg_match( $this->fileMatch, $file );
        }
        return $result;
    }

    protected function read()
    {
        $this->configuration = parse_ini_file( $this->file );
    }

    protected function write()
    {
        $file = fopen( $this->filePath, 'w+' );
    }

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