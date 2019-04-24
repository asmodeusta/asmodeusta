<?php

namespace Usf\Components\Factories;

use Usf\Base\Interfaces\FactoryInterface;
use Usf\Base\ModuleExtension;
use Usf\Base\Traits\Cacheable;
use Usf\Components\Database;

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
                    $module = require_once $filePath;
                }
            }
            if ($module instanceof ModuleExtension) {
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

    public function installModule(string $path, string $file)
    {
        if ($module = $this->getModuleObject($path, $file)) {
            $slag = $module->getSlag();
            if (!array_key_exists($slag, $this->modules)) {
                if ($this->addModule(
                    $slag,
                    $module->getName(),
                    $module->getDescription(),
                    $module->getVersion(),
                    $path,
                    $file
                )) {
                    return $module->install();
                }
            }
        }
        return false;
    }

    public function activateModule($slag)
    {
        if (array_key_exists($slag, $this->modules) && !$this->modules[ $slag ][ 'active' ]) {
            $module = $this->modules[ $slag ];
            if ($this->saveModule(
                $module[ 'id' ],
                $slag,
                $module[ 'name' ],
                $module[ 'description' ],
                $module[ 'version' ],
                $module[ 'path' ],
                $module[ 'file' ],
                true
            )) {
                $module = $this->create($slag);
                return $this->modules[ $slag ][ 'active' ] = $module->activate();
            }
        }
        return false;
    }

    public function deactivateModule($slag)
    {
        if (array_key_exists($slag, $this->modules) && $this->modules[ $slag ][ 'active' ]) {
            $module = $this->modules[ $slag ];
            if ($this->saveModule(
                $module[ 'id' ],
                $slag,
                $module[ 'name' ],
                $module[ 'description' ],
                $module[ 'version' ],
                $module[ 'path' ],
                $module[ 'file' ],
                false
            )) {
                $module = $this->create($slag);
                return !($this->modules[ $slag ][ 'active' ] = !$module->deactivate());
            }
        }
        return false;
    }

    public function uninstallModule($slag)
    {

    }

    protected function getModule()
    {

    }

    protected function getModuleObject(string $path, string $file)
    {
        $filePath = DIR_MODULES . DS . $path . DS . $file;
        if (is_file($filePath)) {
            $module = require_once $filePath;
            if ($module instanceof ModuleExtension) {
                $this->cache($module->getSlag(), $module);
                return $module;
            }
        }
        return false;
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

    protected function addModule($slag, $name, $description, $version, $path, $file, $active = false)
    {
        $sql = 'insert into usf_modules(`slag`, `name`, `description`, `version`, `path`, `file`, `active`) values (:slag, :name, :description, :version, :path, :file, :active);';
        if ($st = $this->db->prepare($sql)) {
            $st->bindValue(':slag', $slag, Database::PARAM_STR);
            $st->bindValue(':name', $name, Database::PARAM_STR);
            $st->bindValue(':description', $description, Database::PARAM_STR);
            $st->bindValue(':version', $version, Database::PARAM_STR);
            $st->bindValue(':path', $path, Database::PARAM_STR);
            $st->bindValue(':file', $file, Database::PARAM_STR);
            $st->bindValue(':active', format01($active), Database::PARAM_INT);
            if ($st->execute()) {
                return $this->db->lastInsertId();
            }
        }
        return false;
    }

    protected function saveModule($id, $slag, $name, $description, $version, $path, $file, $active)
    {
        $sql = 'insert into usf_modules(`id`, `slag`, `name`, `description`, `version`, `path`, `file`, `active`) 
                values (:id, :slag, :name, :description, :version, :path, :file, :active)
                on duplicate key update 
                `slag` = values(`slag`),
                `name` = values(`name`),
                `description` = values(`description`),
                `version` = values(`version`),
                `path` = values(`path`),
                `file` = values(`file`),
                `active` = values(`active`);';
        if ($st = $this->db->prepare($sql)) {
            $st->bindValue(':id', $id, Database::PARAM_INT);
            $st->bindValue(':slag', $slag, Database::PARAM_STR);
            $st->bindValue(':name', $name, Database::PARAM_STR);
            $st->bindValue(':description', $description, Database::PARAM_STR);
            $st->bindValue(':version', $version, Database::PARAM_STR);
            $st->bindValue(':path', $path, Database::PARAM_STR);
            $st->bindValue(':file', $file, Database::PARAM_STR);
            $st->bindValue(':active', format01($active), Database::PARAM_INT);
            if ($st->execute()) {
                return $this->db->lastInsertId();
            }
        }
        return false;
    }

}