<?php

namespace PlayerInfo;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use UiLibrary\UiLibrary;

class PlayerInfo extends PluginBase implements Listener {

    private static $instance = null;
    public $pre = "§e•";
    public $num = 10000;

    public static function getInstance() {
        return self::$instance;
    }

    public function onLoad() {
        self::$instance = $this;
    }

    public function onEnable() {
        date_default_timezone_set("Asia/Seoul");
        $this->ui = UiLibrary::getInstance();
        @mkdir($this->getDataPath());
        $this->config = new Config($this->getDataPath() . "config.yml", Config::YAML, ["lastNumber" => 10000, "player" => []]);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->num = $this->getLastNumber();
    }

    public function getDataPath() {
        return "/home/Data/PlayerInfo/";
        //return "C:/Users/mial7/Downloads/root/Server/Data/PlayerInfo/";
    }

    //자살

    public function getLastNumber() {
        $data = (new Config($this->getDataPath() . "config.yml", Config::YAML))->getAll();
        return $data["lastNumber"];
    }

    public function onJoin(PlayerJoinEvent $ev) {
        $player = $ev->getPlayer();
        if (!$this->isRegistered($player->getName())) {
            $this->register($player->getName());
        }
    }

    public function isRegistered(string $name) {
        $name = mb_strtolower($name);
        $data = (new Config($this->getDataPath() . "config.yml", Config::YAML))->getAll();
        return isset($data["player"][$name]);
    }

    public function register(string $name) {
        $name = mb_strtolower($name);
        $config = new Config($this->getDataPath() . "config.yml", Config::YAML);
        $data = $config->getAll();
        $data["player"][$name]["시리얼넘버"] = $this->num++;
        $data["player"][$name]["최초가입일"] = date("Y년 m월 d일", microtime(true));
        $data["lastNumber"]++;
        $config->setAll($data);
        $config->save();
    }

    public function PlayerInfoUI(Player $player) {
        $form = $this->ui->SimpleForm(function (Player $player, array $data) {
        });
        $form->setTitle("Tele PlayerInfo");
        $form->setContent($this->getInfo($player->getName()));
        $form->sendToPlayer($player);
    }

    private function getInfo(string $name) {
        $name = mb_strtolower($name);
        $text = "";
        $text .= "§l§c▶ §r§f닉네임 : {$name}\n";
        $text .= "§l§6▶ §r§f고유번호 : {$this->getNumber($name)}\n";
        $text .= "§l§e▶ §r§f최초 가입일 : {$this->getTime($name)}\n";
        $text .= "\n";
        $text .= "§l§a▶ §r§f배경음악 정보\n";
        $text .= "  - 해당 배경음악, 'Peritune - Sakuya2' 는\n    창작 공유 라이센스(CC-BY)를 준수하며,\n";
        $text .= "    BreakingCopyright에 의해 홍보되었습니다.\n    (https://youtu.be/2QSW80F7xfk)\n";
        $text .= "\n";
        $text .= "  - Song: 'Peritune - Sakuya2' is under\n    a Creative Commons license (CC-BY).\n";
        $text .= "    Music promoted by\n    BreakingCopyright.\n    (https://youtu.be/2QSW80F7xfk)";
        return $text;
    }

    public function getNumber(string $name) {
        $name = mb_strtolower($name);
        $data = (new Config($this->getDataPath() . "config.yml", Config::YAML))->getAll();
        return $data["player"][$name]["시리얼넘버"];
    }

    public function getTime(string $name) {
        $name = mb_strtolower($name);
        $data = (new Config($this->getDataPath() . "config.yml", Config::YAML))->getAll();
        return $data["player"][$name]["최초가입일"];
    }
}
