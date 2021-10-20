    <div class="panel theme--ee<?=substr(APP_VER, 0, 1)?>">
        <div class="panel-heading">
            <div class="title-bar">
                <h3 class="title-bar__title"><?= lang('instructions') ?></h3>					
            </div>
        </div>
        <div class="panel-body">
            <div class="col-group">
                <div class="txt-wrap">
                    <p>Put one of these tags in your template:</p>

                    <ul class="checklist">
                        <li>
                            <strong>Intelligent mode</strong> aka Use-Last-Segment-Mode: <em>{exp:seo_lite use_last_segment="yes"}</em>
                        </li>
                        <li>
                            By <strong>segment</strong>: <em>{exp:seo_lite url_title="{segment_3}"}</em>
                        </li>
                        <li>
                            By <strong>entry_id</strong>: <em>{exp:seo_lite entry_id="{entry_id}"}</em>
                        </li>
                        <li>
                            <strong>Static mode</strong> aka I-Will-Provide-Values-In-Template: (this will output "About Us" for the title tag but still use the default keywords/description for the site): <em>{exp:seo_lite default_title="About us"}</em>
                        </li>
                        <li>
                            <strong>Static mode</strong> with everything overridden: <em>{exp:seo_lite default_title="About us" default_keywords="new, keywords" default_description="This description is unique for this page"}</em>
                        </li>
                    </ul>

                    <p>Either of these tags will output the template specified in the SEO Lite settings with the title/keywords/description specific for the content. The SEO Lite template is parsed as a normal EE template, so you can use any EE global variables and conditionals etc.</p>
                </div>
            </div>
        </div>
    </div>
<?php
/* End of file instructions.php */