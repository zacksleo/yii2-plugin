<?php
/**
 * Yii-Plugin module
 * 
 * @author Viking Robin <healthlolicon@gmail.com> 
 * @link https://github.com/health901/yii-plugins
 * @license https://github.com/health901/yii-plugins/blob/master/LICENSE
 * @version 1.0
 */
abstract class PluginAbstract extends PluginBase {

	public $plugin;

	public function run() {
		return FALSE;
	}

	public function __get($name) {
		$value = parent::__get($name);
		if (!$value && $this->plugin->$name !== NULL) {
			return $this->plugin->$name;
		} else {
			return $value;
		}
	}

	public function Owner($plugin) {
		$this->plugin = $plugin;
		$this->i18n = $plugin->i18n;
		$this->pluginDir = $plugin->pluginDir;
	}

}

/**
 * 插件单页类
 */
class PluginAction extends PluginAbstract {
	
}
/**
 * 插件管理页类
 */
class PluginAdmin extends PluginAbstract {
	
}
/**
 * 插件钩子类
 */
class PluginHook extends PluginAbstract {
	
}

?>
