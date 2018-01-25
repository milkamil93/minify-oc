<?php
/**
 ***
 *** Minify by https://github.com/milkamil93
 ***
 **/
class ControllerExtensionModuleMinify extends Controller {

    private $error = array();

    public function install() {
        $this->load->model('setting/setting');
        $settings = [
            'minify_status' => 1,
            'minify_gzip' => 0,
            'minify_css' => 1,
            'minify_js' => 1,
            'minify_html' => 1,
            'minify_time' => '43200',
            'minify_async' => 0
        ];
        $this->model_setting_setting->editSetting('minify', $settings);

        $data = file_get_contents(DIR_SYSTEM . 'framework.php');
        if (!strpos($data,'$loader->controller(\'extension/module/minify/minify\');')) {
            $data = str_replace('$response->output();', '$loader->controller(\'extension/module/minify/minify\');' . PHP_EOL . '$response->output();', $data);
            file_put_contents(DIR_SYSTEM . 'framework.php', $data);
        }
    }

    public function uninstall() {
        $this->load->model('setting/setting');
        $this->model_setting_setting->deleteSetting('minify');

        $data = file_get_contents(DIR_SYSTEM . 'framework.php');
        $data = str_replace('$loader->controller(\'extension/module/minify/minify\');' . PHP_EOL, '', $data);
        file_put_contents(DIR_SYSTEM . 'framework.php', $data);
    }

    public function index() {
        $data = $this->load->language('extension/module/minify');
        $this->document->setTitle($this->language->get('heading_title'));
        $data['text_edit'] = $this->language->get('text_edit');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['cache_del'] = $this->language->get('cache_del');
        $data['text_gzip'] = $this->language->get('text_gzip');
        $data['text_css'] = $this->language->get('text_css');
        $data['text_js'] = $this->language->get('text_js');
        $data['text_time'] = $this->language->get('text_time');
        $data['text_async'] = $this->language->get('text_async');

        $this->load->model('setting/setting');
        $config = $this->model_setting_setting->getSetting('minify');

        if (($this->request->server['REQUEST_METHOD'] === 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('minify', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            //$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true));

        }

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/minify', 'token=' . $this->session->data['token'], true)
        );


        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['minify_status'])) {
            $data['minify_status'] = $this->request->post['minify_status'];
        } elseif (!empty($config)) {
            $data['minify_status'] = $config['minify_status'];
        } else {
            $data['minify_status'] = '';
        }

        if (isset($this->request->post['minify_gzip'])) {
            $data['minify_gzip'] = $this->request->post['minify_gzip'];
        } elseif (!empty($config)) {
            $data['minify_gzip'] = $config['minify_gzip'];
        } else {
            $data['minify_gzip'] = '';
        }

        if (isset($this->request->post['minify_css'])) {
            $data['minify_css'] = $this->request->post['minify_css'];
        } elseif (!empty($config)) {
            $data['minify_css'] = $config['minify_css'];
        } else {
            $data['minify_css'] = '';
        }

        if (isset($this->request->post['minify_js'])) {
            $data['minify_js'] = $this->request->post['minify_js'];
        } elseif (!empty($config)) {
            $data['minify_js'] = $config['minify_js'];
        } else {
            $data['minify_js'] = '';
        }

        if (isset($this->request->post['minify_html'])) {
            $data['minify_html'] = $this->request->post['minify_html'];
        } elseif (!empty($config)) {
            $data['minify_html'] = $config['minify_html'];
        } else {
            $data['minify_html'] = '';
        }

        if (isset($this->request->post['minify_time'])) {
            $data['minify_time'] = $this->request->post['minify_time'];
        } elseif (!empty($config)) {
            $data['minify_time'] = $config['minify_time'];
        } else {
            $data['minify_time'] = '';
        }

        if (isset($this->request->post['minify_async'])) {
            $data['minify_async'] = $this->request->post['minify_async'];
        } elseif (!empty($config)) {
            $data['minify_async'] = $config['minify_async'];
        } else {
            $data['minify_async'] = '';
        }

        $data['token'] = $this->session->data['token'];

        $data['action'] = $this->url->link('extension/module/minify', 'token=' . $this->session->data['token'], true);
        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);
        $data['clear'] = $this->url->link('extension/module/minify/clear', 'token=' . $this->session->data['token'], true);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/minify', $data));
    }

    public function clear() {
        $this->load->language('extension/module/minify');

        $json = array();

        if (!$this->validate()) {
            $json['error'] = $this->language->get('error_permission');
        } else {

            if ($this->config->get('config_theme') == 'theme_default') {
                $theme = $this->config->get('theme_default_directory');
            } else {
                $theme = $this->config->get('config_theme');
            }

            $minify_folder = $_SERVER['DOCUMENT_ROOT'] . '/catalog/view/theme/' . $theme . '/minify/';

            $patterns = array(
                $minify_folder . '*.css',
                $minify_folder . '*.js',
                $minify_folder . '*.jgz'
            );

            foreach ($patterns as $pattern) {
                foreach (glob($pattern) as $file) {
                    unlink($file);
                }
            }

            $json['success'] = true;

        }

        $this->session->data['success'] = $this->language->get('text_cache');
        $this->response->redirect($this->url->link('extension/module/minify', 'token=' . $this->session->data['token'], true));
    }

    protected function validate() {
        if (!$this->user->hasPermission('access', 'extension/module/minify')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

}