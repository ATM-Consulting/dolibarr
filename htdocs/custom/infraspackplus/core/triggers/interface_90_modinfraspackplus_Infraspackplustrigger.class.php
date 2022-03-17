<?php
	/************************************************
	* Copyright (C) 2016-2022	Sylvain Legrand - <contact@infras.fr>	InfraS - <https://www.infras.fr>
	*
	* This program is free software: you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation, either version 3 of the License, or
	* (at your option) any later version.
	*
	* This program is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with this program.  If not, see <http://www.gnu.org/licenses/>.
	************************************************/

	/************************************************
	* 	\file		../infraspackplus/core/triggers/interface_90_modinfraspackplus_Infraspackplustrigger.class.php
	* 	\ingroup	InfraS
	* 	\brief		Trigger for the module InfraS
	************************************************/

	// Libraries ************************************
	dol_include_once('/infraspackplus/core/lib/infraspackplusAdmin.lib.php');

	// Description and activation class *************
	class InterfaceInfraspackplustrigger extends DolibarrTriggers
	{
		protected $db;	// Database handler @var DoliDB
		public $name				= '';	// Name of the trigger @var mixed|string
		public $description			= '';	// Description of the trigger @var string
		public $version				= self::VERSION_DEVELOPMENT;	// Version of the trigger @var string
		public $picto				= 'technic';	// Image of the trigger @var string
		public $family				= '';	// Category of the trigger @var string
		public $errors				= array();	// Errors reported by the trigger @var array
		const VERSION_DEVELOPMENT	= 'development';	// @var string module is in development
		const VERSION_EXPERIMENTAL	= 'experimental';	// @var string module is experimental
		const VERSION_DOLIBARR		= 'dolibarr';	// @var string module is dolibarr ready

		/**
		* Constructor
		*
		* 	@param		DoliDB		$db		Database handler
		*/
		public function __construct($db)
		{
			global $langs, $conf;

			$langs->load('infraspackplus@infraspackplus');

			$this->db			= $db;
			$this->name			= preg_replace('/^Interface/i', '', get_class($this));
			$this->family		= 'Modules '.$langs->trans('basename');
			$this->description	= $langs->trans('Module550000DescTrigger');
			$currentversion		= infraspackplus_getLocalVersionMinDoli('infraspackplus');
			$this->version		= $currentversion[0];	// 'development', 'experimental', 'dolibarr' or version
			$this->picto		= 'infraspackplus@infraspackplus';
		}

		/**
		* Trigger name
		*
		* 	@return		string	Name of trigger file
		*/
		public function getName()
		{
			return $this->name;
		}

		/**
		* Trigger description
		*
		* 	@return		string	Description of trigger file
		*/
		public function getDesc()
		{
			return $this->description;
		}

		/**
		* Trigger version
		*
		* 	@return		string	Version of trigger file
		*/
		public function getVersion()
		{
			global $langs;
			$langs->load('admin');

			if ($this->version == 'development')		return $langs->trans('Development');
			elseif ($this->version == 'experimental')	return $langs->trans('Experimental');
			elseif ($this->version == 'dolibarr')		return DOL_VERSION;
			elseif ($this->version)						return $this->version;
			else										return $langs->trans('Unknown');
		}

		/**
		* Function called when a Dolibarrr business event is done.
		* All functions "run_trigger" are triggered if file
		* is inside directory core/triggers
		*
		* 	@param		string		$action		Event action code
		* 	@param		Object		$object		Object
		* 	@param		User		$user		Object user
		* 	@param		Translate	$langs		Object langs
		* 	@param		conf		$conf		Object conf
		* 	@return		int						<0 if KO, 0 if no triggered ran, >0 if OK
		*/
		public function runTrigger($action, $object, User $user, Translate $langs, Conf $conf)
		{
			$ParamLogoEmet	= isset($conf->global->INFRASPLUS_PDF_SET_LOGO_EMET_TIERS)	? $conf->global->INFRASPLUS_PDF_SET_LOGO_EMET_TIERS : 0;
			if ($action == 'COMPANY_CREATE' && $ParamLogoEmet) {
				dol_include_once('/infraspackplus/core/lib/infraspackplus.lib.php');
				$updateLogoEmet	= infraspackplus_setLogoEmet($object->id, GETPOST('logosChoice', 'alpha'));
				if ($updateLogoEmet) {
					$this->errors = $updateLogoEmet;
					return -1;
				}
			}
			return 0;
		}
	}
?>