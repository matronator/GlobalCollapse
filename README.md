![Global Collapse: Pandemic](dev/front/images/logo-black.png)

<div style="display:flex;align-items:center;justify-content:flex-start;flex-wrap:wrap;height:36px;"><a href="https://ko-fi.com/U7U2MDBC" target="_blank" style="margin-right:16px;"><img height="36" style="border:0px;height:36px;" src="https://az743702.vo.msecnd.net/cdn/kofi2.png?v=2" alt="Buy Me a Coffee at ko-fi.com"></a>
<a href="https://www.paypal.me/matronator" target="_blank" style="margin-right:16px;"><img src=".github/paypal.png" height="36"></a>
<a href="https://www.blockchain.com/btc/payment_request?address=35VRpVQaqFWjUCnVRpGineShz76QyYgSVg" target="_blank"><img src=".github/RibbonDonateBitcoin.png" height="36"></a></div>

<a href="https://www.best-mmorpgs.com/" target="_blank">Vote on Best MMORPGs<img src="https://www.best-mmorpgs.com/button.php?u=matronator" alt="Vote on Best MMORPGs" style="display: none;" width="1px" height="1px" /></a>

#### Notice: If you like this project, please consider donating. The battery on my macbook died and I need to get it replaced to be able to work, but a battery replacement for macbook is kinda expensive and money's little tight right now. Any help would be much appretiated! Thank you :)


# Global Collapse: Pandemic (Now PWA compatible!)

### Deadly virus is spreading across the globe. Will you survive?

New deadly strain of coronavirus is spreading across the entire planet. The world economy is in ruins and governments are collapsing. Join now to see if you can survive in this dystopian future.

Join now at https://global-collapse.com

MMORPG Persistent Browser Based Game (PBBG) set in an alternate near future in where the COVID-19 mutated and wiped most of the human population. Made with [Nette](www.nette.org) Framework.

## Features

Still very early access, so much of the features are still yet to come.

- [x] PWA
  - [x] Players can add the site to their home screen and have it behave like a native app
  - [ ] Offline capabilities
- [ ] **RPG Character**
  - [x] Leveling
  - [x] Skill training
- [ ] **MMO elements**
  - [x] PvP
  - [x] Leaderboard
- [x] **Events**
  - [x] Social distancing event - bar is closed
- [x] **Darknet** - black market
  - [x] Drug trade
    - [x] Drug prices change every 5 hours to a semi-random value. Business 101: Buy low, sell high
  - [ ] Weapon trade
- [ ] **Buildings**
  - [x] Player lands - each player will have own land with set number of plots to build buildings on
  - [x] Resource production (drug labs, plantations, etc)
- [ ] **Market** - for buying other stuff
- [x] **Bar** - this is where you'll get your missions from
  - [x] Missions
- [x] **Wastelands** - area outside the city
  - [x] Scavenging
    - [x] Go scavenging into the wastelands and get some small reward
    - [x] Useful for when you will be out of the game for a while, so that your character does at least something while you're AFK

## Support the project

You can support this project by donating. Any donation would be a huge help and is much appreciated.

Ko-fi: https://ko-fi.com/U7U2MDBC

PayPal: https://www.paypal.me/matronator

Bitcoin: **35VRpVQaqFWjUCnVRpGineShz76QyYgSVg**

<a href="https://www.blockchain.com/btc/payment_request?address=35VRpVQaqFWjUCnVRpGineShz76QyYgSVg" target="_blank"><img src=".github/btc.png"></a>

## Changelog

For full changelog see [CHANGELOG.md](CHANGELOG.md)

## [0.2.0] - 2020-06-24

- Mobile pop-up informing that the site is PWA compatible and instructions how to install
- Added: Russian translation (by [pase80](https://vk.com/pase80))
- Added: Leaderboard with all player, not just top 10
- Added: News page
- Added: Account settings
  - Timezone preferences
  - Email settings
- Added: Assault statistics
- Added: Land upgrades
  - Players can now upgrade their lands to get more building slots
- Changed: Assaults page and player detail
- Various updates and minor fixes

## [0.1.4] - 2020-05-24

- Updated: Darknet prices (see the [Wiki](https://github.com/matronator/GlobalCollapse/wiki/Darknet) for current prices)
- Added: New jobs
- Added: "Did you know" cards during jobs
- Adjusted: Experience gain from missions

## [0.1.3] - 2020-05-23

- Added: Player Assaults (PvP)!
- Added: Building upgrades
- Fixed: Money overflowing 32bit integer range ([#2][i2])
- UI Adjustments

## [0.1.2] - 2020-05-15

- Added: Buildings
- Added: Player incomes (collected from buildings every 5 hours)
- Adjusted mission and scavenging rewards (now based on level)
- Database optimization

More in [CHANGELOG.md](CHANGELOG.md)

[i2]: https://github.com/matronator/GlobalCollapse/issues/2
