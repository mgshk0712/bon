<?php

class Modelsellerprofilesellerprofile extends Model
{

    public function getseller($seller_id)
    {
        $query = $this->db->query('SELECT DISTINCT *,c.description as seller_description FROM '.DB_PREFIX.'customer  c

		LEFT JOIN '.DB_PREFIX.'seller_group_description  sgd ON (c.seller_group_id = sgd.seller_group_id)
		LEFT JOIN '.DB_PREFIX."seller_group sg ON (c.seller_group_id = sg.seller_group_id)
		WHERE customer_id = '".(int) $seller_id."'
		AND sgd.language_id = '".(int) $this->config->get('config_language_id')."'
		");

        return $query->row;
    }

    public function getSellerGroupId()
    {
        $seller_data = array();

        $query = $this->db->query('SELECT seller_group_id FROM '.DB_PREFIX."customer
		WHERE customer_id = '".(int) $this->customer->getID()."'

		");

        foreach ($query->rows as $result) {
            $seller_data[] = $result['seller_group_id'];
        }

        return $seller_data;
    }

    public function getsellerByEmail($email)
    {
        $query = $this->db->query('SELECT DISTINCT * FROM '.DB_PREFIX."customer WHERE LCASE(email) = '".$this->db->escape(utf8_strtolower($email))."'");

        return $query->row;
    }

    public function getsellers($data = array())
    {
        $sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name, cgd.name AS seller_group FROM ".DB_PREFIX.'customer c LEFT JOIN '.DB_PREFIX."seller_group_description cgd ON (c.seller_group_id = cgd.seller_group_id) WHERE cgd.language_id = '".(int) $this->config->get('config_language_id')."'";

        $implode = array();

        if (!empty($data['filter_name'])) {
            $implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%".$this->db->escape($data['filter_name'])."%'";
        }

        if (!empty($data['filter_email'])) {
            $implode[] = "c.email LIKE '%".$this->db->escape($data['filter_email'])."%'";
        }

        if (isset($data['filter_newsletter']) && $data['filter_newsletter'] !== null) {
            $implode[] = "c.newsletter = '".(int) $data['filter_newsletter']."'";
        }

        if (!empty($data['filter_seller_group_id'])) {
            $implode[] = "c.seller_group_id = '".(int) $data['filter_seller_group_id']."'";
        }

        if (!empty($data['filter_ip'])) {
            $implode[] = 'c.seller_id IN (SELECT seller_id FROM '.DB_PREFIX."customer_ip WHERE ip = '".$this->db->escape($data['filter_ip'])."')";
        }

        if (isset($data['filter_status']) && $data['filter_status'] !== null) {
            $implode[] = "c.status = '".(int) $data['filter_status']."'";
        }

        if (isset($data['filter_seller_approved']) && $data['filter_seller_approved'] !== null) {
            $implode[] = "c.seller_approved = '".(int) $data['filter_seller_approved']."'";
        }

        if (!empty($data['filter_date_added'])) {
            $implode[] = "DATE(c.seller_date_added) = DATE('".$this->db->escape($data['filter_date_added'])."')";
        }

        if ($implode) {
            $sql .= ' AND '.implode(' AND ', $implode);
        }

        $sort_data = array(
            'name',
            'c.email',
            'seller_group',
            'c.status',
            'c.seller_approved',
            'c.ip',
            'c.seller_date_added',
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= ' ORDER BY '.$data['sort'];
        } else {
            $sql .= ' ORDER BY name';
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= ' DESC';
        } else {
            $sql .= ' ASC';
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= ' LIMIT '.(int) $data['start'].','.(int) $data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getSellerGroup($seller_group_id)
    {
        $query = $this->db->query('SELECT DISTINCT * FROM '.DB_PREFIX.'seller_group cg LEFT JOIN '.DB_PREFIX."seller_group_description cgd ON (cg.seller_group_id = cgd.seller_group_id) WHERE cg.seller_group_id = '".(int) $seller_group_id."' AND cgd.language_id = '".(int) $this->config->get('config_language_id')."'");

        return $query->rows;
    }

    public function getbankaccount($bankaccount_id)
    {
        $bankaccount_query = $this->db->query('SELECT * FROM '.DB_PREFIX."bankaccount WHERE bankaccount_id = '".(int) $bankaccount_id."'");

        if ($bankaccount_query->num_rows) {
            return array(
                'bankaccount_id' => $bankaccount_query->row['bankaccount_id'],
                'seller_id' => $bankaccount_query->row['customer_id'],
                'firstname' => $bankaccount_query->row['firstname'],
                'lastname' => $bankaccount_query->row['lastname'],
                'company_id' => $bankaccount_query->row['company_id'],
                'branch_id' => $bankaccount_query->row['branch_id'],
                'bankaccount_1' => $bankaccount_query->row['bankaccount_1'],
                'bank_id' => $bankaccount_query->row['bank_id'],
                'bankaccount_2' => $bankaccount_query->row['bankaccount_2'],

            );
        }
    }

    public function getbankaccounts($seller_id)
    {
        $bankaccount_data = array();

        $query = $this->db->query('SELECT bankaccount_id FROM '.DB_PREFIX."bankaccount WHERE customer_id = '".(int) $seller_id."'");

        foreach ($query->rows as $result) {
            $bankaccount_info = $this->getbankaccount($result['bankaccount_id']);

            if ($bankaccount_info) {
                $bankaccount_data[$result['bankaccount_id']] = $bankaccount_info;
            }
        }

        return $bankaccount_data;
    }

    public function addHistory($seller_id, $comment)
    {
        $this->db->query('INSERT INTO '.DB_PREFIX."customer_history SET customer_id = '".(int) $seller_id."', comment = '".$this->db->escape(strip_tags($comment))."', date_added = NOW()");
    }

    public function getHistories($seller_id, $start = 0, $limit = 10)
    {
        if ($start < 0) {
            $start = 0;
        }

        if ($limit < 1) {
            $limit = 10;
        }

        $query = $this->db->query('SELECT comment, date_added FROM '.DB_PREFIX."customer_history WHERE customer_id = '".(int) $seller_id."' ORDER BY date_added DESC LIMIT ".(int) $start.','.(int) $limit);

        return $query->rows;
    }

    public function getTotalHistories($seller_id)
    {
        $query = $this->db->query('SELECT COUNT(*) AS total FROM '.DB_PREFIX."customer_history WHERE customer_id = '".(int) $seller_id."'");

        return $query->row['total'];
    }

    public function getTransactions($seller_id, $start = 0, $limit = 10)
    {
        if ($start < 0) {
            $start = 0;
        }

        if ($limit < 1) {
            $limit = 10;
        }

        $query = $this->db->query('SELECT * FROM '.DB_PREFIX."seller_transaction WHERE customer_id = '".(int) $seller_id."' ORDER BY date_added DESC LIMIT ".(int) $start.','.(int) $limit);

        return $query->rows;
    }

    public function getTotalTransactions($seller_id)
    {
        $query = $this->db->query('SELECT COUNT(*) AS total  FROM '.DB_PREFIX."seller_transaction WHERE customer_id = '".(int) $seller_id."'");

        return $query->row['total'];
    }

    public function getTransactionTotal($seller_id)
    {
        $query = $this->db->query('SELECT SUM(amount) AS total FROM '.DB_PREFIX."seller_transaction WHERE customer_id = '".(int) $seller_id."'");

        return $query->row['total'];
    }

    public function getTotalTransactionsByOrderId($order_id)
    {
        $query = $this->db->query('SELECT COUNT(*) AS total FROM '.DB_PREFIX."seller_transaction WHERE order_id = '".(int) $order_id."'");

        return $query->row['total'];
    }

    public function addrequest_membership($seller_id, $data)
    {
        $seller_info = $this->getseller($this->customer->getId());

        $this->load->language('sellerprofile/sellerprofile');

        $this->load->model('setting/store');
        if (isset($store_info)) {
            $store_info = $this->model_setting_store->getStores($seller_info['store_id']);
            $store_name = $store_info['name'];
            $store_url = $store_info['url'].'admin/index.php?route=common/login';
        } else {
            $store_name = $this->config->get('config_name');
            $store_url = HTTP_SERVER.'admin/index.php?route=common/login';
        }

        $customer_url = HTTP_SERVER.'admin/index.php?route=seller/seller';
        $message = $store_url."\n\n";

        $isseller = $this->customer->isSeller();
        if ($isseller != '0') {
            if ($seller_info['seller_approved'] == '0') {
                $this->db->query('
			UPDATE '.DB_PREFIX."customer
			SET seller_group_id = '".(int) $data['seller_group_id']."'	WHERE customer_id = '".(int) $this->customer->getId()."'
			");

                $this->db->query('DELETE FROM '.DB_PREFIX."category_to_seller WHERE seller_id = '".$this->customer->getId()."'");
                foreach ($data['seller_category'] as $category_id) {
                    $this->db->query('INSERT INTO '.DB_PREFIX."category_to_seller SET seller_id = '".$this->customer->getId()."', category_id = '".(int) $category_id."'");
                }

            // Sent to admin email
            $message = $this->language->get('text_firstname').' '.$seller_info['firstname']."\n";
                $message .= $this->language->get('text_lastname').' '.$seller_info['lastname']."\n";
                $message .= $this->language->get('text_seller_group').' '.$seller_info['name']."\n";
                $message .= $this->language->get('text_email').' '.$seller_info['email']."\n";
                $message .= $this->language->get('text_telephone').' '.$seller_info['telephone']."\n";

                $mail = new Mail();
                $mail->protocol = $this->config->get('config_mail_protocol');
                $mail->parameter = $this->config->get('config_mail_parameter');
                $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
                $mail->smtp_username = $this->config->get('config_mail_smtp_username');
                $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
                $mail->smtp_port = $this->config->get('config_mail_smtp_port');
                $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
                $mail->setTo($this->config->get('config_email'));
                $mail->setFrom($this->customer->getEmail());
                $mail->setSender($this->customer->getFirstName().' '.$this->customer->getLastName());
                $mail->setSubject(html_entity_decode(sprintf($this->language->get('text_add_request_subject'), ''), ENT_QUOTES, 'UTF-8'));
                $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
                $mail->send();
            } else {
                $this->db->query('
			UPDATE '.DB_PREFIX."customer
			SET seller_changegroup = '".(int) $data['seller_group_id']."'	WHERE customer_id = '".(int) $this->customer->getId()."'
			");

                $this->db->query('DELETE FROM '.DB_PREFIX."category_to_seller WHERE seller_id = '".$this->customer->getId()."'");
                  if (isset($data['seller_category'])) {
                foreach ($data['seller_category'] as $category_id) {
                    $this->db->query('INSERT INTO '.DB_PREFIX."category_to_seller SET seller_id = '".$this->customer->getId()."', category_id = '".(int) $category_id."'");
                }
              }
            // Sent to admin email
            $message = $this->language->get('text_firstname').' '.$seller_info['firstname']."\n";
                $message .= $this->language->get('text_lastname').' '.$seller_info['lastname']."\n";
                $message .= $this->language->get('text_seller_group').' '.$this->request->post['seller_group_name']."\n";
                $message .= $this->language->get('text_email').' '.$seller_info['email']."\n";
                $message .= $this->language->get('text_telephone').' '.$seller_info['telephone']."\n";

                $mail = new Mail();
                $mail->protocol = $this->config->get('config_mail_protocol');
                $mail->parameter = $this->config->get('config_mail_parameter');
                $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
                $mail->smtp_username = $this->config->get('config_mail_smtp_username');
                $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
                $mail->smtp_port = $this->config->get('config_mail_smtp_port');
                $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
                $mail->setTo($this->config->get('config_email'));
                $mail->setFrom($this->customer->getEmail());
                $mail->setSender($this->customer->getFirstName().' '.$this->customer->getLastName());
                $mail->setSubject(html_entity_decode(sprintf($this->language->get('text_upgrade_request_subject'), ''), ENT_QUOTES, 'UTF-8'));
                $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
                $mail->send();
            }
        } else {
            $this->db->query('
			UPDATE '.DB_PREFIX."customer
			SET seller_group_id = '".(int) $data['seller_group_id']."'	WHERE customer_id = '".(int) $this->customer->getId()."'
			");

            $this->db->query('DELETE FROM '.DB_PREFIX."category_to_seller WHERE seller_id = '".$this->customer->getId()."'");

            if (isset($data['seller_category'])) {

            foreach ($data['seller_category'] as $category_id) {
                $this->db->query('INSERT INTO '.DB_PREFIX."category_to_seller SET seller_id = '".$this->customer->getId()."', category_id = '".(int) $category_id."'");
            }
}
            // Sent to admin email
            $message = $this->language->get('text_firstname').' '.$this->customer->getFirstName()."\n";
            $message .= $this->language->get('text_lastname').' '.$this->customer->getLastName()."\n";
            $message .= $this->language->get('text_seller_group').' '.$this->request->post['seller_group_name']."\n";
            $message .= $this->language->get('text_email').' '.$this->customer->getEmail()."\n";
            $message .= $this->language->get('text_telephone').' '.$this->customer->getTelephone()."\n";

            $mail = new Mail();
            $mail->protocol = $this->config->get('config_mail_protocol');
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
            $mail->smtp_username = $this->config->get('config_mail_smtp_username');
            $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
            $mail->smtp_port = $this->config->get('config_mail_smtp_port');
            $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
            $mail->setTo($this->config->get('config_email'));
            $mail->setFrom($this->customer->getEmail());
            $mail->setSender($this->customer->getFirstName().' '.$this->customer->getLastName());
            $mail->setSubject(html_entity_decode(sprintf($this->language->get('text_add_request_subject'), ''), ENT_QUOTES, 'UTF-8'));
            $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
            $mail->send();
        }

            // Sent to custuer email

            $message = sprintf($this->language->get('text_request_message'), $this->request->post['seller_group_name'])."\n\n";

        $mail = new Mail();
        $mail->protocol = $this->config->get('config_mail_protocol');
        $mail->parameter = $this->config->get('config_mail_parameter');
        $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
        $mail->smtp_username = $this->config->get('config_mail_smtp_username');
        $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
        $mail->smtp_port = $this->config->get('config_mail_smtp_port');
        $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
        $mail->setTo($this->customer->getEmail());
        $mail->setFrom($this->config->get('config_email'));
        $mail->setSender($this->config->get('config_name'));
        $mail->setSubject(html_entity_decode(sprintf($this->language->get('text_request_message'), $this->request->post['seller_group_name']), ENT_QUOTES, 'UTF-8'));
        $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
        $mail->send();
    }

    public function deleterequest_membership($order_id)
    {
        $this->db->query('DELETE FROM '.DB_PREFIX."customer_request_membership WHERE order_id = '".(int) $order_id."' AND points > 0");
    }

    public function getsellerreviewbysellerID($seller_id)
    {
        $query = $this->db->query('SELECT AVG(rating) AS total
		FROM '.DB_PREFIX."sellerreview r1
		WHERE r1.seller_id = '".(int) $seller_id."'
		AND r1.status = '1'
		GROUP BY r1.seller_id
		");

        return $query->row;
    }

    public function getsellerbadges()
    {
        $query = $this->db->query('SELECT * FROM '.DB_PREFIX.'badge_description  bd
		 LEFT JOIN '.DB_PREFIX."badge_to_seller bts ON (bts.badge_id = bd.badge_id) WHERE bd.language_id = '".(int) $this->config->get('config_language_id')."'
		 AND bts.seller_id =  '".(int) $this->customer->getid()."'

		");

        return $query->rows;
    }

    public function getsellerbadgesbysellerID($seller_id)
    {
        $query = $this->db->query('SELECT * FROM '.DB_PREFIX.'badge_description  bd
		 LEFT JOIN '.DB_PREFIX."badge_to_seller bts ON (bts.badge_id = bd.badge_id) WHERE bd.language_id = '".(int) $this->config->get('config_language_id')."'
		 AND bts.seller_id =  '".(int) $seller_id."'

		");

        return $query->rows;
    }


    public function getbadgeseller($seller_id)
    {
        $seller_data = array();

        $query = $this->db->query('SELECT * FROM '.DB_PREFIX."badge_to_seller WHERE seller_id = '".(int) $seller_id."'");

        foreach ($query->rows as $result) {
            $seller_data[] = $result['badge_id'];
        }

        return $seller_data;
    }

    public function getProducts($start = 0, $limit = 10)
    {
        if ($start < 0) {
            $start = 0;
        }

        if ($limit < 1) {
            $limit = 10;
        }
        $sql = 'SELECT * FROM '.DB_PREFIX.'product p
		LEFT JOIN '.DB_PREFIX.'product_description pd ON (p.product_id = pd.product_id)
		LEFT JOIN '.DB_PREFIX."product_to_seller pts ON (p.product_id = pts.product_id)
		WHERE pd.language_id = '".(int) $this->config->get('config_language_id')."'
		AND pts.seller_id = '".(int) $this->customer->getid()."'

		";

        $sql .= ' GROUP BY p.product_id';

        $sql .= ' LIMIT '.(int) $start.','.(int) $limit;
        $query = $this->db->query($sql);

        return $query->rows;
    }

	public function getAdvertises($start = 0, $limit = 10)
    {
        if ($start < 0) {
            $start = 0;
        }

        if ($limit < 1) {
            $limit = 10;
        }
        $sql = "SELECT * FROM ".DB_PREFIX."store_offers WHERE seller_id = '".(int) $this->customer->getid()."'";

        $sql .= ' GROUP BY advertise_id';

        $sql .= ' LIMIT '.(int) $start.','.(int) $limit;
        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getTotalProductsbysellerID($seller_id)
    {
        $sql = 'SELECT COUNT(DISTINCT p.product_id) AS total FROM '.DB_PREFIX.'product p
    LEFT JOIN '.DB_PREFIX.'product_description pd ON (p.product_id = pd.product_id)
    LEFT JOIN '.DB_PREFIX.'product_to_seller pts ON (p.product_id = pts.product_id)
    ';

        $sql .= " WHERE pts.seller_id = '".(int) $seller_id."'";

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getTotalProducts($data = array())
    {
        $sql = 'SELECT COUNT(DISTINCT p.product_id) AS total FROM '.DB_PREFIX.'product p
		LEFT JOIN '.DB_PREFIX.'product_description pd ON (p.product_id = pd.product_id)
		LEFT JOIN '.DB_PREFIX.'product_to_seller pts ON (p.product_id = pts.product_id)
		';

        $sql .= " WHERE pd.language_id = '".(int) $this->config->get('config_language_id')."'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.name LIKE '%".$this->db->escape($data['filter_name'])."%'";
        }

        if (!empty($data['filter_model'])) {
            $sql .= " AND p.model LIKE '%".$this->db->escape($data['filter_model'])."%'";
        }

        if (!empty($data['filter_price'])) {
            $sql .= " AND p.price LIKE '%".$this->db->escape($data['filter_price'])."%'";
        }

        if (isset($data['filter_quantity']) && $data['filter_quantity'] !== null) {
            $sql .= " AND p.quantity = '".(int) $data['filter_quantity']."'";
        }

        if (isset($data['filter_status']) && $data['filter_status'] !== null) {
            $sql .= " AND p.status = '".(int) $data['filter_status']."'";
        }

        $sql .= " AND pts.seller_id = '".(int) $this->customer->getid()."'";

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getsellerproducts($seller_id)
    {
        $seller_data = array();

        $query = $this->db->query('SELECT * FROM '.DB_PREFIX."product_to_seller WHERE seller_id = '".(int) $seller_id."'");

        foreach ($query->rows as $result) {
            $seller_data[] = $result['product_id'];
        }

        return $seller_data;
    }

	public function getselleradvertise($seller_id)
    {
        $seller_data = array();

        $query = $this->db->query('SELECT * FROM '.DB_PREFIX."store_offers WHERE seller_id = '".(int) $seller_id."'");

        foreach ($query->rows as $result) {
            $seller_data[] = $result['advertise_id'];
        }

        return $seller_data;
    }

    public function getSellerGroups($data = array())
    {
        $sql = 'SELECT * FROM '.DB_PREFIX.'seller_group cg LEFT JOIN '.DB_PREFIX."seller_group_description cgd ON (cg.seller_group_id = cgd.seller_group_id) WHERE cgd.language_id = '".(int) $this->config->get('config_language_id')."' AND cg.status='1'";

        $sort_data = array(
            'cgd.name',
            'cg.sort_order',
            'cg.product_limit',
            'cg.subscription_price',
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= ' ORDER BY '.$data['sort'];
        } else {
            $sql .= ' ORDER BY cgd.name';
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= ' DESC';
        } else {
            $sql .= ' ASC';
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= ' LIMIT '.(int) $data['start'].','.(int) $data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getSellerGroupCommission($seller_group_id)
    {
        $sql = 'SELECT * FROM '.DB_PREFIX.'commission_rate_to_seller_group wc
			LEFT JOIN '.DB_PREFIX."commission_rate wcd ON (wc.commission_rate_id = wcd.commission_rate_id)
			WHERE wc.seller_group_id = '".(int) $seller_group_id."'";

        $query = $this->db->query($sql);
        if (isset($query->row['name'])) {
            return $query->row['name'];
        } else {
            return 0;
        }
    }

    public function getSellerGroupCommissionRate($seller_group_id)
    {
        $sql = 'SELECT * FROM '.DB_PREFIX.'commission_rate_to_seller_group wc
			LEFT JOIN '.DB_PREFIX."commission_rate wcd ON (wc.commission_rate_id = wcd.commission_rate_id)
			WHERE wc.seller_group_id = '".(int) $seller_group_id."'";

        $query = $this->db->query($sql);

        if (isset($query->row['rate'])) {
            return $query->row['rate'];
        } else {
            return 0;
        }
    }

    public function getbankes($data = array())
    {
        if ($data) {
            $sql = 'SELECT * FROM '.DB_PREFIX.'bank wc LEFT JOIN '.DB_PREFIX."bank_description wcd ON (wc.bank_id = wcd.bank_id) WHERE wcd.language_id = '".(int) $this->config->get('config_language_id')."'";

            $sort_data = array(
                'title',

            );

            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                $sql .= ' ORDER BY '.$data['sort'];
            } else {
                $sql .= ' ORDER BY title';
            }

            if (isset($data['order']) && ($data['order'] == 'DESC')) {
                $sql .= ' DESC';
            } else {
                $sql .= ' ASC';
            }

            if (isset($data['start']) || isset($data['limit'])) {
                if ($data['start'] < 0) {
                    $data['start'] = 0;
                }

                if ($data['limit'] < 1) {
                    $data['limit'] = 20;
                }

                $sql .= ' LIMIT '.(int) $data['start'].','.(int) $data['limit'];
            }

            $query = $this->db->query($sql);

            return $query->rows;
        } else {
            $bank_data = $this->cache->get('bank.'.(int) $this->config->get('config_language_id'));

            if (!$bank_data) {
                $query = $this->db->query('SELECT * FROM '.DB_PREFIX.'bank wc LEFT JOIN '.DB_PREFIX."bank_description wcd ON (wc.bank_id = wcd.bank_id) WHERE wcd.language_id = '".(int) $this->config->get('config_language_id')."'");

                $bank_data = $query->rows;

                $this->cache->set('bank.'.(int) $this->config->get('config_language_id'), $bank_data);
            }

            return $bank_data;
        }
    }

    public function getSellerrequest()
    {
        $seller_query = $this->db->query('
			SELECT * FROM '.DB_PREFIX."customer
			WHERE customer_id = '".(int) $this->customer->getId()."'
			");

        return $seller_query->row;
    }

    public function getProductsellers($product_id)
    {
        $query = $this->db->query('
		SELECT * FROM '.DB_PREFIX.'product_to_seller pts
		LEFT JOIN '.DB_PREFIX.'customer c ON (c.customer_id = pts.seller_id)
		LEFT JOIN '.DB_PREFIX.'seller_group_description  sgd ON (c.seller_group_id = sgd.seller_group_id)
		LEFT JOIN '.DB_PREFIX."seller_group sg ON (c.seller_group_id = sg.seller_group_id)
		WHERE product_id = '".(int) $product_id."'
		AND sgd.language_id = '".(int) $this->config->get('config_language_id')."'
		");

        return $query->row;
    }

    public function CancelRequest()
    {
  
        $this->db->query('
			UPDATE '.DB_PREFIX."customer
			SET 	seller_changegroup = '0'	WHERE customer_id = '".(int) $this->customer->getId()."'
			");

        return true;
    }


    public function Sellerdeleteimage()
    {
        $this->db->query('UPDATE '.DB_PREFIX."customer SET image = '' WHERE customer_id='".(int) $this->customer->getId()."'");
    }

    public function Sellerdeletebanner()
    {
        $this->db->query('UPDATE '.DB_PREFIX."customer SET banner = '' WHERE customer_id='".(int) $this->customer->getId()."'");
    }


    public function SellerProfileSave($data)
    {
        $columns = '';
        $seller_details = $this->getSellerrequest();

        if($seller_details['nickname'] != $data['nickname'] || $seller_details['banner'] != $data['banner']) {
            $columns .= ', seller_approved = 0, seller_verified = 0';
        }

        $mob = (!empty($data['store_mobile_num']) && is_array($data['store_mobile_num'])) ? implode(",", array_filter($data['store_mobile_num'])) : '';

        if (!empty($data['store_ll_code']) && !empty($data['store_ll_num'])) {
            for($i=0; $i<count($data['store_ll_code']); $i++) {
                $land_line_num[] = $data['store_ll_code'][$i].'-'.$data['store_ll_num'][$i];
            }

            $land = ltrim(implode(",", array_filter($land_line_num)), '-,');

            $columns .= ", store_ll_num = '".$this->db->escape($land)."'";
        }
	
	if ($data['referred_by'] != "") {
	    $ref_by = $this->db->escape($data['referred_by']);
	}
	else {
	    $ref_by = "-";
	}

        $this->db->query('UPDATE '.DB_PREFIX."customer 
            SET nickname = '".$this->db->escape($data['nickname'])."', banner = '".$this->db->escape($data['banner'])."',
            image = '".$this->db->escape($data['image'])."', description = '".$this->db->escape($data['seller_description'])."',
            tin = '".$this->db->escape($data['tin'])."', pan = '".$this->db->escape($data['pan'])."', 
            lat = '".$this->db->escape($data['lat'])."', lng = '".$this->db->escape($data['lng'])."',
            owner_name = '".$this->db->escape($data['owner_name'])."', store_email = '".$this->db->escape($data['store_email'])."', referred_by = '".$ref_by."',
            store_mobile_num = '".$this->db->escape($mob)."', delivery_type = '".$this->db->escape($data['delivery_type'])."',
            active = '".(int) $this->db->escape($data['store_activate'])."' ".$columns." 
            WHERE customer_id='".(int) $this->customer->getId()."'");
    }

    public function saveStoreAddress ($data, $customer_id) {
        $this->db->query('UPDATE '.DB_PREFIX."customer 
            SET address_1 = '".$this->db->escape($data['address_1'])."', address_2 = '".$this->db->escape($data['address_2'])."', 
            city = '".$this->db->escape($data['city'])."', postcode = '".$this->db->escape($data['postcode'])."',
            country_id = '".$this->db->escape($data['country_id'])."', zone_id = '".$this->db->escape($data['zone_id'])."'
            WHERE customer_id='".(int) $customer_id."'");
    }

    public function saveStorePortals ($data, $customer_id) {
        $this->db->query('UPDATE '.DB_PREFIX."customer 
            SET instagram = '".$this->db->escape($data['instagram'])."', googleplus = '".$this->db->escape($data['googleplus'])."', 
            twitter = '".$this->db->escape($data['twitter'])."', facebook = '".$this->db->escape($data['facebook'])."',
            website = '".$this->db->escape($data['website'])."' WHERE customer_id='".(int) $customer_id."'");
    }

    //website = '".$this->db->escape($data['website'])."', facebook = '".$this->db->escape($data['facebook'])."', twitter = '".$this->db->escape($data['twitter'])."', 
	//googleplus = '".$this->db->escape($data['googleplus'])."', instagram = '".$this->db->escape($data['instagram'])."',

    // public function SellerProfileSave($data)
    // {
    //     $seller_details = $this->getSellerrequest();

    //     if (isset($data['nickname'])) {
    //         if($seller_details['nickname'] != $data['nickname']){       
    //             $this->db->query('UPDATE '.DB_PREFIX."customer SET nickname = '".$this->db->escape($data['nickname'])."', seller_approved = '0', seller_verified = '0' WHERE customer_id='".(int) $this->customer->getId()."'");
    //         }
    //     }

    //     if (isset($data['banner'])) {
    //         if($seller_details['banner'] != $data['banner']){       
    //             $this->db->query('UPDATE '.DB_PREFIX."customer SET banner = '".$this->db->escape($data['banner'])."', seller_approved = '0', seller_verified = '0' WHERE customer_id='".(int) $this->customer->getId()."'");
    //         }
    //     }

    //     if (isset($data['image'])) {
    //         $this->db->query('UPDATE '.DB_PREFIX."customer SET image = '".$this->db->escape($data['image'])."' WHERE customer_id='".(int) $this->customer->getId()."'");
    //     }

    //     if (isset($data['seller_description'])) {
    //         $this->db->query('UPDATE '.DB_PREFIX."customer SET description = '".$this->db->escape($data['seller_description'])."' WHERE customer_id='".(int) $this->customer->getId()."'");
    //     }

    //     if (isset($data['seller_address'])) {
    //         $this->db->query('UPDATE '.DB_PREFIX."customer SET address = '".$this->db->escape($data['seller_address'])."' WHERE customer_id='".(int) $this->customer->getId()."'");
    //     }

    //     if (isset($data['website'])) {
    //         $this->db->query('UPDATE '.DB_PREFIX."customer SET website = '".$this->db->escape($data['website'])."' WHERE customer_id='".(int) $this->customer->getId()."'");
    //     }

    //     if (isset($data['facebook'])) {
    //         $this->db->query('UPDATE '.DB_PREFIX."customer SET facebook = '".$this->db->escape($data['facebook'])."' WHERE customer_id='".(int) $this->customer->getId()."'");
    //     }

    //     if (isset($data['twitter'])) {
    //         $this->db->query('UPDATE '.DB_PREFIX."customer SET twitter = '".$this->db->escape($data['twitter'])."' WHERE customer_id='".(int) $this->customer->getId()."'");
    //     }

    //     if (isset($data['googleplus'])) {
    //         $this->db->query('UPDATE '.DB_PREFIX."customer SET googleplus = '".$this->db->escape($data['googleplus'])."' WHERE customer_id='".(int) $this->customer->getId()."'");
    //     }

    //     if (isset($data['instagram'])) {
    //         $this->db->query('UPDATE '.DB_PREFIX."customer SET instagram = '".$this->db->escape($data['instagram'])."' WHERE customer_id='".(int) $this->customer->getId()."'");
    //     }

    //     if (isset($data['tin'])) {
    //         $this->db->query('UPDATE '.DB_PREFIX."customer SET tin = '".$this->db->escape($data['tin'])."' WHERE customer_id='".(int) $this->customer->getId()."'");
    //     }

    //     if (isset($data['pan'])) {
    //         $this->db->query('UPDATE '.DB_PREFIX."customer SET pan = '".$this->db->escape($data['pan'])."' WHERE customer_id='".(int) $this->customer->getId()."'");
    //     }

    //     if (isset($data['lat'])) {
    //         $this->db->query('UPDATE '.DB_PREFIX."customer SET lat = '".$this->db->escape($data['lat'])."' WHERE customer_id='".(int) $this->customer->getId()."'");
    //     }

    //     if (isset($data['lng'])) {
    //         $this->db->query('UPDATE '.DB_PREFIX."customer SET lng = '".$this->db->escape($data['lng'])."' WHERE customer_id='".(int) $this->customer->getId()."'");
    //     }

    //     if (isset($data['owner_name'])) {
    //         $this->db->query('UPDATE '.DB_PREFIX."customer SET owner_name = '".$this->db->escape($data['owner_name'])."' WHERE customer_id='".(int) $this->customer->getId()."'");
    //     }

    //     if (isset($data['owner_ll_num'])) {
    //         $this->db->query('UPDATE '.DB_PREFIX."customer SET owner_ll_num = '".$this->db->escape($data['owner_ll_num'])."' WHERE customer_id='".(int) $this->customer->getId()."'");
    //     }

    //     if (isset($data['store_email'])) {
    //         $this->db->query('UPDATE '.DB_PREFIX."customer SET store_email = '".$this->db->escape($data['store_email'])."' WHERE customer_id='".(int) $this->customer->getId()."'");
    //     }

    //     if($seller_details['referred_by'] == '') {
    //         if (isset($data['referred_by'])) {
    //             $this->db->query('UPDATE '.DB_PREFIX."customer SET referred_by = '".$this->db->escape($data['referred_by'])."' WHERE customer_id='".(int) $this->customer->getId()."'");
    //         } else {
    //             $this->db->query('UPDATE '.DB_PREFIX."customer SET referred_by = '---' WHERE customer_id='".(int) $this->customer->getId()."'");
    //         }
    //     }

    //     if (!empty($data['store_mobile_num'])) {
    //         $mob = implode(",", array_filter($data['store_mobile_num']));//print_r($mob); die;
    //         $this->db->query('UPDATE '.DB_PREFIX."customer SET store_mobile_num = '".$this->db->escape($mob)."' WHERE customer_id='".(int) $this->customer->getId()."'");
    //     }

    //     if ((!empty($data['store_ll_code'])) && (!empty($data['store_ll_num']))) {
    //         for($i=0; $i<count($data['store_ll_code']); $i++){
    //             $land_line_num[] = $data['store_ll_code'][$i].'-'.$data['store_ll_num'][$i];
    //         }
    //         $land = ltrim(implode(",", array_filter($land_line_num)), '-,');
    //         $this->db->query('UPDATE '.DB_PREFIX."customer SET store_ll_num = '".$this->db->escape($land)."' WHERE customer_id='".(int) $this->customer->getId()."'");
    //     }
    //     if (isset($data['delivery_type'])) {
    //         $this->db->query('UPDATE '.DB_PREFIX."customer SET delivery_type = '".$this->db->escape($data['delivery_type'])."' WHERE customer_id='".(int) $this->customer->getId()."'");
    //     }
    // }

	public function getStoreImages($seller_id) {

		$sql ="SELECT * FROM " . DB_PREFIX . "store_image WHERE seller_id = '" . (int)$seller_id . "' ORDER BY sort_order ASC";

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function SellerStoreImageSave($seller_id,$data) {//print_r($data); die;

		$this->db->query('DELETE FROM '.DB_PREFIX."store_image WHERE seller_id = '".(int) $seller_id."'");
		$data['store_image'] = (isset($data['store_image']) && $data['store_image'] !='') ? $data['store_image'] : '';
		if($data['store_image']) {
			foreach ($data['store_image'] as $store_images) {
				$this->db->query('INSERT INTO '.DB_PREFIX."store_image SET seller_id = '".(int) $seller_id."', image = '".$this->db->escape($store_images['image'])."', sort_order = '".(int) $store_images['sort_order']."'");
			}
		}

		//return $query->rows;
	}

	public function getStoreCategoryAprrove() {

		$this->db->query('UPDATE '.DB_PREFIX."customer SET seller_approved = '0' WHERE customer_id='".(int) $this->customer->getId()."'");

	}

	public function featureStore($amount, $from_date, $end_date) {

		$this->db->query('UPDATE '.DB_PREFIX."customer SET feature_store_amount = '".$amount."', feature_store_start = '".$from_date."', feature_store_end = '".$end_date."' WHERE customer_id='".(int) $this->customer->getId()."'");

	}

	public function getfavourites_customer_count($seller_id)
    {
        $query = $this->db->query('SELECT COUNT(*) AS total FROM '.DB_PREFIX."customer_store_favourites WHERE store_id = '".(int) $seller_id."'");

        return $query->row['total'];
    }

	public function getAdvertise_position($advertise_id, $position)
    {
		$sql = "SELECT x.advertise_id, x.position AS position FROM (SELECT advertise_id, @rownum := @rownum + 1 AS position FROM oc_store_offers JOIN (SELECT @rownum := 0) r WHERE CURDATE() >= from_date AND CURDATE() <= end_date AND position = '".(int) $position."' and price <> 0 ORDER BY advertise_id DESC) x WHERE x.advertise_id = '".(int) $advertise_id."'";

		//print_r($sql);

		$query = $this->db->query($sql);

		return $query->row['position'];
	}

	public function getHomeBanner_days($advertise_id) {

		$query = $this->db->query('SELECT COUNT(*) AS days_left FROM '.DB_PREFIX."home_top_banner_date WHERE store_offer_advertise_id = '".(int) $advertise_id."' AND CURDATE() <= date");

        return $query->row['days_left'];
	}

	public function SellerStoretimings($data) {

	$this->db->query('DELETE FROM '.DB_PREFIX."store_timing WHERE uid='".(int) $this->customer->getId()."'");

	$this->db->query('INSERT INTO '.DB_PREFIX."store_timing SET uid = '".(int) $this->customer->getId()."'");

	$timing_id = $this->db->getLastId();
	//print_r($data); die;

		foreach($data as $dat) {

			$this->db->query('UPDATE '.DB_PREFIX."store_timing SET ".$dat['day']." = '".serialize($dat)."' WHERE uid='".(int) $this->customer->getId()."' AND timing_id='".$timing_id."'");
			
		}
	}

	public function getstore_timings($uid) {

		$query = $this->db->query('SELECT * FROM '.DB_PREFIX."store_timing WHERE uid = '".(int) $uid."'");

        return $query->row;
	}

	public function clear_visitor_counter() {

		$this->db->query('UPDATE '.DB_PREFIX."customer SET seller_counter = '0' WHERE customer_id='".(int) $this->customer->getId()."'");
	}

	public function getLatitudesLongitude($seller_id)
    {
        $seller_data = array();

        $query = $this->db->query('SELECT lat,lng FROM '.DB_PREFIX."customer WHERE customer_id = '".(int) $seller_id."'");

        foreach ($query->rows as $result) {
            $seller_data['lat'] = $result['lat'];
			$seller_data['lng'] = $result['lng'];
        }

        return $seller_data;
    }

	public function SellerStoreAllowProductsCart($data) {

		$this->db->query('UPDATE '.DB_PREFIX."customer SET ".$data['valt_title']." = '".$data['valt']."' WHERE customer_id='".(int) $this->customer->getId()."'");
	}

	public function StoreReferrer($data) {

		$this->db->query('INSERT INTO '.DB_PREFIX."store_referred SET seller_id = '".(int) $this->customer->getId()."', refer_name = '".$data['refer_name']."', refer_mobile = '".$data['refer_mobile']."', refer_email = '".$data['refer_email']."', bank_name = '".$data['bank_name']."', branch = '".$data['branch']."', account_holder_name = '".$data['account_holder_name']."', account_number = '".$data['account_number']."', ifsc = '".$data['ifsc']."', date_added = NOW()");

		//$this->db->query('UPDATE '.DB_PREFIX."customer SET referred_by = '".$data['refer_mobile']."-".$this->db->getLastId()."' WHERE customer_id='".(int) $this->customer->getId()."'");

		return $data['refer_mobile'];
	}
	
	public function StoreReferrerNum($reffer_id)
    {
        $seller_data = array();

        $query = $this->db->query('SELECT refer_mobile FROM '.DB_PREFIX."store_referred WHERE id = '".(int) $reffer_id."'");
		$res = $query->row;
		//print_r($res);die;
		return $res;		
	}

    public function GetStoreReferrerNum($mobile)
    {
        $seller_data = array();

        $query = $this->db->query('SELECT refer_mobile FROM '.DB_PREFIX."store_referred WHERE refer_mobile = '".$mobile."'");
		$res = $query->row;
		//print_r($res);die;
		return $res;		
	}
	
}
