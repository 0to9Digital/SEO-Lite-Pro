<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * SEO Lite (Pro) Tab File
 *
 * @category   Module
 * @package    ExpressionEngine
 * @subpackage Addons
 * @author     0to9 Digital - Robin Treur
 * @link       https://0to9.nl
 */

class Seo_lite_tab {

    private $EE;
    private $return_data;

    public function __construct()
    {
        $this->EE = get_instance();
        $this->EE->lang->loadfile('seo_lite');
        // don't load config if we have this config item. This is used for overriding from a master config
        if(!$this->EE->config->item('seolite_show_keywords_field')) {
            $this->EE->load->config('seolite');
        }

        $tab_title = $this->EE->config->item('seolite_tab_title') ? $this->EE->config->item('seolite_tab_title') : $this->EE->config->item('seo_lite_tab_title');   // config item was renamed but we support the old seo_lite_tab_title as well
        if($tab_title) {
            $this->EE->lang->language['seo_lite'] = $tab_title;
        }
    }

    public function display($channel_id, $entry_id = '')
    {
        $settings = array();

        $title = $keywords = $description = $robotsdirective = $ogtitle = $ogdescription = $ogtype = $ogurl = $ogimage = $twittertitle = $twitterdescription = $twittertype = $twitterimage = '';

        $publisher_url = ee('Addon')->get('publisher') && ee('Addon')->get('publisher')->isInstalled() ? '&publisher_id=1' : false;
        $audit_url = isset($data['publisher']) ? ee('CP/URL', 'addons/settings/seo_lite/audit_entry')->setQueryStringVariable('entry_id', $entry_id)->setQueryStringVariable('publisher_id', $publisher_url) : ee('CP/URL', 'addons/settings/seo_lite/audit_entry')->setQueryStringVariable('entry_id', $entry_id);

        if($entry_id)
        {
            $table_name = 'seolite_content';
            $where = array(
                 'entry_id' => $entry_id,
                 'site_id' => $this->EE->config->item('site_id')
             );


            // -------------------------------------------
            // Allows one to modify the SEO Lite pulled up in the tab (ie. for translation addons)
            //
            // Params sent in:
            // - $where - an array of where (activerecord) to check for .. already contains 'entry_id' and 'site_id'
            // - $table_name - the name of the table to pull data from (without db prefix, defaults to 'seolite_content')
            //
            // Return value:
            // Please return nothing at all or an array which contains 'where' and/or 'table_name' to replace the existing
            // where array and table name to pull data from. This will be used to ->get(where, table_name) the data so
            // you can basically pull whatever from any table.
            //
            // But remember the results must contain 'title', 'keywords', 'description' which SEO Lite rely on for the
            // tab content.
            //
            // -------------------------------------------
            if ($this->EE->extensions->active_hook('seo_lite_tab_content') === TRUE)
            {
                $hook_result = $this->return_data = $this->EE->extensions->call('seo_lite_tab_content', $where, $table_name);
                if($hook_result && isset($hook_result['where'])) {
                    $where = $hook_result['where'];
                }
                if($hook_result && isset($hook_result['table_name'])) {
                    $table_name = $hook_result['table_name'];
                }

                if ($this->EE->extensions->end_script === TRUE) return;
            }

            $q = $this->EE->db->get_where($table_name, $where);

            if($q->num_rows())
            {
                $title = $q->row('title');
                $keywords = $q->row('keywords');
                $description = $q->row('description');
                $robotsdirective = $q->row('robots_directive');
                $ogtitle = $q->row('og_title');
                $ogdescription = $q->row('og_description');
                $ogtype = $q->row('og_type');
                $ogurl = $q->row('og_url');
                $ogimage = $q->row('og_image');
                $twittertitle = $q->row('twitter_title');
                $twitterdescription = $q->row('twitter_description');
                $twittertype = $q->row('twitter_type');
                $twitterimage = $q->row('twitter_image');
            }
        }

        $settings['seo_lite_title'] = array(
           'field_id' => 'seo_lite_title',
           'field_label' => lang('seotitle'),
           'field_required' => 'n',
           'field_data' => $title,
           'field_list_items' => '',
           'field_fmt' => '',
           'options' => array(),
           'field_instructions' => lang('title_instructions'),
           'field_show_fmt' => 'n',
           'field_fmt_options' => array(),
           'field_pre_populate' => 'n',
           'field_text_direction' => 'ltr',
           'field_type' => 'text',
           'field_maxl' => '90'
       );

        if($this->EE->config->item('seolite_show_keywords_field') != 'n' && $keywords) {
            $settings['seo_lite_keywords'] = array(
               'field_id' => 'seo_lite_keywords',
               'field_label' => lang('seokeywords'),
               'field_required' => 'n',
               'field_data' => $keywords,
               'field_list_items' => '',
               'field_fmt' => '',
               'field_instructions' => lang('keywords_instructions'),
               'field_show_fmt' => 'n',
               'field_fmt_options' => array(),
               'field_pre_populate' => 'n',
               'field_text_direction' => 'ltr',
               'field_type' => 'text',
               'field_maxl' => '1024'
           );
        }

        $settings['seo_lite_description'] = array(
           'field_id' => 'seo_lite_description',
           'field_label' => lang('seodescription'),
           'field_required' => 'n',
           'field_data' => $description,
           'field_list_items' => '',
           'field_fmt' => '',
           'field_instructions' => lang('description_instructions'),
           'field_show_fmt' => 'n',
           'field_fmt_options' => array(),
           'field_pre_populate' => 'n',
           'field_text_direction' => 'ltr',
           'field_type' => 'text',
           'field_maxl' => '1024'
        );

        $settings['seo_lite_og_title'] = array(
            'field_id' => 'seo_lite_og_title',
            'field_label' => lang('og_title'),
            'field_required' => 'n',
            'field_data' => $ogtitle,
            'field_list_items' => '',
            'field_fmt' => '',
            'field_instructions' => lang('og_title_instructions'),
            'field_show_fmt' => 'n',
            'field_fmt_options' => array(),
            'field_pre_populate' => 'n',
            'field_text_direction' => 'ltr',
            'field_type' => 'text',
            'field_maxl' => '90'
        );

        $settings['seo_lite_og_description'] = array(
            'field_id' => 'seo_lite_og_description',
            'field_label' => lang('og_description'),
            'field_required' => 'n',
            'field_data' => $ogdescription,
            'field_list_items' => '',
            'field_fmt' => '',
            'field_instructions' => lang('og_description_instr'),
            'field_show_fmt' => 'n',
            'field_fmt_options' => array(),
            'field_pre_populate' => 'n',
            'field_text_direction' => 'ltr',
            'field_type' => 'text',
            'field_maxl' => '1024'
        );

        $settings['seo_lite_og_type'] = array(
            'field_id' => 'seo_lite_og_type',
            'field_label' => lang('og_type'),
            'field_required' => 'n',
            'field_data' => $ogtype,
            'field_list_items' => array('Article', 'Website', 'Book', 'Music - Song', 'Music - Album', 'Music - Playlist', 'Music - Radio Station', 'Profile', 'Video - Movie', 'Video - Episode', 'Video - TV Show', 'Video - Other'),
            'field_fmt' => '',
            'field_instructions' => lang('og_type_instructions'),
            'field_show_fmt' => 'n',
            'field_fmt_options' => array(),
            'field_pre_populate' => 'n',
            'field_text_direction' => 'ltr',
            'field_type' => 'select'
        );

        $settings['seo_lite_og_url'] = array(
            'field_id' => 'seo_lite_og_url',
            'field_label' => lang('og_url'),
            'field_required' => 'n',
            'field_data' => $ogurl,
            'field_list_items' => '',
            'field_fmt' => '',
            'field_instructions' => lang('og_url_instructions'),
            'field_show_fmt' => 'n',
            'field_fmt_options' => array(),
            'field_pre_populate' => 'n',
            'field_text_direction' => 'ltr',
            'field_type' => 'text',
            'field_maxl' => '1024'
        );

        $settings['seo_lite_og_image'] = array(
            'field_id' => 'seo_lite_og_image',
            'field_label' => lang('og_image'),
            'field_required' => 'n',
            'field_data' => $ogimage,
            'field_list_items' => '',
            'field_fmt' => '',
            'field_instructions' => lang('og_image_instructions'),
            'field_show_fmt' => 'n',
            'field_fmt_options' => '',
            'field_pre_populate' => 'n',
            'field_text_direction' => 'ltr',
            'field_type' => 'file'
        );

        $settings['seo_lite_twitter_title'] = array(
            'field_id' => 'seo_lite_twitter_title',
            'field_label' => lang('twitter_title'),
            'field_required' => 'n',
            'field_data' => $twittertitle,
            'field_list_items' => '',
            'field_fmt' => '',
            'field_instructions' => lang('twitter_title_instructions'),
            'field_show_fmt' => 'n',
            'field_fmt_options' => array(),
            'field_pre_populate' => 'n',
            'field_text_direction' => 'ltr',
            'field_type' => 'text',
            'field_maxl' => '90'
        );

        $settings['seo_lite_twitter_description'] = array(
            'field_id' => 'seo_lite_twitter_description',
            'field_label' => lang('twitter_description'),
            'field_required' => 'n',
            'field_data' => $twitterdescription,
            'field_list_items' => '',
            'field_fmt' => '',
            'field_instructions' => lang('twitter_description_instructions'),
            'field_show_fmt' => 'n',
            'field_fmt_options' => array(),
            'field_pre_populate' => 'n',
            'field_text_direction' => 'ltr',
            'field_type' => 'text',
            'field_maxl' => '1024'
        );

        $settings['seo_lite_twitter_type'] = array(
            'field_id' => 'seo_lite_twitter_type',
            'field_label' => lang('twitter_type'),
            'field_required' => 'n',
            'field_data' => $twittertype,
            'field_list_items' => array('Summary', 'Summary - Large Image', 'Player'),
            'field_fmt' => '',
            'field_instructions' => lang('twitter_type_instructions'),
            'field_show_fmt' => 'n',
            'field_fmt_options' => array(),
            'field_pre_populate' => 'n',
            'field_text_direction' => 'ltr',
            'field_type' => 'select'
        );

        $settings['seo_lite_twitter_image'] = array(
            'field_id' => 'seo_lite_twitter_image',
            'field_label' => lang('twitter_image'),
            'field_required' => 'n',
            'field_data' => $twitterimage,
            'field_list_items' => '',
            'field_fmt' => '',
            'field_instructions' => lang('twitter_image_instructions'),
            'field_show_fmt' => 'n',
            'field_fmt_options' => '',
            'field_pre_populate' => 'n',
            'field_text_direction' => 'ltr',
            'field_type' => 'file'
        );

        $settings['seo_lite_robots_directive'] = array(
            'field_id' => 'seo_lite_robots_directive',
            'field_label' => lang('robots_directive'),
            'field_required' => 'n',
            'field_data' => $robotsdirective,
            'field_list_items' => array('INDEX, FOLLOW', 'NOINDEX, FOLLOW', 'INDEX, NOFOLLOW', 'NOINDEX, NOFOLLOW'),
            'field_fmt' => '',
            'field_instructions' => lang('robots_directive_instructions'),
            'field_show_fmt' => 'n',
            'field_fmt_options' => array(),
            'field_pre_populate' => 'n',
            'field_text_direction' => 'ltr',
            'field_type' => 'select'
        );

        $settings['seo_lite_extra'] = array(
            'field_id' => 'seo_lite_extra',
            'field_label' => lang('audit'),
            'field_required' => 'n',
            'field_instructions' => lang('audit_goto_instr').'<br><a class="button button--primary" href="'. $audit_url . '" target="_blank"><i class="fas fa-clipboard-check"></i> '.lang('audit_goto_button').'</a>',
            'field_data' => '',
            'field_type' => 'hidden'
        );

        return $settings;
    }

    function validate($channel_entry, $params)
    {
        return TRUE;
    }

    /**
     * Save the data to the db
     *
     * @param  $params
     * @return void
     */
    function save($channel_entry, $params)
    {
        $site_id = $channel_entry->site_id;
        $entry_id = $channel_entry->entry_id;

        $content = array(
            'site_id' => $site_id,
            'entry_id' => $entry_id,
            'title' => $params['seo_lite_title'],
            'keywords' => isset($params['seo_lite_keywords']) ? $params['seo_lite_keywords'] : '',
            'description' => $params['seo_lite_description'],
            'robots_directive' => $params['seo_lite_robots_directive'],
            'og_title' => $params['seo_lite_og_title'],
            'og_description' => $params['seo_lite_og_description'],
            'og_type' => $params['seo_lite_og_type'],
            'og_url' => $params['seo_lite_og_url'],
            'og_image' => $params['seo_lite_og_image'],
            'twitter_title' => $params['seo_lite_twitter_title'],
            'twitter_description' => $params['seo_lite_twitter_description'],
            'twitter_type' => $params['seo_lite_twitter_type'],
            'twitter_image' => $params['seo_lite_twitter_image']
        );

        $table_name = 'seolite_content';
        $where = array(
            'entry_id' => $entry_id,
            'site_id' => $site_id
        );

        $default_where = $where;
        $default_content = $content;
        $default_table_name = $table_name;

        // -------------------------------------------
        // Allows one to modify the SEO Lite saved in the tab (ie. for translation addons)
        //
        // Params sent in:
        // - $where - an array of where (activerecord) on UPDATE .. already contains 'entry_id' and 'site_id'
        // - $table_name - the name of the table to pull data from (without db prefix, defaults to 'seolite_content')
        // - $content - the current content saved (an array of site_id, entry_id, title, keywords, description)
        //
        // Return value:
        // Please return nothing at all or an array which contains 'where' and/or 'table_name' and/or 'content' to
        // replace any of these.
        //
        // But remember the content must contain 'site_id', 'entry_id', 'title', 'keywords', 'description'
        //
        // -------------------------------------------
        if ($this->EE->extensions->active_hook('seo_lite_tab_content_save') === TRUE) {

            $hook_result = $this->return_data = $this->EE->extensions->call('seo_lite_tab_content_save', $where, $table_name, $content);
            if($hook_result && isset($hook_result['where'])) {
                $where = $hook_result['where'];
            }
            if($hook_result && isset($hook_result['table_name'])) {
                $table_name = $hook_result['table_name'];
            }
            if($hook_result && isset($hook_result['content'])) {
                $content = $hook_result['content'];
            }

            if ($this->EE->extensions->end_script === TRUE) return;
        }

        $q = $this->EE->db->get_where($table_name, $where);

        if($q->num_rows())
        {
            $this->EE->db->where($where);
            $this->EE->db->update($table_name, $content);
        }
        else
        {
            $this->EE->db->insert($table_name, $content);
        }

        /**
         * If the data was stored to another table (ie if a third party addon took control over this, we still just
         * store the content in case that third_party addon is uninstalled later. Note that this may cause problems
         * with addons that store multiple versions for the same entry_id (ie. Publisher). If so SEO Lite will end
         * up with the latest stored version (which could be in language 1 or language 2 etc.) .. but in cases like
         * these a lot of data won't make sense anyway so .. in other cases, where the addon uses a different entry_id
         * for each type of content everything should work just fine if uninstalling that addon.
         */
        if($table_name != $default_table_name) {
            $q = $this->EE->db->get_where($default_table_name, $default_where);

            if($q->num_rows())
            {
                $this->EE->db->where($default_where);
                $this->EE->db->update($default_table_name, $default_content);
            }
            else
            {
                $this->EE->db->insert($default_table_name, $default_content);
            }
        }
    }

    /**
     * Delete seo data if entry is deleted
     *
     * @param  $params
     * @return void
     */
    function delete($entry_ids)
    {

        foreach($entry_ids as $i => $entry_id)
        {
            $this->EE->db->where('entry_id', $entry_id);
            $this->EE->db->delete('seolite_content');
        }
    }

}
