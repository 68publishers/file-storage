# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.0.0] - 2026-10-28

### Changed

- Minimal PHP version is 8.3 (BC break).
- Added support for `doctrine/dbal` 4, `symfony/console` 7 and 8, `guzzlehttp/psr7` 3 (dev), `nette/application` 3.3 and `latte/latte` 3.1.
- The Doctrine integration is tested with `68publishers/doctrine-bridge` 2.
- `symfony/console` older than 5.4 is no longer supported (conflict `<5.4`, BC break).
- Updated dev dependencies: PHPStan 2, `phpstan/phpstan-nette` 2, `nette/tester` 2.6+, `mockery/mockery` 1.6.12+.
- Tests no longer hide deprecations.
- GitHub Actions run the tests on PHP 8.3, 8.4 and 8.5 with the highest dependencies and on PHP 8.3 with the lowest dependencies.

### Fixed

- `ResourceFactory::createResourceFromFile()` with a URL: the MIME type and the file size are read from the headers of the final response when redirects are followed (previously the headers of the first, redirect response were used).
- `ResourceFactory::createResourceFromFile()` with a URL handled by a user-space stream wrapper (e.g. `s3://`): `getMimeType()` and `getFilesize()` no longer throw a `TypeError`.
- `StorageCleaner` detects kept files (`.gitignore`, `.gitkeep`) on Windows too, Flysystem paths always use `/`.

## [1.4.1] - 2026-03-04

### Changed

- Updated HTTP headers when a resource is created using URL.

## [1.4.0] - 2026-02-20

### Added

- Added argument `$absolute = true` to `LinkGeneratorInterface` and `FileInfoInterface`.

## [1.3.0] - 2025-05-29

### Added

- Added compatibility with PHP 8.4.

## [1.2.0] - 2025-05-13

### Added

- Added method `ResourceFactoryInterface::createResourceFromPsrStream()` - support for PSR-7 streams.
- Added stream context option `follow_location: true` when resource is created from an url.
- Added tests.

## [1.1.3] - 2024-08-06

### Added

- Added support for `psr/log:^2.0` and `psr/log:^3.0`.

## [1.1.2] - 2024-03-20

### Fixed

- Fixed `Content-Type` header parsing in `ResourceFactory::getResourceFromUrl()`.

## [1.1.1] - 2024-03-12

### Added

- Added method `FileInfoInterface::toArray()` (same output as `FileInfoInterface::jsonSerialize()`).

## [1.1.0] - 2024-03-12

### Added

- Added resource class `StreamResource`.
- Added methods `ResourceInterface::getMimeType()` and `ResourceInterface::getFilesize()`.

### Changed

- Renamed method `ResourceFactoryInterface::createResourceFromLocalFile()` to `ResourceFactoryInterface::createResourceFromFile()`.
- ResourceFactory can now create a resource from a URL.
- Changed PHP-Cs-Fixer configuration.

### Removed

- Removed resource class `SimpleResource`.

### Fixed

- Fixed unit tests.

## [1.0.0] - 2022-12-29

### Added

- Added support for PHP `^8.1` (PHP `7` not supported).
- Added support for Latte `^3.0` (Latte `2` not supported).
- Added Dockerfile and docker-compose.yml with containers for all supported PHP versions, Makefile, GitHub Actions, PHPStan (level 9) and full coverage tests.

### Changed

- Updated version of Symfony dependencies to `^5.4 || ^6.0`.
- Interfaces (and their implementors) `PathInfoInterface` and `FileInfoInterface` are now immutable.
- Added strict types for all properties and return types for all methods.
- Improved coding style.
- Updated README.

## [0.2.0] - 2022-12-26

### Changed

- Added support for PHP 8.1 only.
- Refactored the code with PHP 8.1 features.
- Improved config style.
- Added PHPStan, Dockerfile and docker-compose.yml with PHP 8.1, Makefile and GitHub Actions.
- Updated README.

## [0.1.2] - 2021-10-27

### Fixed

- The method `Path::parse()` returned a string `.` when an empty string was passed as the argument.

## [0.1.1] - 2021-01-28

### Fixed

- Fixed whitespaces in README and return type declaration in Resource.

## [0.1] - 2021-01-28

### Added

- Initial release.

[Unreleased]: https://github.com/68publishers/file-storage/compare/v2.0.0...HEAD
[2.0.0]: https://github.com/68publishers/file-storage/compare/v1.4.1...v2.0.0
[1.4.1]: https://github.com/68publishers/file-storage/compare/v1.4.0...v1.4.1
[1.4.0]: https://github.com/68publishers/file-storage/compare/v1.3.0...v1.4.0
[1.3.0]: https://github.com/68publishers/file-storage/compare/v1.2.0...v1.3.0
[1.2.0]: https://github.com/68publishers/file-storage/compare/v1.1.3...v1.2.0
[1.1.3]: https://github.com/68publishers/file-storage/compare/v1.1.2...v1.1.3
[1.1.2]: https://github.com/68publishers/file-storage/compare/v1.1.1...v1.1.2
[1.1.1]: https://github.com/68publishers/file-storage/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/68publishers/file-storage/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/68publishers/file-storage/compare/v0.2.0...v1.0.0
[0.2.0]: https://github.com/68publishers/file-storage/compare/v0.1.2...v0.2.0
[0.1.2]: https://github.com/68publishers/file-storage/compare/v0.1.1...v0.1.2
[0.1.1]: https://github.com/68publishers/file-storage/compare/v0.1...v0.1.1
[0.1]: https://github.com/68publishers/file-storage/releases/tag/v0.1
