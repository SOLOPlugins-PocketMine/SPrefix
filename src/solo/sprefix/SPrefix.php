<?php

namespace solo\sprefix;

use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;

class SPrefix extends PluginBase{

  public static $prefix = "§l§b[SPrefix] §r§7";

  private $prefixSetting;

  public function onEnable(){
    @mkdir($this->getDataFolder());

    if(file_exists($this->getDataFolder() . "prefix.json")){
      $this->prefixSetting = json_decode(file_get_contents($this->getDataFolder() . "prefix.json"));
    }else{
      $this->prefixSetting = ["plugins" => []];
    }

    foreach([
      "DefaultPrefixCommand",
      "PluginPrefixCommand"
    ] as $class){
      $class = "\\solo\\sprefix\\command\\" . $class;
      $this->getServer()->getCommandMap()->register("sprefix", new $class($this));
    }
  }

  public function save(){
    file_put_contents($this->getDataFolder() . "prefix.json", json_encode($this->prefixSetting));
  }

  public function getDefaultPrefix(){
    return $this->prefixSetting["default"] ?? null;
  }

  public function setDefaultPrefix(string $prefix = null){
    $this->prefixSetting["default"] = $prefix;
  }

  public function getPluginPrefix(Plugin $plugin){
    return $this->prefixSetting["plugins"][$plugin->getName()] ?? null;
  }

  public function setPluginPrefix(Plugin $plugin, string $prefix = null){
    if($prefix === null){
      unset($this->prefixSetting["plugin"][$plugin->getName()]);
      return;
    }
    $this->prefixSetting["plugins"][$plugin->getName()] = $prefix;
  }

  public function updatePrefix(){
    foreach($this->getServer()->getPluginManager()->getPlugins() as $plugin){
    	$ref = new \ReflectionClass(get_class($plugin));
    	try{
    		$property = $ref->getProperty("prefix");
    	}catch(\Throwable $e){
    		continue;
    	}
      $prefix = $this->getPluginPrefix($plugin);
      if($prefix === null){
        $prefix = $this->getDefaultPrefix();
        if($prefix === null){
          continue;
        }
      }
    	$property->setValue($plugin, $prefix);
    }
  }
}
