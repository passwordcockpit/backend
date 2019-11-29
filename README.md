# password-cockpit/backend

## General
This markdown shows information related to the backend side of the project. For more detailed information about the project please check the [Passwordcockpit README](https://github.com/passwordcockpit/passwordcockpit/blob/master/README.md).

## Running tests

Tests are run with `Codeception`. They are under the `tests/api` folder.<br>
Setting and more information for the tests can be found in the `tests/api.suite.yml` file.

Run with `vendor/bin/codecept run api`

Single files can be run with `vendor/bin/codecept run api PasswordsTestCest`

## Language
Current supported languages are: `'en', 'it', 'de', 'fr'`.
To install a custom language in the backend, it has to be already [installed in frontend](https://github.com/passwordcockpit/frontend/blob/master/README.md).
Then under the `config/language/` folder, create your own poedits files following the structure of the existing ones.