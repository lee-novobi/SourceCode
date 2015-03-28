<?php
global $arrDefined;
$arrDefined['location'][]               = 'HL';
$arrDefined['location'][]               = 'QT';
$arrDefined['department_special'] = array('TGM', 'GMT', 'GDM');
$arrDefined['custom_view']['gui'] = array(
	CI_SERVER => array(
		'col'          => CUSTOM_VIEW_SERVER_COL,
		'group_order'  => array(
			STR_SERVER_GROUP_BASIC_INFO         => array('group_index' => 0, 'group_col'  => 0, 'group_row'  => 0),
			STR_SERVER_GROUP_LOCATION_INFO      => array('group_index' => 1, 'group_col'  => 1, 'group_row'  => 0),
			STR_SERVER_GROUP_CONFIGURATION_INFO => array('group_index' => 2, 'group_col'  => 1, 'group_row'  => 1),
			STR_SERVER_GROUP_NETWORK_INFO       => array('group_index' => 3, 'group_col'  => 2, 'group_row'  => 0),
			STR_SERVER_GROUP_SECURITY_INFO      => array('group_index' => 4, 'group_col'  => 2, 'group_row'  => 1),
			STR_SERVER_GROUP_OPERATION_INFO     => array('group_index' => 5, 'group_col'  => 2, 'group_row'  => 2),
		)
	),
	CI_PRODUCT => array(
		'col'          => CUSTOM_VIEW_PRODUCT_COL,
		'group_order'  => array(
			STR_SERVER_GROUP_BASIC_INFO         => array('group_index' => 0, 'group_col'  => 0, 'group_row'  => 0),
			STR_SERVER_GROUP_OPERATION_INFO     => array('group_index' => 1, 'group_col'  => 0, 'group_row'  => 1),
		)
	),
	CI_DEPARTMENT => array(
		'col'          => CUSTOM_VIEW_DEPARTMENT_COL,
		'group_order'  => array(
			STR_SERVER_GROUP_BASIC_INFO         => array('group_index' => 0, 'group_col'  => 0, 'group_row'  => 0),
			STR_SERVER_GROUP_OPERATION_INFO     => array('group_index' => 1, 'group_col'  => 0, 'group_row'  => 1),
		)
	),
	CI_DIVISION => array(
		'col'          => CUSTOM_VIEW_DIVISION_COL,
		'group_order'  => array(
			STR_SERVER_GROUP_BASIC_INFO         => array('group_index' => 0, 'group_col'  => 0, 'group_row'  => 0),
			STR_SERVER_GROUP_OPERATION_INFO     => array('group_index' => 1, 'group_col'  => 0, 'group_row'  => 1),
		)
	)
);
$arrDefined['ignore_ci_field'] = array(
	CI_PRODUCT => array(
		'meta_fields'    => array('_id', 'deleted'),
		'private_fields' => array('division_id', 'department_id')
	),
	CI_SERVER => array(
		'meta_fields'    => array('_id', 'deleted'),
		'private_fields' => array('vlan', 'technical_group_id',	'product_id', 'rack_id', 'chassis_id')
	),
	CI_DEPARTMENT => array(
		'meta_fields'    => array('_id', 'deleted'),
		'private_fields' => array('division_id')
	),
	CI_DIVISION => array(
		'meta_fields'    => array('_id', 'deleted'),
		'private_fields' => array()
	)
);
$arrDefined['ci_field_display_filter'] = array(
	CI_SERVER => array(
		'server_type'       => 'DisplayFilter_ServerType',
		'interface'         => 'DisplayFilter_ServerInterface',
		'private_interface' => 'DisplayFilter_ServerInterface',
		'public_interface'  => 'DisplayFilter_ServerInterface',
		'status'            => 'DisplayFilter_ServerStatus',
		'power_status'      => 'DisplayFilter_ServerPowerStatus',
		'memory_size'       => 'DisplayFilter_ServerMemorySize',
		'created_date'      => 'DisplayFilter_ServerFromUnixTime',
		'last_updated'      => 'DisplayFilter_ServerFromUnixTime',
		//'note'      		=> 'DisplayFilter_ServerNote'
	),
	CI_PRODUCT => array(
		'status'            => 'DisplayFilter_ProductStatus',
	),
	CI_DEPARTMENT => array(
		'status'            => 'DisplayFilter_DepartmentStatus',
	),
	CI_DIVISION => array(
		'status'            => 'DisplayFilter_DivisionStatus',
	)
);
$arrDefined['field_value_to_text'] = array(
	'ci' => array(
		CI_SERVER => array(
			'status' => array(
				VALUE_SERVER_STATUS_UNUSED        => STR_SERVER_STATUS_UNUSED,
				VALUE_SERVER_STATUS_IN_USED       => STR_SERVER_STATUS_IN_USED,
				VALUE_SERVER_STATUS_BORROW        => STR_SERVER_STATUS_BORROW,
				VALUE_SERVER_STATUS_TRANSFER      => STR_SERVER_STATUS_TRANSFER,
				VALUE_SERVER_STATUS_ERROR         => STR_SERVER_STATUS_ERROR
			),
			'power_status' => array(
				VALUE_SERVER_POWER_STATUS_OFF     => STR_SERVER_POWER_STATUS_ON,
				VALUE_SERVER_POWER_STATUS_ON      => STR_SERVER_POWER_STATUS_OFF,
				VALUE_SERVER_POWER_STATUS_UNKNOWN => STR_SERVER_POWER_STATUS_UNKNOWN,
			),
			'server_type' => array(
				VALUE_SERVER_TYPE_VIRTUAL         => STR_SERVER_TYPE_VIRTUAL,
				VALUE_SERVER_TYPE_U               => STR_SERVER_TYPE_U,
				VALUE_SERVER_TYPE_CHASSIS         => STR_SERVER_TYPE_CHASSIS,
				VALUE_SERVER_TYPE_UNKNOWN         => STR_SERVER_TYPE_UNKNOWN,
			)
		),
		CI_PRODUCT => array(
			'status' => array(
				VALUE_PRODUCT_STATUS_NEW         => STR_PRODUCT_STATUS_NEW,
				VALUE_PRODUCT_STATUS_IN_USED     => STR_PRODUCT_STATUS_IN_USED,
				VALUE_PRODUCT_STATUS_TRANSFERRING => STR_PRODUCT_STATUS_TRANSFERRING,
				VALUE_PRODUCT_STATUS_REMOVED     => STR_PRODUCT_STATUS_REMOVED,
				VALUE_PRODUCT_STATUS_CLOSED      => STR_PRODUCT_STATUS_CLOSED
			)
		),
		CI_DEPARTMENT => array(
			'status' => array(
				VALUE_DEPARTMENT_STATUS_INACTIVE => STR_DEPARTMENT_STATUS_INACTIVE,
				VALUE_DEPARTMENT_STATUS_ACTIVE   => STR_DEPARTMENT_STATUS_ACTIVE
			)
		),
		CI_DIVISION => array(
			'status' => array(
				VALUE_DIVISION_STATUS_INACTIVE   => STR_DIVISION_STATUS_INACTIVE,
				VALUE_DIVISION_STATUS_ACTIVE     => STR_DIVISION_STATUS_ACTIVE
			)
		)
	)
);
$arrDefined['ci_detail_display_setting'] = array(
	CI_SERVER => array(
		'display_type' => CI_DETAIL_DISPLAY_TYPE_TABS,
		'groups_icon' => array(
			STR_SERVER_GROUP_BASIC_INFO          => 'icon-save',
			STR_SERVER_GROUP_OPERATION_INFO      => '',
			STR_SERVER_GROUP_LOCATION_INFO       => '',
			STR_SERVER_GROUP_CONFIGURATION_INFO  => '',
			STR_SERVER_GROUP_NETWORK_INFO        => '',
			STR_SERVER_GROUP_SECURITY_INFO       => ''
		)
	),
	CI_PRODUCT => array(
		'display_type' => CI_DETAIL_DISPLAY_TYPE_TABS,
		'groups_icon' => array(
			STR_SERVER_GROUP_BASIC_INFO          => '',
			STR_SERVER_GROUP_OPERATION_INFO      => ''
		)
	),
	CI_DEPARTMENT => array(
		'display_type' => CI_DETAIL_DISPLAY_TYPE_TABS,
		'groups_icon' => array(
			STR_SERVER_GROUP_BASIC_INFO          => '',
			STR_SERVER_GROUP_OPERATION_INFO      => ''
		)
	),
	CI_DIVISION => array(
		'display_type' => CI_DETAIL_DISPLAY_TYPE_TABS,
		'groups_icon' => array(
			STR_SERVER_GROUP_BASIC_INFO          => '',
			STR_SERVER_GROUP_OPERATION_INFO      => ''
		)
	)
);
?>