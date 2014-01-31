<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Создание xml карты сайта
 * PROJECT_BASE=/home/shurick/kupiskidku-trunk /home/shurick/kupiskidku-trunk/src/lib/kohana/modules/minion/minion Assets_Compress
 * PROJECT_BASE=/home/kupiskidku php /home/kupiskidku/src/src/lib/kohana/modules/minion/minion Assets_Compress
 */
class Task_Assets_Compress extends Minion_Task {

    protected function _execute(array $param){

        $queue_file = Kohana::$config->load('zAssets.compress_queue');

        if(!file_exists($queue_file)) return;

        if(!is_writeable($queue_file)){
            Log::instance()->add(Log::ERROR, 'Assets queue file is not writeable');
            return;
        }

        $fp = @fopen($queue_file, 'a');
        flock($fp, LOCK_EX); // Блокирование файла для записи

        //считываем
        $queue = file($queue_file);
        //очищаем
        ftruncate($fp,0);

        flock($fp, LOCK_UN); // Снятие блокировки
        fclose($fp);

        if(count($queue)){

            foreach($queue as $command){

                $command = trim($command);

                if(mb_strpos($command, 'node ', 0, 'utf-8')===false
                    || (mb_strpos($command, 'sqwish', 0, 'utf-8')===false && mb_strpos($command, 'uglifyjs', 0, 'utf-8')===false)
                ) continue;

                system($command);
            }
        }

    }
}