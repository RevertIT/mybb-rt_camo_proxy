## RT Camo Proxy
is a plugin which camouflages images inside posts and prevent 3rd-party image hosting obtaining visitors IP addresses. Instead your server will get image and serve it directly (and ask browser to cache it).

### Table of contents
1. [❗ Dependencies](#-dependencies)
2. [📃 Features](#-features)
3. [➕ Installation](#-installation)
4. [🔼 Update](#-update)
5. [➖ Removal](#-removal)
6. [💡 Feature request](#-feature-request)
7. [🙏 Questions](#-questions)
8. [🐞 Bug reports](#-bug-reports)
9. [📷 Preview](#-preview)

### ❗ Dependencies
- MyBB 1.8.x
- https://github.com/frostschutz/MyBB-PluginLibrary (>= 13)
- PHP >= 7.4 (preferred 8.0 and up)

### 📃 Features
* Camouflage images to prevent ip-address sniffing on users from 3rd-party hostings.
* Set which usersgroups can use this feature
* Once image is set, it will ask browser to cache it for several hours

### ➕ Installation
1. Copy the directories from the plugin inside your root MyBB installation.
2. Settings for the plugin are located in the "Plugin Settings" tab. (`/admin/index.php?module=config-settings`)

### 🔼 Update
1. Deactivate the plugin.
2. Replace the plugin files with the new files.
3. Activate the plugin again.

### ➖ Removal
1. Uninstall the plugin from your plugin manager.
2. _Optional:_ Delete all the RT Camo Proxy plugin files from your MyBB folder.

### 💡 Feature request
Open a new idea by [clicking here](https://github.com/RevertIT/mybb-rt_camo_proxy/discussions/new?category=ideas)

### 🙏 Questions
Open a new question by [clicking here](https://github.com/RevertIT/mybb-rt_camo_proxy/discussions/new?category=q-a)

### 🐞 Bug reports
Open a new bug report by [clicking here](https://github.com/RevertIT/mybb-rt_camo_proxy/issues/new)

### 📷 Preview
Changes original image eg: `somedomain.com/image.png` to `myforumdomain.com/misc.php?action=rt_camo&digest=privatekey&image=hashedimage`
