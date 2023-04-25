<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * SEO Lite (Pro) Module Control Panel File
 *
 * @category   Module
 * @package    ExpressionEngine
 * @subpackage Addons
 * @author     0to9 Digital - Robin Treur
 * @link       https://0to9.nl
 */
class Seo_lite_mcp 
{
	var $base;			// the base url for this module			
	var $form_base;		// base url for forms
	var $module_name = "seo_lite";	

	function __construct( $switch = TRUE )
	{   
        // uncomment this if you want navigation buttons at the top
		ee()->cp->set_right_nav(array(
				'settings'			=> $this->base,
				'docs'	=> 'https://github.com/0to9Digital/SEO-Lite-v2',
			));


		//  Onward!
		ee()->load->library('table');
		ee()->load->library('javascript');
		ee()->load->helper('form');
		ee()->lang->loadfile('seo_lite');

        // The Control Panel's left sidebar is built with the Sidebar Service:
        $sidebar = ee('CP/Sidebar')->make();
        if(substr(APP_VER, 0, 1) < 6) {
            // IF EE5
            $sidebar_list = $sidebar->addHeader('Sidebar');
            $sidebar_items = $sidebar_list->addBasicList();
            $sidebar_items->addItem('Settings', ee('CP/URL', 'addons/settings/seo_lite'));
            $sidebar_items->addItem('Instructions', ee('CP/URL', 'addons/settings/seo_lite/instructions'));
            $sidebar_items->addItem('Audit Overview', ee('CP/URL', 'addons/settings/seo_lite/audit_overview'));
        } else {
            // IF EE6
            $settings = $sidebar->addItem('Settings', ee('CP/URL', 'addons/settings/seo_lite'));
            $settings->withIcon('cog');
            $instructions = $sidebar->addItem('Instructions', ee('CP/URL', 'addons/settings/seo_lite/instructions'));
            $instructions->withIcon('info-circle');
            $audit = $sidebar->addItem('Audit Overview', ee('CP/URL', 'addons/settings/seo_lite/audit_overview'));
            $audit->withIcon('clipboard-check');
        }
        
        ee('CP/URL', 'addons/settings/seo_lite/audit_entry');

        ee()->cp->add_to_head("<link rel='stylesheet' href='" . URL_THIRD_THEMES . "seo_lite/css/seo_lite.css?v2.2.2'>");
        ee()->cp->add_to_foot("<script type='text/javascript' charset='utf-8' src='". URL_THIRD_THEMES . "seo_lite/js/seo_lite.js?v2.2.2'></script>");
	}

	function index() 
	{
		$vars = array();

        $site_id = ee()->config->item('site_id');
        $config = ee()->db->get_where('seolite_config', array('site_id' => $site_id));

        if($config->num_rows() == 0) // we did not find any config for this site id, so just load any other
        {
            $config = ee()->db->get_where('seolite_config');
        }

		$vars['template'] = $config->row('template');
        $vars['default_description'] = $config->row('default_description');
        $vars['default_keywords'] = $config->row('default_keywords');
        $vars['default_title_postfix'] = $config->row('default_title_postfix');
        $vars['default_og_description'] = $config->row('default_og_description');
        $vars['default_og_image'] = $config->row('default_og_image');
        $vars['default_twitter_description'] = $config->row('default_twitter_description');
        $vars['default_twitter_image'] = $config->row('default_twitter_image');
        $vars['include_pagination_in_canonical'] = $config->row('include_pagination_in_canonical');
        $vars['save_settings_url'] =  ee('CP/URL', 'addons/settings/seo_lite/save_settings');

        $view = ee('View')->make('seo_lite:index');

        return $view->render($vars);
	}

    function instructions() 
	{

        $view = ee('View')->make('seo_lite:instructions');

        return $view->render();
	}

    function getAuditOverviewData($publisherInstalled) {
        $page = intval(ee()->input->get('page'));
        $per_page = 15;
        $site_id = ee()->config->item('site_id');

        if (empty($page)) $page = 1;

        $start_num = ($page * $per_page) - $per_page;

        $total = ee()->db->count_all_results('channel_titles');

        // If no matching channel_id, total is 0
        if (empty($total)) {
            $total_records = 0;
        } else {
            $total_records = $total;
        }
        
        if ($publisherInstalled) {
            $data['entries'] =
            ee()->db->select('
                ct.entry_id,
                ct.title,
                sl.title as meta_title,
                sl.description as meta_description,
                sl.keywords as meta_keywords,
                sl.robots_directive as meta_robots,
                sl.og_title as og_title,
                sl.og_type as og_type,
                sl.og_description as og_description,
                sl.og_url as og_url,
                sl.og_image as og_image,
                sl.twitter_title as twitter_title,
                sl.twitter_type as twitter_type,
                sl.twitter_description as twitter_description,
                sl.twitter_image as twitter_image,
                sl.publisher_lang_id as publisher_lang_id,
                sl.publisher_status as publisher_status,
                pub.short_name as language_name
            ')
            ->from('channel_titles ct')
            ->where('ct.site_id', $site_id)
            ->limit($per_page, $start_num)
            ->order_by('ct.entry_id', 'desc')
            ->join('publisher_seolite_content sl', 'ct.entry_id = sl.entry_id', 'left')
            ->join('publisher_languages pub', 'sl.publisher_lang_id = pub.id', 'left')
            ->get()
            ->result_array();
        } else {
            $data['entries'] =
            ee()->db->select('
                ct.entry_id,
                ct.title,
                sl.title as meta_title,
                sl.description as meta_description,
                sl.keywords as meta_keywords,
                sl.robots_directive as meta_robots,
                sl.og_title as og_title,
                sl.og_type as og_type,
                sl.og_description as og_description,
                sl.og_url as og_url,
                sl.og_image as og_image,
                sl.twitter_title as twitter_title,
                sl.twitter_type as twitter_type,
                sl.twitter_description as twitter_description,
                sl.twitter_image as twitter_image
            ')
            ->from('channel_titles ct')
            ->where('ct.site_id', $site_id)
            ->limit($per_page, $start_num)
            ->join('seolite_content sl', 'ct.entry_id = sl.entry_id', 'left')
            ->get()
            ->result_array();
        }

        // Get pagination
        $data['pagination'] = ee('CP/Pagination', $total_records)
            ->currentPage($page)
            ->perPage($per_page)
            ->queryStringVariable('page')
            ->displayPageLinks(5)
            ->render(ee('CP/URL', 'addons/settings/seo_lite/audit_overview'));

        return $data;
    }
    
    function getAuditEntryData($publisherInstalled, $publisher_id) {
        $site_id = ee()->config->item('site_id');
        $entry_id = ee()->input->get('entry_id');
        

        if($publisherInstalled) {
            $data = ee()->db->select('
                ct.entry_id,
                ct.title,
                sl.title as meta_title,
                sl.description as meta_description,
                sl.keywords as meta_keywords,
                sl.robots_directive as meta_robots,
                sl.og_title as og_title,
                sl.og_type as og_type,
                sl.og_description as og_description,
                sl.og_url as og_url,
                sl.og_image as og_image,
                sl.twitter_title as twitter_title,
                sl.twitter_type as twitter_type,
                sl.twitter_description as twitter_description,
                sl.twitter_image as twitter_image,
                sl.publisher_lang_id as publisher_lang_id,
                sld.default_keywords as default_keywords,
                sld.default_description as default_description,
                sld.default_title_postfix as default_title_postfix,
                sld.default_og_description as default_og_description,
                sld.default_og_image as default_og_image,
                sld.default_twitter_description as default_twitter_description,
                sld.default_twitter_image as default_twitter_image,
                pub.short_name as language_name,
                pub.short_name_segment as language_segment
            ')
            ->from('channel_titles ct')
            ->where('ct.entry_id', $entry_id)
            ->where('publisher_lang_id', $publisher_id)
            ->where('ct.site_id', $site_id)
            ->join('publisher_seolite_content sl', 'ct.entry_id = sl.entry_id', 'left')
            ->join('seolite_config sld', 'ct.site_id = sld.site_id', 'left')
            ->join('publisher_languages pub', 'sl.publisher_lang_id = pub.id', 'left')
            ->get();
            
        } else {
            $data = ee()->db->select('
                ct.entry_id,
                ct.title,
                sl.title as meta_title,
                sl.description as meta_description,
                sl.keywords as meta_keywords,
                sl.robots_directive as meta_robots,
                sl.og_title as og_title,
                sl.og_type as og_type,
                sl.og_description as og_description,
                sl.og_url as og_url,
                sl.og_image as og_image,
                sl.twitter_title as twitter_title,
                sl.twitter_type as twitter_type,
                sl.twitter_description as twitter_description,
                sl.twitter_image as twitter_image,
                sld.default_keywords as default_keywords,
                sld.default_description as default_description,
                sld.default_title_postfix as default_title_postfix,
                sld.default_og_description as default_og_description,
                sld.default_og_image as default_og_image,
                sld.default_twitter_description as default_twitter_description,
                sld.default_twitter_image as default_twitter_image
            ')
            ->from('channel_titles ct')
            ->where('ct.entry_id', $entry_id)
            ->where('ct.site_id', $site_id)
            ->join('seolite_content sl', 'ct.entry_id = sl.entry_id', 'left')
            ->join('seolite_config sld', 'ct.site_id = sld.site_id', 'left')
            ->get();
        }
        
        if($data->num_rows == 0) {
            $data = ee()->db->select('
                ct.entry_id,
                ct.title,
                sld.default_keywords as default_keywords,
                sld.default_description as default_description,
                sld.default_title_postfix as default_title_postfix,
                sld.default_og_description as default_og_description,
                sld.default_og_image as default_og_image,
                sld.default_twitter_description as default_twitter_description,
                sld.default_twitter_image as default_twitter_image
            ')
            ->from('channel_titles ct')
            ->where('ct.entry_id', $entry_id)
            ->where('ct.site_id', $site_id)
            ->join('seolite_config sld', 'ct.site_id = sld.site_id', 'left')
            ->get();
        }

        return $data->result_array()[0];
    }
    
    function PublisherInstalled() {
        return ee('Addon')->get('publisher') && ee('Addon')->get('publisher')->isInstalled();
    }

    function audit_overview() 
	{
        $publisher = $this->PublisherInstalled();
        
        $vars['data'] =  $this->getAuditOverviewData($publisher);
        $vars['data']['publisher'] = $publisher;

        $view = ee('View')->make('seo_lite:audit_overview');
        return $view->render($vars);
	}

    function audit_entry() 
	{
        $publisher = $this->PublisherInstalled();
        $publisher_id = ee()->input->get('publisher_id');
        $vars['data'] =  $this->getAuditEntryData($publisher, $publisher_id);

        if($publisher) {
            $vars['data']['publisher'] = $publisher;
            $vars['data']['publisher_id'] = $publisher_id;
            $vars['data']['languages'] = ee()->db->select('
                id,
                short_name,
                long_name,
                short_name_segment
            ')
            ->from('publisher_languages')
            ->where('is_enabled', 'y')
            ->get()
            ->result_array();
        }

        $view = ee('View')->make('seo_lite:audit_entry');

        return $view->render($vars);
	}
	
	function save_settings()
	{
		$template = ee()->input->post('seolite_template');
        $default_keywords = ee()->input->post('seolite_default_keywords');
        $default_description = ee()->input->post('seolite_default_description');
        $default_title_postfix = ee()->input->post('seolite_default_title_postfix');
        $default_og_description = ee()->input->post('seolite_default_og_description');
        $default_og_image = ee()->input->post('seolite_default_og_image');
        $default_twitter_description = ee()->input->post('seolite_default_twitter_description');
        $default_twitter_image = ee()->input->post('seolite_default_twitter_image');
        
        $include_pagination_in_canonical = ee()->input->post('seolite_include_pagination_in_canonical');

        $site_id = ee()->config->item('site_id');
        $config = ee()->db->get_where('seolite_config', array('site_id' => $site_id));

        $data_arr = array(
                'template' => $template,
                'default_keywords' => $default_keywords,
                'default_description' => $default_description,
                'default_title_postfix' => $default_title_postfix,
                'default_og_description' => $default_og_description,
                'default_og_image' => $default_og_image,
                'default_twitter_description' => $default_twitter_description,
                'default_twitter_image' => $default_twitter_image,
                'include_pagination_in_canonical' => $include_pagination_in_canonical,
            );

        if($config->num_rows() == 0)
        {
            $data_arr['site_id'] = $site_id;
            ee()->db->insert('seolite_config', $data_arr);
        }
        else
        {
            ee()->db->where('site_id', $site_id);
            ee()->db->update('seolite_config', $data_arr);
        }

        ee('CP/Alert')->makeStandard('seolite-settings-saved')
            ->asSuccess()
            ->withTitle(lang('seolite_settings_saved_title'))
            ->addToBody(lang('seolite_settings_saved'))
            ->defer();

		ee()->functions->redirect(ee('CP/URL', 'addons/settings/seo_lite'));
	}

}

/* End of file mcp.seo_lite.php */