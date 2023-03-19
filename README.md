<div align="center">
  <h1>Venus</h1>
  <p>A Laravel package that contains everything to write code without thinking about codestyle 🚀</p>
</div>

<div align="center">

[![Putain de CI - Laravel](https://github.com/Manoz/venus/actions/workflows/main.yml/badge.svg?branch=main)](https://github.com/Manoz/venus/actions/workflows/main.yml)
[![version]][version]
[![licenses][licenses]][licenses-url]

</div>

## Motivations

I work a lot on Laravel projects. As a front-end developer, I always have to deal with the front-end stack installation and codestyle/code quality stuff.

I'm tired of doing the same things over and over again. Grab my ESLint config files, copy them to my new project. Grab all PHP Codestyle stuff, copy everything to my new project. Install all the dependencies, and so on.  

I could have used a Laravel starter project but I just love to start from scratch with a fresh Laravel installation using the Laravel installer commands.

This is why I created **Venus**.  

Venus *- the Roman goddess of love, beauty, fertility, prosperity, and desire -* is a **very opinionated** Laravel package designed to streamline the integration of essential code style tools, such as ESLint, Duster, and Prettier, into new Laravel projects. With the help of artisan commands, Venus simplifies the installation process, promoting cleaner and more consistent code across all of my projects.

## Features

This package contains the following tools:

-  **[ESLint](https://eslint.org/):** a tool for identifying and reporting on patterns found in ECMAScript/JavaScript code, with the goal of making code more consistent and avoiding bugs.
- **[Duster](https://github.com/tighten/duster):** a tool for identifying and reporting on patterns found in PHP code, with the goal of making code more consistent and avoiding bugs.
- **[Prettier](https://prettier.io/):** an opinionated code formatter. It enforces a consistent style by parsing your code and re-printing it with its own rules.
- Complete front-end stack installation with **[PostCSS](https://postcss.org/)**, **[Tailwind CSS](https://tailwindcss.com/)** and **[Alpine.js](https://alpinejs.dev/)**.

## Installation

You can install the package via composer:

```bash
composer require manoz/venus --dev
```

## Usage

Once the package is installed, you can run the following artisan commands to install the tools:

**Install everything:**
```bash
php artisan venus:install --all

npm install
```

**Install only ESLint:**
```bash
php artisan venus:install --eslint

npm install
```

**Install only Duster:**
```bash
php artisan venus:install --duster
```

**Install only Prettier:**
```bash
php artisan venus:install --prettier

npm install
```

**Install only the Front-end stack:**
```bash
php artisan venus:install --frontend

npm install
```

Don't forget to import Tailwind CSS in your `resources/css/app.css` file:

```css
@import "./tailwind.css";
```

Each tool will have its own configuration file and dependencies.
Everything will be installed for you and config files will be copied to your project.

**Be careful**: this package will overwrite your existing ESLint, Duster and Prettier config files.  
It will also add npm scripts and package to your `package.json` file.  
It will also add composer scripts to your `composer.json` file.

## Configuration

### ESLint

You can configure ESLint by editing the `.eslintrc.js` file in the root of your project. More informations can be found on the [ESLint website](https://eslint.org/docs/latest/use/configure).

### Duster

There's a lot of stuff happening behind the scenes with Duster.  
You should check usage and options on the [Duster](https://github.com/tighten/duster) website.

### Prettier

You can configure Prettier by editing the `.prettierrc.json` file in the root of your project. More informations can be found on the [Prettier website](https://prettier.io/docs/en/configuration.html).


### Front-end stack

Tailwind CSS and Alpine.js are already configured. You can extend Tailwind's configuration by editing the `tailwind.config.js` file in the root of your project. More informations can be found on the [Tailwind CSS website](https://tailwindcss.com/docs/configuration).

## Contributing

Thank you for considering contributing to Venus! The contribution guide can be found [here](.github/CONTRIBUTING.md).

## Security Vulnerabilities

If you discover any security-related issues, please review [the security policy](https://github.com/Manoz/venus/security/policy) instead of using the issue tracker.

## License

Licensed under the MIT License, Copyright © Manoz.

See [LICENSE](https://github.com/Manoz/venus/blob/main/LICENSE) for more information.

[licenses-url]: https://github.com/Manoz/venus/blob/main/LICENSE
[licenses]: https://img.shields.io/badge/license-MIT-blue.svg

[version]: https://img.shields.io/badge/version-1.1.0-%23d85a94.svg
