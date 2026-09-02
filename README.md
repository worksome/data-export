# Worksome Data Export Package

[![Tests](https://img.shields.io/github/actions/workflow/status/worksome/data-export/tests.yml?style=flat-square&label=Tests)](https://github.com/worksome/data-export/actions/workflows/main.yml)
[![Code Analysis](https://img.shields.io/github/actions/workflow/status/worksome/data-export/static.yml?style=flat-square&label=Static%20Analysis)](https://github.com/worksome/data-export/actions/workflows/code-analysis.yml)

## Installation

```php
// config/app.php
'providers' => [
...
        /*
         * Package Service Providers...
         */
        Worksome\DataExport\DataExportServiceProvider::class,
...
];
```
