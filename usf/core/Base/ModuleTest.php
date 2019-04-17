<?php

namespace Usf\Base;

class ModuleTest extends Component
{

    protected $information = [
        'name' => 'Module test',
        'description' => 'This is basic module class',
        'version' => '0.0.1',
        'author' => [
            'name' => 'asmodeusta',
            'email' => 'asmodeusta@gmail.com',
            'site' => 'asmodeusta.com'
        ]
    ];

    protected $active = false;

    public function __construct()
    {

    }

    public function initialize()
    {

    }

    public function configure()
    {

    }

    public function run( $controller, $action )
    {

    }

    public function activate()
    {

    }

    public function deactivate()
    {

    }

    public function delete()
    {

    }

}