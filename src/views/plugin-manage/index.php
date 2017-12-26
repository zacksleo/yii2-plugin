<?php
use yii\web\View;
use yii\helpers\Url;
use zacksleo\yii2\plugin\components\PluginManger;
use zacksleo\yii2\plugin\Module;

/* @var $this View */
?>

<div class="portlet light ">
    <div class="portlet-title">
        <div class="caption">
            插件管理
        </div>
    </div>
    <div class="portlet-body">
        <?php foreach ($plugins as $status => $_plugins):
        if (empty($_plugins)) {
            continue;
        }
        ?>
        <div id="tbl-plugins">
            <?php
            switch ($status):
                case PluginManger::STATUS_ENABLED:
                    echo '<h4 class="text-success">' . Module::t("plugin", "Enabled Plugins") . ': </h4>';
                    break;
                case PluginManger::STATUS_INSTALLED:
                    echo '<h4 class="text-warning">' . Module::t("plugin", "Disabled Plugins") . ': </h4>';
                    break;
                case PluginManger::STATUS_NOT_INSTALLED:
                    echo '<h4 class="text-info">' . Module::t("plugin", "Not Installed Plugins") . ': </h4>';
                    break;
            endswitch;
            ?>
            <br/>
            <?php foreach ($_plugins as $plugin): ?>
                <div class="row">
                    <div class="col-md-1">
                        <img class="img-responsive"
                             src="<?php echo $plugin['plugin']->icon() ? $plugin['plugin']->icon() : $this->context->defaultIcon; ?>"/>
                    </div>
                    <div class="col-md-4">
                        <div class="plg-name"> <?php echo $plugin['plugin']->name; ?>
                            (Ver:<?php echo $plugin['plugin']->version; ?>)
                        </div>
                        <div class="plg-id"><?php echo $plugin['plugin']->identify; ?></div>
                        <div class="plg-cp"><a
                                href="<?php echo $plugin['plugin']->website; ?>"><?php echo $plugin['plugin']->copyright; ?></a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div>
                            <em>
                                <?php echo Module::t('plugin', "Description"); ?>
                                : </em><?php echo $plugin['plugin']->description; ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-right">
                            <?php
                            switch ($status):
                                case PluginManger::STATUS_ENABLED:
                                    ?>
                                    <a class="btn btn-xs btn-link p_disable"
                                       title="<?php echo Module::t('plugin', 'Disable'); ?>"
                                       data-id="<?php echo $plugin['plugin']->identify; ?>">
                                        <i class="fa fa-pause fa-2x"></i>
                                    </a>

                                    <a class="btn btn-xs btn-link"
                                       title="<?php echo Module::t('plugin', 'Setting'); ?>"
                                       href="<?php echo Url::to(['/plugin/plugin-manage/setting', 'id' => $plugin['plugin']->identify]); ?>"
                                    >
                                        <i class="fa fa-cogs fa-2x"></i>
                                    </a>

                                    <a class="btn btn-xs btn-link p_uninstall"
                                       title="<?php echo Module::t('plugin', 'Uninstall'); ?>"
                                       data-id="<?php echo $plugin['plugin']->identify; ?>">
                                        <i class="fa fa-trash fa-2x"></i>
                                    </a>
                                    <?php
                                    break;
                                case PluginManger::STATUS_INSTALLED:
                                    ?>
                                    <a class="btn btn-xs btn-link p_enable"
                                       title="<?php echo Module::t('plugin', 'Enable'); ?>"
                                       data-id="<?php echo $plugin['plugin']->identify; ?>">
                                        <i class="fa fa-play fa-2x" rel="tooltip"></i>
                                    </a>

                                    <a class="btn btn-xs btn-link"
                                       title="<?php echo Module::t('plugin', 'Setting'); ?>"
                                       href="<?php echo Url::to(['/plugin/plugin-manage/setting', 'id' => $plugin['plugin']->identify]); ?>">
                                        <i class="fa fa-cogs fa-2x"></i>
                                    </a>
                                    <a class="btn btn-xs btn-link p_uninstall"
                                       title="<?php echo Module::t('plugin', 'Uninstall'); ?>"
                                       data-id="<?php echo $plugin['plugin']->identify; ?>">
                                        <i class="fa fa-trash fa-2x"></i>
                                    </a>
                                    <?php
                                    break;
                                case PluginManger::STATUS_NOT_INSTALLED:
                                    ?>
                                    <a
                                        class="btn btn-xs btn-link p_install"
                                        title="<?php echo Module::t('plugin', 'Install'); ?>"
                                        data-id="<?php echo $plugin['plugin']->identify; ?>">
                                        <i class="fa fa-download fa-2x" rel="tooltip"></i>
                                    </a>
                                    <?php
                                    break;
                            endswitch;
                            ?>
                            <a class="btn btn-xs btn-link"
                               title="<?php echo Module::t('plugin', 'View'); ?>">
                                <i class="fa fa-eye fa-2x" rel="tooltip"></i>
                            </a>
                        </div>
                    </div>
                    <div style="clear:both;"></div>
                    <hr/>
                </div>
            <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>


<?php

$script = '
var csrfToken = $(\'meta[name="csrf-token"]\').attr("content");
jQuery(".p_install").click(function(){
		jQuery.post("' . Url::to(["/plugin/plugin-manage/install"]) . '",{id:jQuery(this).data("id"),_csrf : csrfToken},function(data){
			if(data.status){
				window.location.reload();
			}
		},"json");
});
jQuery(".p_uninstall").click(function(){
	if(confirm("' . Module::t('plugin', "Plugin data will be removed after uninstall, sure to uninstall?") . '")==true){
		jQuery.post("' . Url::to(["/plugin/plugin-manage/uninstall"]) . '",{id:jQuery(this).data("id"),_csrf : csrfToken},function(data){
			if(data.status){
				window.location.reload();
			}
		},"json");
	}else{
		jQuery(this).mouseout();
	}
});
jQuery(".p_enable").click(function(){
	jQuery.post("' . Url::to(["/plugin/plugin-manage/enable"]) . '",{id:jQuery(this).data("id"),_csrf : csrfToken},function(data){
		if(data.status){
			window.location.reload();
		}
	},"json");
});
jQuery(".p_disable").click(function(){
	jQuery.post("' . Url::to(["/plugin/plugin-manage/disable"]) . '",{id:jQuery(this).data("id"),_csrf : csrfToken},function(data){
		if(data.status){
			window.location.reload();
		}
	},"json");
});';

Yii::$app->request->isAjax ? print "<script>$script</script>" : $this->registerJs($script, View::POS_END);
?>
