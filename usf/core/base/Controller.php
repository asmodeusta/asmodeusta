<?php

namespace Usf\Core\Base;

use Usf\Core\Base\Exceptions\ControllerException;

/**
 * Class Controller
 * @package Usf\Core\Base
 */
abstract class Controller extends Component
{
    /**
     * Module
     * @var Module
     */
    protected $module;

    /**
     * Ba
     * @var string
     */
    protected $basename;

    /**
     * Controller constructor.
     * @param Module $module
     */
    public function __construct( Module $module )
    {
        $this->module = $module;
        $shortName = $this->getReflector()->getShortName();
        $this->basename = lcfirst( substr( $shortName, 0, strpos( 'Controller', $shortName ) ) );
    }

    /**
     * Get action
     * @param $actionName
     * @return array
     * @throws ControllerException
     */
    public function getAction( $actionName )
    {
        //$action = [ $this, 'actionDefaultError' ];
        $actionMethodName = 'action' . ucfirst( $actionName );
        if ( method_exists( $this, $actionMethodName ) ) {
            $action = [ $this, $actionMethodName ];
        } else {
            throw new ControllerException( 'Action "' . $actionName . '" does not exist!' );
        }
        return $action;
    }

    public function getBaseName()
    {
        return $this->basename;
    }

    /**
     * Default error action
     */
    public function actionDefaultError()
    {
        echo 'Page not found(';
    }

}