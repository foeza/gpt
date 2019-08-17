<?php
/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
    public $recursive = -1;
    public $actsAs = array('Containable', 'Common');

    public function save($data = null, $validate = true, $fieldList = array()) {
        $this->setDataSource('master');
        $response = parent::save($data, $validate, $fieldList);
        $this->setDataSource('default');

        return $response;
    }

    public function updateAll($fields, $conditions = true) {
        $this->setDataSource('master');
        $response = parent::updateAll($fields, $conditions);
        $this->setDataSource('default');

        return $response;
    }

    public function delete($id = null, $cascade = true) {
        $this->setDataSource('master');
        $response = parent::delete($id, $cascade);
        $this->setDataSource('default');

        return $response;
    }

    public function deleteAll($conditions = null, $cascade = true, $callbacks = false) {
        $this->setDataSource('master');
        $response = parent::deleteAll($conditions, $cascade, $callbacks);
        $this->setDataSource('default');

        return $response;
    }

    function find($conditions = null, $fields = array(), $order = null, $recursive = null) {
        $doQuery = true;
        $aliasName = $this->name;
        $primaryKey = $this->primaryKey;

        // Check Type First & kondisi ada PK, tdk perlu pake Order
        $primaryKeyField = __('%s.%s', $aliasName, $primaryKey);

        if( ($conditions == 'first' && !empty($fields['conditions'][$primaryKeyField]) && !empty($fields['order'])) || $conditions == 'count' ) {
            unset($fields['order']);
        }

        // check if we want the cache
        if (!empty($fields['cache'])) {
            $cacheConfig = 'default_master';

            // check if we have specified a custom config, e.g. different expiry time
            if (!empty($fields['cacheConfig'])) {
                $cacheConfig = $fields['cacheConfig'];
            }

            $cacheName = $fields['cache'];

            // if so, check if the cache exists
            if (($data = Cache::read($cacheName, $cacheConfig)) === false) {
                $data = parent::find($conditions, $fields, $order, $recursive);
                Cache::write($cacheName, $data, $cacheConfig);
            }

            $doQuery = false;
        }

        if ($doQuery) {
            $data = parent::find($conditions, $fields, $order, $recursive);
        }

        return $data;
    }

    private function _callGetExt ( $file = false ) {
        $fileArr = explode('.', $file);
        return end($fileArr);
    }
}
