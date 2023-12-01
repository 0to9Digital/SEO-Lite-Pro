<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * SEO Lite (Pro) Update File
 *
 * @category   Module
 * @package    ExpressionEngine
 * @subpackage Addons
 * @author     0to9 Digital - Robin Treur
 * @link       https://0to9.nl
 */

class Seo_lite_upd
{

    var $version = '2.2.2';
    var $module_name = "Seo_lite";

    /**
     * @var Devkit_code_completion
     */
    public $EE;

    function __construct($switch = TRUE)
    {
        // Make a local reference to the ExpressionEngine super object
        $this->EE = get_instance();
    }

    /**
     * Installer for the Seo_lite module
     */
    function install()
    {
        $site_id = $this->EE->config->item('site_id');
        if ($site_id == 0)    // if SEO Lite is installed with a theme site_id will be 0, so set it to 1
        {
            $site_id = 1;
        }

        $data = array(
            'module_name'      => $this->module_name,
            'module_version' => $this->version,
            'has_cp_backend' => 'y',
            'has_publish_fields' => 'y'
        );

        $this->EE->db->insert('modules', $data);

        $this->EE->load->dbforge();

        $seolite_content_fields = array(
            'seolite_content_id' => array(
                'type' => 'int',
                'constraint' => '10',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'site_id' => array(
                'type' => 'int',
                'constraint' => '10',
                'null' => FALSE,
            ),
            'entry_id' => array(
                'type' => 'int',
                'constraint' => '10',
                'null' => FALSE,
            ),
            'title' => array(
                'type' => 'varchar',
                'constraint' => '1024',
            ),
            'keywords' => array(
                'type' => 'varchar',
                'constraint' => '1024',
            ),
            'description' => array(
                'type' => 'text',
            ),
            'robots_directive' => array(
                'type' => 'varchar',
                'constraint' => '70',
            ),
            'og_title' => array(
                'type' => 'varchar',
                'constraint' => '70',
            ),
            'og_type' => array(
                'type' => 'varchar',
                'constraint' => '70',
            ),
            'og_url' => array(
                'type' => 'varchar',
                'constraint' => '1024',
            ),
            'og_description' => array(
                'type' => 'varchar',
                'constraint' => '1024',
            ),
            'og_image' => array(
                'type' => 'varchar',
                'constraint' => '1024',
            ),
            'twitter_title' => array(
                'type' => 'varchar',
                'constraint' => '70',
            ),
            'twitter_type' => array(
                'type' => 'varchar',
                'constraint' => '1024',
            ),
            'twitter_description' => array(
                'type' => 'varchar',
                'constraint' => '1024',
            ),
            'twitter_image' => array(
                'type' => 'varchar',
                'constraint' => '1024',
            ),
        );

        $this->EE->dbforge->add_field($seolite_content_fields);
        $this->EE->dbforge->add_key('seolite_content_id', TRUE);
        $this->EE->dbforge->create_table('seolite_content');

        $seolite_config_fields = array(
            'seolite_config_id' => array(
                'type' => 'int',
                'constraint' => '10',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'site_id' => array(
                'type' => 'int',
                'constraint' => '10',
                'unsigned' => TRUE,
            ),
            'template' => array(
                'type' => 'text',
            ),
            'default_keywords' => array(
                'type' => 'varchar',
                'constraint' => '1024',
                'null' => FALSE,
            ),
            'default_description' => array(
                'type' => 'varchar',
                'constraint' => '1024',
                'null' => FALSE
            ),
            'default_title_postfix' => array(
                'type' => 'varchar',
                'constraint' => '70',
                'null' => FALSE
            ),
            'include_pagination_in_canonical' => array(
                'type' => 'ENUM(\'y\',\'n\')',
                'default' => 'y',
                'null' => FALSE
            ),
            'default_og_description' => array(
                'type' => 'varchar',
                'constraint' => '1024',
                'null' => FALSE
            ),
            'default_og_image' => array(
                'type' => 'varchar',
                'constraint' => '1024',
                'null' => FALSE
            ),
            'default_twitter_description' => array(
                'type' => 'varchar',
                'constraint' => '1024',
                'null' => FALSE
            ),
            'default_twitter_image' => array(
                'type' => 'varchar',
                'constraint' => '1024',
                'null' => FALSE
            ),
        );

        $this->EE->dbforge->add_field($seolite_config_fields);
        $this->EE->dbforge->add_key('seolite_config_id', TRUE);
        $this->EE->dbforge->create_table('seolite_config');

        // insert default config
        $this->EE->db->insert('seolite_config', array(
            'template' => "<title>{title}</title><meta name='description' content='{meta_description}' /><link rel='canonical' href='{canonical_url}' /><meta name='robots' content='{robots_directive}' /><!-- Open Graph --><meta property='og:title' content='{og_title}' />{if og_description}<meta property='og:description' content='{og_description}' />{/if}{if og_type}<meta property='og:type' content='{og_type}' />{/if}{if og_url}<meta property='og:url' content='{og_url}' />{/if}{if og_image}<meta property='og:image' content='{og_image}' />{/if}<!-- Twitter Card --><meta property='twitter:title' content='{twitter_title}' />{if twitter_type}<meta property='twitter:card' content='{twitter_type}' />{/if}{if twitter_description}<meta property='twitter:description' content='{twitter_description}' />{/if}{if twitter_image}<meta property='twitter:image' content='{twitter_image}' />{/if}<!-- generated by SEO Lite (pro) (0to9 Values-Driven Creativity) -->",
            'site_id' => $site_id,
            'default_keywords' => '',
            'default_description' => '',
            'default_title_postfix' => '&nbsp;|&nbsp;[SITENAME]',
            'default_og_description' => '',
            'default_og_image' => '',
            'default_twitter_description' => '',
            'default_twitter_image' => ''
        ));

        $this->EE->load->library('layout');
        $this->EE->layout->add_layout_tabs($this->tabs(), 'seo_lite');

        return TRUE;
    }

    function tabs()
    {
        $tabs['seo_lite'] = array(
            'seo_lite_title' => array(
                'visible'    => 'true',
                'collapse'    => 'false',
                'htmlbuttons'    => 'false',
                'width'        => '100%'
            ),
            'seo_lite_keywords' => array(
                'visible'    => 'true',
                'collapse'    => 'false',
                'htmlbuttons'    => 'false',
                'width'        => '100%'
            ),
            'seo_lite_description' => array(
                'visible'    => 'true',
                'collapse'    => 'false',
                'htmlbuttons'    => 'false',
                'width'        => '100%',
            ),
            'seo_lite_robots_directive' => array(
                'visible'    => 'true',
                'collapse'    => 'false',
                'htmlbuttons'    => 'false',
                'width'        => '100%',
            ),
            'seo_lite_og_title' => array(
                'visible'    => 'true',
                'collapse'    => 'false',
                'htmlbuttons'    => 'false',
                'width'        => '100%',
            ),
            'seo_lite_og_type' => array(
                'visible'    => 'true',
                'collapse'    => 'false',
                'htmlbuttons'    => 'false',
                'width'        => '100%',
            ),
            'seo_lite_og_description' => array(
                'visible'    => 'true',
                'collapse'    => 'false',
                'htmlbuttons'    => 'false',
                'width'        => '100%',
            ),
            'seo_lite_og_url' => array(
                'visible'    => 'true',
                'collapse'    => 'false',
                'htmlbuttons'    => 'false',
                'width'        => '100%',
            ),
            'seo_lite_og_image' => array(
                'visible'    => 'true',
                'collapse'    => 'false',
                'htmlbuttons'    => 'false',
                'width'        => '100%',
            ),
            'seo_lite_twitter_title' => array(
                'visible'    => 'true',
                'collapse'    => 'false',
                'htmlbuttons'    => 'false',
                'width'        => '100%',
            ),
            'seo_lite_twitter_type' => array(
                'visible'    => 'true',
                'collapse'    => 'false',
                'htmlbuttons'    => 'false',
                'width'        => '100%',
            ),
            'seo_lite_twitter_description' => array(
                'visible'    => 'true',
                'collapse'    => 'false',
                'htmlbuttons'    => 'false',
                'width'        => '100%',
            ),
            'seo_lite_twitter_type' => array(
                'visible'    => 'true',
                'collapse'    => 'false',
                'htmlbuttons'    => 'false',
                'width'        => '100%',
            ),
            'seo_lite_twitter_image' => array(
                'visible'    => 'true',
                'collapse'    => 'false',
                'htmlbuttons'    => 'false',
                'width'        => '100%',
            ),
        );

        return $tabs;
    }


    /**
     * Uninstall the Seo_lite module
     */
    function uninstall()
    {
        $this->EE->load->dbforge();

        $this->EE->db->select('module_id');
        $query = $this->EE->db->get_where('modules', array('module_name' => $this->module_name));

        if (version_compare(APP_VER, '6.0', '>=')) {
            $this->EE->db->delete('module_member_roles', ['module_id' => $query->row('module_id')]);
        } else {
            $this->EE->db->delete('module_member_groups', ['module_id' => $query->row('module_id')]);
        }

        $this->EE->db->where('module_name', $this->module_name);
        $this->EE->db->delete('modules');

        $this->EE->db->where('class', $this->module_name);
        $this->EE->db->delete('actions');

        $this->EE->db->where('class', $this->module_name . '_mcp');
        $this->EE->db->delete('actions');

        $this->EE->dbforge->drop_table('seolite_content');
        $this->EE->dbforge->drop_table('seolite_config');

        $this->EE->load->library('layout');
        $this->EE->layout->delete_layout_tabs($this->tabs(), 'seo_lite');

        return TRUE;
    }

    /**
     * Update the Seo_lite module
     *
     * @param $current current version number
     * @return boolean indicating whether or not the module was updated
     */
    function update($current = '')
    {
        if ($current == $this->version) {
            return FALSE;
        }

        // Update seolite database fields for version 2.0.0
        if ($current < '2.0.0') {

            $this->EE->load->dbforge();

            // Add SEO Lite (Pro) config fields
            $seolite_config_fields = array(
                'default_og_description' => array(
                    'type' => 'varchar',
                    'constraint' => '1024',
                    'null' => FALSE
                ),
                'default_og_image' => array(
                    'type' => 'varchar',
                    'constraint' => '1024',
                    'null' => FALSE
                ),
                'default_twitter_description' => array(
                    'type' => 'varchar',
                    'constraint' => '1024',
                    'null' => FALSE
                ),
                'default_twitter_image' => array(
                    'type' => 'varchar',
                    'constraint' => '1024',
                    'null' => FALSE
                ),
            );

            $this->EE->dbforge->add_column('seolite_config', $seolite_config_fields);

            // Add SEO Lite (Pro) content fields
            $seolite_content_fields = array(
                'robots_directive' => array(
                    'type' => 'varchar',
                    'constraint' => '70',
                ),
                'og_title' => array(
                    'type' => 'varchar',
                    'constraint' => '70',
                ),
                'og_type' => array(
                    'type' => 'varchar',
                    'constraint' => '70',
                ),
                'og_url' => array(
                    'type' => 'varchar',
                    'constraint' => '1024',
                ),
                'og_description' => array(
                    'type' => 'varchar',
                    'constraint' => '1024',
                ),
                'og_image' => array(
                    'type' => 'varchar',
                    'constraint' => '1024',
                ),
                'twitter_title' => array(
                    'type' => 'varchar',
                    'constraint' => '70',
                ),
                'twitter_type' => array(
                    'type' => 'varchar',
                    'constraint' => '1024',
                ),
                'twitter_description' => array(
                    'type' => 'varchar',
                    'constraint' => '1024',
                ),
                'twitter_image' => array(
                    'type' => 'varchar',
                    'constraint' => '1024',
                ),
            );

            $this->EE->dbforge->add_column('seolite_content', $seolite_content_fields);
        }

        return TRUE;
    }
}

/* End of file upd.seo_lite.php */
