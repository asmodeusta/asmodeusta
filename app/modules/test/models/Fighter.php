<?php

class Fighter
{

    const MIN_STRENGTH = 1;
    const MAX_STRENGTH = 10;
    const MIN_AGILITY = 1;
    const MAX_AGILITY = 10;
    const MIN_STAMINA = 1;
    const MAX_STAMINA = 10;
    const MIN_HP = 30;
    const MAX_HP = 70;
    const PROPERTIES_COUNT = 20;

    const BASIC_HIT_POWER = 5;

    protected $strength = 0;
    protected $agility = 0;
    protected $stamina = 0;
    protected $hp;

    public function __construct()
    {
        $this->setProperties();
        $this->setHp();
    }

    private function setProperties() {
        $result = false;
        if($this->strength===0||$this->agility===0||$this->stamina===0) {
            $this->strength++;
            $this->agility++;
            $this->stamina++;
            for($i=1;$i<=self::PROPERTIES_COUNT-3;$i++) {
                $r = rand(1,3);
                switch ($r) {
                    case 1:
                        $this->strength++;
                        break;
                    case 2:
                        $this->agility++;
                        break;
                    case 3:
                        $this->stamina++;
                        break;
                    default:
                        $i--;
                        break;
                }
            }
            $result = true;
        }
        return $result;
    }

    private function setHp() {
        $result = false;
        if ($this->hp===0&&$this->stamina!==0) {
            $hp_value = self::MIN_HP + $this->stamina * (self::MAX_HP - self::MIN_HP)/(self::MAX_STAMINA - self::MIN_STAMINA);
            $this->hp = intval($hp_value);
            $result = true;
        }
        return $result;
    }

    public function hit($power)
    {

    }


}