<?php

namespace Usf\Base;

abstract class ModuleUsf extends Component
{

    protected $information = [
        'slag' =>'module-test',
        'name' => 'Module test',
        'description' => 'This is basic module class',
        'version' => '0.0.1',
        'author' => [
            'name' => 'asmodeusta',
            'email' => 'asmodeusta@gmail.com',
            'site' => 'asmodeusta.com'
        ]
    ];

    protected $active;

    public function __construct( $slag, $name, $description, $version, $active = true )
    {

    }

    public function initialize()
    {

    }

    public function configure()
    {

    }

    public function getCallback( $args )
    {

    }

    /**
     *
     */
    public function install()
    {

    }

    public function activate()
    {

    }

    public function deactivate()
    {

    }

    public function uninstall()
    {

    }
    /**
     *
     */


}