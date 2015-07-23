<div class="operator-info float-break<?php if (!isset($start_data_fields['remove_operator_space']) || $start_data_fields['remove_operator_space'] == false) : ?> mb10 round-profile<?php else : ?><?php endif;?>">
	<div class="left pr5">		 
     	<?php if ($theme !== false && $theme->operator_image_url != '') : ?>
     			<img src="<?php echo $theme->operator_image_url?>" alt="" />
     	<?php else : ?>
     		<i class="icon-user icon-assistant"></i>
     	<?php endif;?> 
     </div>
     <div class="pl10">
     	 <?php $rightLanguage = true;?>
	 	<?php include(erLhcoreClassDesign::designtpl('pagelayouts/parts/switch_language.tpl.php'));?>
	    <h5 class="subheader"><i><?php if ($theme !== false && $theme->intro_operator_text != '') : ?><?php echo htmlspecialchars($theme->intro_operator_text); ?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Have a question? Ask us!');?>
	    <?php endif;?>
	    </i></h5>
     </div>
</div>
