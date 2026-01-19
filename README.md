# ACF Relative URLs

Stores ACF link and URL fields as relative paths for environment portability.

## Problem

ACF stores link fields with absolute URLs. When migrating databases between environments (local → staging → production), these URLs break unless you run search-replace scripts.

## Solution

This mu-plugin intercepts ACF field saves/loads:
- **On save**: Converts internal absolute URLs to relative paths
- **On load**: Converts relative paths back to absolute URLs using current site URL

## Installation

### Via Composer (Bedrock)

```bash
composer require giantpeach/acf-relative-urls
```

### Manual

Copy `acf-relative-urls.php` to your `mu-plugins` directory.

## How It Works

| Action | Input | Stored in DB |
|--------|-------|--------------|
| Save (local) | `https://mysite.lndo.site/about` | `/about` |
| Load (local) | `/about` | `https://mysite.lndo.site/about` |
| Load (production) | `/about` | `https://mysite.com/about` |

External URLs (e.g., `https://google.com`) are not modified.

## Supported Field Types

- `link` - ACF Link field (array with url, title, target)
- `url` - ACF URL field (plain string)

## License

MIT
