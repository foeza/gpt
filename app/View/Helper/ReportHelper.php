<?php
class ReportHelper extends AppHelper {
	var $helpers = array(
		'Rumahku', 'Html', 'Number',
		'Session'
	);

	function _callLabelName( $value ) {
        $region = $this->Rumahku->filterEmptyField($value, 'Region', 'name');
        $city = $this->Rumahku->filterEmptyField($value, 'City', 'name');
        $_type = $this->Rumahku->filterEmptyField($value, 'PropertyType', 'name');
        $_action = $this->Rumahku->filterEmptyField($value, 'PropertyAction', 'name');
        $result = false;

		if( !empty($_type) ) {
        	$result = $_type;
		} else {
        	$result = __('Semua Properti');
		}

		if( !empty($_action) ) {
        	$result .= '&nbsp;'.$_action;
		}

		if( !empty($city) ) {
        	$result .= __(', di %s', $city);
		}

		if( !empty($region) ) {
        	$result .= __(', %s', $region);
		}

		return $result;
	}

	function _callSpec ( $value, $options = array() ) {
        $empty = $this->Rumahku->filterEmptyField($options, 'empty');
        $divider = $this->Rumahku->filterEmptyField($options, 'divider', false, ', ', false);
        $tag = $this->Rumahku->filterEmptyField($options, 'tag');

        $spec = $this->Rumahku->filterEmptyField($value, 'Specification');
        $result = array();

        if( !empty($spec) ) {
        	foreach ($spec as $label => $val) {
        		if( !empty($tag) ) {
        			$label = $this->Html->tag($tag, $label);
        		}

    			$result[] = __('%s: %s', $label, $val);
        	}

        	$result = implode($divider, $result);
        }

        if( !empty($result) ) {
        	return $result;
        } else {
        	return $empty;
        }
	}

    function _callActions ( $value, $action_detail = 'detail' ) {
        $id = $this->Rumahku->filterEmptyField($value, 'Report', 'id');
        $document_status = $this->Rumahku->filterEmptyField($value, 'Report', 'document_status');

        $actions = array(
            $this->Html->link(
                $this->Rumahku->icon('rv4-magnify'), array(
                'controller' => 'reports',
                'action' => $action_detail,
                $id,
                'admin' => true,
            ), array(
                'escape' => false,
                'title' => __('Lihat Detil'),
            )),
        );

        if($document_status == 'completed') {
            $actions[] = $this->Html->link(
                $this->Rumahku->icon('rv4-download'), array(
                'controller' => 'reports',
                'action' => 'download',
                $id,
                'admin' => true,
            ), array(
                'escape' => false,
                'title' => __('Unduh Laporan'),
            ));
        }

        // $actions[] = $this->Html->link(
        //     $this->Rumahku->icon('rv4-trash'), array(
        //     'controller' => 'reports',
        //     'action' => 'delete',
        //     $id,
        //     'admin' => true,
        // ), array(
        //     'escape' => false,
        //     'title' => __('Hapus'),
        // ), __('Anda yakin ingin menghapus laporan ini?'));

        return $actions;
    }

    function _callDetail ( $value ) {
        $details = $this->Rumahku->filterEmptyField($value, 'ReportDetail');
        $result = false;

        if( !empty($details) ) {
            $contentLi = false;

            foreach ($details as $key => $detail) {
                $titleField = $this->Rumahku->filterEmptyField($detail, 'ReportDetail', 'title');
                $value_name = $this->Rumahku->filterEmptyField($detail, 'ReportDetail', 'value_name');

                if( $titleField != 'params' ) {
                    $titleField = str_replace('_', ' ', $titleField);

                    if( is_array($value_name) ) {
                        $value_name = implode(', ', $value_name);
                    }

                    $contentLi .= $this->Html->tag('li', __('%s: %s', $titleField, $this->Html->tag('strong', $value_name)));
                }
            }

            $result = $this->Html->tag('ul', $contentLi);
        }

        return $result;
    }

    function _callAccessDownload ( $value ) {
        $type = $this->Rumahku->filterEmptyField($value, 'Report', 'report_type_id');
        $group_by = Set::extract('/ReportDetail/ReportDetail/field', $value);
        $admin_rumahku = Configure::read('User.Admin.Rumahku');
        $user_id = Configure::read('User.data.group_id');

        if( $user_id == 20 ) {
            return true;
        } else if( empty($admin_rumahku) || in_array($type, array( 'performance', 'summary', 'kprs' )) ) {
            return true;
        } else {
            return false;
        }
    }

    function getdateRange($default = false){
        $date_to = date('Y-m-d');
        $params = $this->params->params;

        switch ($default) {
            case 'monthly':
                $date_from = date('Y-m-d');
                $periode_id = '1';
                break;

            case 'half_yearly':
                $date_from = date('Y-m-d', strtotime('-5 month'));
                $periode_id = '6';
                break;

            case 'yearly':
                $date_from = date('Y-m-d', strtotime('-11 month'));
                $periode_id = '12';
                break;

            // quarterly
            default:
                $date_from = date('Y-m-d', strtotime('-2 month'));
                $periode_id = '3';
                break;
        }

        $periode_id = Common::hashEmptyField($params, 'named.periode_id', $periode_id);

        return array(
            'date_to' => $date_to,
            'date_from' => $date_from,
            'periode_id' => $periode_id,
        );
    }

    function callAttributes($type, $options = array()){
        // options
        $wrapperWrite = 'wrapper-dashboard-area';
        $wrapperWriteParent = 'wrapper-dashboard-parent-area';
        $idChart = 'chart_area';
        $label = __('Top Area Klien');

        if(!empty($type)){
            switch ($type) {
                case 'client_age':
                    $wrapperWrite = 'wrapper-dashboard-client-age';
                    $wrapperWriteParent = 'wrapper-dashboard-parent-client-age';
                    $idChart = 'chart_age';
                    $label = __('Umur Klien');
                    break;

                case 'top_area':
                    $wrapperWrite = 'wrapper-dashboard-top-area';
                    $wrapperWriteParent = 'wrapper-dashboard-parent-top-area';
                    $idChart = 'chart_area';
                    $label = __('Top Area Klien');
                    break;

                case 'marital':
                    $wrapperWrite = 'wrapper-dashboard-marital';
                    $wrapperWriteParent = 'wrapper-dashboard-parent-marital';
                    $idChart = 'chart_marital';
                    $label = __('Marital Status');              
                    break;
            }
        }

        return array(
            'label' => $label,
            'idChart' => sprintf('%s', $idChart),
            'wrapperWrite' => sprintf('%s', $wrapperWrite),
            'wrapperWriteParent' => sprintf('%s', $wrapperWriteParent),
        );
    }

    function getDateVisitor($default, $options = array()){
        $period = Common::_callPeriodeDate($default);

        $title = Common::hashEmptyField($period, 'title');

        $before_date_to = Common::hashEmptyField($period, 'periode_date_from');
        $before_date_to = Common::dateAdd($before_date_to, '-1');

        //compare dengan periode sebelumnya 
        $beforePeriod = Common::_callPeriodeDate($default, false, array(
            'date_to' => $before_date_to,
        ));
        $beforeTitle = Common::hashEmptyField($beforePeriod, 'title');

        $period = json_encode($period);
        $beforePeriod = json_encode($beforePeriod);

        return array(
            'period' => $period,
            'beforePeriod' => $beforePeriod,
            'title' => $title,
            'beforeTitle' => $beforeTitle,
        );
    }

    function _callTipsDailyActivity ( $values, $percentage, $ranking ) {
        if( !empty($values) ) {
            $tmpTxt = __('Tingkatkan terus aktivitas CRM untuk mencapai hasil yang maksimal.');
            $tmpTxtOpening = __('Aktivitas follow up harian');
            $maxTarget = 75;

            if( $percentage >= $maxTarget ) {
                $tips = __('%s %s, tercapai %s dari target. %s', $ranking, $this->Html->tag('strong', $percentage.'%', array(
                    'class' => 'cgreen',
                )), $tmpTxt);
            } else if( $percentage < $maxTarget ) {
                if( !empty($percentage) ) {
                    $tips = __('%s %s, hanya tercapai %s dari target. %s', $tmpTxtOpening, $ranking, $this->Html->tag('strong', $percentage.'%', array(
                        'class' => 'cred',
                    )), $tmpTxt);
                } else {
                    $tips = __('%s %s, tidak mencapai target. %s', $tmpTxtOpening, $ranking, $tmpTxt);
                }
            } else {
                $tips = $tmpTxt;
            }
        } else {
            $tips = __('Tidak ada Aktivitas pada hari ini. Tingkatkan terus aktivitas CRM untuk mencapai hasil yang maksimal.');
        }

        if( !empty($tips) ) {
            return $this->Html->tag('div', $this->Html->tag('li', $tips), array(
                'class' => 'wrapper-tips-activity-daily hide',
            ));
        } else {
            return false;
        }
    }

    function _callTipsPerformanceCRM ( $percentage, $total_avg_activity ) {
        $tmpTxt = __('Performa CRM hingga klien melakukan booking mengalami');

        if( empty($total_avg_activity) ) {
            $tips = __('Tidak ada CRM yang complete pada periode ini. Tingkatkan CRM untuk mencatat aktivitas dengan klien, sebagai pengingat dan catatan anda.', $this->Html->tag('strong', $percentage.'%', array(
                'class' => 'cred',
            )));
        } else if( $percentage < 0 ) {
            $percentage = abs($percentage);
            $tips = __('%s penurunan %s. Tingkatkan aktivitas & follow up kepada klien.', $tmpTxt, $this->Html->tag('strong', $percentage.'%', array(
                'class' => 'cred',
            )));
        } else if( $percentage > 0 ) {
            $tips = __('%s peningkatan %s dari periode sebelumnya.', $tmpTxt, $this->Html->tag('strong', $percentage.'%', array(
                'class' => 'cgreen',
            )));
        }

        if( !empty($tips) ) {
            return $this->Html->tag('div', $this->Html->tag('li', $tips), array(
                'class' => 'wrapper-tips-performance-crm hide',
            ));
        } else {
            return false;
        }
    }

    function _callTipsSales ( $percentage, $prev_target, $additional_info = array(), $total_listing = 0 ) {
        $date_from  = Common::hashEmptyField($additional_info,'date_from');
        $date_to    = Common::hashEmptyField($additional_info,'date_to');

        $date_info = $this->dateReportInfo($date_from, $date_to);

        $tmpTxt = __('Tingkatkan aktivitas marketing seperti Event, Newsletter & informasi promo untuk menarik minat klien.');
        $tmpTxtOpening = __('Total penjualan mengalami');

        if( $percentage < 0 ) {
            $percentage = abs($percentage);

            if(!empty($prev_target)){
                if(!empty($date_info)){
                    $tips = __('%s penurunan %s pada periode %s. %s', $tmpTxtOpening, $this->Html->tag('strong', $percentage.'%', array(
                        'class' => 'cred',
                    )), $date_info, $tmpTxt);
                }else{
                    $tips = __('%s penurunan %s. %s', $tmpTxtOpening, $this->Html->tag('strong', $percentage.'%', array(
                        'class' => 'cred',
                    )), $tmpTxt);
                }
            }else{
                if(!empty($date_info)){
                    $tips = __('Tidak ada penjualan pada periode %s. %s', $date_info, $tmpTxt);
                }else{
                    $tips = __('Tidak ada penjualan pada periode ini. %s', $tmpTxt);
                }
            }
        } else if( $percentage > 0 ) {
            if(!empty($date_info)){
                $tips = __('%s peningkatan %s dari %s.', $tmpTxtOpening, $this->Html->tag('strong', $percentage.'%', array(
                    'class' => 'cgreen',
                )), $date_info);
            }else{
                $tips = __('%s peningkatan %s dari periode sebelumnya.', $tmpTxtOpening, $this->Html->tag('strong', $percentage.'%', array(
                    'class' => 'cgreen',
                )));
            }
                
        } else if( !is_numeric($percentage) ) {
            if(!empty($date_info)){
                $tips = __('Tidak ada penjualan pada periode %s. %s', $date_info, $tmpTxt);
            }else{
                $tips = __('Tidak ada penjualan pada periode ini. %s', $tmpTxt);
            }
        } else {
            if(empty($prev_target) && !empty($total_listing)){
                if(!empty($date_info)){
                    $tips = __('Anda telah menjual %s properti pada periode %s.', $total_listing, $date_info);
                }else{
                    $tips = __('Anda telah menjual %s properti pada periode ini.', $total_listing);
                }
            }else{
                if(!empty($date_info)){
                    $tips = __('Tidak ada penjualan pada periode %s. %s', $date_info, $tmpTxt);
                }else{
                    $tips = __('Tidak ada penjualan pada periode ini. %s', $tmpTxt);
                }
            }
        }

        if( !empty($tips) ) {
            return $this->Html->tag('div', $this->Html->tag('li', $tips), array(
                'class' => 'wrapper-tips-sales hide',
            ));
        } else {
            return false;
        }
    }

    function _callTipsTopAreaClient ( $values ) {
        $name = Common::hashEmptyField($values, 'name');
        $percentage = Common::hashEmptyField($values, 'value');
        $tmpTxt = __('Tingkatkan Aktivitas & Event, untuk mendapatkan leads & meningkatkan penjualan.');

        if( !empty($name) ) {
            $tips = __('Sebanyak %s klien berasal dari area %s. ', $this->Html->tag('strong', $percentage.'%', array(
                'class' => 'bold',
            )), $this->Html->tag('strong', $name, array(
                'class' => 'bold',
            )));

            if( $percentage <= 30 ) {
                $tips .= $tmpTxt;
            }
        } else {
            $tips = __('Tidak ada data klien pada periode ini. %s', $tmpTxt);
        }

        if( !empty($tips) ) {
            return $this->Html->tag('div', $this->Html->tag('li', $tips), array(
                'class' => 'wrapper-tips-toparea-client hide',
            ));
        } else {
            return false;
        }
    }

    function _callTipsAgeClient ( $values ) {
        $name = Common::hashEmptyField($values, 'name');
        $percentage = Common::hashEmptyField($values, 'value');
        $tmpTxt = __('Anda dapat mengisi/mengelompokan klien berdasarkan rentang umur %s.', $this->Html->link(__('disini'), array(
            'controller' => 'users',
            'action' => 'clients',
            'admin' => true,
        ), array(
            'target' => '_blank',
        )));

        if( !empty($name) ) {
            $tips = __('Rentang umur %s paling banyak yakni sebesar %s. ', $this->Html->tag('strong', $name, array(
                'class' => 'bold',
            )), $this->Html->tag('strong', $percentage.'%', array(
                'class' => 'bold',
            )));

            if( $percentage <= 30 ) {
                $tips .= $tmpTxt;
            }
        } else {
            $tips = __('Tidak ada data klien pada periode ini. %s', $tmpTxt);
        }

        if( !empty($tips) ) {
            return $this->Html->tag('div', $this->Html->tag('li', $tips), array(
                'class' => 'wrapper-tips-agent-age hide',
            ));
        } else {
            return false;
        }
    }

    function _callTipsTopUnit ( $unit, $additional_info = array() ) {
        $date_from  = Common::hashEmptyField($additional_info,'date_from');
        $date_to    = Common::hashEmptyField($additional_info,'date_to');

        $date_info = $this->dateReportInfo($date_from, $date_to);

        if( !empty($unit) ) {
            $most_sold_text = Common::hashEmptyField($unit, 'text');
            $most_sold_total = Common::hashEmptyField($unit, 'total');

            if(!empty($date_info)){
                $tips = __('Unit %s telah terjual sebanyak %s unit pada periode %s. Menjadikan unit ini paling banyak dibooking oleh klien.', $this->Html->tag('strong', $most_sold_text, array(
                    'class' => 'bold',
                )), $this->Html->tag('strong', $most_sold_total, array(
                    'class' => 'bold',
                )), $date_info);
            }else{
                $tips = __('Unit %s telah terjual sebanyak %s unit. Menjadikan unit ini paling banyak dibooking oleh klien.', $this->Html->tag('strong', $most_sold_text, array(
                    'class' => 'bold',
                )), $this->Html->tag('strong', $most_sold_total, array(
                    'class' => 'bold',
                )));
            }
        } else {
            $tips = __('Tidak ada unit terjual / tersewa periode ini. Tingkatkan aktivitas promosi Anda melalui Event, Newsletter, & Sosial Media.');
        }

        if( !empty($tips) ) {
            return $this->Html->tag('div', $this->Html->tag('li', $tips), array(
                'class' => 'wrapper-tips-top-unit hide',
            ));
        } else {
            return false;
        }
    }

    function _callTipsCrmActivity ( $values, $percentage, $options = array() ) {
        $date_from = Common::hashEmptyField($options,'date_from');
        $date_to = Common::hashEmptyField($options,'date_to');
        $tmpTxt = __('Tingkatkan aktivitas follow up anda & gunakan marketing tools seperti Newsletter & informasi promo untuk menarik minat klien.');

        $date_info = $this->dateReportInfo($date_from, $date_to);

        if( !empty($values) ) {
            // $maxTarget = 75;
            $tmpTxtOpening = __('Aktivitas follow up mengalami');

            if( !empty($percentage) ) {
                if( $percentage > 0 ) {
                    if(!empty($date_info)){
                        $tips = __('%s peningkatan %s dari %s', $tmpTxtOpening, $this->Html->tag('strong', $percentage.'%', array(
                            'class' => 'cgreen',
                        )), $date_info);
                    }else{
                        $tips = __('%s peningkatan %s dari periode sebelumnya', $tmpTxtOpening, $this->Html->tag('strong', $percentage.'%', array(
                            'class' => 'cgreen',
                        )));
                    }
                } else if( $percentage < 0 ) {
                    $percentage = abs($percentage);

                    if(!empty($date_info)){
                        $tips = __('%s mengalami penurunan %s pada periode %s. %s', $tmpTxtOpening, $this->Html->tag('strong', $percentage.'%', array(
                            'class' => 'cred',
                        )), $date_info, $tmpTxt);
                    }else{
                        $tips = __('%s mengalami penurunan %s. %s', $tmpTxtOpening, $this->Html->tag('strong', $percentage.'%', array(
                            'class' => 'cred',
                        )), $tmpTxt);
                    }
                }
            } else {
                $tips = $tmpTxt;
                // if(!empty($date_info)){
                //     $tips = __('Tidak ada Aktivitas dari %s. %s', $date_info, $tmpTxt);
                // }else{
                //     $tips = __('Tidak ada Aktivitas pada hari ini. %s', $tmpTxt);
                // }
            }
        } else {
            if(!empty($date_info)){
                $tips = __('Tidak ada Aktivitas dari %s. %s', $date_info, $tmpTxt);
            }else{
                $tips = __('Tidak ada Aktivitas pada hari ini. %s', $tmpTxt);
            }
        }

        if( !empty($tips) ) {
            return $this->Html->tag('div', $this->Html->tag('li', $tips), array(
                'class' => 'wrapper-tips-activity hide',
            ));
        } else {
            return false;
        }
    }

    function _callTipsTargetSales ( $target, $percentage, $last_percentage, $additional_info = array() ) {
        $date_from  = Common::hashEmptyField($additional_info,'date_from');
        $date_to    = Common::hashEmptyField($additional_info,'date_to');

        $date_info = $this->dateReportInfo($date_from, $date_to);

        if( !empty($target) ) {
            $percentage -= $last_percentage;
            $tmpTxt = __('Tingkatkan aktivitas marketing seperti Event, Newsletter & informasi promo untuk menarik minat klien.');
            $tmpTxtOpening = __('Pencapaian target penjualan');
            $maxTarget = 75;

            if( $percentage > 0 ) {
                if(!empty($date_info)){
                    $tips = __('%s meningkatkan %s dari %s.', $tmpTxtOpening, $this->Html->tag('strong', $percentage.'%', array(
                        'class' => 'cgreen',
                    )), $date_info);
                }else{
                    $tips = __('%s meningkatkan %s dari periode sebelumnya.', $tmpTxtOpening, $this->Html->tag('strong', $percentage.'%', array(
                        'class' => 'cgreen',
                    )));
                }
            } else if( $percentage < 0 ) {
                $percentage = abs($percentage);

                if(!empty($date_info)){
                    $tips = __('%s mengalami penurunan %s pada periode %s. %s', $tmpTxtOpening, $this->Html->tag('strong', $percentage.'%', array(
                        'class' => 'cred',
                    )), $date_info, $tmpTxt);
                }else{
                    $tips = __('%s mengalami penurunan %s. %s', $tmpTxtOpening, $this->Html->tag('strong', $percentage.'%', array(
                        'class' => 'cred',
                    )), $tmpTxt);
                }
            }
        } else {
            $tips = __('Belum ada pengaturan target penjualan. Silakan atur %s anda, agar memudahkan anda dalam penetapan rangkaian strategi penjualan yang akan gunakan.', $this->Html->link(__('target penjualan'), 'http://primedev.pasiris.com/bantuan/49/pengaturan', array(
                'target' => '_blank',
            )));
        }

        if( !empty($tips) ) {
            return $this->Html->tag('div', $this->Html->tag('li', $tips), array(
                'class' => 'wrapper-tips-target-sales hide',
            ));
        } else {
            return false;
        }
    }

    function _callTipsProject ( $percentage, $total ) {
        if( !empty($percentage) ) {
            $tmpTxt = __('Tingkatkan aktivitas marketing seperti Event, Newsletter & informasi promo untuk menarik minat klien.');

            $tips = __('Pencapaian unit terjual %s dari total %s unit.', $this->Html->tag('strong', $percentage.'%', array(
                'class' => 'bold',
            )), $this->Html->tag('strong', $total, array(
                'class' => 'bold',
            )));
        } else {
            $tips = __('Tidak ada unit terjual pada periode ini.');
        }

        if( !empty($tips) ) {
            return $this->Html->tag('div', $this->Html->tag('li', $tips), array(
                'class' => 'wrapper-tips-project hide',
            ));
        } else {
            return false;
        }
    }

    function _callTipsSellingMedia ( $total, $top_selling_media, $additional_info = array() ) {
        $date_from  = Common::hashEmptyField($additional_info,'date_from');
        $date_to    = Common::hashEmptyField($additional_info,'date_to');

        $date_info = $this->dateReportInfo($date_from, $date_to);

        if( !empty($top_selling_media) ) {
            $tmpTxt = __('Tingkatkan aktivitas marketing seperti Event & Newsletter untuk mempromosikan project anda.');
            $media_name = Common::hashEmptyField($top_selling_media, 'ViewBookingReport.media');
            $media_total = Common::hashEmptyField($top_selling_media, 'ViewBookingReport.cnt');

            $percentage = Common::_callTargetPercentage($media_total, $total);

            if(!empty($date_info)){
                $tips = __('Media penjualan paling banyak dilakukan pada %s sebesar %s dari %s. %s', $this->Html->tag('strong', $media_name, array(
                    'class' => 'bold',
                )), $this->Html->tag('strong', $percentage.'%', array(
                    'class' => 'bold',
                )), $date_info, $tmpTxt);
            }else{
                $tips = __('Media penjualan paling banyak dilakukan pada %s sebesar %s. %s', $this->Html->tag('strong', $media_name, array(
                    'class' => 'bold',
                )), $this->Html->tag('strong', $percentage.'%', array(
                    'class' => 'bold',
                )), $tmpTxt);
            }
                
        } else {
            if(!empty($date_info)){
                $tips = __('Tidak ada penjualan pada periode %s. Manfaatkan media Website & Event untuk mempromosikan project anda.', $date_info);
            }else{
                $tips = __('Tidak ada penjualan pada periode ini. Manfaatkan media Website & Event untuk mempromosikan project anda.');
            }
        }

        if( !empty($tips) ) {
            return $this->Html->tag('div', $this->Html->tag('li', $tips), array(
                'class' => 'wrapper-tips-top-selling-media hide',
            ));
        } else {
            return false;
        }
    }

    function _callTipsAgentCategory ( $values ) {
        $name = Common::hashEmptyField($values, 'name');
        $percentage = Common::hashEmptyField($values, 'value');
        $tmpTxt = __('Anda dapat mengisi/mengelompokan klien berdasarkan jenis klien %s.', $this->Html->link(__('disini'), array(
            'controller' => 'users',
            'action' => 'clients',
            'admin' => true,
        ), array(
            'target' => '_blank',
        )));

        if( !empty($name) ) {
            $tips = __('Jenis %s paling banyak yakni sebesar %s. ', $this->Html->tag('strong', $name, array(
                'class' => 'bold',
            )), $this->Html->tag('strong', $percentage.'%', array(
                'class' => 'bold',
            )));

            if( $percentage <= 30 ) {
                $tips .= $tmpTxt;
            }
        } else {
            $tips = __('Tidak ada data klien pada periode ini. %s', $tmpTxt);
        }

        if( !empty($tips) ) {
            return $this->Html->tag('div', $this->Html->tag('li', $tips), array(
                'class' => 'wrapper-tips-agent-category hide',
            ));
        } else {
            return false;
        }
    }

    function _callTipsPaymentMethod ( $values ) {
        $name = Common::hashEmptyField($values, 'name');
        $percentage = Common::hashEmptyField($values, 'value');
        $tmpTxt = __('Tingkatkan Aktivitas & Event, untuk mendapatkan leads & meningkatkan penjualan.');

        if( !empty($name) ) {
            $tips = __('Sebanyak %s klien menggunakan metode pembayaran %s. ', $this->Html->tag('strong', $percentage.'%', array(
                'class' => 'bold',
            )), $this->Html->tag('strong', $name, array(
                'class' => 'bold',
            )));

            if( $percentage <= 30 ) {
                $tips .= $tmpTxt;
            }
        } else {
            $tips = __('Tidak ada data klien pada periode ini. %s', $tmpTxt);
        }

        if( !empty($tips) ) {
            return $this->Html->tag('div', $this->Html->tag('li', $tips), array(
                'class' => 'wrapper-tips-payment-method hide',
            ));
        } else {
            return false;
        }
    }

    function dateReportInfo($date_from = false, $date_to = false){
        $result = '';

        if(!empty($date_from) || !empty($date_to)){
            if(!empty($date_from) && !empty($date_to)){
                $date_from  = Common::formatDate($date_from, 'd M Y');
                $date_to    = Common::formatDate($date_to, 'd M Y');

                $result = __('%s sampai %s', $date_from, $date_to);
            }else if(!empty($date_from)){
                $date_from  = Common::formatDate($date_from, 'd M Y');

                $result = __('dari %s', $date_from);
            }else if(!empty($date_to)){
                $date_to    = Common::formatDate($date_to, 'd M Y');

                $result = __('sampai %s', $date_to);
            }
        }

        return $result;
    }

    function _callTipsEbrosur ( $percentage, $prev_target, $additional_info = array(), $total_ebrosur = 0 ) {
        $date_from  = Common::hashEmptyField($additional_info,'date_from');
        $date_to    = Common::hashEmptyField($additional_info,'date_to');

        $date_info = $this->dateReportInfo($date_from, $date_to);

        $tmpTxt = __('Tingkatkan aktivitas pembuatan ebrosur untuk menunjang promosi dan penjualan listing Anda.');
        $tmpTxtOpening = __('Total pembuatan ebrosur mengalami');

        if( $percentage < 0 ) {
            $percentage = abs($percentage);

            if(!empty($prev_target)){
                if(!empty($date_info)){
                    $tips = __('%s penurunan %s pada periode %s. %s', $tmpTxtOpening, $this->Html->tag('strong', $percentage.'%', array(
                        'class' => 'cred',
                    )), $date_info, $tmpTxt);
                }else{
                    $tips = __('%s penurunan %s. %s', $tmpTxtOpening, $this->Html->tag('strong', $percentage.'%', array(
                        'class' => 'cred',
                    )), $tmpTxt);
                }
            }else{
                if(!empty($date_info)){
                    $tips = __('Tidak ada aktifitas pembuatan ebrosur pada periode %s. %s', $date_info, $tmpTxt);
                }else{
                    $tips = __('Tidak ada aktifitas pembuatan ebrosur pada periode. %s', $tmpTxt);
                }
            }
        } else if( $percentage > 0 ) {
            if(!empty($date_info)){
                $tips = __('%s peningkatan %s dari %s.', $tmpTxtOpening, $this->Html->tag('strong', $percentage.'%', array(
                    'class' => 'cgreen',
                )), $date_info);
            }else{
                $tips = __('%s peningkatan %s dari periode sebelumnya.', $tmpTxtOpening, $this->Html->tag('strong', $percentage.'%', array(
                    'class' => 'cgreen',
                )));
            }
                
        } else if( !is_numeric($percentage) ) {
            if(!empty($date_info)){
                $tips = __('Tidak ada aktifitas pembuatan ebrosur pada periode %s. %s', $date_info, $tmpTxt);
            }else{
                $tips = __('Tidak ada aktifitas pembuatan ebrosur pada periode ini. %s', $tmpTxt);
            }
        } else {
            if(!empty($total_ebrosur)){
                if(!empty($date_info)){
                    $tips = __('Anda telah membuat %s ebrosur pada periode %s.', $total_ebrosur, $date_info);
                }else{
                    $tips = __('Anda telah membuat %s ebrosur pada periode ini.', $total_ebrosur);
                }
            }else{
                if(!empty($date_info)){
                    $tips = __('Tidak ada aktifitas pembuatan ebrosur pada periode %s. %s', $date_info, $tmpTxt);
                }else{
                    $tips = __('Tidak ada aktifitas pembuatan ebrosur pada periode. %s', $tmpTxt);
                }
            }
                
        }

        if( !empty($tips) ) {
            return $this->Html->tag('div', $this->Html->tag('li', $tips), array(
                'class' => 'wrapper-tips-sales hide',
            ));
        } else {
            return false;
        }
    }

    function _callTipsTopAgen($name, $value = 0){
        if(!empty($name)){
            $tips = __('%s menjadi marketing terbaik periode ini dengan penjualan %s properti', $name, number_format($value));
        }else{
            $tips = __('Tidak ada top marketing pada periode ini. Tingkatkan aktivitas marketing seperti Event, Newsletter & informasi promo untuk menarik minat klien');
        }
            
        if( !empty($tips) ) {
            return $this->Html->tag('div', $this->Html->tag('li', $tips), array(
                'class' => 'wrapper-tips-top-sales hide',
            ));
        } else {
            return false;
        }
    }

    function callTipsDivision($tips = array()){
        $groupName = Common::hashEmptyField($tips, 'groupName');

        if($groupName){
            $tips = __('%s lebih mendominasi keaktifan menggunakan sistem dibandingkan dengan divisi lain.', $this->Html->tag('strong', $groupName));
        } else {
            $tips = __('Tidak ada aktivitas yang dilakukan oleh user. Mohon untuk dilakukan follow up penggunaan Primesystem serta keuntungan & kerugian apabila tdk ada aktivitas dilakukan.');
        }
        
        return $this->Html->tag('div', $this->Html->tag('li', $tips), array(
            'class' => 'wrapper-tips-usage-activity-division hide',
        ));
    }

    function callTipsDevice($tips = array()){
        $result = array();

        if($tips){
            foreach ($tips as $slug => $tip) {
                $percentage = Common::hashEmptyField($tip, 'percentage');
                $percentageCompare = Common::hashEmptyField($tip, 'percentageCompare');
                $text = Common::hashEmptyField($tip, 'text');
                $status = Common::hashEmptyField($tip, 'status');

                if($percentage > 70){
                    $case = __('pertahankan');
                } else {
                    $case = __('tingkatkan');
                }

                switch ($status) {
                    case 'up':
                        $case2 = __('selamat periode ini mengalami peningkatan');
                        break;
                    
                    case 'down':
                        $case2 = __('periode ini memburuk karena mengalami penurunan');
                        break;

                    case 'stuck':
                        $case2 = __('periode ini memburuk karena tidak mengalami perubahan');
                        break;
                }

                $val = __('Total yang menggunakan %s %s %s aktivitas di periode selanjutnya, %s dari periode sebelumnya sebesar %s%%', strtolower($slug), $text, $case, $case2, round($percentageCompare, 2));   
                $result[] = $this->Html->tag('li', $val);
            }
        } else {
            $result = array(
                $this->Html->tag('li', __('Tidak ada usage activity pada periode ini. Mohon untuk di follow up klien-klien yang usage-nya rendah.')),
            );
        }

        return $this->Html->tag('div', implode('', $result), array(
            'class' => 'wrapper-tips-usage-activity-device hide',
        ));
    }

    function callTipsOnTime($tips = array()){
        $tipsText = __('sangat baik jika diberikan informasi dan manfaat dalam melakukan aktivitas pada jam-jam tertentu untuk meningkatkan trafic dan leads.');

        if($tips){
            $time = Common::hashEmptyField($tips, 'time');
            $total = Common::hashEmptyField($tips, 'total');

            $total = $this->Rumahku->getConvertStringDecimal($total);

            $freeText = __('Waktu paling sibuk pada jam %s dengan total aktivitas %s, %s', $this->Html->tag('strong', $time), $this->Html->tag('strong', $total), $tipsText);
        } else {
            $freeText = __('Tidak ada usage activity untuk menghitung jam paling sibuk, %s', $tipsText);
        }

        return $this->Html->tag('div', $this->Html->tag('li', $freeText), array(
            'class' => 'wrapper-tips-usage-activity-time hide',
        ));
    }

    function callTipsModule($tips = array(), $parentCnt = 0, $totalParent = 0){
        $topActivity = Common::hashEmptyField($tips, 'topActivity');
        $topCompany = Common::hashEmptyField($tips, 'topCompany');
        $notActive = Common::hashEmptyField($tips, 'notActive');
        $result = array();

        // $result[] = $this->Html->tag('li', __('Terdapat %s kantor yang aktif menggunakan prime system dari total %s kantor agen yang terdaftar', $this->Html->tag('strong', $parentCnt), $this->Html->tag('strong', $totalParent)));

        if($topActivity){
            $title = Common::hashEmptyField($topActivity, 'title');
            $textCnt = Common::hashEmptyField($topActivity, 'textCnt');
            $cntParent = Common::hashEmptyField($topActivity, 'cntParent');
            $textCntParent = Common::hashEmptyField($topActivity, 'textCntParent');
            $totalParent = Common::hashEmptyField($topActivity, 'totalParent');

            $percentage = round($cntParent/$totalParent*100, 2);

            $freeText = __('Aktivitas tertinggi terdapat pada modul %s sebanyak %s dengan %s kantor agen yang aktif di dalamnya', $this->Html->tag('strong', $title), $textCnt, $textCntParent);

            if($percentage < 70){
                $freeText .= __(', terdapat ketidak seimbangan pada jumlah aktivitas dan jumlah kantor yang menggunakan modul tersebut. Harap berikan solusi');
            }

            // $freeText = __('Modul %s dengan aktivitas tertinggi %s total kantor agen yang menggunakan prime system sebanyak %s(%s%%)', $this->Html->tag('strong', $title), $textCnt, $textCntParent, $percentage);

            // if($percentage < 70){
            //     $freeText .= __(', memiliki aktivitas tertinggi tetapi sedikit kantor agen yang menggunakan prime system, mohon berikan solusi');
            // }


            $result[] = $this->Html->tag('li', $freeText);
        }

        if($topCompany){
            $title = Common::hashEmptyField($topCompany, 'title');
            $textCnt = Common::hashEmptyField($topCompany, 'textCnt');
            $textCntParent = Common::hashEmptyField($topCompany, 'textCntParent');

            $freeText = __('Modul %s dengan kantor agen terbanyak yang menggunakan prime system sebesar %s, tetapi aktivitas modul %s sedikit yaitu %s, periode selanjutnya follow up kantor agen untuk pakai prime system', $this->Html->tag('strong', $title), $textCntParent, $this->Html->tag('strong', $title), $textCnt);

            $result[] = $this->Html->tag('li', $freeText);
        }

        if($notActive){
            $cnt = Common::hashEmptyField($notActive, 'cnt');

            $freeText = __('Jumlah modul yang tidak pernah dipakai selama periode ini sebanyak %s, follow up kantor agen untuk periode selanjutnya', $this->Html->tag('strong', $cnt));

            $result[] = $this->Html->tag('li', $freeText);
        }

        if( empty($result) ) {
            $result = array(
                $this->Html->tag('li', __('Tidak ada usage activity module pada periode ini dari total %s kantor agen. Mohon untuk dianalisa dan diberikan feedback mengapa user tidak tidak menggunakan module Primesystem, apakah penggunaannya sudah, terlalu ribet atau kurang user friendly ?', $this->Html->tag('strong', $totalParent))),
            );
        }


        return $this->Html->tag('div', implode('', $result), array(
            'class' => 'wrapper-tips-usage-activity-module hide',
        ));
    }

    function _callTipsTopSearchArea($area){
        if(!empty($area)){
            $tips = __('area %s menjadi top area properti yang paling banyak dikunjungi', $area);
        }else{
            $tips = __('Tidak ada top area pada periode ini. Tingkatkan aktivitas marketing seperti Event, Newsletter & informasi promo untuk menarik minat klien');
        }
            
        if( !empty($tips) ) {
            return $this->Html->tag('div', $this->Html->tag('li', $tips), array(
                'class' => 'wrapper-tips-top-search-area hide',
            ));
        } else {
            return false;
        }
    }

    function _callTipsTopAreaKpr ( $values ) {
        $name = Common::hashEmptyField($values, 'name');
        $value = Common::hashEmptyField($values, 'value');
        $tmpTxt = __('Tingkatkan Aktivitas & Event, untuk mendapatkan leads & meningkatkan penjualan.');

        if( !empty($name) ) {
            $tips = __('Sebanyak %s Pengajuan KPR berasal dari area %s. ', $this->Html->tag('strong', $value, array(
                'class' => 'bold',
            )), $this->Html->tag('strong', $name, array(
                'class' => 'bold',
            )));
        } else {
            $tips = __('Tidak ada data Pengajuan KPR pada periode ini. %s', $tmpTxt);
        }

        if( !empty($tips) ) {
            return $this->Html->tag('div', $this->Html->tag('li', $tips), array(
                'class' => 'wrapper-tips-toparea-kpr hide',
            ));
        } else {
            return false;
        }
    }

    function _callTipsBankFavoriteKpr ( $values ) {
        $name = Common::hashEmptyField($values, 'name');
        $value = Common::hashEmptyField($values, 'value');
        $tmpTxt = __('Dapatkan pilihan Promo terbaik untuk KPR klien anda serta komisi special khusus untuk Agent Primesystem');

        if( !empty($name) ) {
            $tips = __('Aplikasi KPR paling banyak diajukan pada %s sebanyak %s Aplikasi', $this->Html->tag('strong', $name, array(
                'class' => 'bold',
            )), $this->Html->tag('strong', $value, array(
                'class' => 'bold',
            )));
        } else {
            $tips = __('Tidak ada data Pengajuan KPR pada periode ini. %s', $tmpTxt);
        }

        if( !empty($tips) ) {
            return $this->Html->tag('div', $this->Html->tag('li', $tips), array(
                'class' => 'wrapper-tips-bank-favorite-kpr hide',
            ));
        } else {
            return false;
        }
    }

    function _callTipsTopPriceKpr ( $values ) {
        $name = Common::hashEmptyField($values, 'name');
        $value = Common::hashEmptyField($values, 'value');

        if( !empty($name) ) {
            $tips = __('Rata-rata harga properti %s paling banyak diajukan KPR', $this->Html->tag('strong', $name, array(
                'class' => 'bold',
            )));
        }

        if( !empty($tips) ) {
            return $this->Html->tag('div', $this->Html->tag('li', $tips), array(
                'class' => 'wrapper-tips-top-price-kpr hide',
            ));
        } else {
            return false;
        }
    }

    function getTablePopUp($cnt = 0, $parentCnt = 0, $totalCompany = 0, $options){
        $notActive = $totalCompany - $parentCnt;

        $cnt = $this->Html->link($cnt, array_merge(array(
            'controller' => 'reports',
            'action' => 'activity_detail',
            'admin' => true,
        ), $options), array(
            'target' => 'blank',
        ));

        $parentCnt = $this->Html->link($parentCnt, array_merge(array(
            'controller' => 'reports',
            'action' => 'company_detail',
            'admin' => true,
        ), array_merge($options, array(
            'slug' => 'time-active',
        ))), array(
            'target' => 'blank',
        ));

        $notActive = $this->Html->link($notActive, array_merge(array(
            'controller' => 'reports',
            'action' => 'company_detail',
            'admin' => true,
        ), array_merge($options, array(
            'slug' => 'time-not-active',
        ))), array(
            'target' => 'blank',
        ));

        return '<div class="margin-vert-2 top-agent-list-item">
            <div class="top-agent-border">
                <div class="row">
                    <div class="col-md-12">
                        <span class="disblock fregular">Aktivitas</span>
                        <span class="disblock fregular">'.$cnt.'</span>
                        <span class="disblock fregular">Jumlah Kantor Aktif</span>
                        <span class="disblock fregular">'.$parentCnt.'</span>
                        <span class="disblock fregular">Jumlah Kantor Tidak Aktif</span>
                        <span class="disblock fregular">'.$notActive.'</span>
                    </div>  
                </div>
            </div>
        </div>';

        // return '<div id="table-advice" class="table-responsive"> <table class="table grey">
        //     <thead>
        //         <tr><th>Aktivitas</th><th>Perusahaan Aktif</th><th>Perusahaan Tidak Aktif</th></tr>
        //     </thead>
        //     <tbody>
        //         <tr>
        //             <td class="tacenter">'.$cnt.'</td>
        //             <td class="tacenter">'.$parentCnt.'</td>
        //             <td class="tacenter">'.$notActive.'</td>
        //         </tr>
        //     </tbody>
        // </table></div>';
    }

    function getDevice($slug = false){
        $result = false;

        if($slug == 'browser'){
            $result = 'Dekstop';
        } else if($slug == 'mobile'){
            $result = 'Mobile';
        }
        return $result;
    }

    function _callLabelShare ($value) {
        $type = Common::hashEmptyField($value, 'ShareLog.type');

        switch ($type) {
            case 'property':
                $mls_id = Common::hashEmptyField($value, 'Property.mls_id');
                $title = Common::hashEmptyField($value, 'Property.title');

                $label = __('#%s - %s', $mls_id, $title);
                break;
            case 'berita':
                $label = Common::hashEmptyField($value, 'Advice.title', '-');
                break;
            case 'developer':
                $label = Common::hashEmptyField($value, 'ApiAdvanceDeveloper.name', '-');
                break;
            case 'ebrosur':
                $label = Common::hashEmptyField($value, 'UserCompanyEbrochure.property_title', '-');
                break;
            default:
                $label = $type;
                break;
        }

        return ucwords($label);
    }
}