# Changelog

All notable changes to `php-filelist` will be documented in this file.

## v0.1.02 - 2025-09-02

Fix a problem with specific characters like `{}`.

## v0.1.01 - 2024-08-24

Add alternative to `glob` for OS like Alpine Linux

## v0.1.0 - 2024-07-25

**BREAKING CHANGES**

- Now `throwOnError()` method is removed. By default, `FileList::class` will throw an error if command fails. If you want to ignore the error, you can use `safeOnError()` method.
- `withScout()` is now `withScoutSeeker()` because `scout-seeker` is publish on crates.io.

## v0.0.33 - 2024-07-13

Add `onlyExtensions()` method to parse only files with specific extensions.

## v0.0.32 - 2024-07-13

Add `onlyExtensions()` method to parse only files with specific extensions.

## v0.0.31 - 2024-06-12

Upgrade `scout` binary to 0.2.0

## v0.0.3 - 2024-06-12

Add `toArray()` and `getArguments()` to `FileListCommand::class` to help for debug

## v0.0.2 - 2024-06-12

Add `getCommand()` to `FileList::class` to get command output when binary is used.
