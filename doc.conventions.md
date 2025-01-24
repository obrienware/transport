# Naming Convensions

HTML IDs should use Kabeb Case and should start with the shorthand element type followed the name (e.g. `tbl-vehicles` or `btn-my-button`)

HTML Form names will also use Kabeb Case.

Action button IDs as above should use Kabeb Case, start with the element type, then the action name, then the name (e.g. `btn-save-vehicle`)

CSS classes should all use Kabeb Case (unles otherwise required by a 3rd party library)

Javascript variables will all use Camel Case.

MySQL column names will use Snake Case.

PHP variables will use Camel Case. (Note when converting data from MySQL to Object properties and visa versa)

## File naming

File names will be lower case using a period (.) to separate the parts of the filename.

Classes will be in the classes folder and will carry the same naming convension as the path in which it resides (taking into account the namespace of the class). Class names be in PascalCase so will always start with a capital letter. So for example - the `\Transport\Base` class will be in `/classes/Transport/Base.php`.

Primary pages (that include HTML headers and footers) will start with the keyword `page.` with the exception of `index.php` itself.

PHP/HTML Sections that will be loaded via ajax will start with the keyword ".section". The "subject" will use Kabeb Case. Where applicable will start with the name of the action. When the content is primarily a list table the action will be `list` - as in `section.list-` (e.g. `section.list-drivers.php`), and when the content is a edit form, the action will be `edit` as in `section.edit-` (e.g. `section.edit-user.php`)

PHP Include files will start with `inc.`

REST operations (e.g. POST and GET) where json data is exchanged should be located in the `/api` folder. The filename should start with the request method (e.g. `post.` or `get.`). The "subject" of the filename should use Kabeb Case and if an identifiable action is performed, the name of the action should be prepended (e.g. `save-user` so the full filename would be `/api/post.save-user.php`)

Event Names will use Camel Case