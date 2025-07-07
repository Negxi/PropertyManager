<?php

/** 
 * 
 * @author      Negxi
 * @license     MIT License
 * @link        https://github.com/Negxi/PropertyManager (optional)               
 *    _____                 _ 
 *   |   | | ___  ___  _ _ |_|
 *   | | | || -_|| . ||_'_|| |
 *   |_|___||___||_  ||_,_||_|
 *               |___|           ( ･∀･)っ🌱< ネギ！
 * 
 *  This file is part of the PropertyManager plugin.         
 *  You are free to use, modify, and distribute this file as long as you include this notice. 
 */

namespace Negxi;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\types\BoolGameRule;
use pocketmine\plugin\PluginBase;

class First extends PluginBase implements Listener
{
    private static ?self $instance = null;
    private array $worldOptions = [];

    public static function getInstance() : ?self{
		return self::$instance;
	}

    protected function onLoad(): void {
        self::$instance = $this;
    }

    protected function onEnable(): void
    {
        $this->saveDefaultConfig();

        $this->worldOptions = $this->getConfig()->get('World-Options') ?? [];

        $this->getServer()->getCommandMap()->register('Negxi', new GameruleCmd());
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function WhenPlayerJoin(PlayerJoinEvent $ev): void
    {
        $player = $ev->getPlayer();
        if ($player === null) return;

        $gameRules = [
            "showcoordinates" => new BoolGameRule($this->worldOptions['Show-Coordinates'] ?? false, false),
            "friendlyfire" => new BoolGameRule($this->worldOptions['Friendly-Fire'] ?? true, false),
            "showdaysplayed" => new BoolGameRule($this->worldOptions['Show-Days-Played'] ?? false, false),
            "firespreads" => new BoolGameRule($this->worldOptions['Fire-Spreads'] ?? true, false),
            "tntexplodes" => new BoolGameRule($this->worldOptions['TNT-Explodes'] ?? true, false),
            "naturalregeneration" => new BoolGameRule($this->worldOptions['Natural-Regeneration'] ?? false, false),
            "mobsloot" => new BoolGameRule($this->worldOptions['Mob-Loot'] ?? true, false),
            "tiledrops" => new BoolGameRule($this->worldOptions['Tile-Drops'] ?? true, false),
            "bedworks" => new BoolGameRule($this->worldOptions['Beds-Work'] ?? true, false),
            "immediaterespawn" => new BoolGameRule($this->worldOptions['Immediate-Respawn'] ?? false, false),
            "locatorbar" => new BoolGameRule($this->worldOptions['Locator-Bar'] ?? false, false),
            "respawnblocksexplode" => new BoolGameRule($this->worldOptions['Respawn-Blocks-Explode'] ?? true, false),
            "recipesunlock" => new BoolGameRule($this->worldOptions['Recipes-Unlock'] ?? true, false)
        ];

        $packet = GameRulesChangedPacket::create($gameRules);
        $player->getNetworkSession()->sendDataPacket($packet);
    }
}