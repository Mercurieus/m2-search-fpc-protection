# Magento 2 Search FPC Protection Module

## Overview
The "Search FPC Protection" module prevents Magento from caching pages when the search engine is down or not responding properly. This helps maintain a better user experience by ensuring that cached pages with potentially broken search functionality are not served to users.

## Installation

### Composer Installation
```bash
composer require mercurieus/m2-search-fpc-protection
```
### Manual Installation
1. Create the following directory structure in your Magento installation:
   
   ```app/code/Mercurieus/SearchFpcProtection```
   
2. Download the module files and place them in this directory
3. Enable the module by running:
   
   ```bin/magento module:enable Mercurieus_SearchFpcProtection```
   
4. Run the Magento setup upgrade:
   
   ```bin/magento setup:upgrade```
   
5. Compile Magento (production mode):

   ```bin/magento setup:di:compile```
   
6. Deploy static content (production mode):

   ```bin/magento setup:static-content:deploy```
   
7. Clear the cache:

   ```bin/magento cache:clean```
   

## Compatibility
- Magento 2.3.x, 2.4.x
- Requires Magento Search module (101.1.*)

## Features
- Prevents full page caching when the search engine is unavailable
- Seamlessly integrates with Magento's existing caching system
- No configuration needed - works out of the box

## How It Works
The module monitors the health of the search engine and dynamically adjusts the page caching behavior. When the search engine is detected as unavailable, the module prevents those pages from being cached, ensuring users always get functional search results.

## License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support
For issues or feature requests, please create an issue in the GitHub repository.