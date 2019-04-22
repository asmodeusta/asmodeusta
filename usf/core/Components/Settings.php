<?php

namespace Usf\Components;

use Usf\Base\Configuration;

/**
 * Class Settings
 * @package Usf\Core\Components
 */
class Settings extends Configuration
{

    /**
     * @param string $name
     * @param mixed $value
     * @return bool
     */
    public function validate($name, &$value) : bool
    {
        $result = false;
        switch ($name) {
            case 'theme':
                $result = $this->validateTheme($value);
                break;
        }
        return $result;
    }

    /**
     * @return mixed
     */
    public function getTheme()
    {
        return $this->get('theme');
    }

    /**
     * @param mixed $theme
     */
    public function setTheme($theme) : void
    {
        $this->set('theme', $theme);
    }

    /**
     * @param string $theme
     * @return bool
     */
    protected function validateTheme(&$theme)
    {
        $themesDirs = scandir(DIR_THEMES);
        if (in_array($theme, $themesDirs) && $this->config[ 'theme' ] !== $theme) {
            return true;
        } else {
            if (in_array('default', $themesDirs)) {
                $theme = 'default';
                return true;
            }
        }
        return false;
    }


}