<?php

    $static_pages = ee()->config->item('site_pages');
    $uris = $static_pages[ee()->config->item('site_id')]['uris'];
    $auditUrl = $uris[$data['entry_id']] ?? '';

    function googleAudit($data) {
        $title = isset($data['meta_title']) ? $data['meta_title'] . $data['default_title_postfix'] : $data['title'] . $data['default_title_postfix'];

        if(strlen($title) < 50) {
            $titleInfo = ' has--info info--warning';
            $titleInfoBody = 'Title is a bit short';
        } elseif (strlen($title) > 70) {
            $titleInfo = ' has--info info--error';
            $titleInfoBody = 'Title is too long';
        } else {
            $titleInfo = ' has--info info--success';
            $titleInfoBody = 'Title is a good length';
        }

        $description = isset($data['meta_description']) ? $data['meta_description'] : $data['default_description'];
        $description = strlen($description) > 155 ? substr($description,0,155)."..." : $description;
        $descriptionInfo = isset($data['meta_description']) ? '' : ' has--info info--error';
        $descriptionInfoBody = $data['default_description'] ? 'This is a default description' : 'No description added';

        $entryUrl = $_SERVER['SERVER_NAME'] . ee('Model')
                ->get('ChannelEntry')
                ->filter('entry_id', $data['entry_id'])
                ->first()
                ->getPageURI();

        return <<<EOT
        <div class="c-google-audit">
            <div class="google-audit__link">$entryUrl</div>
            <div class="google-audit__heading$titleInfo" data-info="$titleInfoBody">$title</div>
            <div class="google-audit__description$descriptionInfo" data-info="$descriptionInfoBody">$description</div>
        </div>
EOT;
    }

    function facebookAudit($data) {
        $title = isset($data['og_title']) ? $data['og_title'] : $data['title'];
        $entryUrl = $_SERVER['SERVER_NAME'];
        $description = isset($data['og_description']) ? $data['og_description'] : $data['default_og_description'];
        $descriptionInfo = isset($data['og_description']) ? '' : ' has--info info--error';
        $descriptionInfoBody = $data['default_og_description'] ? 'This is a default description' : 'No description added';

        $og_image_file = isset($data['og_image']) ? $data['og_image'] : $data['default_og_image'];

        if((string) (int) $og_image_file === (string) $og_image_file) {
            $og_image = ee('Model')->get('File', $og_image_file)->first()->getAbsoluteURL();
        } else {
            ee()->load->library('file_field');
            $og_image =  ee()->file_field->parse_string($og_image_file);
        }

        $imageInfo = $og_image ? '' : ' has--info info--error';
        $imageInfoBody = 'No image added';


        return <<<EOT
        <div class="c-facebook-audit">
            <div class="facebook-audit__image$imageInfo" data-info="$imageInfoBody"><img class="facebook-audit__image-src" src="$og_image"/></div>
            <div class="facebook-audit__body">
                <div class="facebook-audit__link">$entryUrl</div>
                <div class="facebook-audit__heading">$title</div>
                <div class="facebook-audit__description$descriptionInfo" data-info="$descriptionInfoBody"><span class="facebook-audit__description-inner">$description</span></div>
            </div>
        </div>
EOT;
    }

    function twitterAudit($data) {
        $title = isset($data['twitter_title']) ? $data['twitter_title'] : $data['title'];
        $entryUrl = $_SERVER['SERVER_NAME'];
        $description = isset($data['twitter_description']) ? $data['twitter_description'] : $data['default_twitter_description'];
        $descriptionInfo = isset($data['twitter_description']) ? '' : ' has--info info--error';
        $descriptionInfoBody = $data['default_twitter_description'] ? 'This is a default description' : 'No description added';

        $twitter_image_file = isset($data['twitter_image']) ? $data['twitter_image'] : $data['default_twitter_image'];

        if((string) (int) $twitter_image_file === (string) $twitter_image_file) {
            $twitter_image = ee('Model')->get('File', $twitter_image_file)->first()->getAbsoluteURL();
        } else {
            ee()->load->library('file_field');
            $twitter_image =  ee()->file_field->parse_string($twitter_image_file);
        }

        $imageInfo = $twitter_image ? '' : ' has--info info--error';
        $imageInfoBody = 'No image added';
        $twitterType = isset($data['twitter_type']) ? ' type--'.$data['twitter_type'] : ' type--0';

        return <<<EOT
        <div class="c-twitter-audit$twitterType">
            <div class="twitter-audit__image$imageInfo" data-info="$imageInfoBody"><img class="twitter-audit__image-src" src="$twitter_image"/></div>
            <div class="twitter-audit__body">
                <div class="twitter-audit__heading">$title</div>
                <div class="twitter-audit__description$descriptionInfo" data-info="$descriptionInfoBody"><span class="facebook-audit__description-inner">$description</span></div>
                <div class="twitter-audit__link">$entryUrl</div>
            </div>
        </div>
EOT;
    }

    $languageName = isset($data['language_name']) ? $data['language_name'] : 'page not yet translated';
?>

    <div class="panel theme--ee<?=substr(APP_VER, 0, 1)?>">
        <div class="panel-heading">
            <div class="title-bar">
                <h3 class="title-bar__title"><?=lang('audit')?> '<?=$data['title']?>' <?=isset($data['publisher']) ? '(' .  $languageName . ')' : ''?></h3>
                <fieldset class="right title-bar__extra-tools">
                <a href="<?= ee('CP/URL', 'publish/edit/entry/'. $data['entry_id'])?>" class="button button--secondary">Edit page <i class="button__icon fal fa-edit"></i></a>
                </fieldset>
            </div>
        </div>
        <div class="panel-body">
            <?php
            if (isset($data['languages'])) :
            ?>
            <div class="c-languages">
                <?php foreach ($data['languages'] as $language) : ?>
                <a class="languages__language <?=isset($data['publisher_id']) && $language['id'] == $data['publisher_id'] ? ' is--active' : '' ?>" href="/admin.php?/cp/addons/settings/seo_lite/audit_entry?entry_id=<?=$data['entry_id']?>&publisher_id=<?=$language['id']?>"><?=$language['long_name']?></a>
                <?php endforeach;?>
            </div>
            <?php endif; ?>

            <div class="tab-wrap js-active-tab-group">
          <div class="tab-bar">
              <div class="tab-bar__tabs">
                  <button type="button" class="tab-bar__tab js-tab-button active" rel="t-0">Analysis</button>
                  <button type="button" class="tab-bar__tab js-tab-button" rel="t-1">Examples</button>
                  <button type="button" class="tab-bar__tab js-tab-button" rel="t-2" js-page-speed-tab>Page Speed</button>
              </div>
          </div>
            <div class="tab t-0 tab-open">
                <div class="panel-inner">
                    <table class="audit" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Basic Meta</th>
                                <th>OG Tags</th>
                                <th>Twitter Tags</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="audit__status has--detail">
                                    <div class="audit__status-item<?= empty($data['title']) ? ' is--empty' : ' is--full'  ?>" data-name="Title"></div>
                                    <div class="audit__status-item<?= empty($data['meta_description']) ? ' is--empty' : ' is--full'  ?>" data-name="Description"></div>
                                    <div class="audit__status-item<?= empty($data['meta_keywords']) ? ' is--empty' : ' is--full'  ?>" data-name="Keywords"></div>
                                    <div class="audit__status-item<?= empty($data['meta_robots']) ? ' is--full' : ' is--info'  ?>" data-name="Robots"></div>
                                </td>
                                <td class="audit__status has--detail">
                                    <div class="audit__status-item<?= empty($data['og_title']) ? ' is--empty' : ' is--full'  ?>" data-name="Title"></div>
                                    <div class="audit__status-item<?= empty($data['og_description']) ? ' is--empty' : ' is--full'  ?>" data-name="Description"></div>
                                    <div class="audit__status-item<?= empty($data['og_type']) ? ' is--empty' : ' is--full'  ?>" data-name="Type"></div>
                                    <div class="audit__status-item<?= empty($data['og_url']) ? ' is--empty' : ' is--full'  ?>" data-name="URL"></div>
                                    <div class="audit__status-item<?= empty($data['og_image']) ? ' is--empty' : ' is--full'  ?>" data-name="Image"></div>
                                </td>
                                <td class="audit__status has--detail">
                                    <div class="audit__status-item<?= empty($data['twitter_title']) ? ' is--empty' : ' is--full'  ?>" data-name="Title"></div>
                                    <div class="audit__status-item<?= empty($data['twitter_description']) ? ' is--empty' : ' is--full'  ?>" data-name="Description"></div>
                                    <div class="audit__status-item<?= empty($data['twitter_type']) ? ' is--empty' : ' is--full'  ?>" data-name="Type"></div>
                                    <div class="audit__status-item<?= empty($data['twitter_image']) ? ' is--empty' : ' is--full'  ?>" data-name="Image"></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <?php
                        $languageSegment = isset($data['language_segment']) ? '/'.$data['language_segment'] : '';
                    ?>
                    <div class="audit__full-scan is--loading" js-seo-lite-full-scan="<?=$languageSegment.$auditUrl?>">
                    </div>
                </div>
            </div>
            <div class="tab t-1">
                <div class="panel-inner">
                    <table class="audit" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Google</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <p class="panel-intro">This is an example of how the page looks like on Google Search.</p>
                                    <?= googleAudit($data) ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="audit" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Facebook</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <p class="panel-intro">This is an example of how the page looks like on Facebook.</p>
                                    <?= facebookAudit($data) ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="audit" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Twitter</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <p class="panel-intro">This is an example of how the page looks like on Twitter.</p>
                                    <?= twitterAudit($data) ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab t-2">
                <div class="panel-inner">
                    <div class="c-page-speed is--loading" js-page-speed></div>
                </div>
            </div>
        </div>
    </div>

<?php
/* End of file audit_entry.php */
