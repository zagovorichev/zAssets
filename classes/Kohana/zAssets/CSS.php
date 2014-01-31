<?php defined('SYSPATH') OR die('No direct script access.');

class Kohana_zAssets_CSS extends Assets {

    protected function __construct(){
        parent::__construct();
        $this->source_host = $this->site['css_host'];
        $this->source_path = $this->config['css_path'];
        $this->file_ext = '.css';
    }

    public function before_source(Assets_Source $source){
        if(is_a($source, 'Assets_Source_Code'))
            return '<link type="text/css" rel="stylesheet">';
        else return '<link type="text/css" rel="stylesheet" href="';
    }

    public function after_source(Assets_Source $source){
        if(is_a($source, 'Assets_Source_Code'))
            return '</link>';
        else return '" />';
    }

    public function compress_command($file_name=''){
        // Strict очень много убирает и режет стили
        return '/usr/local/bin/node '.$this->config['css_compress'].' '.$this->config['path'].$file_name.' -o '.$this->config['path'].$file_name;//.' --strict';
    }

}
