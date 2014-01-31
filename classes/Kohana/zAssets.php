<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Kohana_zAssets {


    protected static $_instances = array();

    /**
     * Factory for Assets type (css/js)
     * @param string $type
     * @return mixed
     */
    public static function factory($type=''){

        // Be sure to only profile if it's enabled
        if (Kohana::$profiling === TRUE)
        {
            // Start a new benchmark
            $benchmark = Profiler::start('Assets', __FUNCTION__);
        }

        if(!isset(self::$_instances[$type])){
            $class = 'Assets_'.mb_strtoupper($type, 'utf-8');
            self::$_instances[$type] = new $class;
        }

        if (isset($benchmark))
        {
            // Stop the benchmark
            Profiler::stop($benchmark);
        }

        return self::$_instances[$type];
    }



    /**
     * Asset_Collection - class of candidate collections to assets
     * @var Assets_Source $_collection
     */
    public  $_collection = null;

    /**
     * Always loaded groups
     * ['group_name'] = true
     * @var array
     */
    private $_is_loaded = array();

    /**
     * site config
     * @var array
     */
    public $site=array();

    /**
     * Assets config
     * @var array
     */
    public $config = array();

    /**
     * Try find and change paths (to static.) in asset content
     * @var bool
     */
    public static $change_path_in_content = true;

    /**
     * Defines for source files (extension, path to dir and sources uri)
     * @var string
     */
    public $file_ext = '';
    public $source_path='';
    public $source_host='';

    protected function __construct(){

        $this->_collection = new Assets_Collection($this);

        $this->site = Kohana::$config->load('ks.general');
        $this->config = Kohana::$config->load('zAssets');
    }

    /** delegate collections write */

    /**
     * Add file in asset group
     * @param string $group
     * @param string $file_name
     * @param int $sort
     * @param bool $safe
     */
    public function file($group='', $file_name='', $sort=0, $safe=false){

        if (Kohana::$profiling === TRUE)
            $benchmark = Profiler::start('Assets', __FUNCTION__);

        $this->_collection->file($group, $file_name, $sort, $safe);

        if (isset($benchmark))
            Profiler::stop($benchmark);
    }

    /**
     * Add many files in asset group
     * @param string $group
     * @param array $names
     * @param int $sort
     * @param bool $safe
     */
    public function files($group='', $names=array(), $sort=0, $safe=false){

        if (Kohana::$profiling === TRUE)
            $benchmark = Profiler::start('Assets', __FUNCTION__);

        $this->_collection->files($group, $names, $sort, $safe);

        if (isset($benchmark))
            Profiler::stop($benchmark);
    }

    /**
     * Add source code in asset group
     * @param string $group
     * @param string $code
     * @param int $sort
     * @param bool $safe
     */
    public function code($group='', $code = '', $sort=0, $safe=false){
        if (Kohana::$profiling === TRUE)
            $benchmark = Profiler::start('Assets', __FUNCTION__);

        $this->_collection->code($group, $code, $sort, $safe);

        if (isset($benchmark))
            Profiler::stop($benchmark);
    }
    /** collections end */


    /**
     * Get assets group
     * @param string $group
     * @param bool $source
     * @return string
     */
    public function group($group='', $source=false){

        if (Kohana::$profiling === TRUE)
            $benchmark = Profiler::start('Assets', __FUNCTION__);

        if(isset($this->_is_loaded[$group])) {
            if (isset($benchmark))
                Profiler::stop($benchmark);
            return '';
        }

        $this->_is_loaded[$group] = true;

        if(!$this->_collection->loaded()) {
            if (isset($benchmark))
                Profiler::stop($benchmark);
            return '';
        }

        //if sources needed
        if($this->config['active'] === FALSE || $source===true){

            $result = Assets_View::factory('Unchanged', $this->_collection)->html($group);//$this->group_source_script($group);
            Profiler::stop($benchmark);
            return $result;
        }

        $result = Assets_View::factory('Compress', $this->_collection)->html($group);//$this->group_assets($group);

        if (isset($benchmark))
            Profiler::stop($benchmark);

        return $result;
    }

    /**
     * Before source (code or file_name)
     * @param Assets_Source $source
     * @return string
     */
    abstract public function before_source(Assets_Source $source);

    /**
     * After source
     * @param Assets_Source $source
     * @return string
     */
    abstract public function after_source(Assets_Source $source);
}