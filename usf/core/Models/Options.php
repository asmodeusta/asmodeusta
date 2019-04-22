<?php

namespace Usf\Models;

use Usf\Base\Traits\Cacheable;
use Usf\Components\Database;

/**
 * Class Options
 * @package Usf\Models
 */
class Options
{

    use Cacheable;

    /**
     * @var Database
     */
    protected $db;

    /**
     * @var int
     */
    protected $module;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * Options constructor.
     * @param int $module
     */
    public function __construct(int $module)
    {
        $this->db = db();
        $this->module = $module;
        $this->readOptions();
    }

    /**
     * Options destructor
     */
    public function __destruct()
    {
        $this->saveOptions();
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key)
    {
        if (!array_key_exists($key, $this->options)) {
            if ($this->issetCache($key)) {
                return null;
            } elseif (!$this->read($key)) {
                return null;
            }
        }
        return $this->options[ $key ][ 'active' ] ? $this->options[ $key ][ 'value' ] : null;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param int $autoload
     * @param int $autosave
     * @return mixed
     */
    public function set(string $key, $value, $autoload = 0, $autosave = 1)
    {
        if (!array_key_exists($key, $this->options)) {
            if (!$this->read($key)) {
                $this->options[ $key ] = [
                    'id' => 0,
                    'save' => true,
                    'value' => $value,
                    'autoload' => $autoload,
                    'autosave' => $autosave,
                    'active' => true
                ];
                return $value;
            }
        }
        if ($this->options[ $key ][ 'value' ] !== $value
            || $this->options[ $key ][ 'autoload' ] !== $autoload
            || $this->options[ $key ][ 'autosave' ] !== $autosave) {
            $this->options[ $key ][ 'value' ] = $value;
            $this->options[ $key ][ 'autoload' ] = $autoload;
            $this->options[ $key ][ 'autosave' ] = $autosave;
            $this->options[ $key ][ 'save' ] = $autosave;
        }
        $this->options[ $key ][ 'delete' ] = ! ( $this->options[ $key ][ 'active' ] = true );
        return $value;
    }

    /**
     * @param string $key
     */
    public function unset(string $key)
    {
        if (!array_key_exists($key, $this->options)) {
            if (!$this->read($key)) {
                return;
            }
        }
        $this->options[ $key ][ 'delete' ] = ! ( $this->options[ $key ][ 'active' ] = false );
    }

    /**
     * @param string $key
     * @return bool
     */
    public function isset(string $key)
    {
        return array_key_exists($key, $this->options)
            ? boolval($this->options[ $key ][ 'active' ])
            : ($this->issetCache($key) ? false : $this->read($key));
    }

    /**
     * @return bool
     */
    protected function readOptions()
    {
        $sql = 'select 
                `key`,
                id, 
                `value`, 
                modified,
                autoload,
                autosave,
                active,
                0 as `save`,
                0 as `delete` 
                from usf_options 
                where module = :module
                  and active = 1';
        if ($st = $this->db->prepare($sql, $this->db::FETCH_ASSOC)) {
            $st->bindValue(':module', $this->module, $this->db::PARAM_INT);
            if ($st->execute()) {
                while ($item = $st->fetch($this->db::FETCH_ASSOC)) {
                    $item[ 'value' ] = unserialize($item[ 'value' ]);
                    $this->options[ array_shift($item) ] = $item;
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Saving options
     */
    protected function saveOptions()
    {
        $insert = [];
        $delete = [];
        $update = [];
        $modified = 'from_unixtime(' . time() . ')';
        function formatBoolInt($val)
        {
            return $val ? 1 : 0;
        }

        foreach ($this->options as $key => $option) {
            if ($option[ 'id' ] === 0) {
                $insert[] = '(' . implode(', ', [
                        $this->db->quote($key),
                        intval($this->module),
                        $this->db->quote(serialize($option[ 'value' ])),
                        formatBoolInt($option[ 'autoload' ]),
                        formatBoolInt($option[ 'autosave' ]),
                        formatBoolInt($option[ 'active' ]),
                    ]) . ')';
            } elseif ($option[ 'delete' ]) {
                $delete[] = $option[ 'id' ];
            } elseif ($option[ 'save' ]) {
                $update[] = '(' . implode(', ', [
                        $option[ 'id' ],
                        intval($this->module),
                        $this->db->quote($key),
                        $this->db->quote(serialize($option[ 'value' ])),
                        $modified,
                        formatBoolInt($option[ 'autoload' ]),
                        formatBoolInt($option[ 'autosave' ]),
                        formatBoolInt($option[ 'active' ]),
                    ]) . ')';
            }
        }
        $sql = '';
        if (!empty($delete)) {
            $deleteIds = implode(', ', $delete);
            $sql .= 'delete from usf_options where id in (' . $deleteIds . ');';
        }
        if (!empty($update)) {
            $updateValues = implode(', ', $update);
            $sql .= 'insert into usf_options(id, module, `key`, value, modified, autoload, autosave, active) 
                    values ' . $updateValues . ' on duplicate key update 
                    `module` = values(`module`),
                    `key` = values(`key`),
                    `value` = values(`value`),
                    `modified` = values(`modified`),
                    `autoload` = values(`autoload`),
                    `autosave` = values(`autosave`),
                    `active` = values(`active`);';
        }
        if (!empty($insert)) {
            $insertValues = implode(', ', $insert);
            $sql .= 'insert into usf_options(`key`, module, value, autoload, autosave, active) values ' . $insertValues . ';';
        }
        if (!empty($sql)) {
            $this->db->exec($sql);
        }
    }

    /**
     * @param string $key
     * @return bool
     */
    protected function read(string $key)
    {
        $sql = 'select 
                id, 
                `value`, 
                modified,
                autoload,
                autosave,
                active,
                0 as `save`,
                0 as `delete` 
                from usf_options 
                where module = :module
                  and `key` = :key
                  and active = 1';
        if ($st = $this->db->prepare($sql, $this->db::FETCH_ASSOC)) {
            $st->bindValue(':module', $this->module, $this->db::PARAM_INT);
            $st->bindValue(':key', $key, $this->db::PARAM_STR);
            if ($st->execute()) {
                if ($item = $st->fetch($this->db::FETCH_ASSOC)) {
                    $item[ 'value' ] = unserialize($item[ 'value' ]);
                    $this->options[ $key ] = $item;
                    return true;
                }
            }
        }
        $this->cache($key, 'null');
        return false;
    }

}