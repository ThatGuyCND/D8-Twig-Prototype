Feature: LESS and SCSS support
    As a prototype editor
    I can use less and scss syntax
    To structure my CSS markup

Scenario: generated stylesheets markup
    When I am on "http://prontotype-foo.lo/less-scss"
    Then the "head" element should contain "_cache/prontotype-foo/assets/_less-test_1.less"
    And  the "head" element should contain "_cache/prontotype-foo/assets/_scss-test_1.scss"

Scenario: generated files
    When I am on "http://prontotype-foo.lo/_cache/prontotype-foo/assets/_less-test_1.less"
    Then the response should contain "background-color: red;"
    When I am on "http://prontotype-foo.lo/_cache/prontotype-foo/assets/_scss-test_1.scss"
    Then the response should contain "background-color: orange;"
