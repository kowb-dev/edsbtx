# GEMINI.md

## Project Overview

This project is a Bitrix CMS-based e-commerce website for a company called "EDS - Electric Distribution Systems". The website sells professional solutions for power distribution and signal management. The site is built using PHP, with a custom Bitrix template and components. It uses AJAX for a dynamic user experience, including features like adding products to the cart, a newsletter subscription, and contact forms.

## Building and Running

There are no explicit build steps documented in the files. The project seems to be a standard Bitrix installation. To run the project, you would typically need a web server with PHP and a MySQL database, and you would need to configure the Bitrix environment.

**TODO:** Document the exact steps for setting up a local development environment.

## Development Conventions

*   The project uses a custom Bitrix template located in `/local/templates/edsy_main/`.
*   Custom components are located in `/local/components/eds/`.
*   Global PHP functions and event handlers are defined in `/local/php_interface/init.php`.
*   AJAX handlers are located in the `/local/ajax/` directory.
*   The code is written in Russian, including comments and variable names.
*   The project uses jQuery and the Phosphor Icons library.
*   There is a commented-out "Bitrix License Blocker" in `php_interface/init.php`, which suggests that the project might be running on an unlicensed version of Bitrix.

