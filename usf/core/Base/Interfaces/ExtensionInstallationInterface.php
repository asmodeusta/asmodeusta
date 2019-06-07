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
     * @return bool True if installed and False if not
     */
    public function install() : bool;

    /**
     * Is called on module activation
     * @return bool True if activated and False if not
     */
    public function activate() : bool;

    /**
     * Is called on module deactivation
     * @return bool True if deactivated and False if not
     */
    public function deactivate() : bool;

    /**
     * Is called on module deleting
     * @return bool True if uninstalled and False if not
     */
    public function uninstall() : bool;

}