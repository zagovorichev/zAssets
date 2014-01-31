<?php defined('SYSPATH') OR die('No direct script access.');

class Kohana_zAssets_View_Compress extends Assets_View{

    private $_assets=null;
    private $_assets_groups=null;

    private function assets(){

        if(!isset($this->_assets)){

            $this->_assets = array();

            foreach($this->_collection->applicants() as $item)
                if($item->check())
                    $this->_assets[$item->hash()] = $item;
        }
    }

    private function sort($keys, &$group){

        $tmp_group = $group;
        $md5= $group['md5'];
        $group=array();

        foreach($keys as $key)
            $group[$tmp_group[$key]->hash()] = $tmp_group[$key];

        $group['md5'] = $md5;
    }

    private function assets_groups(){

        $sort_groups = array();

        if(!isset($this->_assets_groups)){

            $this->_assets_groups = array();

            foreach($this->_assets as $hash => $item){
                $this->_assets_groups[$item->group()][] = $item;
                $this->_assets_groups[$item->group()]['md5'] = md5((isset($this->_assets_groups[$item->group()]['md5'])?$this->_assets_groups[$item->group()]['md5']:'').$hash);
                $sort_groups[$item->group()][] = $item->sort();
            }

            //sorting
            foreach($this->_assets_groups as $group=>$val){
                $this->smart_sort($sort_groups[$group]);
                $this->sort(array_keys($sort_groups[$group]), $this->_assets_groups[$group]);
            }

        }
    }

    protected function groups(){

        $this->assets();

        $this->assets_groups();

        return $this;

    }

    public function html($group=''){

        if(isset($this->_assets_groups)
            && isset($this->_assets_groups[$group])
            && count($this->_assets_groups[$group]))
        {
            $file_name = Cache::instance('memcache')->get($this->_assets_groups[$group]['md5'], '');

            $compiled_file = new Assets_Source_File_Asset($this->_collection, $file_name, $group);
            if(!$compiled_file->check()) $file_name = '';
        }

        if(empty($file_name))
            $file_name = $this->compile($group);

        $compiled_file = new Assets_Source_File_Asset($this->_collection, $file_name, $group);

        return $compiled_file->html() . Assets_View::factory('Unchanged', Assets_Source::$_sources)->html($group);
    }

    /**
     * Compile group
     * @param string $group
     * @return string
     * @throws AssetsException
     */
    private function compile($group=''){

        if(!isset($this->_assets_groups)
            || !isset($this->_assets_groups[$group])
            || !count($this->_assets_groups[$group])) return '';

        $memcache = Cache::instance('memcache');

        // Проверяем, что для данной группы еще не запущена генерация
        if($memcache->get($this->_assets_groups[$group]['md5'].'_progress') === TRUE){
            sleep(15);
            //если через 15 секунд тоже самое, значит висим, запускаем новую генерацию
            if($memcache->get($this->_assets_groups[$group]['md5'].'_progress') !== TRUE){
                return '';
            }
        }

        //create new assets file
        do
        {
            $dir = substr(md5(date('Y-m-d')), 3, 7);
            if(!file_exists($this->_collection->assets->config['path']."/".$dir))
                if(!mkdir($this->_collection->assets->config['path']."/".$dir, 0755, 1))
                    throw new AssetsException('Failed on create assets directory');

            $file_name = $dir.'/'.substr(md5(time()), 3, 8).$this->_collection->assets->file_ext;
            $file = $this->_collection->assets->config['path']."/".$file_name;
            if(file_exists($file))
                return '';
            $fp = @fopen($file, 'w');
        }
        while(!$fp);

        $memcache->set($this->_assets_groups[$group]['md5'].'_progress', TRUE);

        $files_cnt = 0;

        foreach($this->_assets_groups[$group] as $key => $item){
            if($key == 'md5') continue;
            elseif($item->check()){
                if(isset($item->safe) && $item->safe && self::$change_path_in_content)
                    $content = str_replace('/images/', $this->_collection->assets->site['img_host'], $item->content());
                else $content = $item->content();

                fwrite($fp, $content);
                $files_cnt++;
            }
        }
        fclose($fp);

        if(!$files_cnt){
            unlink($file);
            return '';
        }

        //one month
        $memcache->set($this->_assets_groups[$group]['md5'], $file_name, 2592000);
        $memcache->delete($this->_assets_groups[$group]['md5'].'_progress');

        //try compress
        $this->deferred_compression($file_name);
        //$this->compress($file_name);

        return $file_name;
    }

    /*
     * TODO сделать сжатие не через файл, а через гирмана, отправлять воркеру задачу на сжатие
     */
    private function deferred_compression($file_name=''){

        // write compress command in queue
        if(!file_exists($this->_collection->assets->config['compress_queue']))
            throw new AssetsException('Compress file queue does not exists');

        $fp = @fopen($this->_collection->assets->config['compress_queue'], 'a');
        if($fp === FALSE){
            Log::instance()->add(Log::ALERT, 'Assets '.$this->_collection->assets->config['compress_queue'].' access denied');
        }else{
            fwrite($fp, $this->_collection->assets->compress_command($file_name)."\r\n");
            fclose($fp);
        }
    }
}