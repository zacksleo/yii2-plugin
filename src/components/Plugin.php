<?php

namespace zacksleo\yii2\plugin\components;

use yii;
use yii\helpers\Url;
use zacksleo\yii2\plugin\models\PluginSetting;

/**
 * Class Plugin.
 * @package zacksleo\yii2\plugin\components
 * @property string $identify
 */
abstract class Plugin extends PluginBase
{
    public $identify;

    private $info = [
        'identify' => '',
        'version' => '',
        'copyright' => '',
        'website' => '',
        'description' => '',
        'name' => '',
        'icon' => '',
    ];

    public function __construct()
    {
        parent::__construct();
        $class = get_class($this);
        $reflection = new \ReflectionClass($class);
        $this->pluginDir = dirname($reflection->getFileName());
    }

    /**
     * return an array of hooks,all plugins have to implement this method.
     * @return array Hooks list
     */
    abstract public function hooks();

    /**
     * adv usage
     * return an array of Actions's class name.
     * @return array Actions list
     */
    public function actions()
    {
        return false;
    }

    /**
     * adv usage
     * return admincp file's name
     * @return string admincp
     */
    public function setAdminCp()
    {
        return false;
    }

    /**
     * install plugin
     * this method will called automatic when "Install" clicked.
     * inherit this method to do more things such as execute a sql statement
     * MUST TETURN TRUE.
     *
     * @return boolean
     */
    public function install()
    {
        return true;
    }

    /**
     * uninstall plugin
     * this method will called automatic when "Uninstall" clicked.
     * inherit this method to do more things such as execute a sql statement
     * MUST TETURN TRUE
     * @return boolean
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * methods below can be used in plugins.
     * execute a sql statement
     * prefix in sql will be replace to the project's prefix
     * @param string $sql sql statement
     * @param string $prefix prefix in sql statement
     * @return boolean
     */
    protected function query($sql, $prefix = 'tbl_')
    {
        $db = Yii::$app->db;
        $tablePrefix = $db->tablePrefix;
        if ($tablePrefix != $prefix) {
            $sql = str_replace($prefix, $tablePrefix, $sql);
        }
        $transaction = $db->beginTransaction();
        try {
            $db->createCommand($sql)->execute();
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            return false;
        }
        return true;
    }

    /**
     * Internal methods
     */
    public function icon()
    {
        if (!$this->icon) {
            return false;
        }
        $publishedPath = Yii::$app->getAssetManager()->publish($this->pluginDir . DIRECTORY_SEPARATOR . $this->icon);
        return (Url::to($publishedPath[1], true));
    }

    /**
     * @param Plugin
     * @return PluginHook|boolean
     */
    public function loadHook($string)
    {
        $hk = explode('.', $string);
        if (!$hk[1]) {
            return false;
        }
        include_once($this->pluginDir . DIRECTORY_SEPARATOR . $hk[0] . DIRECTORY_SEPARATOR . $this->identify . $hk[1] . '.php');
        $class = $this->identify . $hk[1];
        if (!class_exists($class)) {
            return false;
        }
        $hook = new $class();
        $hook->Owner($this);
        return $hook;
    }

    public function clear()
    {
        PluginSetting::clear($this->identify);
        return true;
    }

    public function __get($name)
    {
        $value = parent::__get($name);
        if ($value !== false) {
            return $value;
        }
        if (isset($this->info[$name])) {
            return htmlspecialchars($this->info[$name]);
        }
    }

    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            return $this->$setter($name, $value);
        } elseif (isset($this->info[$name])) {
            $this->info[$name] = $value;
        } else {
            return false;
        }
    }
}
