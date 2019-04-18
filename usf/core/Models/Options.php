<?php


namespace Usf\Models;


use Usf\Base\Traits\StaticCacheable;

class Options
{

    use StaticCacheable;

    protected $db;

    protected $module;

    protected $options = [];

    public function __construct($module)
    {
        $this->db = db();
        if (self::issetStaticCache($module)) {
            $this->options = self::staticCache($module);
        } else {
            $this->readOptions($module);
        }
    }

    public function __destruct()
    {
        $insert = [];
        $delete = [];
        $update = [];
        $modified = 'from_unixtime('.time().')';
        function formatBoolInt($val) {
            return $val ? 1 : 0;
        }
        foreach ($this->options as $key => $option) {
            if ($option['id'] === 0 ) {
                $insert[] = implode(', ', [
                    $this->db->quote($key),
                    $this->db->quote(serialize($option['value'])),
                    formatBoolInt($option['autoload']),
                    formatBoolInt($option['autosave']),
                    formatBoolInt($option['active']),
                ]);
            } elseif ($option['delete']) {
                $delete[] = $option['id'];
            } elseif ($option['save']) {
                $update[] = implode(', ', [
                    $option['id'],
                    $this->db->quote($key),
                    $this->db->quote(serialize($option['value'])),
                    $modified,
                    formatBoolInt($option['autoload']),
                    formatBoolInt($option['autosave']),
                    formatBoolInt($option['active']),
                    ]);
            }
        }
        $sql = '';
        if (!empty($delete)) {
            $deleteIds = implode(', ', $delete);
            $sql .= 'delete from usf_options where id in ('.$deleteIds.');';
        }
        if (!empty($update)) {
            $updateValues = implode(', ', $update);
            $sql .= 'insert into usf_options(id, `key`, value, modified, autoload, autosave, active) 
                    values '.$updateValues.' on duplicate key update 
                    `key` = values(`key`),
                    `value` = values(`value`),
                    `modified` = values(`modified`),
                    `autoload` = values(`autoload`),
                    `autosave` = values(`autosave`),
                    `active` = values(`active`);';
        }
        if (!empty($insert)) {
            $insertValues = implode(', ', $insert);
            $sql .= 'insert into usf_options(`key`, value, autoload, autosave, active) values '.$insertValues.';';
        }
        if (!empty($sql)) {
            $this->db->exec($sql);
        }
    }

    protected function readOptions($module)
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
            $st->bindValue(':module', $module, $this->db::PARAM_INT);
            if ( $st->execute() ) {
                while ($item = $st->fetch($this->db::FETCH_ASSOC)) {
                    $item['value'] = unserialize($item['value']);
                    $this->options[array_shift($item)] = $item;
                }
                self::staticCache($module, $this->options);
                return true;
            }
        }
        return false;
    }

}