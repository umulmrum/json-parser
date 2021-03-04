# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.4.0] - 2021-03-04

### Added
- Support for PHP 8.0.
- Tests run by GitHub action.
- Psalm (level 2).

### Changed
- FileDataSource no longer uses a buffer, but relies on PHP buffering.

### Fixed
- UTF-8 characters do no longer lead to "rare" errors.

### Removed
- [BC Break] Remove InvalidJsonException::trigger (throw exception directly instead - should only have been used internally).

[Unreleased]: https://github.com/umulmrum/json-parser/compare/0.4.0...master
[0.4.0]: https://github.com/umulmrum/json-parser/compare/0.3.3...0.4.0
