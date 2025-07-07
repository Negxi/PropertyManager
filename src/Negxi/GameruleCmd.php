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

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\types\BoolGameRule;

class GameruleCmd extends Command
{
    private array $supportedGameRules = [
        "showcoordinates" => "Show-Coordinates",
        "friendlyfire" => "Friendly-Fire",
        "showdaysplayed" => "Show-Days-Played",
        "firespreads" => "Fire-Spreads",
        "tntexplodes" => "TNT-Explodes",
        "naturalregeneration" => "Natural-Regeneration",
        "mobsloot" => "Mob-Loot",
        "tiledrops" => "Tile-Drops",
        "bedworks" => "Beds-Work",
        "immediaterespawn" => "Immediate-Respawn",
        "locatorbar" => "Locator-Bar",
        "respawnblocksexplode" => "Respawn-Blocks-Explode",
        "recipesunlock" => "Recipes-Unlock"
    ];

    public function __construct()
    {
        parent::__construct("gamerule", "Sets or querires a game rule value.");
        $this->setPermission("gamerule.permission");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (count($args) !== 3) {
            $sender->sendMessage("§cUsage: /gamerule <rule> <true|false> <server|self>");
            return;
        }

        [$rule, $valueStr, $target] = $args;

        $value = filter_var($valueStr, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if (!is_bool($value)) {
            $sender->sendMessage('§cSyntax error: Unexpected "' . $args[1] . '": at "' . $rule . ' >>' . $args[1] . '<<"');
            return;
        }

        $packetKey = strtolower(str_replace(["-", "_"], "", $rule));

        if (!array_key_exists($packetKey, $this->supportedGameRules)) {
            $sender->sendMessage('§cSyntax error: Unexpected "' . $rule . '": at "/gamerule >>' . $rule . '<< ' . $value . '"');
            return;
        }

        if ($target === "server") {
            $this->handleServerChange($sender, $rule, $packetKey, $value);
        } elseif ($target === "self") {
            if (!$sender instanceof Player) {
                $sender->sendMessage("§cThis command can only be used by players.");
                return;
            }
            $this->sendGamerulePacket($sender, $packetKey, $value);
            $sender->sendMessage("§fYour gamerule '{$rule}' has been updated to " . ($value ? "true" : "false") . ".");
        } else {
            $sender->sendMessage("§cInvalid target. Use 'server' or 'self'.");
        }
    }

    private function handleServerChange(CommandSender $sender, string $rule, string $packetKey, bool $value): void
    {
        $plugin = First::getInstance();
        $config = $plugin->getConfig();

        $configKey = $this->supportedGameRules[$packetKey];

        $config->setNested("World-Options." . $configKey, $value);
        $config->save();

        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            $this->sendGamerulePacket($player, $packetKey, $value);
        }

        $sender->sendMessage("§fGamerule {$rule} has been updated to " . ($value ? "true" : "false"));
    }


    private function sendGamerulePacket(Player $player, string $rule, bool $value): void
    {
        $packet = GameRulesChangedPacket::create([
            $rule => new BoolGameRule($value, false)
        ]);
        $player->getNetworkSession()->sendDataPacket($packet);
    }
}
