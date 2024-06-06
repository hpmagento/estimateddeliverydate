# Hardik_EstimatedDeliveryDate Module

## Overview

The `Hardik_EstimatedDeliveryDate` module adds functionality to calculate and display the earliest possible delivery
date for Flat Rate, Free Shipping and Table Rate shipping method during the checkout process in Magento 2. The module takes into account various factors
such as order date and time, delivery and dispatch days, exception dates, and cutoff times.

## Installation

1. **Download the module into your Magento 2 project:**
   ```sh
   app/code/Hardik/EstimatedDeliveryDate
2. **Enable the module:**
   ```sh
   php bin/magento module:enable Hardik_EstimatedDeliveryDate
3. **Run the setup upgrade command:**
    ```sh
   php bin/magento setup:upgrade
4. **Deploy static content:**
   ```sh
   php bin/magento setup:static-content:deploy
5. **Generated code and dependency**
    ```sh
   php bin/magento setup:di:compile
6. **Clear the cache:**
   ```sh
   php bin/magento cache:clean


## Configuration
**Access the configuration settings:**
```sh
Navigate to Stores > Configuration > Sales > Shipping Methods.
```
**Configure the delivery and dispatch settings:**

For each shipping method (e.g., Flat Rate, Free Shipping), you can configure the following settings:
```sh
Delivery Days
Dispatch Days
Delivery Time
Dispatch Cutoff Time
Delivery Exception Dates
Dispatch Exception Dates
```
## Note
This extension is tested on magento version 2.4.6-p3
Review recorded of testing this extension in magento version 2.4.6-p3 : https://www.awesomescreenshot.com/video/28363598?key=ca787448e64aef0346493a5d1fb24782

"# estimateddeliverydate" 
