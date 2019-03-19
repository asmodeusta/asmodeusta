<?php

namespace Usf\Core\Components;

use Usf\Core\Base\Component;
use Usf\Core\Base\Factories\ConfigHandlerFactory;

class Settings extends Component
{

    protected $configHandler;

    protected $theme;

    protected $modified;

    public function __construct( $configFile )
    {
        $this->configHandler = ConfigHandlerFactory::create( $configFile );
        $this->parseConfig();
    }

    protected function parseConfig()
    {
        $config = $this->configHandler->getFullConfig();

        /**
         * Theme
         */
        $this->theme = if_set( $config[ 'theme' ], null );
    }

    public function saveConfig()
    {
        if ( $this->modified ) {
            $this->configHandler->setFullConfig( [
                'theme' => $this->theme
            ] );
            $this->configHandler->save();
        }
    }

    /**
     * @return mixed
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @param mixed $theme
     */
    public function setTheme( $theme ): void
    {
        $themesDirs = scandir( DIR_USF . DS. 'themes' );
        if ( in_array( $theme, $themesDirs ) && $this->theme !== $theme ) {
            $this->theme = $theme;
            $this->modified = true;
        }
    }



}