# Adminx Contribution Guide
To start contributing to this project:

- Fork this repository on Github
- Clone your fork
- Create a branch
- Make your changes and commit
- Push your branch to your fork
- Make a pull request on github to this project

## Branch
Send pull requests to branch `master`.

## Tests
To run the tests, run this command:

```bash
$ composer test
```

## PHP CS Fixer
To run php cs fixer:

```bash
$ composer format
```

This command makes style fixes changes on the source code.

## Make ready environment
After cloning this project, do the following steps to make ready development environment:

```bash
git clone <your-fork>
cd adminx

# install the dependencies
composer install

# run php cs and tests
composer all
```

Enjoy it!

## Idea
If you want to find a task for contribution:

- [Check TODO file](/TODO.md)
- [Check Github issues](https://github.com/parsampsh/adminx/issues)
