# TYPO3 Extension redirect_tab

## 1 Features

* Show redirects of the respective page in a tab in the page properties

## 2 Usage

### 2.1 Installation

#### Installation using Composer

The recommended way to install the extension is using Composer.

Run the following command within your [Composer][1] based TYPO3 project:

```
composer require ayacoo/redirect-tab
```

## 3 Administration corner

### 3.1 Versions and support

| redirect_tab | TYPO3 | PHP       | Support / Development       |
|--------------|-------|-----------|-----------------------------|
| 4.x          | 13.x  | 8.2 - 8.4 | features, bugfixes, security updates |
| 3.x          | 12.x  | 8.1 - 8.4 | bugfixes, security updates  |
| 2.x          | 11.x  | 7.4 - 8.0 | bugfixes, security updates  |
| 1.x          | 10.x  | 7.2 - 7.4 | no support any more         |

### 3.2 Release Management

redirect_tab uses [**semantic versioning**][2], which means, that

* **bugfix updates** (e.g. 1.0.0 => 1.0.1) just includes small bugfixes or
  security relevant stuff without breaking changes,
* **minor updates** (e.g. 1.0.0 => 1.1.0) includes new features and smaller
  tasks without breaking changes,
* and **major updates** (e.g. 1.0.0 => 2.0.0) breaking changes which can be
  refactorings, features or bugfixes.

### 3.3 Contribution

**Pull Requests** are gladly welcome! Nevertheless please don't forget to add an
issue and connect it to your pull requests. This
is very helpful to understand what kind of issue the **PR** is going to solve.

**Bugfixes**: Please describe what kind of bug your fix solve and give us
feedback how to reproduce the issue. We're going
to accept only bugfixes if we can reproduce the issue.

## 4 Developer corner

### 4.1 PSR-14 Events

**ModifyRedirectsEvent**

The ModifyRedirectsEvent offers the possibility to influence the resultset of
the redirects. For example, the LIKE %Value% search from the core sometimes
displays incorrect hits for the respective page. These can be filtered out with
the event.

Example for registration:

```
# EXT:my_ext/Configuration/Services.yaml
services:
    Vendor\Extension\Listener\MyListener:
    tags:
    - name: event.listener
      identifier: 'ext-extension/myListener'
      method: 'myListenerMethod'
      event: Ayacoo\RedirectTab\Event\ModifyRedirectsEvent
```

## 5 Thanks / Notices

Special thanks to Georg Ringer and his [news][3] extension. A good template to
build a TYPO3 extension. Here, for example, the structure of README.md is used.

This extension is a result of a core proof of concept and uses many parts of the
TYPO3 core, especially parts of the redirect extension.

[1]: https://getcomposer.org/

[2]: https://semver.org/

[3]: https://github.com/georgringer/news

## 6 Support

If you are happy with the extension and would like to support it in any way, I
would appreciate the support of social institutions.
