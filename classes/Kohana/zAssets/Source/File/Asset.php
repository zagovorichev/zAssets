<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Достаем файл ассета (сгенерированный)
 * Class Kohana_zAssets_Source_File_Asset
 */
class Kohana_zAssets_Source_File_Asset extends Assets_Source_File {

    public function html(){

        return $this->collection->assets->before_source($this)
                . $this->collection->assets->config->host.$this->source
                .$this->collection->assets->after_source($this);
    }

    public function check(){

        $path = $this->collection->assets->config->path.$this->source;

        if(!file_exists($path) || !is_readable($path))
            return false;

        return true;
    }

}