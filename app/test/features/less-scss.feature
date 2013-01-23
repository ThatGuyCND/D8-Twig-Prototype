Feature: LESS and SCSS support
    As a prototype editor
    I can use less and scss syntax
    To structure my CSS markup

Scenario: stylesheets markup
    When I am on "http://prontotype-foo.lo/less-scss"
    Then the "head" element should contain "_cache/foo/assets/_less-test_1.less"
    And  the "head" element should contain "_cache/foo/assets/_scss-test_1.scss"
