<?php defined('SYSPATH') OR die('No direct script access.');

class Kohana_zAssets_Collection{

    /**
     * Parent asset (creator)
     * @var Assets $assets
     */
    public $assets=null;

    /**
     * @param Assets $assets
     */
    public function __construct(Assets $assets){
        $this->assets=$assets;
    }

    /**
     * Collection of candidates to assets
     * @var array
     */
    private $_applicants = array();

    /**
     * Add file in collection
     * @param string $group # group for output (head/body/footer/you group)
     * @param string $name  # file name
     * @param int $sort     # position DESC [0-default]
     * @param bool $safe    # don't change url and hosts in files
     */
    public function file($group='', $name='', $sort=0, $safe=false){
        $this->_applicants[] = new Assets_Source_File($this, $name, $group, $sort, $safe);
    }

    /**
     * @param string $group # group for output (head/body/footer/you group)
     * @param array $names  # array of file_names
     * @param int $sort     # position DESC [0-default]
     * @param bool $safe    # don't change url and hosts in files
     */
    public function files($group='', $names=array(), $sort=0, $safe=false){

        foreach($names as $name){

            if(empty($name)) continue;
            $this->file($group, $name, $sort, $safe);
        }
    }

    /**
     * Add code to assets
     * @param string $group # group for output (head/body/footer/you group)
     * @param string $code  # string of code
     * @param int $sort     # position DESC [0-default]
     * @param bool $safe    # don't change url and hosts in files
     */
    public function code($group='', $code = '', $sort=0, $safe=false){
        $this->_applicants[] = new Assets_Source_Code($this, $code, $group, $sort, $safe);
    }

    public function applicants(){
        return $this->_applicants;
    }

    public function loaded(){
        if(count($this->_applicants)) return true;
        return false;
    }

}