# WordPress.org SVN Instructions

After your plugin is approved for the WordPress.org plugin directory, you will receive SVN access details.

## SVN Structure

The SVN repository for WordPress.org plugins has the following structure:

```
/assets/    - Plugin assets (banner, icon, screenshots)
/tags/      - Tagged releases (1.0.0, 1.1.0, etc.)
/trunk/     - Current development version
```

## Initial SVN Setup

1. After receiving SVN access, check out the repository:

```bash
svn checkout https://plugins.svn.wordpress.org/wp-devlog/
cd wp-devlog
```

2. Copy your plugin files to the `/trunk` directory:

```bash
# From your development directory
cp -R * /path/to/svn/wp-devlog/trunk/
```

3. Copy assets to the `/assets` directory:

```bash
cp -R assets/* /path/to/svn/wp-devlog/assets/
```

4. Add all files to SVN:

```bash
cd /path/to/svn/wp-devlog/
svn add trunk/* --force
svn add assets/* --force
```

5. Commit the changes:

```bash
svn commit -m "Initial plugin version"
```

## Tagging a New Release

When you release a new version:

1. Update version numbers in:
   - devlog.php (Plugin header)
   - readme.txt (Stable tag)
   - Update changelog in readme.txt

2. Commit the changes to trunk:

```bash
svn commit -m "Update to version X.Y.Z"
```

3. Create a new tag:

```bash
svn copy https://plugins.svn.wordpress.org/wp-devlog/trunk https://plugins.svn.wordpress.org/wp-devlog/tags/X.Y.Z -m "Tagging version X.Y.Z"
```

## Updating Plugin Assets

To update banners, icons, or screenshots:

```bash
cd /path/to/svn/wp-devlog/
svn commit assets/ -m "Update plugin assets"
```

## WordPress.org Plugin Guidelines

Always ensure your plugin follows the [WordPress.org Plugin Guidelines](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/).

Common issues to avoid:
- Make sure all code is GPL compatible
- Do not include obfuscated code
- Make sure the plugin works with the latest WordPress version
- Do not track users without consent
- Keep the SVN repository clean and well-organized 