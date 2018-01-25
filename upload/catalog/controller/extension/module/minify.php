<?php
/**
 ***
 *** Minify by https://github.com/milkamil93
 ***
 **/
require_once 'minify/cssmin.class.php';
require_once 'minify/jsmin.class.php';

class ControllerExtensionModuleMinify extends Controller {

    // время хранения файла в секундах
    private $file_time_expired = '3600';

    // массив списка js файлов со страницы
    private $js_array = [];

    // массив списка css файлов со страницы
    private $css_array = [];

    // путь до js файла
    private $output_js;

    // путь до css файла
    private $output_css;

    // путь до дериктории
    private $out_folder;

    private $status = [
        'css' => 0,
        'js' => 0,
        'html' => 0,
        'gzip' => 0,
        'async' => 0
    ];

    // основная функция запуска
    public function minify () {

        if(!$this->config->get('minify_status')) return false;
        $this->status['gzip'] = $this->config->get('minify_gzip');
        $this->status['css'] = $this->config->get('minify_css');
        $this->status['js'] = $this->config->get('minify_js');
        $this->status['html'] = $this->config->get('minify_html');
        $this->status['async'] = $this->config->get('minify_async');

        $this->jgz = $this->status['gzip'] ? '.jgz' : '';

        $this->file_time_expired = $this->config->get('minify_time') ? $this->config->get('minify_time') : $this->file_time_expired;

        // получаем папку темы
        if ($this->config->get('config_theme') == 'theme_default') {
            $theme = $this->config->get('theme_default_directory');
        } else {
            $theme = $this->config->get('config_theme');
        }

        // указываем путь относительно темы
        $this->out_folder = 'catalog/view/theme/' . $theme . '/minify';

        // получаем html
        $buffer = $this->response->getOutput();

        // проверяем существование директории
        $this->check_path($this->out_folder);

        // собираем стили
        if($this->status['css']) $buffer = $this->css($buffer);

        // собираем скрипты
        if($this->status['js']) $buffer = $this->js($buffer);

        // собираем js из контента, сжимаем html и js
        if($this->status['html']) $buffer = $this->html($buffer);

        // вставляем склеиные скрипты
        $buffer = $this->out($buffer);

        // рендерим новый html
        $this->response->setOutput($buffer);
    }

    // сжимаем собранные js или css в переменную
    private function concatFiles($type) {

        switch ($type) {
            case 'css':
                $css = $this->accept_array($this->css_array, 'css');
                $minify = CSSMin::minify($css);
                $output_file = $this->output_css;
                break;
            case 'js':
                $js = $this->accept_array($this->js_array);
                if ($this->status['async']) {
                    $js .= 'for(f in minify_out){minify_out[f]();};';
                }
                $minify = JSMin::minify($js);
                $output_file = $this->output_js;
                break;
        }

        if (empty($minify) || empty($output_file)) return false;

        $minify = $this->status['gzip'] ? gzencode($minify) : $minify;
        $result = file_put_contents($output_file, $minify);

        return $result;
    }

    // проверяем существования пути
    private function check_path($path) {
        if (!file_exists($path)) {
            mkdir($path);
        }

        if (!is_readable($path)) {
            trigger_error('Directory for compressed assets is not readable.');
        }

        if (!is_writable($path)) {
            trigger_error('Directory for compressed assets is not writable.');
        }
    }

    private function accept_array($array, $type = false) {
        $data = '';
        foreach ($array as &$item) {
            $file = file_get_contents($item) . PHP_EOL;
            if ($type === 'css') {
                $file = preg_replace('#url\((?!\s*[\'"]?(data\:image|/|http([s]*)\:\/\/))\s*([\'"])?#i', "url($3{$this->getPath($item)}", $file);
            }
            $data .= $file;
            unset($item,$file);
        }
        return $data;
    }

    private function file_check($file) {
        if (is_file($file)) {
            $time = time() - filemtime($file);
            if ($time > $this->file_time_expired) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    // собираем css
    private function css($buffer) {
        preg_match_all('/<link[^>]*href="([^"]*).css"[^>]*>/is', $buffer, $styles);
        $styles = $this->external_url($this->unique($styles['0']));
        foreach ($styles as &$style) {
            preg_match('/^<link.*?href=(["\'])(.*?)\1.*$/', $style, $style_name);
            $file_name = $style_name['2'];
            if (!empty($file_name)) {
                if (is_readable($file_name)) {
                    if(stristr($style_name['2'],'//')) {
                        if(stristr($style_name['2'],$_SERVER['SERVER_NAME']))
                            $this->css_array[] = $file_name;
                    } else {
                        $this->css_array[] = $file_name;
                    }
                }
            }
            unset($style,$style_name,$file_name);
        }

        // удаляем старые стили
        $buffer = str_replace($this->css_array, 'remove', $buffer);
        $buffer = preg_replace('/<link(.*?)href="remove"(.*?)>/', '', $buffer);
        $_css_file = md5(serialize($this->css_array)) . '.css' . $this->jgz;
        $this->output_css = $this->out_folder . '/' . $_css_file;
        if ($this->file_check($this->output_css)) {
            $this->concatFiles('css');
        }

        return $buffer;
    }

    // собираем js
    private function js($buffer) {
        preg_match_all('/<script\b[^>]*><\/script>/is', $buffer, $scripts);
        $scripts = $this->external_url($this->unique($scripts['0']));
        foreach ($scripts as &$script) {
            preg_match('/src=(["\'])(.*?)\1/', $script, $script_name);
            $file_name = $script_name['2'];
            if (!empty($file_name)) {
                if (is_readable($file_name)) {
                    if(stristr($script_name['2'],'//')) {
                        if(stristr($script_name['2'],$_SERVER['SERVER_NAME'])) {
                            $this->js_array[] = $file_name;
                        }
                    } else {
                        $this->js_array[] = $file_name;
                    }
                }
            }
            unset($script,$script_name,$file_name);
        }

        // удаляем старые скрипты
        $buffer = str_replace($this->js_array, 'remove', $buffer);
        $buffer = preg_replace('/<script(.*?)remove(.*?)><\/script>/', '', $buffer);
        $_js_file = md5(serialize($this->js_array)) . '.js' . $this->jgz;
        $this->output_js = $this->out_folder . '/' . $_js_file;
        if ($this->file_check($this->output_js)) {
            $this->concatFiles('js');
        }

        return $buffer;
    }

    // собираем js из контента, сжимаем html и js
    private function html($buffer) {
        preg_match_all('/<script>(.*?)<\/script>/is', $buffer, $html_js_1);
        preg_match_all('/<script type="text\/javascript">(.*?)<\/script>/is', $buffer, $html_js_2);
        $html_js = array_merge($html_js_1['1'], $html_js_2['1']);

        foreach ($html_js as $i => &$js) {
            if(!empty($js)) {
                $search = ['<script>'.$js.'</script>','<script type="text/javascript">'.$js.'</script>'];
                $buffer = str_replace($search,'<script data-s="' . $i . '" type="text/javascript">' . $js . '</script>', $buffer);
                unset($search);
            }
            unset($js,$i);
        }

        // сжимаем html
        $buffer= preg_replace('|\s+|', ' ', $buffer);

        // возвращаем js на место
        foreach ($html_js as $i => &$js) {
            $js = JSMin::minify($js);
            if (!$this->status['async']) {
                //$buffer = preg_replace('/<script data-s="' . $i . '" type="text\/javascript">(.*?)<\/script>/is', '<script type="text/javascript">' . $js . '</script>', $buffer);
            } else {
                $buffer = preg_replace('/<script data-s="' . $i . '" type="text\/javascript">(.*?)<\/script>/is', '<script type="text/javascript">minify_out.script_' . $i . '=function(){' . $js . '};</script>', $buffer);
            }
            unset($js,$i);
        }

        return $buffer;
    }

    // вставляем наши новые файлы в конец тега head
    private function out($buffer) {
        $string = '';
        $string .= $this->status['js'] && !$this->status['async'] ? '<script src="/' . $this->output_js . '" type="text/javascript"></script>' : '';
        $string .= $this->status['css'] ? '<link href="/' . $this->output_css . '" type="text/css" rel="stylesheet" />' : '';
        $buffer = str_replace('</head>', $string . '</head>', $buffer);

        if ($this->status['async']) {
            $buffer = str_replace('</head>','<script type="text/javascript">var minify_out={};</script></head>', $buffer);
            $buffer = str_replace('</body>','<script src="/' . $this->output_js . '" type="text/javascript" async></script></body>', $buffer);
        }

        $buffer = str_replace('</body>', '<!-- Minify by https://github.com/milkamil93 --></body>',$buffer);
        return $buffer;
    }

    // исправляем путь до файлов прописанные в url() css
    private function getPath($file){
        if(empty($file)) return '';
        $outFile = dirname($file) . "/";
        $outFile = '/' . str_replace($this->out_folder, '/', $outFile) . '/';
        $outFile = str_replace('//', '/', $outFile);
        return $outFile;
    }

    // проверка массива на пустоту и повторения
    private function unique($array) {
        $array = array_diff($array, array(''));
        $array = array_unique($array);
        return array_map('trim', $array);
    }

    // поиск и удаление внешних ссылок в массиве
    private function external_url($array) {
        $out = [];
        foreach ($array as &$item) {
            if (!(strpos($item,'//') && !strpos($item,$_SERVER['HTTP_HOST'])))
                $out[] = $item;
            unset($item);
        }
        return $out;
    }
}