<?php

namespace solo\sprefix\command;

use pocketmine\command\CommandSender;

use solo\sprefix\SPrefix;
use solo\sprefix\SPrefixCommand;

class PluginPrefixCommand extends SPrefixCommand{

  private $owner;

  public function __construct(SPrefix $owner){
    parent::__construct("pluginprefix", "set plugin's prefix", "/pluginprefix <plugin> <prefix...>");
    $this->setPermission("sprefix.command.pluginprefix");

    $this->owner = $owner;
  }

  public function _execute(CommandSender $sender, string $label, array $args) : bool{
    if(!$sender->hasPermission($this->getPermission())){
      $sender->sendMessage(SPrefix::$prefix . "이 명령을 실행할 권한이 없습니다.");
      return true;
    }
    if(!isset($args[1])){
      $sender->sendMessage(SPrefix::$prefix . "사용법 : " . $this->getUsage() . " - " . $this->getDescription());
      $sender->sendMessage(SPrefix::$prefix . "<prefix> 를 null로 입력시 prefix 설정값을 해제합니다.");
      return true;
    }
    $find = str_replace("_", " ", array_shift($args));
    $plugin = $this->owner->getServer()->getPluginManager()->getPlugin($find);
    if($plugin === null){
      $sender->sendMessage(SPrefix::$prefix . "플러그인 \"" . $find . "\" 을 찾을 수 없습니다.");
      return true;
    }
    $prefix = implode(" ", $args);
    $this->owner->setPluginPrefix($plugin, $prefix === "null" ? null : $prefix);
    $this->owner->updatePrefix();
    $this->owner->save();
    $sender->sendMessage(SPrefix::$prefix . $plugin->getName() . " 플러그인의 Prefix를 \"" . $prefix . "\" 으로 설정하였습니다.");
    return true;
  }
}
