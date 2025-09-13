########################
What is this repository?
########################

This is a fork of CodeIgniter 3, with the goal of keeping it up to date with **PHP 8.4** and beyond. There is no intention to add new features or change the way CI3 works. This is purely a maintenance fork.

The original CodeIgniter 3.x branch is no longer maintained, and has not been updated to work with PHP 8.2, or any newer version. This fork is intended to fill that gap.

If the original CodeIgniter 3.x branch is updated to work with PHP 8.2+, and starts to be maintained again, this fork might be retired.

****************
Issues and Pulls
****************

Issues and Pull Requests are welcome, but please note that this is a maintenance fork. New features will not be accepted. If you have a new feature you would like to see in CodeIgniter, please submit it to the original CodeIgniter 3.x branch.

*******************
Server Requirements
*******************

PHP version 5.4 or newer, same as the original CI3 requirements.

************
Installation
************

You can install this fork using Composer:

.. code-block:: bash

	composer require pocketarc/codeigniter

After installation, you need to point CodeIgniter to the new system directory. In your `index.php` file, update the `$system_path` variable:

.. code-block:: php

	$system_path = 'vendor/pocketarc/codeigniter/system';

**Alternative Installation (Manual)**

If you prefer the traditional approach of replacing the system directory:

1. Download this repository
2. Replace your existing `system/` directory with the one from this fork
3. No changes to `index.php` are needed with this method

**Note:** The Composer method makes future updates easier with `composer update`, while the manual method requires downloading and replacing the system directory each time.

**Upgrading from Original CI3**

If you're migrating from the original CodeIgniter 3.x:

1. Install via Composer as shown above
2. Update the `$system_path` in your `index.php`
3. Your existing `application/` directory remains unchanged
4. Test thoroughly with your PHP version (especially if using PHP 8.2+)
