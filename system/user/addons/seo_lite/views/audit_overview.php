<?php
function getMeta() {
    $resultsItems = '<div class="audit__status-item"></div>';
    $resultsItems .= '<div class="audit__status-item">';
}
?>

    <div class="panel theme--ee<?=substr(APP_VER, 0, 1)?>">
        <div class="panel-heading">
            <div class="title-bar">
                <h3 class="title-bar__title"><?=lang('audit_overview')?></h3>					
            </div>
        </div> 
        <div class="panel-body"> 
            <div class="col-group">
                <div class="txt-wrap">
                    <?php if (empty($data['entries'])) : ?>
                        <div class="txt-wrap">
                            <em><?=lang('no_entries')?></em>
                        </div>
                    <?php else : ?>
                        <table class="audit" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Entry ID</th>
                                    <th>Title</th>
                                    <th>Meta</th>
                                    <th>OG</th>
                                    <th>Twitter</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($data['entries'] as $entry) : 
                                $publisher_id = isset($entry['publisher_lang_id']) ? $entry['publisher_lang_id'] : '1';
                            ?>
                                <tr>
                                    <td class="audit__id"><?= $entry['entry_id'] ?><?= isset($entry['language_name']) ? '<span class="audit__language">'.$entry['language_name'].'</span>' : '' ?> <?= isset($entry['publisher_status']) ? '<span class="audit__language">'.$entry['publisher_status'].'</span>' : '' ?></td>
                                    <td class="audit__title"><?= $entry['title'] ?></td>
                                    <td class="audit__status">
                                        <div class="audit__status-item<?= empty($entry['title']) ? ' is--empty' : ' is--full'  ?>" data-name="Title">T</div>
                                        <div class="audit__status-item<?= empty($entry['meta_description']) ? ' is--empty' : ' is--full'  ?>" data-name="Description">D</div>
                                        <div class="audit__status-item<?= empty($entry['meta_keywords']) ? ' is--empty' : ' is--full'  ?>" data-name="Keywords">K</div>
                                        <div class="audit__status-item<?= empty($entry['meta_robots']) ? ' is--full' : ' is--info'  ?>" data-name="Robots">R</div>
                                    </td>
                                    <td class="audit__status">
                                        <div class="audit__status-item<?= empty($entry['og_title']) ? ' is--empty' : ' is--full'  ?>" data-name="Title">T</div>
                                        <div class="audit__status-item<?= empty($entry['og_description']) ? ' is--empty' : ' is--full'  ?>" data-name="Description">D</div>
                                        <div class="audit__status-item<?= empty($entry['og_type']) ? ' is--empty' : ' is--full'  ?>" data-name="Type">T</div>
                                        <div class="audit__status-item<?= empty($entry['og_url']) ? ' is--empty' : ' is--full'  ?>" data-name="URL">U</div>
                                        <div class="audit__status-item<?= empty($entry['og_image']) ? ' is--empty' : ' is--full'  ?>" data-name="Image">I</div>
                                    </td>
                                    <td class="audit__status">
                                        <div class="audit__status-item<?= empty($entry['twitter_title']) ? ' is--empty' : ' is--full'  ?>" data-name="Title">T</div>
                                        <div class="audit__status-item<?= empty($entry['twitter_description']) ? ' is--empty' : ' is--full'  ?>" data-name="Description">D</div>
                                        <div class="audit__status-item<?= empty($entry['twitter_type']) ? ' is--empty' : ' is--full'  ?>" data-name="Type">T</div>
                                        <div class="audit__status-item<?= empty($entry['twitter_image']) ? ' is--empty' : ' is--full'  ?>" data-name="Image">I</div>
                                    </td>
                                    <td class="audit__buttons">
                                        <a href="<?= ee('CP/URL', 'publish/edit/entry/'. $entry['entry_id'])?>" class="button button--secondary">Edit page <i class="button__icon fal fa-edit"></i></a>
                                        <a href="<?= isset($data['publisher']) ? ee('CP/URL', 'addons/settings/seo_lite/audit_entry')->setQueryStringVariable('entry_id', $entry['entry_id'])->setQueryStringVariable('publisher_id', $publisher_id) : ee('CP/URL', 'addons/settings/seo_lite/audit_entry')->setQueryStringVariable('entry_id', $entry['entry_id']) ?>" class="button button--primary">More details<i class="button__icon fal fa-chevron-right"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach;?>
                            </tbody>
                        </table>

                        <div class="paginate">
                            <?=$data['pagination']?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php
/* End of file audit_overview.php */