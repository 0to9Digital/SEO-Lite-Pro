    <div class="panel theme--ee<?=substr(APP_VER, 0, 1)?>">
        <div class="panel-heading">
            <div class="title-bar">
                <h3 class="title-bar__title"><?= lang('settings'); ?></h3>					
            </div>
        </div> 
        <div class="panel-body">
                <?php echo form_open($save_settings_url, array('class' => 'settings'))?>
                <?php echo ee('CP/Alert')->get('seolite-settings-saved')?>

                <input type="hidden" name="seolite_default_keywords" id="seolite_default_keywords" value=""/>

                <fieldset class="col-group">
                    <div class="setting-txt">
                        <label for="seolite_default_description"><?= lang('default_description'); ?></label>
                    </div>
                    <div class="setting-field">
                        <div class="seeo__instructions field-instruct"><em><?= lang('default_description_instructions'); ?></em></div>
                        <input type="text" name="seolite_default_description" id="seolite_default_description" value="<?=htmlspecialchars($default_description)?>"/>
                    </div>
                </fieldset>

                <fieldset class="col-group">
                    <div class="setting-txt">
                        <label for="seolite_default_title_postfix"><?= lang('default_title_postfix'); ?></label>
                    </div>
                    <div class="setting-field">
                        <div class="seeo__instructions field-instruct"><em><?= lang('default_title_postfix_description'); ?></em></div>
                        <input type="text" name="seolite_default_title_postfix" id="seolite_default_title_postfix" value="<?=htmlspecialchars($default_title_postfix)?>"/>
                    </div>
                </fieldset>

                <h4><?= lang('og_tags'); ?></h4>

                <fieldset class="col-group">
                    <div class="setting-txt">
                        <label for="seolite_default_og_description"><?= lang('og_description'); ?></label>
                    </div>
                    <div class="setting-field">
                        <div class="seeo__instructions field-instruct"><em><?= lang('og_description_instr'); ?></em></div>
                        <input type="text" name="seolite_default_og_description" id="seolite_default_og_description" value="<?=htmlspecialchars($default_og_description)?>"/>
                    </div>
                </fieldset>

                <fieldset class="col-group">
                    <div class="setting-txt">
                        <label for="seolite_default_og_image"><?= lang('og_image'); ?></label>
                    </div>
                    <div class="setting-field">
                        <div class="seeo__instructions field-instruct"><em><?= lang('og_image_instructions'); ?></em></div>
                        <div class="field-control c-upload-image">
                            <?php
                                $filepicker = ee('CP/FilePicker')->make();

                                $link = $filepicker->getLink(($default_og_image) ? 'Change OG image' : 'Add OG image');
                                $link->setAttribute('class', 'button button--secondary upload-image__button');
                                $link->withValueTarget('seolite_default_og_image');
                                
                                if($default_og_image) {
                                    $file = ee('Model')->get('File')
                                        ->filter('file_id', $default_og_image)
                                        ->filter('site_id', ee()->config->item('site_id'))
                                        ->first()
                                        ->getAbsoluteURL();
                            ?>
                                <img class="upload-image__preview" src="<?=$file?>" />
                            <?php
                                }
                            ?>
                            <input name="seolite_default_og_image" id="seolite_default_og_image" value="<?=$default_og_image?>" type="hidden">
                            <?=$link->render();?>
                        </div>
                    </div>
                </fieldset>

                <h4><?= lang('twitter_tags'); ?></h4>

                <fieldset class="col-group">
                    <div class="setting-txt">
                        <label for="seolite_default_twitter_description"><?= lang('twitter_description'); ?></label>
                    </div>
                    <div class="setting-field">
                        <div class="seeo__instructions field-instruct"><em><?= lang('twitter_description_instructions'); ?></em></div>
                        <input type="text" name="seolite_default_twitter_description" id="seolite_default_twitter_description" value="<?=htmlspecialchars($default_twitter_description)?>"/>
                    </div>
                </fieldset>

                <fieldset class="col-group">
                    <div class="setting-txt">
                        <label for="seolite_default_twitter_image"><?= lang('twitter_image'); ?></label>
                    </div>  
                    <div class="setting-field">
                        <div class="seeo__instructions field-instruct"><em><?= lang('twitter_image_instructions'); ?></em></div>
                        <div class="field-control">
                            <div class="field-control c-upload-image">
                            <?php
                                $filepicker = ee('CP/FilePicker')->make();

                                $link = $filepicker->getLink(($default_twitter_image) ? 'Change Twitter image' : 'Add Twitter image');
                                $link->setAttribute('class', 'button button--secondary upload-image__button');
                                $link->withValueTarget('seolite_default_twitter_image');
                                
                                if($default_twitter_image) {
                                    $file = ee('Model')->get('File')
                                        ->filter('file_id', $default_twitter_image)
                                        ->filter('site_id', ee()->config->item('site_id'))
                                        ->first()
                                        ->getAbsoluteURL();
                            ?>
                                <img class="upload-image__preview" src="<?=$file?>" />
                            <?php
                                }
                            ?>
                            <input name="seolite_default_twitter_image" id="seolite_default_twitter_image" value="<?=$default_twitter_image?>" type="hidden">
                            <?=$link->render();?>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="col-group">
                    <div class="field-instruct setting-txt">
                        <label><?= lang('include_pagination_in_canonical_description'); ?></label>
                    </div>

                    <div class="field-control setting-field">
                        <label class="choice mr <?php if($include_pagination_in_canonical == 'y') echo "chosen";?> yes"><input type="radio" value="y" name="seolite_include_pagination_in_canonical" <?php if($include_pagination_in_canonical == 'y') echo 'checked="checked"';?>> <?php echo lang('include_pagination_in_canonical_description_y');?></label>
                        <label class="choice no <?php if($include_pagination_in_canonical == 'n') echo "chosen";?>"><input type="radio" value="n" name="seolite_include_pagination_in_canonical"<?php if($include_pagination_in_canonical == 'n') echo 'checked="checked"';?>> <?php echo lang('include_pagination_in_canonical_description_n');?></label>
                    </div>
                </fieldset>

                <h4><?= lang('development'); ?></h4>

                <fieldset class="col-group">
                    <div class="setting-txt">
                        <label for="seolite_template"><?= lang('template'); ?></label>
                    </div>
                    <div class="setting-field">
                        <textarea name="seolite_template" id="seolite_template" cols="30" rows="9"><?=htmlspecialchars($template)?></textarea>
                    </div>
                </fieldset>

                <fieldset class="form-ctrls">
                    <input class="btn" type="submit" value="<?= lang('update'); ?>">
                </fieldset>
                <?= form_close()?>
            </div>
    </div>
<?php
/* End of file index.php */