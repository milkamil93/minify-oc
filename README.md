# Minify для ocStore 2.3

Объединение, сжатие css, js файлов и форматирование html в одну строку.

## Описание

Собирает все css и js файлы, объединяет их в один и удаляет лишние пробелы, переносы  без ломания кода, а так же может сжать их gzip. 
Умеет форматировать html удаляя лишние пробелы, переносы, попутно сжимая js и css без ломания кода.

## Настройка

Залить содержимое папки upload в корень сайта и установить модуль в админке. Если модуль не появился в списке, то нужно дать права для просмотра, редактирования и проверить не отмечен ли он в списке на скрытие. В настройках модуля выбрать нужные параметры и сохранить.

### Gzip

Для работы gzip сжатия нужно прописать в .htaccess следующий код

```
AddEncoding gzip .jgz
#add support gzip JavaScript
RewriteCond %{HTTP_USER_AGENT} ".*Safari.*" [OR]
RewriteCond %{HTTP:Accept-Encoding} gzip
RewriteCond %{REQUEST_FILENAME}.jgz -f
RewriteRule (.*)\.js$ $1\.js.jgz [L]
AddType "text/javascript" .js.jgz
#add support gzip CSS
RewriteCond %{HTTP_USER_AGENT} ".*Safari.*" [OR]
RewriteCond %{HTTP:Accept-Encoding} gzip
RewriteCond %{REQUEST_FILENAME}.jgz -f
RewriteRule (.*)\.js$ $1\.css.jgz [L]
AddType "text/css" .css.jgz
AddEncoding gzip .jgz
```

## Внимание!!!

* Модуль перезаписывает файл `system/framework.php` и он должен быть доступен для редактирования!
* При изменении CSS и JS необходимо очистить кеш в настройках модуля, чтобы модуль заново сгенерировал файлы!
