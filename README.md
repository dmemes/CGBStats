# CGBStats #
Source for https://cgbstats.cf - a website and ClashGameBot mod to track and aggregate bot statistics.

## Contributing ##
Feel free to send a pull request for anything you'd like to see changed! See the section below.

## Setting up CGBStats for local testing ##

CGBStats requires an Apache/MySQL/PHP stack, and requires the PHP Freetype and GD extensions.

1. Create a new database in MySQL, and import `include/config/setup.sql` using a MySQL administration tool like PHPMyAdmin
2. Edit `include/config/config.php` and enter the config values required (set domain to "localhost")