<?php

namespace Usf\Site\Views;

use Usf\Base\View;

class MainHeartView extends View
{

    protected function render()
    {
        if ( $template = $this->module->getTemplateFile( 'main/heart' ) ) {
            include $template;
        }
    }

}