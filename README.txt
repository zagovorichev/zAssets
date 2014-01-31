Инициализация

<?php
Assets::factory('css')
            ->files('head', array(
                'style.css',
                'ui/smoothness/style.css',
                'plugin/select2.css',
            ), 100);
 
Assets::factory('js')
            ->files('head', array(
                'core/jquery.js',
                'gadgets/user/auth.js',
            ), 100);
 
// еще где-нибудь захотим добавить файл с низким приоритетом (по умолчанию 0)
Assets::factory('js')->file('head', 'file.js');
 
//или css код
Assets::factory('css')->code('head', '.my-class{color: red;}');

Вывод результата

?
...
<head>
    <?=Assets::factory('css')->group('head')?>
    ....
    <?=Assets::factory('js')->group('head')?>

Особенности

По умолчанию, в соответствии с настройками ассетов, все пути к источникам (картинкам, шрифтам ...) будут дополняться js_host или css_host (пример: background: (/images/file.jpg) изменит на background: ('http://static.blog-tree.com/images/file.jpg'));

В классе Assets есть статическая переменная public static $change_path_in_content = true; которая позволяет отменить изменение путей в ассетах. Это очень пригодится при подключении шрифтов и при использовании https шифрованного протокола, т.к. шрифты не подключаться, а протокол не является защищенным, если в нем есть не шифрованные данные.

© 2013 Zagovorichev Alexander
