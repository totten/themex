<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 5                                                  |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2017                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
 */

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2017
 * $Id$
 *
 */

/**
 * Settings metadata file
 */
return array(
  'theme_frontend' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'theme_frontend',
    'type' => 'String',
    'quick_form_type' => 'Select',
    'html_type' => 'Select',
    'html_attributes' => array(
      'class' => 'crm-select2',
    ),
    'pseudoconstant' => array(
      'callback' => 'call://themes/getAvailable',
    ),
    'default' => 'default',
    'add' => '5.8',
    'title' => ts('Frontend Theme'),
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => ts('Theme to use on frontend pages'),
    'help_text' => NULL,
  ),
  'theme_backend' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'core',
    'name' => 'theme_backend',
    'type' => 'String',
    'quick_form_type' => 'Select',
    'html_type' => 'Select',
    'html_attributes' => array(
      'class' => 'crm-select2',
    ),
    'pseudoconstant' => array(
      'callback' => 'call://themes/getAvailable',
    ),
    'default' => 'default',
    'add' => '5.8',
    'title' => ts('Backend Theme'),
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => ts('Theme to use on backend pages'),
    'help_text' => NULL,
  ),
);
