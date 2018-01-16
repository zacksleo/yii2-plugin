# Plugin Module

[![Latest Stable Version](https://poser.pugx.org/zacksleo/yii2-plugin/version)](https://packagist.org/packages/zacksleo/yii2-plugin)
[![Total Downloads](https://poser.pugx.org/zacksleo/yii2-plugin/downloads)](https://packagist.org/packages/zacksleo/yii2-plugin)
[![License](https://poser.pugx.org/zacksleo/yii2-plugin/license)](https://packagist.org/packages/zacksleo/yii2-plugin)

Languages: [English](#) [中文](https://github.com/zacksleo/yii2-plugin/blob/master/README_cn.md)

---
![cp](http://i.minus.com/ibnW5OhPBsUboA.jpg)

## FEATURES
* This module provides a plugin pattern(Plug-and-Play) solustion.
* No need to edit any file to configure plugin, it can install, uninstall, enable and disable at admin control panel.
* Plugins do not modify project files, it can be uninstall safely.
* Extendable. Can add hooks to any views.
* Encapsulate Yii functions, easy for non-Yii developer to create a plugin

## Module Usage

### Install

```
   composer install --prefer-dist zacksleo/yii2-plugin   
```   

Add these array in the project config (if you have more than one entries,add these in both of them)
    
    'components' =>[
        'plugin' => [
            'class' => 'zacksleo\yii2\plugin\components\HookRender'
        ],
    ],
    
    'modules' => [
    
        'plugin' => [
            'class' => 'zacksleo\yii2\plugin\Module',
            'layout' => 'layout',
            'layoutPath' => '@vendor/zacksleo/yii2-backend/src/views/layouts', #布局
            'pluginRoot' => '@vendor/moguyun-plugins/', ##放置插件的namespace目录
            'pluginNamespace' => '@moguyun/plugins',  ##放置插件的namespace
        ],  
    ]


### Create table

```
 yii migrate/up --migrationPath=@zacksleo/yii2/plugin/migrations
```

### Link to plugin control panel
The CP url is :
    
    $this->createUrl(['/plugin/plugin-manage/index']);

### Add hooks in the views

    #just add this to the position you want to be hooked
    Yii::$app->plugin->render('Hook_Name');   # Name the Hook Position and told it to your plugin developers. 

---

## Plugin Develop

### Create A Plugin
For Create a plugin, you need to inherit the class `Plugin`.

And the class name and class file name should end with the word `Plugin`.
For expamle, file `ExamplePlugin.php`:
    
    class ExamplePlugin extends Plugin {
        //codes here
    }
The word `Example` (without word `Plugin`) is the plugin's __identify__ (case sensitive).
###Implement The Plugin
To implement the plugin and makes it work, you should inherit these method and initialize some properties.

    class ExamplePlugin extends Plugin {
        
        public function init() {                    #initialize,config plugin's info, required
            // set plugin's info
            $this->identify = 'Example';            #required, the Unique id for this plugin.
            $this->name = 'Example Plugin';         #required, plugin's name for display.
            $this->version = '1.0';
            $this->description = 'description here';
            $this->copyright = '&copy; Robin &lt;Robin@email.com&gt;';
            $this->website = 'http://example.com';   
            $this->icon = 'icon.png'; #max to 72*72, if not set a default icon will display in the admin cp;
        }
        
        // return hooks array which this plugin want to hook, the value is the method's name
        // for the hook.
        public function hooks(){
            return array(
                //'Hook Position Name' => 'hook method';
                'Hook_Index_Header' => 'header',
            );
        }
        
        // method for hook
        public function header(){
            // some codes here
            echo 'This will echo a sting at position Hook_Index_Header';
        }
        
        // If you want to display a page with an url instead of render as a widget,
        // you need to write a method begin with the word "action", the word after 
        // "action" is the action's name.
        // e.g.:
        public function actionPage() {
            echo "This action have a url like this (with url rewrite):";
            # domain/plugin<Module>/plugin<controller name>/index<router action name>
            # ?id=xx<plugin identify>&action=xxx<action name>
            echo "You_Domain.com/plugin/plugin/index?id=example&action=page";

            #You can create this url by call method 'createUrl'
            echo $this->createUrl('page',array('param'=>'test'));
        }

        public function actionExample(){
            # this action is named with plugin's identify,
            # param $action could be empty or false value
            echo $this->createUrl();
        }      

        // If your plugin allow to set configs at the admin control panel
        // You need to inherit this:
        
        public function admincp() {
            // You can put codes here.
            // Like some inputs
            $this->setSetting('key','value');   # write setting
            echo $this->getSetting('key');      # read setting
        }
        
        // Here you can put some code at the Installation and Uninstallation
        public function install() {
            //codes here
            $sql = "create `tbl_xxxxxx` .....";     # write sql with a table prefix 
            $this->query($sql,'tbl_');              # and pass it at method query
                                                    # as default, it is 'tbl_'
            return true; #This method need to return true, or the installation will fail.
        }
        
        public function uninstall() {
            // just like the method install.
            return true; #This method need to return true, or the uninstallation will fail.
        }
        
        // Then, you created a simple plugin
        // For advanced usage, see demos
    }
