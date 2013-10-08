# Plugin Module
---
## Module Usage
### Install
Add these array in the project config (if you have more than one entries,add these in both of them)
    
    'components' => array(
        array(
            'plugin' => 'application.modules.plugin.components.HookRender', # HookRender path router
        )
    ),
    
    'modules' => array(
        array(
            'plugin' => 'application.modules.plugin.PluginModule', # Module path router
            'pluginDir' => 'application.Plugins',   # Folder for plugins,make sure it is writeable.
            'layout' => '//layout/main',            # layout of admin control panel.
        )
    ),

### Create table
Run sql statement in folder `sql` or apply migrate with yiic (see [Yii doc](http://www.yiiframework.com/doc/guide/1.1/en/database.migration#applying-migrations))

### Link to plugin control panel
The CP url is :
    
    $this->createUrl(array('/plugin/PluginManage/index'));
### Add hooks in the views
just add this to the position you want to be hooked
Yii::app()->plugin->render('Hook_Name');  # Name the Hook Position and told it to your plugin developers. 
### Requirement
* Jquery (for control panel)
* Yii-bootstrap (control panel interface)

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
        
        public function init() {
            $this->pluginDir = dirname(__FILE__);   #required, and it is always the same.
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
        public function Hooks(){
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
        }
        
        // If your plugin allow to set configs at the admin control panel
        // You need to inherit this:
        
        public function admincp() {
            // You can put codes here.
            // Like some inputs
        }
        
        // Here you can put some code at the Installation and Uninstallation
        public function Install() {
            //codes here
            $sql = "create `tbl_xxxxxx` .....";     # write sql with a table prefix 
            $this->query($sql,'tbl_');              # and pass it at method query
                                                    # as default, it is 'tbl_'
            return true; #This method need to return true, or the installation will fail.
        }
        
        public function Uninstall() {
            // just like the method install.
            return true; #This method need to return true, or the uninstallation will fail.
        }
        
        // Then, you created a simple plugin
        // For advanced usage, see examples
    }
