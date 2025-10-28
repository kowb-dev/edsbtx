How it Works

The system uses two different storage methods for favorites:
   * For Authorized Users: Favorite product IDs are stored in the database, in
     a custom user field named UF_FAVORITES.
   * For Guest (Unauthorized) Users: Favorites are stored in the browser's
     localStorage using the key userFavorites.

  When a user logs in, the favorites from their browser's localStorage are
  merged with their server-side favorites.

  File Roles:

  Here is a breakdown of the key files and what they do:

  1. Backend Logic (PHP)

   * `local/php_interface/lib/FavoritesController.php`: This is the main
     controller for authorized users.
       * Role: It handles adding, removing, and merging favorites using modern
         Bitrix D7 architecture.
       * `toggleAction()`: Adds or removes a product from the logged-in user's
         UF_FAVORITES field in the database.
       * `mergeAction()`: Merges favorites from a guest's browser storage with
         their user profile upon login.

   * `local/ajax/toggle_favorite.php`: This is the AJAX handler for guest 
     users.
       * Role: It adds or removes product IDs from the
         $_SESSION['CATALOG_FAVORITES'] array. This seems to be a fallback or
         an alternative implementation, as the primary guest functionality is
         handled client-side.

   * `ajax/get_favorites_html.php`:
       * Role: This script is used to display the list of favorite products
         for guest users.
       * Functionality: It receives a list of product IDs, sets a global
         filter ($arrFavoritesFilter), and uses the bitrix:catalog.section
         component to render and return the HTML for just those products.

  2. Frontend Logic (JavaScript)

   * `local/templates/edsy_main/js/favorites.js`:
       * Role: This file manages the entire "Favorites" experience for guest 
         users on the client side.
       * `FavoriteManager` object:
           * get()/set(): Reads and writes favorite product IDs to the
             browser's localStorage.
           * add()/remove(): Adds or removes a single product ID.
           * updateCounter(): Updates the number displayed on the favorites
             icon in the header.

  3. Display and Page Structure

   * `personal/favorites/index.php`: This is the "Favorites" page.
       * Role: It displays the list of favorite products.
       * For Authorized Users: It gets the favorite IDs from the UF_FAVORITES
         user field and uses the bitrix:catalog.section component to display
         the products.
       * For Guest Users: It redirects to the login page, but the client-side
         JavaScript likely intercepts this and uses an AJAX call to
         ajax/get_favorites_html.php to display the items from localStorage.

   * `local/templates/edsy_main/header.php`:
       * Role: Contains the header of the site, including the link to the
         favorites page and the counter.
       * `#favorites-counter`: A <span> element that is updated by
         favorites.js to show the number of items in the favorites list.

 local/ajax/favorites-handler.php is a fallback AJAX handler for managing the
  "Favorites" list for authorized users.

  Here is its specific role:

   1. Purpose: It's designed to add or remove products from a logged-in user's
      favorites list. It directly modifies the UF_FAVORITES custom field in the
      user's profile, which is the same method used by the main
      FavoritesController.php.

   2. Fallback Mechanism: The comments explicitly state that this is a
      "fallback handler". This means it's intended to be used in situations
      where the more modern Bitrix D7 AJAX method (BX.ajax.runAction(), which
      calls FavoritesController.php) is not available or suitable. It provides
      an alternative way to achieve the same result.

  In short, it's a secondary, more traditional AJAX endpoint that duplicates
  the core "toggle favorite" functionality to ensure compatibility across
  different parts of your website.
Add to favorite buttons locations:
Catalog page: local/templates/.default/components/bitrix/catalog.section/edsys_category
Element page: local/templates/.default/components/bitrix/catalog.element/edsys_product_detail