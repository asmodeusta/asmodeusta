<?php

namespace Usf\Components\Factories;

use Usf\Base\Interfaces\FactoryInterface;
use Usf\Base\ModuleUsf;
use Usf\Base\Traits\Cacheable;

class ModulesFactory implements FactoryInterface
{

    use Cacheable;

    protected $db;

    protected $modules = [];

    public function __construct()
    {
        $this->db = db();

        $this->readModules();
    }

    public function create($slag)
    {
        $module = null;
        if (array_key_exists($slag, $this->modules)) {
            if ($this->issetCache($slag)) {
                $module = $this->cache($slag);
            } else {
                $moduleItem = $this->modules[ $slag ];
                $filePath = DIR_MODULES . DS . $moduleItem[ 'path' ] . DS . $moduleItem[ 'file' ];
                if (is_file($filePath)) {
                    require_once $filePath;
                    $moduleClass = lastDeclaredClass();
                    $module = new $moduleClass(
                        $moduleItem[ 'slag' ],
                        $moduleItem[ 'name' ],
                        $moduleItem[ 'description' ],
                        $moduleItem[ 'version' ],
                        true
                    );
                }
            }
            if ($module instanceof ModuleUsf) {
                $this->cache($slag, $module);
            } else {
                $module = null;
            }
        }
        return $module;
    }

    public function getModules()
    {
        return $this->modules;
    }

    public function installModule($slag, $name, $description, $version, $path, $file)
    {

    }

    public function activateModule($slag)
    {

    }

    public function deactivateModule($slag)
    {

    }

    public function uninstallModule($slag)
    {

    }

    protected function readModules()
    {
        $sql = 'select slag, name, description, version, path, file from usf_modules where active = 1';
        if ($result = $this->db->query($sql, $this->db::FETCH_ASSOC)) {
            foreach ($result as $item) {
                $this->modules[ array_shift($item) ] = $item;
            }
            return true;
        }
        return false;
    }

}