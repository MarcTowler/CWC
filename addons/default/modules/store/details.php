<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Module_Store extends Module {

    public $version = '1.7.4';

    public function info() {

        $info = array(
            'name' => array(
                'en' => 'Store',
                'pl' => 'Sklep',
                'nl' => 'Winkel',
                'it' => 'Negozio',
                'de' => 'Geschäft',
            ),
            'description' => array(
                'en' => 'This is a PyroCMS store module.',
                'pl' => 'To jest moduł sklepu dla systemu PyroCMS.',
                'nl' => 'Dit is de winkel module voor PyroCMS systeem.',
                'it' => 'Questo è un modulo negozio PyroCMS',
                'de' => 'Dies ist eine PyroCMS Shop Modul'
            ),
            'author' => 'Blazej Adamczyk',
            'skip_xss' => TRUE,
            'frontend' => TRUE,
            'backend' => TRUE,
            'menu' => false,
            'roles' => array('catalog', 'promotions', 'shipping', 'gateways'),
            'sections' => array(
                'dashboard' => array(
                    'name' => 'store:dashboard',
                    'uri' => 'admin/store',
                    'shortcuts' => array(
                        'create' => array(
                            'name' => 'store:new_product',
                            'uri' => 'admin/store/products/create',
                            'class' => 'add'
                        ),
                        'data' => array(
                            'name' => 'store:data',
                            'uri' => 'admin/store/data',
                        ),
                    )
                ),
                'products' => array(
                    'name' => 'store:products', // These are translated from your language file

                    'uri' => 'admin/store/products',
                    'shortcuts' => array(
                        'create' => array(
                            'name' => 'store:new_product',
                            'uri' => 'admin/store/products/create',
                            'class' => 'add'
                        ),
                        'options' => array(
                            'name' => 'store:manage_options',
                            'uri' => 'admin/store/options',
                        ),
                        'fields' => array(
                            'name' => 'global:custom_fields',
                            'uri' => 'admin/store/products/fields',
                        ),
                    )
                ),
                'fields' => array(
                    'shortcuts' => array(
                        'create' => array(
                            'name' => 'streams:add_field',
                            'uri' => 'admin/store/products/fields/create',
                            'class' => 'add'
                        ),
                    )
                ),
                'options' => array(
                    'shortcuts' => array(
                        'create' => array(
                            'name' => 'store:new_option',
                            'uri' => 'admin/store/options/create',
                            'class' => 'add'
                        ),
                    )
                ),
                'data' => array(
                    'shortcuts' => array(
                        'import' => array(
                            'name' => 'store:import',
                            'uri' => 'admin/store/data/import',
                            'class' => 'add'
                        ),
                    )
                ),
                'categories' => array(
                    'name' => 'store:categories', // These are translated from your language file
                    'uri' => 'admin/store/categories',
                    'shortcuts' => array()
                ),
                'sales' => array(
                    'name' => 'store:sales', // These are translated from your language file
                    'uri' => 'admin/store/sales',
                    'shortcuts' => array(
                        'group' => array(
                            'name' => 'store:new_special_group',
                            'uri' => 'admin/store/sales/group',
                            'class' => 'add'
                        ),
                        'create' => array(
                            'name' => 'store:new_sale',
                            'uri' => 'admin/store/sales/create',
                            'class' => 'add'
                        )
                    )
                ),
                'coupons' => array(
                    'name' => 'store:coupons', // These are translated from your language file
                    'uri' => 'admin/store/coupons',
                    'shortcuts' => array(
                        'create' => array(
                            'name' => 'store:new_coupon',
                            'uri' => 'admin/store/coupons/create',
                            'class' => 'add'
                        )
                    )
                ),
                'affiliate' => array(
                    'name' => 'store:affiliate', // These are translated from your language file
                    'uri' => 'admin/store/affiliate',
                    'shortcuts' => array(
                        'user' => array(
                            'name' => 'store:new_affiliate_user',
                            'uri' => 'admin/store/affiliate/create/user',
                            'class' => 'add'
                        ),
                        'group' => array(
                            'name' => 'store:new_affiliate_group',
                            'uri' => 'admin/store/affiliate/create/group',
                            'class' => 'add'
                        )
                    )
                ),
                'shipments' => array(
                    'name' => 'store:shipments', // These are translated from your language file
                    'uri' => 'admin/store/shipments',
                    'shortcuts' => array(
                        'create' => array(
                            'name' => 'store:create',
                            'uri' => 'admin/store/shipments/create',
                            'class' => 'add'
                        )
                    )
                ),
                'gateways' => array(
                    'name' => 'store:gateways', // These are translated from your language file
                    'uri' => 'admin/store/gateways',
                )
            )
        );
        return $info;
    }

    public function admin_menu(&$menu) {
        $this->lang->load('store/store');
        $menu['lang:store:store_title'] = array(
            'lang:store:dashboard' => 'admin/store/',
            'lang:store:products' => 'admin/store/products',
            'lang:store:categories' => 'admin/store/categories',
            'lang:store:sales' => 'admin/store/sales',
            'lang:store:coupons' => 'admin/store/coupons',
            'lang:store:affiliate' => 'admin/store/affiliate',
            'lang:store:shipments' => 'admin/store/shipments',
            'lang:store:gateways' => 'admin/store/gateways',
        );

        add_admin_menu_place('lang:store:store_title', 2);
    }

    public function install() {
        //$this->dbforge->drop_table('store');
        $this->uninstall();

        $this->load->driver('Streams');

        $this->streams->streams->add_stream(
                'lang:store:products', 'store_products', 'store', null, null
        );

        $this->streams->fields->add_fields(array(
            array(
                'name' => 'lang:store:manufacturer',
                'slug' => 'manufacturer',
                'namespace' => 'store',
                'type' => 'text',
                'assign' => 'store_products',
                'extra' => array('max_length' => '100', 'default_value' => ''),
                'required' => false
            ),
            array(
                'name' => 'lang:store:weight',
                'slug' => 'weight',
                'namespace' => 'store',
                'type' => 'text',
                'assign' => 'store_products',
                'extra' => array('max_length' => '100', 'default_value' => ''),
                'required' => false
            ),
        ));

        $this->dbforge->add_column('store_products', array(
            'category_id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE),
            'sku' => array('type' => 'VARCHAR', 'constraint' => '100'),
            'name' => array('type' => 'VARCHAR', 'constraint' => '100'),
            'slug' => array('type' => 'VARCHAR', 'constraint' => '100', 'unique' => TRUE),
            'short' => array('type' => 'VARCHAR', 'constraint' => '255'),
            'keywords' => array('type' => 'VARCHAR', 'constraint' => '32'),
            'description' => array('type' => 'TEXT'),
            'attributes' => array('type' => 'TEXT'),
            'price' => array('type' => 'DECIMAL(8,2)'),
            'price_tax' => array('type' => 'DECIMAL(8,2)'),
            'cover_id' => array('type' => 'CHAR', 'constraint' => '15', 'default' => ''),
            'status' => array('type' => "ENUM('in_stock', 'soon_available', 'out_of_stock', 'pre_order', 'disabled')", 'default' => 'in_stock'),
        ));
        $prefix = $this->db->dbprefix('store_products');
        $this->db->simple_query('ALTER TABLE `'.$prefix.'` ADD UNIQUE (`slug`);');

        $products = $this->install_tables(array(
            'store_categories' => array(
                'id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'auto_increment' => TRUE, 'primary' => true),
                'name' => array('type' => 'VARCHAR', 'constraint' => '100'),
                'description' => array('type' => 'TEXT'),
                'slug' => array('type' => 'VARCHAR', 'constraint' => '100'),
                'uri' => array('type' => 'VARCHAR', 'constraint' => '255'),
                'image_id' => array('type' => 'CHAR', 'constraint' => '15', 'default' => ''),
                'parent_id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'default' => 0),
                'order' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'default' => 0),
                'status' => array('type' => 'INT', 'constraint' => '1', 'default' => 1)
            ),
            'store_related' => array(
                'product_id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'key' => TRUE),
                'rel_id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'key' => TRUE),
            ),
            'store_images' => array(
                'product_id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'key' => TRUE),
                'file_id' => array('type' => 'CHAR', 'constraint' => '15', 'default' => ''),
            ),
            'store_sales' => array(
                'id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'auto_increment' => TRUE, 'primary' => true),
                'product_id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE),
                'new_price' => array('type' => 'DECIMAL(8,2)', 'unsigned' => TRUE),
                'new_price_tax' => array('type' => 'DECIMAL(8,2)', 'unsigned' => TRUE),
                'reduction' => array('type' => 'INT', 'constraint' => '2', 'unsigned' => TRUE, 'default' => 0),
                'sale_type' => array('type' => "ENUM('sale', 'reduce', 'promo', 'featured')", 'default' => 'sale'),
            ),
            'store_affiliate_users' => array(
                'user_id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'key' => TRUE, 'unique' => TRUE),
                'discount' => array('type' => 'INT', 'constraint' => '2', 'unsigned' => TRUE, 'default' => 0),
            ),
            'store_affiliate_groups' => array(
                'group_id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'key' => TRUE, 'unique' => TRUE),
                'discount' => array('type' => 'INT', 'constraint' => '2', 'unsigned' => TRUE, 'default' => 0),
            ),
            'store_coupons' => array(
                'id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'auto_increment' => TRUE, 'primary' => true),
                'name' => array('type' => 'VARCHAR', 'constraint' => '100'),
                'code' => array('type' => 'VARCHAR', 'constraint' => '16', 'unique' => TRUE),
                'amount' => array('type' => 'DECIMAL(8,2)', 'unsigned' => TRUE),
                'type' => array('type' => "ENUM('percent', 'fixed')", 'default' => 'fixed'),
                'uses_limit' => array('type' => 'INT', 'constraint' => '8', 'unsigned' => TRUE, 'default' => 1),
                'uses_order' => array('type' => 'INT', 'constraint' => '8', 'unsigned' => TRUE, 'default' => 1),
                'uses_user' => array('type' => 'INT', 'constraint' => '8', 'unsigned' => TRUE, 'default' => 1),
                'date_start' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE),
                'date_end' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE),
                'status' => array('type' => 'INT', 'constraint' => '1', 'unsigned' => TRUE, 'default' => 1),
            ),
            'store_coupon_uses' => array(
                'coupon_id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE),
                'user_id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE),
                'order_id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE),
                'date' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE),
            ),
            'store_product_options' => array(
                'product_id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE),
                'option_id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE),
                'value_id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE),
                'price' => array('type' => 'DECIMAL(6,2)'),
                'sort' => array('type' => 'INT', 'constraint' => '6', 'default' => 0),
            ),
            'store_options' => array(
                'id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'auto_increment' => TRUE, 'primary' => true),
                'title' => array('type' => 'VARCHAR', 'constraint' => '100'),
                'slug' => array('type' => 'VARCHAR', 'constraint' => '100'),
                'field_type' => array('type' => "ENUM('radio', 'checkbox', 'select')", 'default' => 'select'),
            ),
            'store_option_values' => array(
                'id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'auto_increment' => TRUE, 'primary' => true),
                'option_id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'key' => TRUE),
                'label' => array('type' => 'VARCHAR', 'constraint' => '100'),
                'value' => array('type' => 'VARCHAR', 'constraint' => '100', 'key' => TRUE),
            )
                ));



        $transactions = $this->install_tables(array(
            'store_shipments' => array(
                'id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'auto_increment' => TRUE, 'primary' => true),
                'name' => array('type' => 'VARCHAR', 'constraint' => '100'),
                'desc' => array('type' => 'TEXT',),
                'url' => array('type' => 'VARCHAR', 'constraint' => '255'),
                'file_id' => array('type' => 'INT', 'constraint' => '11', 'default' => 0),
                'file_id' => array('type' => 'VARCHAR', 'constraint' => '16', 'default' => 0),
                'price' => array('type' => 'DECIMAL(8,2)'),
                'multiply' => array('type' => "ENUM('full', 'per_price', 'per_weight')", 'default' => 'full'),
                'type' => array('type' => "ENUM('flat', 'weight', 'cost', 'quantity', 'distance')", 'default' => 'flat'),
                'options' => array('type' => 'TEXT'),
                'status' => array('type' => "ENUM('active', 'disabled')", 'default' => 'active'),
                'min' => array('type' => 'DECIMAL(8,2)', 'default' => 0),
                'max' => array('type' => 'DECIMAL(8,2)', 'default' => 0),
            ),
            'store_transactions' => array(
                'id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'auto_increment' => TRUE, 'primary' => true),
                'order_id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE,),
                'transaction_id' => array('type' => 'VARCHAR', 'constraint' => '255', 'default' => ''),
                'status' => array('type' => "ENUM('pending','accepted','rejected')", 'default' => 'pending'),
                'reason' => array('type' => 'TEXT', 'default' => null, 'null' => TRUE),
                'amount' => array('type' => 'DECIMAL(8,2)'),
                'gateway' => array('type' => 'VARCHAR', 'constraint' => '100', 'default' => ''),
                'data' => array('type' => 'TEXT', 'default' => null, 'null' => TRUE),
                'timestamp' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE,),
            ),
            'store_addresses' => array(
                'id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'auto_increment' => TRUE, 'primary' => true),
                'user_id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE,),
                'email' => array('type' => 'VARCHAR', 'constraint' => '100'),
                'first_name' => array('type' => 'VARCHAR', 'constraint' => '100'),
                'last_name' => array('type' => 'VARCHAR', 'constraint' => '100'),
                'company' => array('type' => 'VARCHAR', 'constraint' => '100'),
                'nip' => array('type' => 'VARCHAR', 'constraint' => '100'),
                'address1' => array('type' => 'VARCHAR', 'constraint' => '100'),
                'address2' => array('type' => 'VARCHAR', 'constraint' => '100'),
                'city' => array('type' => 'VARCHAR', 'constraint' => '80'),
                'state' => array('type' => 'VARCHAR', 'constraint' => '80'),
                'country' => array('type' => 'VARCHAR', 'constraint' => '80'),
                'zip' => array('type' => 'VARCHAR', 'constraint' => '10'),
                'phone' => array('type' => 'VARCHAR', 'constraint' => '20')
            ),
            'store_orders' => array(
                'id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'auto_increment' => TRUE, 'primary' => true),
                'user_id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE,),
                'total' => array('type' => 'DECIMAL(8,2)', 'unsigned' => TRUE, 'null' => TRUE, 'default' => NULL),
                'shipping' => array('type' => 'DECIMAL(8,2)'),
                'shipping_code' => array('type' => 'VARCHAR', 'constraint' => '64', 'default' => '',),
                'coupon' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'default' => 0),
                'shipment_method_id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE,),
                'payment_method_id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE,),
                'billing_address_id' => array('type' => "INT", 'constraint' => '11', 'unsigned' => TRUE, 'default' => 0),
                'delivery_address_id' => array('type' => "INT", 'constraint' => '11', 'unsigned' => TRUE, 'default' => 0),
                'order_date' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE,),
                'token' => array('type' => 'VARCHAR', 'constraint' => '32', 'default' => '',),
                'fraud_score' => array('type' => 'INT', 'constraint' => '3', 'default' => 0,),
                'session_id' => array('type' => 'VARCHAR', 'constraint' => '40', 'default' => '',),
            ),
            'store_order_contents' => array(
                'id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'auto_increment' => TRUE, 'primary' => true),
                'order_id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE,),
                'product_id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE,),
                'name' => array('type' => 'VARCHAR', 'constraint' => '100', 'default' => '',),
                'module' => array('type' => 'VARCHAR', 'constraint' => '50', 'default' => 'store',),
                'uri' => array('type' => 'VARCHAR', 'constraint' => '255', 'default' => '',),
                'options' => array('type' => 'TEXT',),
                'qty' => array('type' => 'TINYINT'),
                'price_per' => array('type' => 'DECIMAL(8,2)', 'unsigned' => TRUE),
            ),
            'store_order_history' => array(
                'id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'auto_increment' => TRUE, 'primary' => true),
                'order_id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE,),
                'comment' => array('type' => 'TEXT'),
                'notify' => array('type' => 'INT', 'constraint' => '1'),
                'status' => array('type' => "ENUM('pending', 'reply', 'complete', 'expired', 'processing', 'processed', 'shipping', 'canceled', 'rejected')", 'default' => 'pending'),
                'date_added' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE,),
            ),
            'store_wishlist' => array(
                'user_id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'key' => TRUE),
                'product_id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'key' => TRUE),
                'added_on' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE),
            ),
            'store_invoices' => array(
                'id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'auto_increment' => TRUE, 'primary' => true),
                'order_id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE,),
                'billing_id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE,),
                'invoice_id' => array('type' => 'VARCHAR', 'constraint' => '100'),
                'date_created' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE,),
            ),
            'store_gateways' => array(
                'id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'auto_increment' => TRUE, 'primary' => true),
                'name' => array('type' => 'VARCHAR', 'constraint' => '100'),
                'slug' => array('type' => 'VARCHAR', 'constraint' => '100'),
                'desc' => array('type' => 'TEXT'),
                'visibility' => array('type' => 'VARCHAR', 'constraint' => '100', 'default' => '',),
                'enabled' => array('type' => 'INT', 'constraint' => '1', 'default' => 0),
                'options' => array('type' => 'TEXT'),
            ),
                ));


        if ($products && $transactions && $this->_create_settings() && $this->_create_templates()) {
            return TRUE;
        }
    }

    public function uninstall() {

        $this->load->driver('Streams');
        $this->streams->utilities->remove_namespace('store');

        if ($this->db->table_exists('data_streams')) {
            $this->db->where('stream_namespace', 'store')->delete('data_streams');
        }

        $this->dbforge->drop_table('store_categories');
        $this->dbforge->drop_table('store_products');
        $this->dbforge->drop_table('store_images');
        $this->dbforge->drop_table('store_related');
        $this->dbforge->drop_table('store_sales');
        $this->dbforge->drop_table('store_affiliate_users');
        $this->dbforge->drop_table('store_affiliate_groups');
        $this->dbforge->drop_table('store_coupon_uses');
        $this->dbforge->drop_table('store_coupons');
        $this->dbforge->drop_table('store_options');
        $this->dbforge->drop_table('store_product_options');
        $this->dbforge->drop_table('store_option_values');

        $this->dbforge->drop_table('store_shipments');
        $this->dbforge->drop_table('store_transactions');
        $this->dbforge->drop_table('store_addresses');
        $this->dbforge->drop_table('store_orders');
        $this->dbforge->drop_table('store_order_contents');
        $this->dbforge->drop_table('store_order_history');
        $this->dbforge->drop_table('store_wishlist');
        $this->dbforge->drop_table('store_invoices');
        $this->dbforge->drop_table('store_gateways');

        $this->db->delete('settings', array('module' => 'store'));
        $this->db->delete('search_index', array('module' => 'store'));
        $this->db->delete('email_templates', array('module' => 'store'));
        {

            return TRUE;
        }
    }

    public function upgrade($old_version) {
        if (version_compare('1.6.9', $old_version, '>')) {
            $this->session->set_flashdata('error', 'Upgrade below version 1.7 not supported');
            return FALSE;
        }

        if (version_compare('1.7', $old_version, '>')) {
            $this->install_tables(array(
                'store_affiliate_users' => array(
                    'user_id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'key' => TRUE, 'unique' => TRUE),
                    'discount' => array('type' => 'INT', 'constraint' => '2', 'unsigned' => TRUE, 'default' => 0),
                ),
                'store_affiliate_groups' => array(
                    'group_id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'key' => TRUE, 'unique' => TRUE),
                    'discount' => array('type' => 'INT', 'constraint' => '2', 'unsigned' => TRUE, 'default' => 0),
                )
            ));
            $this->dbforge->add_column('store_products', array(
                'updated' => array('type' => 'DATETIME'),
                'created' => array('type' => 'DATETIME', 'null' => TRUE),
                'ordering_count' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE, 'null' => TRUE),
                'manufacturer' => array('type' => 'VARCHAR', 'constraint' => '100', 'null' => TRUE),
            ));
            $this->dbforge->add_column('store_orders', array(
                'token' => array('type' => 'VARCHAR', 'constraint' => '32', 'default' => '',),
                'shipping_code' => array('type' => 'VARCHAR', 'constraint' => '64', 'default' => '',),
            ));
            $this->dbforge->add_column('store_gateways', array(
                'visibility' => array('type' => 'VARCHAR', 'constraint' => '100', 'default' => '',),
            ));
            $this->dbforge->add_column('store_shipments', array(
                'type' => array('type' => "ENUM('flat', 'weight', 'cost', 'quantity', 'distance')", 'default' => 'flat'),
                'options' => array('type' => 'TEXT'),
            ));
        }
        if (version_compare('1.7.1', $old_version, '>')) {
            $settings = array(
                'store_weight_unit' => array(
                    'title' => 'Weight Units', 'description' => 'Weight Unit used in your store',
                    'type' => 'radio', 'default' => 'kg', 'value' => '', 'options' => 'kg=Kilogrammes|lb=Pounds', 'is_required' => false,
                    'is_gui' => true, 'module' => 'store', 'order' => 773
                ),
                'store_distance_unit' => array(
                    'title' => 'Distance units', 'description' => 'Units used to determine shipping distance',
                    'type' => 'radio', 'default' => 'km', 'value' => '', 'options' => 'km=Kilometers|m=Miles', 'is_required' => false,
                    'is_gui' => true, 'module' => 'store', 'order' => 772
                ),
            );

            $this->install_settings($settings);
        }
        if (version_compare('1.7.2', $old_version, '>')) {
            
            $settings = array(
                'store_fraudlabs_licence' => array(
                    'title' => 'FraudLabs Licence Number', 'description' => 'Licence number for FraudLabs Fraud Detection API <br/>Get it here: <a href="http://www.fraudlabs.com/freelicense.aspx?PackageID=1" target="_blank">http://www.fraudlabs.com/freelicense.aspx?PackageID=1</a>',
                    'type' => 'text', 'default' => '', 'value' => '', 'options' => '', 'is_required' => false,
                    'is_gui' => true, 'module' => 'store', 'order' => 771
                ),
                'store_fraudlabs_mode' => array(
                    'title' => 'Fraud Detection Mode', 'description' => 'Disable fraud detection or select detection mode',
                    'type' => 'radio', 'default' => 0, 'value' => '', 'options' => '0=Disabled|fraudlabs=Fraud Labs|fraudlabspro=Fraud Labs Pro', 'is_required' => false,
                    'is_gui' => true, 'module' => 'store', 'order' => 770
                ),
            );

            $this->install_settings($settings);
            
            $this->dbforge->add_column('store_orders', array(
                'fraud_score' => array('type' => 'INT', 'constraint' => '3', 'default' => 0,),
            ));
            $this->dbforge->add_column('store_order_contents', array(
                'module' => array('type' => 'VARCHAR', 'constraint' => '50', 'default' => 'store',),
                'uri' => array('type' => 'VARCHAR', 'constraint' => '255', 'default' => '',),
            ));
            $this->dbforge->modify_column('store_order_contents', array('title' => array('name' => 'name', 'type' => 'VARCHAR', 'constraint' => '100', 'default' => '',), ));
        }
        if (version_compare('1.7.4', $old_version, '>')) {

            $this->db->query('ALTER TABLE '.$this->db->dbprefix('store_option_values').' ADD  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ');
            
            $this->dbforge->add_column('store_product_options', array(
                'value_id' => array('type' => 'INT', 'constraint' => '11', 'unsigned' => TRUE),
                'price' => array('type' => 'DECIMAL(6,2)'),
            ));
        }
        
        return TRUE;
    }

    public function help() {

        // Return a string containing help info
        // You could include a file and return it here.

        return "No documentation has been added for this module.<br />Contact the module developer for assistance.";
    }

    private function _create_templates() {

        $this->db->insert('email_templates', array(
            'slug' => 'store_made_order',
            'name' => 'Store: User Made Order',
            'description' => 'Email sent to user when order was placed',
            'subject' => '{{ settings:site_name }} :: Order has been placed',
            'body' => '<h3>You have placed an order from {{ settings:site_name }}</h3>

                <p>Thank you for your interest in our store products. 

                Your order has been placed and will be processed shortly.</p>

                <table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">

                <thead>

                <tr>

                    <th style="border: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;" colspan="2">Order Details</td>

                </tr>

                </thead>

                <tbody>

                    <tr>

                        <td style="font-size: 12px; border: 1px solid #DDDDDD; text-align: left; padding: 7px;">
                        {{ if user_exists }}
                        <b>Order ID:</b> {{ order_id }}<br />
                        {{ endif }}
                        <b>Date Added:</b> {{ date_created }}<br />

                        <b>Payment Method:</b> {{ payment_method }}<br />

                        <b>Shipping Method:</b> {{ shipping_method }}</td>

                        

                        <td style="font-size: 12px; border: 1px solid #DDDDDD; text-align: left; padding: 7px;">

                        <b>Email:</b> {{ email }}<br />

                        <b>Telephone:</b> {{ phone }}<br />

                        <b>IP Address:</b> {{ sender_ip }}<br /></td>

                    </tr>

                    <tr>

                    <th style="border: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;">Billing Address</td>

                    <th style="border: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;">Shipping Address</td>

                    </tr>

                    <tr>

                        <td style="font-size: 12px; border: 1px solid #DDDDDD; text-align: left; padding: 7px;">

                        {{ payment_address }}</td>

                        <td style="font-size: 12px; border: 1px solid #DDDDDD; text-align: left; padding: 7px;">

                        {{ shipment_address }}</td>

                    </tr>

                    </tbody>

                </table>

                <p>&nbsp;</p>

                <table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">

                <thead>

                <tr>

                    <th colspan="4" style="border: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;">Order Contents</td>

                </tr>

                </thead>

                <tbody>

                        {{ order_contents }}
                    <tr>
                        <td style="font-size: 12px; border: 1px solid #DDDDDD; text-align: left; padding: 7px;">
                        {{ qty }}x
                        </td>
                        <td style="font-size: 12px; border: 1px solid #DDDDDD; text-align: left; padding: 7px;">
                        <a href="{{ url:site }}store/product/{{ slug }}">{{ name }}</a>
                        </td>
                        <td style="font-size: 12px; border: 1px solid #DDDDDD; text-align: right; padding: 7px;">
                        {{ price }} {{ settings:currency }}
                        </td>
                        <td style="font-size: 12px; border: 1px solid #DDDDDD; text-align: right; padding: 7px;">
                         {{ subtotal }} {{ settings:currency }}
                        </td>
                    </tr>
                        {{ /order_contents }}

                    <tr>

                    <th colspan="3" style="border: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;">
                    {{ if user_exists }}<a href="{{ url:site }}store/customer/order/{{ order_id }}">Order details &raquo;</a>{{ endif }}
                    </th>
                    <th style="border: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: right; padding: 7px; color: #222222;">
                    {{ total_amount }} {{ settings:currency }}</td>
                    </tr>

                    </tbody>

                </table>

                ',
            'lang' => 'en',
            'is_default' => 1,
            'module' => 'store'
        ));



        $this->db->insert('email_templates', array(
            'slug' => 'store_new_order',
            'name' => 'Store: Admin Notification About Order',
            'description' => 'Email sent to admin when order is placed',
            'subject' => 'You have a new order',
            'body' => '<h3>You just received new order</h3>

                <b>Order ID:</b> {{ order_id }}<br />

                <b>Date Added:</b> {{ date_created }}<br />

                <b>Payment Method:</b> {{ payment_method }}<br />

                <b>Shipping Method:</b> {{ shipping_method }}<br />

                <b>Email:</b> {{ email }}<br />

                <b>Telephone:</b> {{ phone }}<br />

                <b>IP Address:</b> {{ sender_ip }}<br /><br />

                <b>Billing Address:</b>{{ payment_address }}<br />

                <b>Shipping Address:</b>{{ shipment_address }}<br /><br />

                <b>Amount:</b>{{ total_amount }}<br />

                <b>Items:</b>{{ total_items }}<br />
                
                <p><a href="{{ url:site }}admin/store/order/{{ order_id }}">See detail &raquo;</a></p>

                ',
            'lang' => 'en',
            'is_default' => 1,
            'module' => 'store'
        ));

        $this->db->insert('email_templates', array(
            'slug' => 'store_transaction',
            'name' => 'Store: Transaction Accepted',
            'description' => 'Email sent to user when transaction is complete',
            'subject' => '{{ settings:site_name }} :: Transaction is complete',
            'body' => '<h3>You just finished payment for your order</h3>

                <b>Order ID:</b> {{ order_id }}<br />

                <b>Date Added:</b> {{ date_created }}<br />

                <b>Billing Address:</b>{{ payment_address }}<br />

                <b>Amount:</b>{{ total_amount }}<br />

                <b>Items:</b>{{ total_items }}<br />

                ',
            'lang' => 'en',
            'is_default' => 1,
            'module' => 'store'
        ));

        $this->db->insert('email_templates', array(
            'slug' => 'store_notify',
            'name' => 'Store: Notification',
            'description' => 'Email sent to user when order status is changed',
            'subject' => '{{ settings:site_name }} :: Notification',
            'body' => '<h3>You just received notification regarding your order</h3>

                <b>Order ID:</b> {{ order_id }}<br />

                <b>Status:</b> {{ status }}<br />

                <p>{{ comment }}</p>

                ',
            'lang' => 'en',
            'is_default' => 1,
            'module' => 'store'
        ));



        return TRUE;
    }

    private function _create_settings() {

        $settings = array(
            'store_currency_code' => array(
                'title' => 'Store Currency', 'description' => 'Currency of the store products (ISO-4217 format, ex. GBP)',
                'type' => 'text', 'default' => 'GBP', 'value' => '', 'options' => '', 'is_required' => false,
                'is_gui' => true, 'module' => 'store', 'order' => 779
            ),
            'store_weight_unit' => array(
                'title' => 'Weight Units', 'description' => 'Weight Unit used in your store',
                'type' => 'radio', 'default' => 'kg', 'value' => '', 'options' => 'kg=Kilogrammes|lb=Pounds', 'is_required' => false,
                'is_gui' => true, 'module' => 'store', 'order' => 773
            ),
            'store_distance_unit' => array(
                'title' => 'Distance units', 'description' => 'Units used to determine shipping distance',
                'type' => 'radio', 'default' => 'km', 'value' => '', 'options' => 'km=Kilometers|m=Miles', 'is_required' => false,
                'is_gui' => true, 'module' => 'store', 'order' => 772
            ),
            'store_tax_value' => array(
                'title' => 'Tax Value (%)', 'description' => 'Percentage tax value in your country',
                'type' => 'text', 'default' => 7, 'value' => '', 'options' => '', 'is_required' => false,
                'is_gui' => true, 'module' => 'store', 'order' => 778
            ),
            'store_login_required' => array(
                'title' => 'Require login to use store', 'description' => 'Does user account is required to access store, and use cart?',
                'type' => 'radio', 'default' => 0, 'value' => '', 'options' => '1=Yes|0=No', 'is_required' => false,
                'is_gui' => true, 'module' => 'store', 'order' => 776
            ),
            'store_ssl_required' => array(
                'title' => 'Enable Secure SSL Payment', 'description' => 'Require to proccess order and payment through SSL',
                'type' => 'radio', 'default' => 0, 'value' => '', 'options' => '1=Yes|0=No', 'is_required' => false,
                'is_gui' => true, 'module' => 'store', 'order' => 775
            ),
            'store_new_duration' => array(
                'title' => '"New" label duration', 'description' => 'Number of days by which the product is marked as "new"',
                'type' => 'select', 'default' => 0, 'value' => '', 'options' => '7=1 Week|14=2 Week|30=1 Month', 'is_required' => false,
                'is_gui' => true, 'module' => 'store', 'order' => 774
            ),
            'store_products_limit' => array(
                'title' => 'Products per page', 'description' => 'How many products show in category view (0 - general pagination settings will be used)',
                'type' => 'text', 'default' => 0, 'value' => '', 'options' => '', 'is_required' => false,
                'is_gui' => true, 'module' => 'store', 'order' => 773
            ),
            'store_fraudlabs_licence' => array(
                'title' => 'FraudLabs Licence Number', 'description' => 'Licence number for FraudLabs Fraud Detection API <br/>Get it here: <a href="http://www.fraudlabs.com/freelicense.aspx?PackageID=1" target="_blank">http://www.fraudlabs.com/freelicense.aspx?PackageID=1</a>',
                'type' => 'text', 'default' => '', 'value' => '', 'options' => '', 'is_required' => false,
                'is_gui' => true, 'module' => 'store', 'order' => 771
            ),
            'store_fraudlabs_mode' => array(
                'title' => 'Fraud Detection Mode', 'description' => 'Disable fraud detection or select detection mode',
                'type' => 'radio', 'default' => 0, 'value' => '', 'options' => '0=Disabled|fraudlabs=Fraud Labs|fraudlabspro=Fraud Labs Pro', 'is_required' => false,
                'is_gui' => true, 'module' => 'store', 'order' => 770
            ),
        );

        return $this->install_settings($settings);
    }

    public function install_settings($param) {
        foreach ($param as $slug => $setting_info) {

            $setting_info['slug'] = $slug;

            if (!$this->db->insert('settings', $setting_info)) {

                return false;
            }
        }

        return TRUE;
    }

}

/* End of file details.php */

