<?php defined('SYSPATH') OR die('No direct script access.');

class Kohana_zAssets_JS extends Assets {

    protected function __construct(){
        parent::__construct();
        $this->source_host = $this->site['js_host'];
        $this->source_path = $this->config['js_path'];
        $this->file_ext = '.js';
    }

    public function before_source(Assets_Source $source){
        if(is_a($source, 'Assets_Source_Code'))
            return '<script type="text/javascript">';
        else return '<script type="text/javascript" src="';
    }

    public function after_source(Assets_Source $source){
        if(is_a($source, 'Assets_Source_Code'))
            return '</script>';
        else return '"></script>';
    }

    public function compress_command($file_name=''){
       return '/usr/local/bin/node '.$this->config['js_compress'].' '.$this->config['path'].$file_name.' -m -c hoist_vars=true,warnings=false -o '.$this->config['path'].$file_name;
    }
}
