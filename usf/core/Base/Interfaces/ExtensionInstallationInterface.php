<?php

namespace Usf\Base\Interfaces;

/**
 * Interface ModuleUsfInterface
 * @package Usf\Base\Interfaces
 */
interface ExtensionInstallationInterface
{

    /**
     * Is called on module installation
     * @return bool True if installed and false if not
     */
    public function install() : bool;

    /**
     * Is called on module activation
     * @return bool True if activated and false if not
     */
    public function activate() : bool;

    /**
     * Is called on module deactivation
     * @return bool True if deactivated and false if not
     */
    public function deactivate() : bool;

    /**
     * Is called on module deleting
     * @return bool
     */
    public function uninstall() : bool;

}