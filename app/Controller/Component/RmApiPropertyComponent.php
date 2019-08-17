<?php
class RmApiPropertyComponent extends Component {
    public $components = array(
        'RmCommon', 'RmProperty',
        'RmUser', 'Rest.Rest'
    );

    function initialize(Controller $controller, $settings = array()) {
        $this->controller = $controller;
    }

    /*
        format json for data integration at rumah123
        result array agent + listing
    */
    function formatListing($data){
        if(!empty($data)){
            App::uses('HtmlHelper', 'View/Helper');
            App::uses('RumahkuHelper', 'View/Helper');
            App::uses('PropertyHelper', 'View/Helper');
            $HtmlHelper     = new HtmlHelper(new View());
            $RumahkuHelper  = new RumahkuHelper(new View());
            $PropertyHelper = new PropertyHelper(new View());

            $temp = array();
            foreach ($data as $key => $value) {
                // ======================
                // ===== Data Agent =====
                // ======================
                //
                // S : define variable data agent
                $profile    = $this->RmCommon->filterEmptyField($value, 'UserProfile', 'description', '', array('type' => 'strip_tags'));
                $id_agent   = $this->RmCommon->filterEmptyField($value, 'User', 'id');
                $firstname  = $this->RmCommon->filterEmptyField($value, 'User', 'first_name');
                $lastname   = $this->RmCommon->filterEmptyField($value, 'User', 'last_name');
                $email      = $this->RmCommon->filterEmptyField($value, 'User', 'email');
                $address    = $this->RmCommon->filterEmptyField($value, 'UserProfile', 'address');
                $UProfile   = $this->RmCommon->filterEmptyField($value, 'UserProfile');
                $city       = $this->RmCommon->filterEmptyField($UProfile, 'City', 'name');
                $subarea    = $this->RmCommon->filterEmptyField($UProfile, 'Subarea', 'name');
                $zip        = $this->RmCommon->filterEmptyField($UProfile, 'Subarea', 'zip');

                $fullAddress= sprintf(__('%s, %s, %s, %s'), $address, $subarea, $city, $zip);

                $phone      = $this->RmCommon->filterEmptyField($value, 'UserProfile', 'phone', '');
                $mobile     = $this->RmCommon->filterEmptyField($value, 'UserProfile', 'no_hp', '');
                $photo      = $this->RmCommon->filterEmptyField($value, 'User', 'photo', '');

                $ParentInfo = $this->RmCommon->filterEmptyField($value, 'ParentInfo');
                $domain     = $this->RmCommon->filterEmptyField($ParentInfo, 'UserCompanyConfig', 'domain');
                $id_company = $this->RmCommon->filterEmptyField($ParentInfo, 'UserCompany', 'id');

                $imgPath = Configure::read('__Site.profile_photo_folder');
                $photoAgent = $RumahkuHelper->photo_thumbnail(array(
                        'save_path' => $imgPath, 
                        'src'       => $photo, 
                        'size'      => 'fullsize',
                        'url'       => true,
                    )
                );

                $photoAgent = $domain.$photoAgent;
                //
                // E : define variable data agent

                $agent = array(
                    'r123agent_id' => $id_agent,
                    'company_id' => $id_company,
                    'firstname'  => $firstname,
                    'lastname'   => $lastname,
                    'email'      => $email,
                    'profile'    => $profile,
                    'address'    => $fullAddress,
                    'photo'      => $photoAgent,
                    'phone'      => $phone,
                    'mobile'     => $mobile
                );

                // ======================
                // ==== Data Listing ====
                // ======================
                //
                // S : define variable data listing
                $id_property     = $this->RmCommon->filterEmptyField($value, 'Property', 'id');
                $tagline         = $this->RmCommon->filterEmptyField($value, 'Property', 'keyword');
                $price           = $this->RmCommon->filterEmptyField($value, 'Property', 'price');
                $description     = $this->RmCommon->filterEmptyField($value, 'Property', 'description');
                $updated_time    = $this->RmCommon->filterEmptyField($value, 'Property', 'modified');
                $soldStatus      = $this->RmCommon->filterEmptyField($value, 'Property', 'sold');
                $statusListing   = $this->RmCommon->getStatusListingfor123($value, false, true);

                $propertyTypeID  = $this->RmCommon->filterEmptyField($value, 'PropertyType', 'id');
                $nameProperty    = $this->RmCommon->filterEmptyField($value, 'PropertyType', 'name');

                $groupListing    = $this->RmCommon->formatCategoryTyper123($propertyTypeID, 'group');
                $typeListing     = $this->RmCommon->formatCategoryTyper123($propertyTypeID, 'type');

                $propertyAction  = $this->RmCommon->filterEmptyField($value, 'PropertyAction', 'id');

                $currency        = $this->RmCommon->filterEmptyField($value, 'Currency', 'alias');
                $certificate     = $this->RmCommon->filterEmptyField($value, 'Certificate', 'name_id');

                $PropertyAddress = $this->RmCommon->filterEmptyField($value, 'PropertyAddress');
                $addressListing  = $this->RmCommon->filterEmptyField($value, 'PropertyAddress', 'address');
                $district        = $this->RmCommon->filterEmptyField($PropertyAddress, 'Subarea', 'name');
                $city            = $this->RmCommon->filterEmptyField($PropertyAddress, 'City', 'name');
                $province        = $this->RmCommon->filterEmptyField($PropertyAddress, 'Region', 'name');

                $PropertyFacility = $this->RmCommon->filterEmptyField($value, 'PropertyFacility');

                $PropertyAsset   = $this->RmCommon->filterEmptyField($value, 'PropertyAsset');
                $propcondition   = $this->RmCommon->filterEmptyField($PropertyAsset, 'PropertyCondition', 'name', '');
                $electricity     = $this->RmCommon->filterEmptyField($PropertyAsset, 'Electricity', 'name', '');
                $lot_unit_name   = $this->RmCommon->filterEmptyField($PropertyAsset, 'LotUnit', 'name');

                $bedroom         = $this->RmCommon->filterEmptyField($value, 'PropertyAsset', 'beds', '');
                $bathroom        = $this->RmCommon->filterEmptyField($value, 'PropertyAsset', 'baths', '');
                $beds_maid       = $this->RmCommon->filterEmptyField($value, 'PropertyAsset', 'beds_maid');
                $baths_maid      = $this->RmCommon->filterEmptyField($value, 'PropertyAsset', 'baths_maid');
                $areasize        = $this->RmCommon->filterEmptyField($value, 'PropertyAsset', 'building_size', '');
                $landsize        = $this->RmCommon->filterEmptyField($value, 'PropertyAsset', 'lot_size', '');
                $cars            = $this->RmCommon->filterEmptyField($value, 'PropertyAsset', 'cars', '');
                $phoneline       = $this->RmCommon->filterEmptyField($value, 'PropertyAsset', 'phoneline', '');
                $floor           = $this->RmCommon->filterEmptyField($value, 'PropertyAsset', 'level', '');
                $furnishedCond   = $this->RmCommon->filterEmptyField($value, 'PropertyAsset', 'furnished', '');

                // condition
                // kalau tanah tandai true, lainnya kosong (bukan null)
                if ($propertyTypeID == 2) {
                    $imb = true;
                } else {
                    $imb = '';
                }

                if ($soldStatus == 1) {
                    $soldstatus = 'y';
                } else {
                    $soldstatus = 'n';
                }

                if ($furnishedCond == 1) {
                    $conditions = 'Unfurnished';
                } elseif ($furnishedCond == 2) {
                    $conditions = 'Semi furnished';
                } elseif ($furnishedCond == 3) {
                    $conditions = 'Furnished';
                } else {
                    $conditions = '';
                }

                if ($propertyAction == 1) {
                    $category = 'For Sale';
                    $period = $this->RmCommon->filterEmptyField($value, 'Period', 'name', '');
                } else {
                    $category = 'For Rent';
                    $period = $this->RmCommon->filterEmptyField($value, 'Period', 'name', 'Monthly');
                }

                // list facility
                if (!empty($PropertyFacility)) {
                    $facilities = array();
                    foreach ($PropertyFacility as $key => $facility) {
                        // debug($facility);die();
                        $facility_name = $this->RmCommon->filterEmptyField($facility, 'Facility', 'name');
                        $facilities[] = $facility_name;
                    }
                }

                // list image
                if (!empty($value['PropertyMedias'])) {
					$list_media = array();
					$property_path = Configure::read('__Site.property_photo_folder');

                    foreach ($value['PropertyMedias'] as $key => $val_media) {
                        $media_path = $this->RmCommon->filterEmptyField($val_media, 'PropertyMedias', 'name');
                        $primary = $this->RmCommon->filterEmptyField($val_media, 'PropertyMedias', 'primary');

                        $image = $RumahkuHelper->photo_thumbnail(array(
                                'save_path' => $property_path, 
                                'src'       => $media_path, 
                                'size'      => 'company',
                                'url'       => true,
                            )
                        );

                        $index = count($list_media);

                        $list_media[$index]['name'] = $domain.$image;
                        // set primary asset
                        if ($primary == true) {
                            $list_media[$key]['default'] = $primary;
                        }
                    }
                }

                // list video
                if (!empty($value['PropertyVideos'])) {
                    $list_video = array();
                    foreach ($value['PropertyVideos'] as $key => $val_video) {
                        $video_path = $this->RmCommon->filterEmptyField($val_video, 'PropertyVideos', 'url');
                        $primary = $this->RmCommon->filterEmptyField($val_video, 'PropertyVideos', 'primary');

                        $index = count($list_video);
                        $list_video[$index]['name'] = $video_path;

                        if($primary){
                        	$list_video[$index]['default'] = $primary;
                        }
                    }
                }

                //
                // E : define variable data listing

                $listing = array(
                    'id' => $id_property,
                    'group' => $groupListing,
                    'type' => $typeListing,
                    'category' => $category,
                    'tagline' => $tagline,
                    'description' => $description,
                    'price' => $price,
                    'currency' => $currency,
                    'period' => $period,
                    'price_desc' => $lot_unit_name,
                    'district' => $district,
                    'city' => $city,
                    'province' => $province,
                    'address' => $addressListing,
                    'bedroom' => $bedroom,
                    'serv_bedroom' => $beds_maid,
                    'bathroom' => $bathroom,
                    'serv_bathroom' => $baths_maid,
                    'areasize' => $areasize,
                    'landsize' => $landsize,
                    'garage' => $cars,
                    'conditions' => $conditions,
                    'propertycondition' => $propcondition,
                    'electricity' => $electricity,
                    'phoneline' => $phoneline,
                    'facility' => isset($facilities)?$facilities:'',
                    'floor' => $floor,
                    'certificate' => $certificate,
                    'imb' => $imb,
                    'status' => $statusListing,
                    'images' => isset($list_media)?$list_media:'',
                    'videos' => isset($list_video)?$list_video:'',
                    'soldstatus' => $soldstatus,
                    'updated_time' => $updated_time,
                );

                // result array agent and listing
                $temp[] = array(
                    'agent' => $agent,
                    'listing' => $listing,
                );
            }

            $data = $temp;
        }

        return $data;
    }
}
?>