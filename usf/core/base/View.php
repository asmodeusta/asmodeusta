<?php

namespace Usf\Core\Base;

/**
 * Class View
 * @package Usf\Core\Base
 */
abstract class View extends DataStorage
{

    /**
     * @var Module
     */
    protected $module;

    /**
     * View constructor.
     * @param Module $module
     * @param array $data
     */
    public function __construct( Module $module, array $data = [] )
    {
        parent::__construct( $data );
        $this->module = $module;

        $this->render();
    }

    protected function getTemplateFile( $name )
    {
        return $this->module->getTemplateFile( $name );
    }

    /**
     * @return mixed
     */
    abstract protected function render();

}