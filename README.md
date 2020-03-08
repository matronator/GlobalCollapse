# How to start a project:

## Install dependencies
Run `composer install` and `npm install`
commands in the root folder of the project

## Prepare the data layer
Create database and run SQL in `/init-db.sql`
or import it via database admin
(phpmyadmin/adminer/...)

Connect database in `/app/config/config.local.neon`.
Provide database name, login and password.

## Set your environment variales
Create `/.env` file and set `HOST` with URL where the project is hosted.
(see [example](https://github.com/motdotla/dotenv#usage)).
This step is necessary if you don't use `npm run serve`.
Otherwise, you can go with the defaults.

# Build process
There are two basic modules - front and admin. Use `./dev` and
its respective subfolders to create or edit front-end assets.
Here is an example of the folder structure:


```
/dev
|-- admin
|   |-- (same structure as front)
`-- front
    |-- images
    |      |-- photo.jpg
    |      `-- chart.png
    |-- icons
    |      |-- mail.svg
    |      `-- arrow.svg
    |-- css
    |   |-- index.js
    |   `-- contact.js
    |-- js
    |   |-- index.css
    |   `-- contact.css
    `-- etc
```

All assets are compiled into `/www/dist` folder. For every module
subfolder with its name is created.

Keep in mind that files in `images` and `etc` preserve their original directory.
Other files (css, js, icons) are generated into the root.
For example in `app/components/Hamburger/Hamburger.css` you
should reference external images as follows:

```css
.hamburger {
    background-image: url(images/hamburger.svg);
}
```

## Development
Run `npm start` if you want to develop the front module. For the admin module
use `npm run start-admin` command.

Whenever a file (except `images/*` and `etc/*`) in `/dev` folder
or template is changed, the web server will automatically
refresh your browser window.

## Production
Run `npm run build` if you want to create production build of the front module. For the admin module
use `npm run build-admin` command.

# Asset usage
Because of cache busting, the only way of using your assets is
by means of `{asset}` custom Latte macro. It accepts two parameters.
First is an asset name and the second is a module name (front or admin).

Examples:
```html
<script src="{asset index.js front}"></script>
...
<link rel="stylesheet" href="{asset panel.css admin}" >
```
Currently, the macro doesn't take `$baseUrl` into account,
so **the server must host to the domain root**

# Coding standard

## Javascript
Formatting is handled by [Prettier](https://prettier.io/). Standard
is enforced by [ESLint](https://eslint.org/) rules (see `eslintConfig.rules` in `package.json` for a reference).

## CSS
Formatting is handled by [Prettier](https://prettier.io/). Standard
is enforced by [Stylelint](https://stylelint.io/) rules (see `stylelint.rules` in `package.json` for a reference).

## PHP
WIP

# GIT
All commit messages must be written in English in present tense.

Run `npm run lint-css` and `npm run lint-js` before you commit to check
if your code adheres to the coding standard. It automatically fixes
problems with formatting. Issues which cannot be solved automatically
are displayed to the console.
