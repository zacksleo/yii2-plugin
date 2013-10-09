<?php
/**
 * Yii-Plugin module
 * 
 * @author Viking Robin <healthlolicon@gmail.com> 
 * @link https://github.com/health901/yii-plugin
 * @license https://github.com/health901/yii-plugins/blob/master/LICENSE
 * @version 1.0
 */
class PluginBase extends CBaseController
{

	public $pluginDir;
	public $viewDir = 'views';
	public $i18n;

	public function __construct(){
		$this->init();
	}

	public function __get($name)
	{
		$getter = 'get' . $name;
		if (method_exists($this, $getter))
			return $this->$getter();
		return FALSE;
	}

	/**
	 * translate a string
	 * see http://www.yiiframework.com/doc/api/1.1/YiiBase#t-detail for detail
	 * @param string $category source filename
	 * @param string $message  the original message
	 * @param array  $params   parameters to be applied to the message
	 * @param string $language the target language
	 * @return string translated the translated message
	 */
	public function T($category, $message, $params = array(), $language = NULL)
	{
		$category = $this->identify . 'Plugin.' . $category;
		return Yii::t($category, $message, $params, NULL, $language);
	}

	/**
	 * Publishes a file or a directory.
	 * This method will copy the specified asset to a web accessible directory and return the URL for accessing the published asset. 
	 * @param string $path the asset (file or directory) to be published
	 * @return string an absolute URL to the published asset
	 */
	public function PublishAssets($path)
	{
		if (substr($path, 0, 1) != '/') {
			$path = DIRECTORY_SEPARATOR . $path;
		}
		$path = $this->pluginDir . $path;
		return '//' . $_SERVER['HTTP_HOST'] . Yii::app()->getAssetManager()->publish($path);
	}

	public function render($view, $data = NULL, $return = FALSE)
	{
		if(!$view)
			return FALSE;
		if (($viewFile = $this->getViewFile($view)) !== FALSE) {
			return $this->renderFile($viewFile, $data, $return);
		}
	}

	public function getViewFile($viewName)
	{
		$ext = '.php';
		if (strpos($viewName, '.'))
			$viewName = str_replace('.', '/', $viewName);
			$root = $this->viewDir ? $this->pluginDir . DIRECTORY_SEPARATOR . $this->viewDir : $this->pluginDir;
		if ($this->i18n == 'path') {
			$root = $root . DIRECTORY_SEPARATOR . Yii::app()->getLanguage();
		}
		$viewFile = $root . DIRECTORY_SEPARATOR . $viewName . $ext;

		if (is_file($viewFile)) {
			return $viewFile;
		}
		return FALSE;
	}
}