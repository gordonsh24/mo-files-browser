# MO Files reviewer

The plugin to review content of MO files.

### Set up

1. You have to use PHP 7.4 or higher
2. Run `composer install --no-dev`
3. Activate plugin: `wp plugin activate mo-files-browser`

### Example of usage

1. Display first 20: `wp mo browser file_path`
2. Display first 50: `wp mo browser file_path --limit=50`
3. Display 30 entries with offset 20: `wp mo browser file_path --limit=30 --offset=20`
4. Search for phrase `lorem` in singular, plural and all translations: `wp mo browser file_path --search=lorem`
5. You can use `--limit` and `--offset` with `--search`
 
