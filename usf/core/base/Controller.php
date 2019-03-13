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

    /**
     * Default error action
     */
    public function actionDefaultError()
    {
        echo 'Page not found(';
    }

}