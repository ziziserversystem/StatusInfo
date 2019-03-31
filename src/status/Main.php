<?php

namespace status;

use pocketmine\Server;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\scheduler\Task;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use onebone\economyapi\EconomyAPI;
use pocketmine\plugin\PluginBase;
use MixCoinSystem\MixCoinSystem;

class Main extends PluginBase implements Listener{

	public function onEnable(){ 
		date_default_timezone_set('Asia/Tokyo');
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
           $this->getScheduler()->scheduleRepeatingTask(new Send($this), 5);
        if (!file_exists($this->getDataFolder())) {
            @mkdir($this->getDataFolder(), 0744, true);
        }
        $this->world = new Config($this->getDataFolder() . "spawnworld.yml", Config::YAML,array('JoinWorld' => 'world'));
        $this->Item = new Config($this->getDataFolder() . "SetItem.yml", Config::YAML,array('スポーン地点アイテム' => '280'));

		$this->getLogger()->notice(TextFormat::GOLD."statusscreen-ver.7.5.1を読み込みました。 by mixpowder");
		$this->api = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
        if($this->api == null){
		$this->getLogger()->error("EconomyAPIが見つかりません　サーバーを停止中");
		$this->getServer()->shutdown();
		}else{
		$this->getLogger()->info(TextFormat::DARK_AQUA."EconomyAPIを見つけました。");
		}
    }
}


class Send extends Task{

	public function onRun(int $tick){
		foreach(Server::getInstance()->getOnlinePlayers() as $player){
			$x = floor($player->getX());
        	$y = floor($player->getY());
        	$z = floor($player->getZ());                       
			$name = $player->getName();
			$money = EconomyAPI::getInstance()->myMoney($name);
			$p = count($player->getServer()->getOnlinePlayers());
			$full = $player->getServer()->getMaxPlayers();
			$item = $player->getInventory()->getItemInHand();
			$id = $item->getId();
			$meta = $item->getDamage();
			$ping = $player->getPing();
			$load = $player->getServer()->getTickUsage();
			$time = date("G時i分");
			$coin = MixCoinSystem::getInstance()->GetCoin($name);
			switch ($player->getDirection()){

            case 0:
				$dire = "東";
			break;

			case 1:
				$dire = "南";
			break;

			case 2:
				$dire = "西";
			break;

			case 3:
				$dire = "北";
			break;
			}
			
            $player->sendTip("§b\n                                                                         ".TextFormat::GREEN."\n                                                                       【{$name}さんのステータス】        §a\n§e                                                                       座標: {$x},{$y},{$z},\n§e                                                                       方位: {$dire}§b\n                                                                       オンライン数: {$p}/{$full}§b\n                                                                       対応速度: {$ping}m/s§b\n                                                                       load率: {$load}％§d\n                                                                       所持金: $".$money."§d\n                                                                       所有コイン: {$coin}§6\n                                                                       時刻: {$time}§f\n                                                                       アイテムID: {$id}:{$meta}");        
                }
	}
}
