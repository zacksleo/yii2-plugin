<style type="text/css">
	#tbl-plugins {
		width: 960px;
		font-size: 12px;
		line-height: 17px;
		margin-bottom: 30px;
	}

	#tbl-plugins .g-plg {
		font-size: 15px;
		font-weight: bold;
		margin-bottom: 5px;
	}

	.col {
		border: 1px solid #EEE;
		border-top: 3px solid #DDD;
		padding: 5px;
		background: #F3F3F5;
	}

	.col .fl {
		float: left;
		min-height: 72px;
	}

	.col .fr {
		float: right;
		min-height: 72px;
	}

	.col-img {
		width: 90px;
	}

	.col-label {
		width: 250px;
	}

	.col-desc {
		width: 488px;
		color: #333;
	}

	.col-option {
		width: 120px;
		text-align: center;
	}

	.col-img img {
		display: block;
	}

	.col-desc em {
		font-style: normal;
	}

	.col-option div {
		margin-top: 25px;
	}
	.col-option a {
		color:black;
		text-decoration : none;
		margin-right: 5px;
	}
	.col-label .plg-name {
		font-size: 13px;
		font-weight: bold;
		margin-top: 5px;
	}

	.col-label .plg-id {
		font-size: 11px;
		margin-top: 5px;
	}

	.col-label .plg-cp {
		margin-top: 10px;
	}

	.col-desc div {
		margin: 5px;
	}
</style>
<?php
foreach ($plugins as $status => $_plugins) {
	if (empty($_plugins))
		continue;
	?>
	<div id="tbl-plugins">
		<?php
		switch ($status) {
			case PluginManger::STATUS_Enabled:
			echo '<div class="g-plg">'.Yii::t("PluginModule.lang","Enabled Plugins").': </div>';
			break;
			case PluginManger::STATUS_Installed:
			echo '<div class="g-plg">'.Yii::t("PluginModule.lang","Disabled Plugins").': </div>';
			break;
			case PluginManger::STATUS_NotInstalled:
			echo '<div class="g-plg">'.Yii::t("PluginModule.lang","Not Installed Plugins").': </div>';
			break;
		}
		?>

		<?php foreach ($_plugins as $plugin) { ?>
		<div class="col">
			<div class="col-img fl">
				<div>
					<img
					src="<?php echo $plugin['plugin']->Icon() ? $plugin['plugin']->Icon() : $this->defaultIcon; ?>"
					width="72"
					height="72"/>
				</div>
			</div>
			<div class="col-label fl">
				<div class="plg-name"> <?php echo $plugin['plugin']->name; ?>
					(Ver:<?php echo $plugin['plugin']->version; ?>)
				</div>
				<div class="plg-id"><?php echo $plugin['plugin']->identify; ?></div>
				<div class="plg-cp"><a
					href="<?php echo $plugin['plugin']->website; ?>"><?php echo $plugin['plugin']->copyright; ?></a>
				</div>
			</div>
			<div class="col-desc fl">
				<div><em><?php echo Yii::t("PluginModule.lang","Description");?>: </em><?php echo $plugin['plugin']->description; ?></div>
			</div>
			<div class="col-option fl">
				<div>
					<?php
					switch ($status) {
						case PluginManger::STATUS_Enabled:
						?>
						<span><?php echo CHtml::link('<i class="icon-pause"></i>','javascript:;',array('data-id'=>$plugin['plugin']->identify,'class'=>'p_disable','title' => Yii::t("PluginModule.lang",'Disable'))); ?></span>
						<span><?php echo CHtml::link('<i class="icon-cog"></i>',$this->createUrl('/plugin/pluginManage/setting',array('id'=>$plugin['plugin']->identify)),array('title'=>Yii::t("PluginModule.lang",'Setting'))); ?></span>
						<span><?php echo CHtml::link('<i class="icon-remove"></i>','javascript:;',array('data-id'=>$plugin['plugin']->identify,'class'=>'p_uninstall','title' => Yii::t("PluginModule.lang",'Uninstall'))); ?></span>
						<?php
						break;
						case PluginManger::STATUS_Installed:
						?>
						<span><?php echo CHtml::link('<i class="icon-play"></i>','javascript:;',array('data-id'=>$plugin['plugin']->identify,'class'=>'p_enable','title' => Yii::t("PluginModule.lang",'Enable'))); ?></span>
						<span><?php echo CHtml::link('<i class="icon-cog"></i>',$this->createUrl('/plugin/pluginManage/setting',array('id'=>$plugin['plugin']->identify)),array('title'=>Yii::t("PluginModule.lang",'Setting'))); ?></span>
						<span><?php echo CHtml::link('<i class="icon-remove"></i>','javascript:;',array('data-id'=>$plugin['plugin']->identify,'class'=>'p_uninstall','title' => Yii::t("PluginModule.lang",'Uninstall'))); ?></span>
						<?php
						break;
						case PluginManger::STATUS_NotInstalled:
						?>
						<span><?php echo CHtml::link('<i class="icon-off"></i>','javascript:;',array('data-id'=>$plugin['plugin']->identify,'class'=>'p_install','title' => Yii::t("PluginModule.lang",'Install'))); ?></span>
						<?php
						break;
					}
					?>
					<span><?php echo CHtml::link('<i class="icon-eye-open"></i>','#',array('title' => Yii::t("PluginModule.lang",'View'))); ?></span>
				</div>
			</div>
			<div style="clear:both;"></div>
		</div>
		<?php } ?>
	</div>
	<?php } ?>
	<script type="text/javascript">
		$(function(){
			$('.p_install').click(function(){
				$.post('<?php echo $this->createUrl("/plugin/pluginManage/install");?>',{id:$(this).data('id')},function(data){
					if(data.status){
						window.location.reload();
					}
				},'json');
			});
			$('.p_uninstall').click(function(){
				if(confirm("<?php echo Yii::t("PluginModule.lang",'Plugin data will be removed after uninstall, sure to uninstall?'); ?>")==true){
					$.post('<?php echo $this->createUrl("/plugin/pluginManage/uninstall");?>',{id:$(this).data('id')},function(data){
						if(data.status){
							window.location.reload();
						}
					},'json');
				}
			});
			$('.p_enable').click(function(){
				$.post('<?php echo $this->createUrl("/plugin/pluginManage/enable");?>',{id:$(this).data('id')},function(data){
					if(data.status){
						window.location.reload();
					}
				},'json');
			});
			$('.p_disable').click(function(){
				$.post('<?php echo $this->createUrl("/plugin/pluginManage/disable");?>',{id:$(this).data('id')},function(data){
					if(data.status){
						window.location.reload();
					}
				},'json');
			});
		});
</script>