<?php
	class ModuleManager
	{
		private $Caller = null;
		private $Modules = null;

		public function __construct(WhatsBotCaller &$Caller) //WhatsProt &$Whatsapp)
		{
			$this->Caller = &$Caller;

			$this->Modules = array();
		}

		public function LoadModules()
		{
			$Modules = file_get_contents('config/Modules.json');
			$Modules = json_decode($Modules, true)['modules']['commands'];

			foreach($Modules as $Module)
			{
				$this->LoadModule($Module);
			}
		}

		private function LoadModule($Name) // public for !load or !reload
		{
			$Filename = "class/modules/{$Name}.json";

			if(is_file($Filename))
			{
				$Data = file_get_contents($Filename);
				$Data = json_decode($Data, true);

				$this->Modules[$Name] = array
				(
					'help' => $Data['help'],
					'version' => $Data['version'],
					'code' => $Data['code']
				);

				return true;
			}

			return false;
		}

		public function CallModule($Name, $Params, $From, $Original, $Data)
		{
			if(isset($this->Modules[strtolower($Name)]))
				return $this->Caller->CallModule($this->Modules[strtolower($Name)]['code'], $Name, $Params, $From, $Original, $Data);
			else
				return false;
		}

		public function ModuleExists($Name)
		{
			return isset($this->Modules[$Name]);
		}

		public function GetModules()
		{
			return array_keys($this->Modules);
		}

		public function GetModuleHelp($Name)
		{
			if(isset($this->Modules[$Name]))
				return $this->Modules[$Name]['help'];

			return false;
		}

		public function LoadIncludes()
		{
			$Includes = file_get_contents('config/Modules.json');
			$Includes = json_decode($Includes, true)['includes'];

			foreach($Includes as $Include)
			{
				$this->LoadInclude($Include);
			}
		}

		private function LoadInclude($Path)
		{
			if(is_file($Path))
			{
				include($Path);

				return true;
			}

			return false;
		}
	}

	/*
	 * To do: 
	 * 
	 * UpdateModules
	 * UpdateModule
	 * 
	 * GetModule_* (all)
	 * GetModuleCode
	 * GetModuleVersion
	 */