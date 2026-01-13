# Helcraw Water Informational Website

**Project Description:**  
Helcraw Water WordPress developed by [Mehluli Hikwa](https://github.com/HikwaMehluli)

**HTML Demo:** [Helcraw Water HTML Demo](https://uncommonpath.netlify.app)
**WordPress Demo:** [Helcraw Water WordPress Demo](...)

## Production Dependencies

### Bootstrap - UI Elements & Components

Install Bootstrap
```bash
npm i --save bootstrap
```

Import Bootstrap styles in your scss file
```scss
@import "../node_modules/bootstrap/scss/bootstrap";
```

Import Bootstrap bundle JS into all HTML pages
```html
<script src="js/bootstrap.bundle.min.js"></script>
```

for WordPress import Bootstrap bundle JS in functions.php inside "wp_enqueue_scripts" function
```php
wp_enqueue_script( 'bootstrap', get_theme_file_uri() . '/js/bootstrap.bundle.min.js', array(), '1.0.0', true );
```

***

### SwiperJS - Placed directly in HTML pages where required
```html
<script src="js/swiper-bundle.min.js"></script>
```

OR WordPress in functions.php inside "wp_enqueue_scripts" function
```php
wp_enqueue_script( 'swiper', get_theme_file_uri() . '/js/swiper-bundle.min.js', array(), '1.0.0', true );
```

for the SCSS/CSS import in your scss file
```scss
@import './swiper-bundle.min';
```

***

### JQuery for easy DOM manipulation & WordPress plugin support
```
npm i jquery
```

## Development Dependencies
#### Install SASS, Webpack & Webpack CLI

You do not need to install these development dependencies if you have them installed globally on your machine.
```bash
npm i sass webpack webpack-cli --save-dev
```

## WordPress Plugin Dependencies
#### ✔️ = Required, ❌ = Optional

❌ Advanced Custom Fields (ACF) - https://wordpress.org/plugins/advanced-custom-fields/

✔️ Carbon Fields - https://carbonfields.net

✔️ Yoast SEO - https://wordpress.org/plugins/wordpress-seo/

_____________________