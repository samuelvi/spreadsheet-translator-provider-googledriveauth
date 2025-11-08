# Spreadsheet Translator Google Drive Provider with Authentication

[![Tests](https://github.com/samuelvi/spreadsheet-translator-provider-googledriveauth/workflows/Tests/badge.svg)](https://github.com/samuelvi/spreadsheet-translator-provider-googledriveauth/actions)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.4-blue.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

This package retrieves protected spreadsheet documents from Google Drive with authentication support.

## Features

- 🔐 OAuth2 authentication with Google Drive API
- 📊 Support for Google Sheets spreadsheets
- 🔄 Automatic token refresh
- 📝 Multiple output formats (XLSX, Matrix)
- 🧪 Comprehensive test coverage
- 🚀 PHP 8.4 ready with modern syntax

## Requirements

- PHP >= 8.4
- Composer
- Google Cloud Platform account with Drive API enabled

## Installation

```bash
composer require samuelvi/spreadsheet-translator-provider-googledriveauth
```

## How to Create Google Drive Credentials

1. **Create a project** at [Google Cloud Console](https://console.developers.google.com). You will need a Google account.
2. **Enable the Google Sheets API** following the [official guide](https://developers.google.com/sheets/api/quickstart/php)
3. **Download the Client Configuration file** (credentials.json) to a private folder
4. **First-time authentication**: When running the application for the first time, a URL will be displayed. Open it in your browser, grant permissions, and paste the authorization code back into the terminal.

### Detailed Setup Steps

1. Log into your Google account
2. Go to [Google Cloud Console](https://console.developers.google.com)
3. Create a new project or select an existing one
4. Enable the Google Sheets API and Google Drive API
5. Create OAuth 2.0 credentials (Desktop app type recommended)
6. Download the credentials JSON file
7. Set up the configuration with the path to your credentials file

## Usage

```php
use Atico\SpreadsheetTranslator\Core\Configuration\Configuration;
use Atico\SpreadsheetTranslator\Provider\GoogleDriveAuth\GoogleDriveAuthProvider;

$configuration = new Configuration([
    'application_name' => 'My Application',
    'credentials_path' => '/path/to/credentials.json',
    'client_secret_path' => '/path/to/token.json',
    'source_resource' => 'https://docs.google.com/spreadsheets/d/SPREADSHEET_ID/edit',
    'format' => 'xlsx',
    'temp_local_source_file' => '/tmp/output.xlsx'
]);

$provider = new GoogleDriveAuthProvider($configuration);
$resource = $provider->handleSourceResource();
```

## Development

### Quick Start

```bash
# Install dependencies
make install

# Run tests
make test

# Run Rector code quality checks
make rector-dry

# Run all CI checks
make ci
```

### Available Make Commands

- `make install` - Install all dependencies
- `make update` - Update all dependencies
- `make test` - Run tests
- `make test-coverage` - Run tests with coverage report
- `make rector` - Apply Rector refactoring
- `make rector-dry` - Check Rector suggestions without applying
- `make clean` - Remove vendor and cache directories
- `make ci` - Run all CI checks (rector + tests)

## Testing

The project includes comprehensive unit tests. Run them using:

```bash
make test

# Or with coverage
make test-coverage
```

## Code Quality

This project uses Rector for automated refactoring and code quality improvements:

```bash
# Check what would be changed
make rector-dry

# Apply changes
make rector
```

## Related Projects

- [Spreadsheet Translator Core](https://github.com/samuelvi/spreadsheet-translator-core) - Core library
- [Spreadsheet Translator Symfony Bundle](https://github.com/samuelvi/spreadsheet-translator-symfony-bundle) - Symfony integration

## Contributing

We welcome contributions! Please feel free to submit pull requests or open issues for bugs and feature requests.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Run tests and code quality checks (`make ci`)
4. Commit your changes (`git commit -m 'Add amazing feature'`)
5. Push to the branch (`git push origin feature/amazing-feature`)
6. Open a Pull Request

All contributors must abide by our code of conduct.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Author

**Samuel Vicent** - [samuelvicent@gmail.com](mailto:samuelvicent@gmail.com)