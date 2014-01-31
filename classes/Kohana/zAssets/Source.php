<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Kohana_zAssets_Source {

    /**
     * If failed on create assets - output just uncompressed source code
     * Assets_Collection
     * @var array
     */
    public static $_sources = null;


    /**
     * group name (head/footer/another_group)
     * @var string
     */
    protected $group='';

    /**
     * file name or code
     * @var string
     */
    protected $source='';

    /**
     * value for content sorting in result file 0 - is downer, 1 - is upper, 2 - more up...so on
     * @var int
     */
    protected $sort=0;

    /**
     * Caller
     * @var null
     */
    protected $collection=null;

    public function __construct( Assets_Collection $collection, $source='', $group='', $sort=0){

        $this->collection = $collection;

        self::$_sources = new Assets_Collection($collection->assets);

        $this->set_group($group);
        $this->set_source($source);
        $this->set_sort($sort);
    }

    protected function set_group($group=''){
        $this->group = $group;
    }

    protected function set_source($source=''){
        $this->source = $source;
    }

    protected function set_sort($sort=''){
        $this->sort = $sort;
    }

    public function group(){
        return $this->group;
    }

    public function sort(){
        return $this->sort;
    }

    public function source(){
        return $this->source;
    }

    /**
     * Get source html without compression
     * @return mixed
     */
    abstract public function html();

    /**
     * Get source hash code
     * @return mixed
     */
    abstract public function hash();

    /**
     * Check current source
     * @return mixed
     */
    abstract public function check();

    /**
     * get content for asserting
     * @return mixed
     */
    abstract public function content();
}