<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Kohana_zAssets_View{

    protected static $_instances = array();

    /**
     * Factory for Assets type (css/js)
     * @param string $type
     * @param Assets_Collection $collection
     * @return mixed
     */
    public static function factory($type='', Assets_Collection $collection){

        $key = $type.$collection->assets->file_ext;

        if(!isset(self::$_instances[$key])){
            $class = 'Assets_View_'.$type;
            self::$_instances[$key] = new $class($collection);
        }

        return self::$_instances[$key];
    }

    /**
     * @var Assets_Collection $collection
     */
    protected $_collection=null;

    /**
     * @param Assets_Collection $collection
     */
    protected function __construct(Assets_Collection $collection){
        $this->_collection = $collection;
        $this->groups();
    }

    /**
     * Have = array(
     *  0 => 3
     *  1 => 5
     *  2 => 3
     *  3 => 0
     *  4 => 0
     * )
     * Needed = array (
     *  1 => 5
     *  0 => 3
     *  2 => 3
     *  3 => 0
     *  4 => 0
     * )
     * @param array $sort
     */
    protected function smart_sort(array &$sort=array()){

        // values sorting DESC
        arsort($sort);

        $tmp = array();

        // collect sort groups
        foreach($sort as $key=>$val){
            if(!isset($tmp[$val])) $tmp[$val] = array();
            $tmp[$val][] = $key;
        }

        // sort groups
        foreach($tmp as $key=>$val){
            sort($tmp[$key]);
        }

        $sort = array();
        foreach($tmp as $key=>$val){
            foreach($val as $v){
                $sort[$v] = $key;
            }
        }
    }

    abstract protected function groups();
    abstract public function html($group);
}