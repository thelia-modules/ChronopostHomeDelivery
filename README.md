# ChronopostHomeDelivery

Allows you to choose between differents delivery modes offered by Chronopost.
Activating one or more of them will let your customers choose which one
they want.

Delivery types currently availables :

- Chrono13
- Chrono18
- Chrono Classic (Delivery in Europe)
- Chrono Express (Express delivery in Europe)
- Fresh13
- Others will be added in future versions

NB1 : You need IDs provided by Chronopost to use this module.


## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is Chronopost.
* Activate it in your thelia administration panel

### Composer

Add it in your main thelia composer.json file

```
composer require thelia/chronopost-home-delivery-module:~1.0
```

## Usage

First, go to your back office, tab Modules, and activate the module Chronopost.
Then go to Chronopost configuration page, tab "Advanced Configuration" and fill the required fields.

After activating the delivery types you wih to use, new tabs will appear. With these, you can
change the shipping prices according to the delivery type and the area, and/or activate free shipping for a given price and/or given area, or just
activate it no matter the are and cart amount.

If you also have the ChronopostLabel module, you can then generate and download labels from the Chronopost Label page accessible from the toolbar on the left of the BackOffice, or directly from the order page.


## Loop

To be written

##Integration

Templates are examples of integration for the default theme of Thelia and should probably be
modified to suit your website better.
