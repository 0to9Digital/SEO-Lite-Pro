<?php

use BoldMinded\Publisher\Enum\Status;
use BoldMinded\Publisher\Model\Language;
use BoldMinded\Publisher\Service\Channel;
use BoldMinded\Publisher\Service\Entry\Entry;
use BoldMinded\Publisher\Service\Query;
use BoldMinded\Publisher\Service\Request;
use BoldMinded\Publisher\Service\Setting;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * SEO Lite Publisher Extension
 *
 * @package     ExpressionEngine
 * @subpackage  Addons
 * @category    Extension
 * @author      0to9 Digital - Robin Treur
 * @link        https://0to9.nl
 */

class Seo_lite_publisher_ext {

    public $settings = [];
    public $version = SEOLITE_PUBLISHER_VERSION;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Setting
     */
    private $publisherSetting;

    /**
     * @param mixed Settings array or empty string if none exist.
     */
    public function __construct($settings = '')
    {
        $this->settings = $settings;

        $this->request = ee(Request::NAME);
        $this->publisherSetting = ee(Setting::NAME);
        $this->currentLanguageId = $this->request->getCurrentLanguage()->getId();
        $this->defaultLanguageId = $this->request->getDefaultLanguage()->getId();
        $this->currentStatus = $this->request->getCurrentStatus();
        $this->saveStatus = $this->request->getSaveStatus();
    }

    /**
     * @return void
     */
    public function activate_extension()
    {
        // Setup custom settings in this array.
        $this->settings = array();

        // check if the Publisher SEO Lite table already exists, if so don't create it.
        if(!ee()->db->table_exists('publisher_seolite_content')) {

            // 1. Create new table for Publisher translated versions of SEO Lite
            ee()->load->dbforge();

            $publisher_seolite_content_fields = [
                'publisher_seolite_content_id' => [
                    'type' => 'int',
                    'constraint' => '10',
                    'unsigned' => true,
                    'auto_increment' => true,
                    ],
                'site_id' => [
                    'type' => 'int',
                    'constraint' => '10',
                    'null' => false,
                    ],
                'entry_id' => [
                    'type' => 'int',
                    'constraint' => '10',
                    'null' => false,
                    ],
                'title' => [
                    'type' => 'varchar',
                    'constraint' => '1024',
                    'null' => false,
                    ],
                'keywords' => [
                    'type' => 'varchar',
                    'constraint' => '1024',
                    'null' => false,
                    ],
                'description' => [
                    'type' => 'text',
                    ],
                'robots_directive' => [
                    'type' => 'varchar',
                    'constraint' => '70',
                    ],
                'og_title' => [
                    'type' => 'varchar',
                    'constraint' => '70',
                    ],
                'og_type' => [
                    'type' => 'varchar',
                    'constraint' => '70',
                    ],
                'og_url' => [
                    'type' => 'varchar',
                    'constraint' => '1024',
                    ],
                'og_description' => [
                    'type' => 'varchar',
                    'constraint' => '1024',
                    ],
                'og_image' => [
                    'type' => 'varchar',
                    'constraint' => '1024',
                    ],
                'twitter_title' => [
                    'type' => 'varchar',
                    'constraint' => '70',
                    ],
                'twitter_type' => [
                    'type' => 'varchar',
                    'constraint' => '1024',
                    ],
                'twitter_description' => [
                    'type' => 'varchar',
                    'constraint' => '1024',
                    ],
                'twitter_image' => [
                    'type' => 'varchar',
                    'constraint' => '1024',
                    ],
                'publisher_status' => [
                    'type' => 'text',
                    ],
                'publisher_lang_id' => [
                    'type' => 'int',
                    'constraint' => '10',
                    'null' => false,
                    ],
            ];

            ee()->dbforge->add_field($publisher_seolite_content_fields);
            ee()->dbforge->add_key('publisher_seolite_content_id', true);
            ee()->dbforge->create_table('publisher_seolite_content');
        }

        /**
         * Hook on to SEO Lite
         */
        $hooks = [
            'seo_lite_tab_content', 'seo_lite_tab_content_save', 'seo_lite_fetch_data'
        ];

        foreach($hooks as $hook) {
            ee()->db->insert('extensions', [
                'class' => __CLASS__,
                'hook' => $hook,
                'method' => $hook,
                'priority' => '10',
                'settings' => serialize($this->settings),
                'version' => $this->version,
                'enabled' => 'y',
            ]);
        }
    }

    /**
     * @param $where
     * @param $table_name
     * @return array
     */
    public function seo_lite_tab_content($where, $table_name)
    {
        $langId = $this->currentLanguageId;
        $status = $this->currentStatus;

/*
        if (ee()->publisher_setting->showFallback()) {
           
            $translatedWhere = [
                'publisher_lang_id' => $langId,
                'publisher_status' => $status,
            ];
            if (isset($where['entry_id'])) {
                $translatedWhere['entry_id'] = $where['entry_id'];
            }
            $q = ee()->db->get_where('publisher_seolite_content', $translatedWhere);

            if (!$q->num_rows()) {
                $langId = $this->defaultLanguageId;
            }
        }
*/

        // where arr used w/activerecord
        $where['publisher_lang_id'] = $langId;
        $where['publisher_status']  = $status;

        return [
            'where' => $where,
            'table_name' => 'publisher_seolite_content' // pull content from Publisher saved data instead of default SEO Lite content
        ];
    }

    /**
     * @param $where
     * @param $table_name
     * @param $content
     * @return array
     */
    public function seo_lite_tab_content_save($where, $table_name, $content)
    {
        // where arr used w/activerecord
        $where['publisher_lang_id'] = $this->currentLanguageId;
        $where['publisher_status'] = $this->saveStatus;

        $content['publisher_lang_id'] = $this->currentLanguageId;
        $content['publisher_status'] = $this->saveStatus;

        // if no SEO Lite title is specified we save the entry's title here - or else
        // we would get the original language's entry title when getting data w/SEO Lite
        if($content['title'] == '') {
            $content['title'] = ee()->input->post('title');
        }

        return [
            'where' => $where,
            'table_name' => 'publisher_seolite_content', // save data to this table instead
            'content' => $content,  // additional content
        ];
    }

    /**
     * @param array $where
     * @param string $table_name
     *
     * @return array
     */
    public function seo_lite_fetch_data($where, $table_name)
    {
        $langId = $this->currentLanguageId;
        $status = $this->currentStatus;
        
        
        if ($this->publisherSetting->showFallback()) {
            /** @var CI_DB_result $q */
            $translatedWhere = [
                'publisher_lang_id' => $langId,
                'publisher_status' => $status,
            ];
            if (isset($where['t.entry_id'])) {
                $translatedWhere['entry_id'] = $where['t.entry_id'];
            } else if (isset($where['url_title'])) {
                if ($this->publisherSetting->get('url_translations')) {
                    /** @var CI_DB_result $entry */
                    $entry = ee()->db->get_where('publisher_titles', array_merge(
                        $translatedWhere, array('url_title' => $where['url_title'])
                    ));

                    if (!$entry->num_rows()) {
                        $entry = ee()->db->get_where('channel_titles', [
                            'url_title' => $where['url_title']
                        ]);
                    }
                } else {
                    $entry = ee()->db->get_where('channel_titles', [
                        'url_title' => $where['url_title']
                    ]);
                }

                if ($entry->num_rows()) {
                    $translatedWhere['entry_id'] = $entry->row('entry_id');
                }
            }

            $q = ee()->db->get_where('publisher_seolite_content', $translatedWhere);

            if (!$q->num_rows()) {
                $langId = $this->defaultLanguageId;
            }
        }

        // where arr used w/activerecord
        $where['publisher_lang_id'] = $langId;
        $where['publisher_status'] = $status;

        return [
            'where' => $where,
            'table_name' => 'publisher_seolite_content' // pull content from Publisher saved data instead of default SEO Lite content
        ];
    }


    /**
     * @return void
     */
    public function disable_extension()
    {
        ee()->db->delete('extensions', ['class' => __CLASS__]);

        // do not delete the publisher_seolite_content table here to allow for enabling / disabling of extension w/o losing data ..
    }

    /**
     * @return mixed void on update / false if none
     */
    public function update_extension($current = '')
    {
        if ($current == '' || $current == $this->version)
        {
            return false;
        }
    }
}
