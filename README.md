# 🌱 PropertyManager

A simple and lightweight PocketMine-MP plugin that allows you to **visually change Minecraft Bedrock Edition game rules** in real time.

> ✨ **Note:**  
> This plugin **only sends game rule packets to the client.**  
> It does not affect the actual server-side game logic or mechanics.

---

## 📦 Features
- 🔄 Change game rules on the fly using `/gamerule` command.
- 🛠️ Supports both **server-wide** and **per-player** settings.
- 💬 Friendly command usage with syntax validation.
- 🎮 Real-time visual changes on the player’s screen.

---

## ⚙️ Example Configuration

```yaml
World-Options:
  Friendly-Fire: true
  Show-Coordinates: false
  Locator-Bar: false
  Show-Days-Played: false
  Fire-Spreads: true
  Recipes-Unlock: true
  TNT-Explodes: true
  Respawn-Blocks-Explode: true
  Mob-Loot: true
  Natural-Regeneration: false
  Tile-Drops: true
  Beds-Work: true
  Immediate-Respawn: false
