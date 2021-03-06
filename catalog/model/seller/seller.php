<?php

class Modelsellerseller extends Model
{
    public function getseller($seller_id)
    {
        $query = $this->db->query("SELECT *,  CONCAT(wc.firstname, ' ', wc.lastname) AS title ,
		(SELECT AVG(rating) AS total
		FROM ".DB_PREFIX."sellerreview r1
		WHERE r1.seller_id = '".(int) $seller_id."'
		AND r1.status = '1'
		GROUP BY r1.seller_id) AS rating,  (SELECT COUNT(sellerreview_id) FROM ".DB_PREFIX."sellerreview r2 WHERE r2.seller_id = wc.customer_id AND r2.status = '1' GROUP BY r2.seller_id) AS review_count
		FROM ".DB_PREFIX.'customer wc
		LEFT JOIN '.DB_PREFIX."seller_group wcd
		ON (wc.seller_group_id = wcd.seller_group_id)
		WHERE wc.customer_id = '".(int) $seller_id."'
		");

        return $query->row;
    }

	public function getseller_rating($seller_id)
    {
        //$sql = "SELECT COUNT(sellerreview_id) FROM ".DB_PREFIX."sellerreview WHERE status = '1' AND seller_id = '".(int) $seller_id."' GROUP BY rating";

		$sql = "SELECT a.rating, COUNT(b.rating) totalCount FROM ( SELECT 1 rating UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 ) a LEFT JOIN oc_sellerreview b ON a.rating = b.rating WHERE status = '1' AND seller_id = '".(int) $seller_id."' GROUP BY a.rating ";

		//echo $sql; die;
		
		$query = $this->db->query($sql);

        return $query->rows;
    }

	public function getAdvertisesFrontStore($position, $seller_id, $limit='', $adv_count='')
    {
        // $sql = "SELECT advertise_id, offer_title, offer_image, nickname, offer_url";

		// //$sql .= " (SELECT AVG(rating) AS total FROM ".DB_PREFIX."sellerreview r1 WHERE r1.seller_id = st_of.seller_id AND r1.status = '1' GROUP BY r1.seller_id) AS rating FROM ".DB_PREFIX."store_offers st_of LEFT JOIN ".DB_PREFIX."customer cs ON (cs.customer_id = st_of.seller_id)";

		// $sql .= " FROM ".DB_PREFIX."store_offers st_of LEFT JOIN ".DB_PREFIX."customer cs ON (cs.customer_id = st_of.seller_id)";

		// $sql .= " WHERE CURDATE() >= st_of.from_date AND CURDATE() <= st_of.end_date  AND st_of.status = 'live' 
        //   AND st_of.seller_id = ".$seller_id;
        //  //AND st_of.position = ".$position.";

		// $sql .= " ORDER BY st_of.price DESC, st_of.from_date DESC, st_of.advertise_id DESC";

		// //$sql .= " st_of.price DESC";

        $sql = "SELECT st_of.advertise_id, st_of.offer_title, st_of.offer_image, cs.nickname, st_of.offer_url 
                FROM ".DB_PREFIX."store_offers st_of 
                LEFT JOIN ".DB_PREFIX."customer cs ON (cs.customer_id = st_of.seller_id) 
                LEFT JOIN ".DB_PREFIX."home_top_banner_date htbd ON (htbd.store_offer_advertise_id = st_of.advertise_id) 
                WHERE 
                    ((CURDATE() >= st_of.from_date AND CURDATE() <= st_of.end_date) OR htbd.date = CURDATE()) 
                    AND st_of.status = 'live' 
                    AND st_of.seller_id = ".$seller_id." 
                    ORDER BY htbd.date DESC, st_of.price DESC, st_of.from_date DESC, st_of.advertise_id DESC";

		//print_r($sql); die;
		if($adv_count !='') {
			$sql .= " limit ".$adv_count.", ".$limit;
		} else {
			$sql .= " limit 0,".$limit;
		}
//echo $sql;
        $query = $this->db->query($sql);

        return $query->rows;
    }


    public function getSellersOnCart()
    {
      $sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name,c.customer_id AS seller_id, cgd.name AS seller_group, c.seller_group_id AS seller_group_id
      FROM ".DB_PREFIX."cart ct
      LEFT JOIN ".DB_PREFIX."product_to_seller pts
      ON (ct.product_id = pts.product_id)
      LEFT JOIN ".DB_PREFIX."customer c
      ON (c.customer_id = pts.seller_id)
      LEFT JOIN ".DB_PREFIX."seller_group_description cgd
      ON (c.seller_group_id = cgd.seller_group_id)
      WHERE cgd.language_id = '".(int) $this->config->get('config_language_id')."'
      AND ct.customer_id = '".(int) $this->customer->getID()."'
      GROUP BY c.customer_id
      ";

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getsellers($data = array())
    {
        if ($data) {
            $sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name, cgd.name AS seller_group FROM ".DB_PREFIX.'customer c LEFT JOIN '.DB_PREFIX."seller_group_description cgd ON (c.seller_group_id = cgd.seller_group_id) WHERE cgd.language_id = '".(int) $this->config->get('config_language_id')."' ";

            $sort_data = array(
                'title',
                'sort_order',
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
        } else {
            $seller_data = $this->cache->get('seller.'.(int) $this->config->get('config_language_id'));

            if (!$seller_data) {
                $query = $this->db->query("
				SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name, cgd.name AS seller_group FROM ".DB_PREFIX.'customer c LEFT JOIN '.DB_PREFIX."seller_group_description cgd ON (c.seller_group_id = cgd.seller_group_id) WHERE cgd.language_id = '".(int) $this->config->get('config_language_id')."' ORDER BY firstname");

                $seller_data = $query->rows;

                $this->cache->set('seller.'.(int) $this->config->get('config_language_id'), $seller_data);
            }

            return $seller_data;
        }
    }

	public function getsellersList($category_id, $search_val, $by_search_val, $limit='', $adv_count='')
    {
        
            //$seller_data = $this->cache->get('seller.'.(int) $this->config->get('config_language_id'));
			if(isset($_COOKIE['myCookie'])){
				$cookie = $_COOKIE['myCookie'];
				$cookie_res = explode(',',$cookie);
				$latitude = $cookie_res[0];
				$longitude = $cookie_res[1];
			} else {
				$latitude = '13.067439';
				$longitude = '80.237617';
			}

			if(isset($_COOKIE['myCookiestart']) && isset($_COOKIE['myCookieend'])){
				$start_km = $_COOKIE['myCookiestart']/1.609344;
				$end_km = $_COOKIE['myCookieend']/1.609344;
			} else {
				$start_km = '0'/1.609344;
				$end_km = '10'/1.609344;
			}

            $sql = "SELECT *, 
            	(SELECT RAND()*(SELECT CASE WHEN feature_store_end > CURDATE() THEN '1' 
            		WHEN feature_store_end < CURDATE() THEN '0' ELSE 'not yet' END) as filtered) as filtered, 
				CONCAT(c.firstname, ' ', c.lastname) AS name, 
				( 3959 * acos( cos( radians(".$latitude.") ) * cos( radians( lat ) ) *  cos( radians( lng ) - radians(".$longitude.") ) + sin( radians(".$latitude.") ) * sin( radians( lat ) ) ) ) AS distance, 
				(SELECT AVG(rating) AS total FROM ".DB_PREFIX."sellerreview r1
				 WHERE r1.seller_id = c.customer_id AND r1.status = '1' GROUP BY r1.seller_id) AS rating,  
				(SELECT COUNT(sellerreview_id) FROM ".DB_PREFIX."sellerreview r2 
					WHERE r2.seller_id = c.customer_id AND r2.status = '1' GROUP BY r2.seller_id) AS review_count, 
				(SELECT COUNT(advertise_id) FROM ".DB_PREFIX."store_offers st_o WHERE st_o.seller_id = c.customer_id 
					AND st_o.status = 'live' AND (CURDATE() >= st_o.from_date AND CURDATE() <= st_o.end_date) GROUP BY st_o.seller_id) AS store_ads, 
				(SELECT COUNT(DISTINCT p.product_id) FROM ".DB_PREFIX."product p LEFT JOIN ".DB_PREFIX."product_to_seller p2l ON ( p2l.product_id = p.product_id)  WHERE p.status = '1' AND p.date_available <= NOW() AND  p2l.seller_id = c.customer_id) AS prod_count 
				FROM ".DB_PREFIX."customer c 
				LEFT JOIN ".DB_PREFIX."category_to_seller cs ON(c.customer_id = cs.seller_id)";

			if($category_id == '' && $search_val == '' && $by_search_val != '') {
				$sql .= " WHERE c.status = '1' AND c.seller_approved = 1 AND c.active = 1";
			} elseif($category_id != '' && $search_val == '' && $by_search_val != '') {
				$sql .= " WHERE cs.category_id = ".$category_id." AND cs.status = 1";
			} elseif($category_id != '' && $search_val != '' && $by_search_val == 1) {
				$sql .= " LEFT JOIN ".DB_PREFIX."category_description cd ON(cs.category_id = cd.category_id) LEFT JOIN ".DB_PREFIX."product_to_seller ps ON(cs.seller_id = ps.seller_id) LEFT JOIN ".DB_PREFIX."product_description pd ON(ps.product_id = pd.product_id) WHERE cs.category_id = ".$category_id." AND cs.status = 1 AND cd.name LIKE '%$search_val%' OR c.nickname LIKE '%$search_val%' OR pd.name LIKE '%$search_val%'";
			} elseif($category_id != '' && $search_val != '' && $by_search_val == 2) {
				$sql .= " LEFT JOIN ".DB_PREFIX."category_description cd ON(cs.category_id = cd.category_id) WHERE cs.category_id = ".$category_id." AND cs.status = 1 AND cd.name LIKE '%$search_val%'";
			} elseif($category_id != '' && $search_val != '' && $by_search_val == 3) {
				$sql .= " WHERE cs.category_id = ".$category_id." AND cs.status = 1 AND c.nickname LIKE '%$search_val%'";
			} elseif($category_id != '' && $search_val != '' && $by_search_val == 4) {
				$sql .= " LEFT JOIN ".DB_PREFIX."product_to_seller ps ON(cs.seller_id = ps.seller_id) LEFT JOIN ".DB_PREFIX."product_description pd ON(ps.product_id = pd.product_id) WHERE cs.category_id = ".$category_id." AND cs.status = 1 AND pd.name LIKE '%$search_val%'";

			} elseif($category_id == '' && $search_val != '' && $by_search_val == 1) {
				$sql .= " LEFT JOIN ".DB_PREFIX."category_description cd ON(cs.category_id = cd.category_id) LEFT JOIN ".DB_PREFIX."product_to_seller ps ON(cs.seller_id = ps.seller_id) LEFT JOIN ".DB_PREFIX."product_description pd ON(ps.product_id = pd.product_id) WHERE cd.name LIKE '%$search_val%' OR c.nickname LIKE '%$search_val%' OR pd.name LIKE '%$search_val%'";
			} elseif($category_id == '' && $search_val != '' && $by_search_val == 2) {
				$sql .= " LEFT JOIN ".DB_PREFIX."category_description cd ON(cs.category_id = cd.category_id) WHERE cd.name LIKE '%$search_val%'";
			} elseif($category_id == '' && $search_val != '' && $by_search_val == 3) {
				$sql .= " WHERE c.nickname LIKE '%$search_val%'";
			} elseif($category_id == '' && $search_val != '' && $by_search_val == 4) {
				$sql .= " LEFT JOIN ".DB_PREFIX."product_to_seller ps ON(cs.seller_id = ps.seller_id) LEFT JOIN ".DB_PREFIX."product_description pd ON(ps.product_id = pd.product_id) WHERE pd.name LIKE '%$search_val%'";
			}

			if($category_id == '' && $search_val == '' && $by_search_val != '') {
				$sql .= " GROUP BY c.customer_id HAVING distance BETWEEN ".$start_km." AND ".$end_km;
			} else {
				$sql .= " AND c.status = '1' AND c.seller_approved = 1 AND c.active = 1 GROUP BY c.customer_id HAVING distance BETWEEN ".$start_km." AND ".$end_km;
			}

			$sql .= " ORDER BY filtered DESC, distance ASC";

			if($adv_count !='') {
				$sql .= " limit ".$adv_count.", ".$limit;
			} else {
				$sql .= " limit 0,".$limit;
			}

			//echo $sql;
			$query = $this->db->query($sql);
            $seller_data = $query->rows;

            return $seller_data;
    }


	public function getHomeautoseach()
    {
        
            //$seller_data = $this->cache->get('seller.'.(int) $this->config->get('config_language_id'));
			if(isset($_COOKIE['myCookie'])){
				$cookie = $_COOKIE['myCookie'];
				$cookie_res = explode(',',$cookie);
				$latitude = $cookie_res[0];
				$longitude = $cookie_res[1];
			} else {
				$latitude = '13.067439';
				$longitude = '80.237617';
			}

			if(isset($_COOKIE['myCookiestart']) && isset($_COOKIE['myCookieend'])){
				$start_km = $_COOKIE['myCookiestart']/1.609344;
				$end_km = $_COOKIE['myCookieend']/1.609344;
			} else {
				$start_km = '0'/1.609344;
				$end_km = '3'/1.609344;
			}

            //if (!$seller_data) {
                $sql = "SELECT *,cd.name, c.nickname, pd.name, ( 3959 * acos( cos( radians(".$latitude.") ) * cos( radians( lat ) ) *  cos( radians( lng ) - radians(".$longitude.") ) + sin( radians(".$latitude.") ) * sin( radians( lat ) ) ) ) AS distance FROM ".DB_PREFIX."customer c LEFT JOIN ".DB_PREFIX."category_to_seller cs ON(c.customer_id = cs.seller_id) LEFT JOIN ".DB_PREFIX."category_description cd ON(cs.category_id = cd.category_id) LEFT JOIN ".DB_PREFIX."product_to_seller ps ON(cs.seller_id = ps.seller_id) LEFT JOIN ".DB_PREFIX."product_description pd ON(ps.product_id = pd.product_id)";

				
				$sql .= " WHERE c.status = '1' AND c.seller_approved = 1 HAVING distance BETWEEN ".$start_km." AND ".$end_km." ORDER BY distance ASC";

				//echo "<pre>"; print_r($sql); die;
				
				$query = $this->db->query($sql);
                $seller_data = $query->rows;
				//echo "<pre>"; print_r($seller_data);
                //$this->cache->set('seller.'.(int) $this->config->get('config_language_id'), $seller_data);
            //}			
            return $seller_data;
    }


    public function getProducts($data = array())
    {
        $sql = 'SELECT p.product_id, p.special_price, (SELECT AVG(rating) AS total FROM '.DB_PREFIX."review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT price FROM ".DB_PREFIX."product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '".(int) $this->config->get('config_customer_group_id')."' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM ".DB_PREFIX."product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '".(int) $this->config->get('config_customer_group_id')."' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special";

        if (!empty($data['filter_category_id'])) {
			//if (!empty($data['filter_sub_category'])) {
				//$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
			//} else {
				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
			//}

			//if (!empty($data['filter_filter'])) {
				//$sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
			//} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
			//}
		} else {
			$sql .= " FROM " . DB_PREFIX . "product p";
		}

        $sql .= ' LEFT JOIN '.DB_PREFIX.'product_description pd ON (p.product_id = pd.product_id) LEFT JOIN '.DB_PREFIX.'product_to_store p2s ON (p.product_id = p2s.product_id)  LEFT JOIN oc_product_to_category pc ON (pc.product_id = p.product_id) LEFT JOIN oc_category_to_seller cs ON (cs.category_id = pc.category_id) LEFT JOIN oc_product_to_category ptc ON (ptc.category_id = cs.category_id) LEFT JOIN '.DB_PREFIX."product_to_seller pta ON (p.product_id = pta.product_id) WHERE pd.language_id = '".(int) $this->config->get('config_language_id')."' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '".(int) $this->config->get('config_store_id')."'";

        if (!empty($data['filter_seller_id'])) {
            $sql .= " AND pta.seller_id = '".(int) $data['filter_seller_id']."'";
			$sql .= " AND cs.seller_id = '".(int) $data['filter_seller_id']."'";
        }
		//search box
		if (!empty($data['filter_prod_search'])) {
            $sql .= " AND pd.name LIKE '".$data['filter_prod_search']."'";
        }
		//search box end
		if (!empty($data['filter_category_id'])) {
			//if (!empty($data['filter_sub_category'])) {
				//$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			//} else {
				//$sql .= " LEFT JOIN " . DB_PREFIX . "product_to_category p2c";
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			//}
		}

        $sql .= ' GROUP BY p.product_id';
		//print_r($sql);
        $sort_data = array(
            'pd.name',
            'p.model',
            'p.quantity',
            'p.price',
            'rating',
            'p.sort_order',
            'p.date_added',
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
                $sql .= ' ORDER BY LCASE('.$data['sort'].')';
            } elseif ($data['sort'] == 'p.price') {
                $sql .= ' ORDER BY (CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE p.price END)';
            } else {
                $sql .= ' ORDER BY '.$data['sort'];
            }
        } else {
            $sql .= ' ORDER BY p.sort_order';
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= ' DESC, LCASE(pd.name) DESC';
        } else {
            $sql .= ' ASC, LCASE(pd.name) ASC';
        }

        if (isset($data['start']) && isset($data['limit'])) {            

            $sql .= ' LIMIT '.(int) $data['start'].','.(int) $data['limit'];
        }else {
			$sql .= " limit 0,".$data['limit'];
		}

	//print_r($sql); die;

        $product_data = array();
//echo $sql;
        $query = $this->db->query($sql);

        foreach ($query->rows as $result) {
            $product_data[$result['product_id']] = $this->getProduct($result['product_id']);
        }

        return $product_data;
    }

    public function getTotalProducts($data = array())
    {
        $sql = 'SELECT COUNT(DISTINCT p.product_id) AS total';

        $sql .= ' FROM '.DB_PREFIX.'product p';

        $sql .= ' LEFT JOIN '.DB_PREFIX.'product_description pd ON (p.product_id = pd.product_id) LEFT JOIN '.DB_PREFIX.'product_to_store p2s ON (p.product_id = p2s.product_id)  LEFT JOIN '.DB_PREFIX."product_to_seller pta ON (p.product_id = pta.product_id) WHERE pd.language_id = '".(int) $this->config->get('config_language_id')."' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '".(int) $this->config->get('config_store_id')."'";

        if (!empty($data['filter_seller_id'])) {
            $sql .= " AND pta.seller_id = '".(int) $data['filter_seller_id']."'";
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

	public function getTotalProductsSeller($data = array())
    {
        $sql = 'SELECT COUNT(DISTINCT p.product_id) AS total';

        $sql .= ' FROM '.DB_PREFIX.'product p';

        $sql .= ' LEFT JOIN '.DB_PREFIX.'product_description pd ON (p.product_id = pd.product_id) LEFT JOIN '.DB_PREFIX.'product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN oc_product_to_category pc ON (pc.product_id = p.product_id) LEFT JOIN oc_category_to_seller cs ON (cs.category_id = pc.category_id) LEFT JOIN oc_product_to_category ptc ON (ptc.category_id = cs.category_id) LEFT JOIN '.DB_PREFIX."product_to_seller pta ON (p.product_id = pta.product_id) WHERE pd.language_id = '".(int) $this->config->get('config_language_id')."' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '".(int) $this->config->get('config_store_id')."'";

        if (!empty($data['filter_seller_id'])) {
            $sql .= " AND pta.seller_id = '".(int) $data['filter_seller_id']."'";
			$sql .= " AND cs.seller_id = '".(int) $data['filter_seller_id']."'";
        }

		//print_r($sql); die;

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getProduct($product_id)
    {
        $query = $this->db->query('SELECT DISTINCT *, pd.name AS name, p.image, m.name AS manufacturer, (SELECT price FROM '.DB_PREFIX."product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '".(int) $this->config->get('config_customer_group_id')."' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM ".DB_PREFIX."product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '".(int) $this->config->get('config_customer_group_id')."' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special, (SELECT points FROM ".DB_PREFIX."product_reward pr WHERE pr.product_id = p.product_id AND customer_group_id = '".(int) $this->config->get('config_customer_group_id')."') AS reward, (SELECT ss.name FROM ".DB_PREFIX."stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '".(int) $this->config->get('config_language_id')."') AS stock_status, (SELECT wcd.unit FROM ".DB_PREFIX."weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '".(int) $this->config->get('config_language_id')."') AS weight_class, (SELECT lcd.unit FROM ".DB_PREFIX."length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '".(int) $this->config->get('config_language_id')."') AS length_class, (SELECT AVG(rating) AS total FROM ".DB_PREFIX."review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT COUNT(*) AS total FROM ".DB_PREFIX."review r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews, p.sort_order, p.special_price FROM ".DB_PREFIX.'product p LEFT JOIN '.DB_PREFIX.'product_description pd ON (p.product_id = pd.product_id) LEFT JOIN '.DB_PREFIX.'product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN '.DB_PREFIX."manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '".(int) $product_id."' AND pd.language_id = '".(int) $this->config->get('config_language_id')."' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '".(int) $this->config->get('config_store_id')."'");

        if ($query->num_rows) {
            return array(
                'product_id' => $query->row['product_id'],
                'name' => $query->row['name'],
                'description' => $query->row['description'],
                'meta_title' => $query->row['meta_title'],
                'meta_description' => $query->row['meta_description'],
                'meta_keyword' => $query->row['meta_keyword'],
                'tag' => $query->row['tag'],
                'model' => $query->row['model'],
                'sku' => $query->row['sku'],
                'upc' => $query->row['upc'],
                'ean' => $query->row['ean'],
                'jan' => $query->row['jan'],
                'isbn' => $query->row['isbn'],
                'mpn' => $query->row['mpn'],
                'location' => $query->row['location'],
                'quantity' => $query->row['quantity'],
                'stock_status' => $query->row['stock_status'],
                'image' => $query->row['image'],
                'manufacturer_id' => $query->row['manufacturer_id'],
                'manufacturer' => $query->row['manufacturer'],
                'price' => ($query->row['discount'] ? $query->row['discount'] : $query->row['price']),
                'special' => $query->row['special'],
				'special_price' => $query->row['special_price'],
                'reward' => $query->row['reward'],
                'points' => $query->row['points'],
                'tax_class_id' => $query->row['tax_class_id'],
                'date_available' => $query->row['date_available'],
                'weight' => $query->row['weight'],
                'weight_class_id' => $query->row['weight_class_id'],
                'length' => $query->row['length'],
                'width' => $query->row['width'],
                'height' => $query->row['height'],
                'length_class_id' => $query->row['length_class_id'],
                'subtract' => $query->row['subtract'],
                'rating' => round($query->row['rating']),
                'reviews' => $query->row['reviews'] ? $query->row['reviews'] : 0,
                'minimum' => $query->row['minimum'],
                'sort_order' => $query->row['sort_order'],
                'status' => $query->row['status'],
                'date_added' => $query->row['date_added'],
                'date_modified' => $query->row['date_modified'],
                'viewed' => $query->row['viewed'],
            );
        } else {
            return false;
        }
    }

    public function getProductsellers($product_id)
    {
        $product_seller_data = array();

        $query = $this->db->query('SELECT * FROM '.DB_PREFIX."product_to_seller WHERE product_id = '".(int) $product_id."'");

        foreach ($query->rows as $result) {
            $product_seller_data[] = $result['seller_id'];
        }

        return $product_seller_data;
    }

	public function getsellers_address($address_id)
    {
        $query = $this->db->query('SELECT * FROM '.DB_PREFIX."address WHERE address_id = '".(int) $address_id."'");

        return $query->rows;
    }


    public function getsellerreviewsBysellerId($seller_id, $start = 0, $limit = 20)
    {
        if ($start < 0) {
            $start = 0;
        }

        if ($limit < 1) {
            $limit = 20;
        }

        $query = $this->db->query('SELECT r.sellerreview_id, r.customer_name, r.phone, r.rating, r.text, p.customer_id, r.date_added FROM '.DB_PREFIX.'sellerreview r LEFT JOIN '.DB_PREFIX."customer p ON (r.seller_id = p.customer_id)  WHERE p.customer_id = '".(int) $seller_id."' AND r.status = '1'  ORDER BY r.date_added DESC LIMIT ".(int) $start.','.(int) $limit);

        return $query->rows;
    }

    public function getTotalsellerreviewsBysellerId($seller_id)
    {
        $query = $this->db->query('
		SELECT COUNT(*) AS total
		FROM '.DB_PREFIX.'sellerreview r
		LEFT JOIN '.DB_PREFIX."customer p ON (r.seller_id = p.customer_id)

		WHERE p.customer_id = '".(int) $seller_id."'


		AND r.status = '1'
		");

        return $query->row['total'];
    }

    public function addsellerReview($seller_id, $data)
    {
		$this->db->query("DELETE FROM ".DB_PREFIX."sellerreview WHERE seller_id = '".$seller_id."' AND customer_id = '".(int) $this->customer->getID()."'");

		$seller_info = $this->getseller($this->customer->getID());

        //$this->db->query('INSERT INTO '.DB_PREFIX."sellerreview SET customer_name = '".$this->db->escape($data['name'])."', seller_id = '".(int) $seller_id."', customer_id = '".(int) $this->customer->getID()."', text = '".$this->db->escape(strip_tags($data['text']))."', rating = '".(int) $data['rate_rating']."', date_added = NOW()");
		if($this->db->escape(strip_tags($data['text'])) != '') {
			$this->db->query('INSERT INTO '.DB_PREFIX."sellerreview SET customer_name = '".$seller_info['title']."', phone = '".$seller_info['telephone']."', seller_id = '".(int) $seller_id."', customer_id = '".(int) $this->customer->getID()."', text = '".$this->db->escape(strip_tags($data['text']))."', rating = '".(int) $data['rate_rating']."', date_added = NOW()");
		} else { 
			$this->db->query('INSERT INTO '.DB_PREFIX."sellerreview SET customer_name = '".$seller_info['title']."', phone = '".$seller_info['telephone']."', seller_id = '".(int) $seller_id."', customer_id = '".(int) $this->customer->getID()."', text = '".$this->db->escape(strip_tags($data['text']))."', rating = '".(int) $data['rate_rating']."', status = '1', date_added = NOW()");
		}

        $sellerreview_id = $this->db->getLastId();

        $this->cache->delete('seller');



        return $sellerreview_id;
    }

	public function addstore_feedback($seller_id, $data)
    {		

		//$this->db->query('INSERT INTO '.DB_PREFIX."store_feedback SET subject_key = '".(int) $data['sel_subject']."', store_id = '".(int) $seller_id."', customer_id = '".(int) $this->customer->getID()."', str_feedback = '".$this->db->escape(strip_tags($data['str_feedback']))."', status = '0', date_added = NOW()");

        return true;
    }

	public function getseller_review_single($seller_id, $customer_id)
    {
		$query = $this->db->query('SELECT rating, text FROM '.DB_PREFIX."sellerreview WHERE seller_id = '".(int) $seller_id."' AND customer_id = '".(int) $customer_id."'");

        return $query->row;
	}

    public function addHistory($seller_id, $comment)
    {
        $this->db->query('INSERT INTO '.DB_PREFIX."customer_history SET customer_id = '".(int) $seller_id."', comment = '".$this->db->escape(strip_tags($comment))."', date_added = NOW()");
    }

    public function addTransaction($seller_id, $description = '', $amount = '', $order_id)
    {
        $seller_info = $this->getseller($seller_id);

        if ($seller_info) {
            $this->db->query('INSERT INTO '.DB_PREFIX."seller_transaction SET customer_id = '".(int) $seller_id."', order_id = '".(int) $order_id."', description = '".$this->db->escape($description)."', amount = '".(float) $amount."', date_added = NOW()");

            $this->load->language('seller/mail_seller');

            $this->load->model('setting/store');

            $store_info = $this->model_setting_store->getStores();

            if ($store_info) {
                $store_name = $store_info['name'];
            } else {
                $store_name = $this->config->get('config_name');
            }

            $message = sprintf($this->language->get('text_transaction_received'), $this->currency->format($amount, $this->config->get('config_currency')))."\n\n";
            $message .= sprintf($this->language->get('text_transaction_total'), $this->currency->format($this->getTransactionTotal($seller_id), $this->config->get('config_currency')));

            $mail = new Mail();
            $mail->protocol = $this->config->get('config_mail_protocol');
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
            $mail->smtp_username = $this->config->get('config_mail_smtp_username');
            $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
            $mail->smtp_port = $this->config->get('config_mail_smtp_port');
            $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
            $mail->setTo($seller_info['email']);
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender($store_name);
            $mail->setSubject(sprintf($this->language->get('text_transaction_subject'), $this->config->get('config_name')));
            $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
            $mail->send();
        }
    }

    public function getTransactionTotal($seller_id)
    {
        $query = $this->db->query('SELECT SUM(amount) AS total FROM '.DB_PREFIX."seller_transaction WHERE customer_id = '".(int) $seller_id."'");

        return $query->row['total'];
    }
    public function getTotalsellerRewardsByOrderId($order_id)
    {
        $query = $this->db->query('SELECT COUNT(*) AS total FROM '.DB_PREFIX."customer_reward WHERE order_id = '".(int) $order_id."'");

        return $query->row['total'];
    }
	
	public function sellercounter($seller_id)
    {
        $this->db->query('UPDATE '.DB_PREFIX."customer SET seller_counter = (seller_counter + 1) WHERE customer_id = '".(int) $seller_id."'");
    }
	
	public function addstorefavourite($seller_id, $data)
    {
		$this->db->query("DELETE FROM ".DB_PREFIX."customer_store_favourites WHERE store_id = '".$seller_id."' AND customer_id = '".(int) $this->customer->getID()."'");

		$this->db->query('INSERT INTO '.DB_PREFIX."customer_store_favourites SET customer_id = '".(int) $this->customer->getID()."', store_id = '".$seller_id."', date_added = NOW()");

		if(isset($data['email']) != '') {
			$this->db->query('UPDATE '.DB_PREFIX."customer_store_favourites SET email = '".$data['email']."' WHERE customer_id = '".(int) $this->customer->getID()."' AND  store_id = '".$seller_id."'");
		}

		//if(isset($data['notification']) != '') {
			//$this->db->query('UPDATE '.DB_PREFIX."customer_store_favourites SET notification = '".$data['notification']."' WHERE customer_id = '".(int) $this->customer->getID()."' AND  store_id = '".$seller_id."'");
		//}

		return "Store successfully added to your favourites";
		//} else {
			//return "Store successfully removed from your favourites";
		//}
    }
	
	public function getstore_favourites($seller_id, $customer_id)
    {
        $query = $this->db->query('SELECT email, notification FROM '.DB_PREFIX."customer_store_favourites WHERE store_id = '".(int) $seller_id."' AND customer_id = '".(int) $customer_id."'");

        return $query->row;
    }
	
	public function getstore_favourites_front($customer_id)
    {
        $sql = "SELECT csf.email, csf.notification, p.nickname, p.banner, p.customer_id FROM ".DB_PREFIX."customer_store_favourites csf LEFT JOIN ".DB_PREFIX."customer p ON (p.customer_id = csf.store_id) WHERE csf.customer_id = '".(int) $customer_id."'";

		$query = $this->db->query($sql);

        return $query->rows;
    }

	public function updatestore_favourites_front($customer_id, $store_id, $value, $fav_name)
    {
        $this->db->query('UPDATE '.DB_PREFIX."customer_store_favourites SET ".$fav_name." = '".(int) $value."' WHERE customer_id = '".(int) $customer_id."' AND store_id = '".(int) $store_id."'");
    }

	public function remove_favourite($store_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_store_favourites WHERE store_id = '" . (int)$store_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'");
	}

	public function getsellerImages($seller_id)
    {
        $query = $this->db->query('SELECT image FROM '.DB_PREFIX."store_image WHERE seller_id = '".(int) $seller_id."' ORDER BY sort_order");

        return $query->rows;
    }

	public function Insert_site_feedback($data)
    {
        $this->db->query('INSERT INTO '.DB_PREFIX."site_feedback SET mobile_num = '".$data['fd_mobile_num']."', email = '".$data['fd_email']."', feedback = '".$data['feedback']."',  date_added = NOW()");

        $feedback_id = $this->db->getLastId();

        return $feedback_id;
    }
	
	public function getstore_timings($seller_id)
    {
        $query = $this->db->query('SELECT * FROM '.DB_PREFIX."store_timing WHERE uid = '".(int) $seller_id."'");

        return $query->row;
    }
}
