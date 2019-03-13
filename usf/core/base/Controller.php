<?php

namespace Usf\Core\Base;


class Controller
{

    public function getAction( $actionName )
    {
        $action = [ $this, 'actionDefaultError' ];
        $actionMethodName = 'action' . ucfirst( $actionName );
        if ( method_exists( $this, $actionMethodName ) ) {
            $action = [ $this, $actionMethodName ];
        }
        return $action;
    }

    public function actionDefaultError()
    {
        echo 'Page not found(';
    }

}